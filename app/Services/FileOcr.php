<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;

class FileOcr
{
    private File $file;
    private string $pdf_to_text_env_path;
    private string $doc_to_text_env_path;

    public function __construct(File $file)
    {
        $this->file = $file;
        $this->pdf_to_text_env_path = env('PDF_TO_TEXT_ENV_PATH') ?? '/usr/bin/pdftotext';
        $this->doc_to_text_env_path = env('DOC_TO_TEXT_ENV_PATH') ?? '/usr/sbin/antiword';
    }

    public function extractText(): bool
    {
        $extracted = false;
        switch ($this->file->content_type)
        {
            case 'application/pdf':
                $this->pdfExtract();
                $extracted = true;
                break;
            case 'application/msword':
//            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $this->docExtract();
                $extracted = true;
                break;
        }
        return $extracted;
    }

    private function pdfExtract()
    {
        try {
            $text = (new Pdf($this->pdf_to_text_env_path))
                ->setPdf(Storage::disk('local')->path($this->file->path))
                ->setOptions(["-enc UTF-8"])
                ->text();
            $this->file->file_text = $text;
            $this->file->save();
        } catch (\Exception $e) {
            logError('PDF to text file: '.$this->file->path, $e->getMessage());
        }
    }

    private function docExtract()
    {
        try {
            $file = escapeshellarg(Storage::disk('local')->path($this->file->path));
            $text = shell_exec($this->doc_to_text_env_path.' -m utf-8 -w 0 '.$file);
            $clearText = html_entity_decode(trim($text));
            $this->file->file_text = $clearText;
            $this->file->save();
        } catch (\Exception $e) {
            logError('DOC to text file: '.$this->file->path, $e->getMessage());
        }
    }
}
