<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        // Data Paket untuk bot
        $systemPrompt = "Anda adalah asisten virtual Griya Rias Asmara. " .
            "Gunakan informasi berikut untuk menjawab: " .
            "- Paket A: Rias + Busana (Rp 5.000.000) " .
            "- Paket B: Rias + Busana + Dekorasi (Rp 10.000.000) " .
            "Pertanyaan pelanggan: " . $userMessage;

        // Menjadi baris ini:
        // Ganti bagian $url menjadi:
        // Gunakan model ini agar lebih stabil dari traffic tinggi
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=" . $apiKey;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    ['parts' => [['text' => $systemPrompt]]]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak mengerti.';
                return response()->json(['reply' => $reply]);
            } else {
                return response()->json(['reply' => 'Gagal terhubung ke AI: ' . $response->body()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
