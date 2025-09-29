<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Thiagoalessio\TesseractOCR\TesseractOCR;

/**
 * خدمة موحّدة لاستخراج النص من PDF:
 * - تحاول أولاً Poppler (pdftotext) إن توفرت.
 * - ثم Smalot\PdfParser.
 * - ثم OCR (Tesseract) كخيار أخير.
 */
class PdfTextExtractionService
{
    public function extractText(string $absPath, string $language = 'arabic'): string
    {
        // 1) Poppler pdftotext (أفضلية للّغات RTL غالبًا)
        $txt = $this->tryPdftotext($absPath);
        if ($this->good($txt)) return $txt;

        // 2) Smalot\PdfParser
        $txt = $this->extractTextFromTextBasedPdf($absPath);
        if ($this->good($txt, 0.25)) return $txt;

        // 3) OCR (Tesseract)
        $txt = $this->extractTextFromScannedPdf($absPath, $language);
        return $txt ?: '';
    }

    protected function tryPdftotext(string $absPath): string
    {
        try {
            $has = trim(@shell_exec('which pdftotext'));
            if ($has === '') return '';
            $tmp = sys_get_temp_dir().'/p2t_'.uniqid().'.txt';
            @shell_exec('pdftotext -layout '.escapeshellarg($absPath).' '.escapeshellarg($tmp));
            $txt = is_file($tmp) ? (@file_get_contents($tmp) ?: '') : '';
            if (is_file($tmp)) @unlink($tmp);
            return $txt ?: '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    protected function extractTextFromTextBasedPdf(string $filePath): string
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            return $pdf->getText() ?? '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    protected function extractTextFromScannedPdf(string $filePath, string $language): string
    {
        try {
            if (!class_exists(TesseractOCR::class)) return '';
            // Tesseract يحتاج صور، لكن مكتبة wrapper تسمح بتمرير PDF ببعض الأنظمة.
            // الأفضل تحويل أولاً لصور (imagick)، هنا نجرب مباشر.
            $ocr = new TesseractOCR($filePath);
            $ocr->lang(($language === 'arabic') ? 'ara' : 'eng');
            return $ocr->run() ?: '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function good(string $s, float $threshold = 0.4): bool
    {
        $s = trim($s);
        if ($s === '') return false;
        $len = mb_strlen($s);
        if ($len < 30) return false;

        $arab = preg_match_all('/[\x{0600}-\x{06FF}]/u', $s);
        // لو اللغة عربية نتوقع نسبة عربية معقولة
        return ($arab / $len) >= $threshold;
    }
}
