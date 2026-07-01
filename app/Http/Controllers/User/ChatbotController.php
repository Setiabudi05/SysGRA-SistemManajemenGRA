<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');
        if (empty($userMessage)) {
            return response()->json(['reply' => 'Pesan tidak boleh kosong.'], 400);
        }

        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-3.1-flash-lite');

        if (empty($apiKey)) {
            return response()->json(['reply' => 'Kunci API Gemini (GEMINI_API_KEY) tidak ditemukan di konfigurasi server.'], 500);
        }

        // 1. Ekstraksi otomatis dari PDF jika diperlukan
        $pdfPath = base_path('griya_rias_asmara.pdf');
        $txtPath = storage_path('app/griya_rias_asmara.txt');

        if (file_exists($pdfPath)) {
            if (!file_exists($txtPath) || filemtime($pdfPath) > filemtime($txtPath)) {
                $scriptPath = base_path('extract_pdf.py');
                if (file_exists($scriptPath)) {
                    try {
                        // Periksa apakah shell_exec diaktifkan di php.ini
                        if (function_exists('shell_exec') && !in_array('shell_exec', explode(', ', ini_get('disable_functions')))) {
                            // Di Linux/Ubuntu gunakan python3, di Windows gunakan python
                            $pythonCmd = (stripos(PHP_OS, 'WIN') === 0) ? 'python' : 'python3';
                            shell_exec($pythonCmd . " " . escapeshellarg($scriptPath) . " 2>&1");
                        }
                    } catch (\Exception $e) {
                        Log::warning('Ekstraksi PDF otomatis via shell_exec gagal: ' . $e->getMessage());
                    }
                }
            }
        }

        // Baca dokumen pengetahuan
        $knowledge = "";
        if (file_exists($txtPath)) {
            $knowledge = file_get_contents($txtPath);
        } else {
            // Fallback sederhana jika file ekstraksi tidak ada
            $knowledge = "Griya Rias Asmara (GRA) merupakan pusat jasa wedding organizer profesional. Nomor kontak: 0838 6130 0111 atau 0858 6665 9930.";
        }

        // 2. System Instruction untuk membatasi ruang lingkup pengetahuan
        $systemInstruction = "Anda adalah Asmara Bot AI, asisten virtual resmi untuk Griya Rias Asmara (GRA).\n" .
            "Tugas Anda adalah melayani dan menjawab pertanyaan pelanggan secara ramah, detail, dan profesional dalam Bahasa Indonesia.\n\n" .
            "PENTING: Anda HANYA diperbolehkan menjawab berdasarkan dokumen pengetahuan resmi Griya Rias Asmara berikut:\n" .
            "--- DOKUMEN PENGETAHUAN ---\n" .
            $knowledge . "\n" .
            "---------------------------\n\n" .
            "ATURAN KETAT:\n" .
            "1. JAWAB HANYA menggunakan informasi yang terdapat dalam dokumen pengetahuan di atas.\n" .
            "2. Jika pertanyaan pelanggan berada di luar konteks dokumen di atas (misalnya menanyakan resep, bantuan pemrograman, informasi umum, obrolan kosong tidak terkait, atau hal lain di luar Griya Rias Asmara), Anda secara otomatis WAJIB MENOLAK menjawab dan menyatakan secara sopan bahwa Anda hanya melayani pertanyaan tentang Griya Rias Asmara saja.\n" .
            "Contoh penolakan: \"Maaf, saya hanya dapat menjawab pertanyaan seputar layanan Griya Rias Asmara.\"\n" .
            "3. Jangan pernah berhalusinasi atau mengarang informasi di luar dokumen.\n" .
            "4. Gunakan gaya bicara yang ramah, sopan, dan hangat (gunakan sapaan seperti Kak/Kakak).\n" .
            "5. Jawablah langsung ke inti pertanyaan dengan format yang rapi dan mudah dibaca.";

        // 3. Kelola chat history (session-based)
        $chatHistory = session()->get('chat_history', []);

        // Tambah pesan baru dari user
        $chatHistory[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ];

        // Batasi histori percakapan (maksimal 10 elemen / 5 giliran)
        if (count($chatHistory) > 10) {
            $chatHistory = array_slice($chatHistory, -10);
        }

        // 4. API Endpoint Gemini v1beta
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => $chatHistory,
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, terjadi kesalahan dalam memproses jawaban Anda.';
                
                // Tambahkan jawaban bot ke histori percakapan
                $chatHistory[] = [
                    'role' => 'model',
                    'parts' => [
                        ['text' => $reply]
                    ]
                ];
                session()->put('chat_history', $chatHistory);

                return response()->json(['reply' => $reply]);
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json(['reply' => 'Maaf, asisten AI sedang sibuk. Silakan coba sesaat lagi.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception in ChatbotController: ' . $e->getMessage());
            return response()->json(['reply' => 'Gagal menghubungi asisten AI: ' . $e->getMessage()], 500);
        }
    }

    public function clear(Request $request)
    {
        session()->forget('chat_history');
        return response()->json(['status' => 'success', 'message' => 'Histori obrolan berhasil dibersihkan.']);
    }
}

