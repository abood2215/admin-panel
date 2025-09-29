<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\PdfTextExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function handle()
    {
        $document = $this->document;
        $document->update(['status' => 'processing']);
        
        $filePath = storage_path('app/public/' . $document->file_path);
        $extractionService = new PdfTextExtractionService();
        
        try {
            $text = $extractionService->extractText($filePath, $document->language);
            
            $document->update([
                'extracted_text' => $text,
                'status' => !empty(trim($text)) ? 'processed' : 'failed'
            ]);
            
        } catch (\Exception $e) {
            $document->update(['status' => 'failed']);
        }
    }
}