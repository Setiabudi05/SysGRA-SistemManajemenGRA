<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncChatbotPdf extends Command
{
    protected $signature = 'chatbot:sync-pdf';
    protected $description = 'Sync and extract text from griya_rias_asmara.pdf to txt file';

    public function handle()
    {
        $pdfPath = base_path('griya_rias_asmara.pdf');
        $txtPath = storage_path('app/griya_rias_asmara.txt');

        if (!file_exists($pdfPath)) {
            $this->error("File griya_rias_asmara.pdf tidak ditemukan di root directory!");
            return 1;
        }

        $scriptPath = base_path('extract_pdf.py');
        if (!file_exists($scriptPath)) {
            $this->error("File extract_pdf.py tidak ditemukan di root directory!");
            return 1;
        }

        $this->info("Sedang mengekstrak teks dari PDF...");
        $pythonCmd = (stripos(PHP_OS, 'WIN') === 0) ? 'python' : 'python3';
        
        $output = shell_exec($pythonCmd . " " . escapeshellarg($scriptPath) . " 2>&1");

        if (str_contains($output, 'Success')) {
            $this->info("Berhasil mengekstrak PDF! Teks disimpan di: storage/app/griya_rias_asmara.txt");
            return 0;
        } else {
            $this->error("Ekstraksi gagal: " . $output);
            return 1;
        }
    }
}
