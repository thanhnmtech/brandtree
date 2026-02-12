<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service trích xuất text từ các loại file
 * 
 * Hỗ trợ:
 * - PDF (sử dụng smalot/pdfparser)
 * - Word (sử dụng phpoffice/phpword)
 * - TXT (đọc trực tiếp)
 * - Image (mô tả bằng Gemini Vision)
 */
class FileExtractionService
{
    /**
     * Trích xuất text từ file dựa trên MIME type
     * 
     * @param string $filePath Đường dẫn tuyệt đối đến file
     * @param string $mimeType MIME type của file
     * @return string Text đã trích xuất
     * @throws \Exception Nếu loại file không được hỗ trợ
     */
    public function extract(string $filePath, string $mimeType): string
    {
        Log::channel('rag')->info("Extraction started for $filePath ($mimeType)");
        $content = match (true) {
            str_contains($mimeType, 'pdf') => $this->extractPdf($filePath),
            str_contains($mimeType, 'word') || str_contains($mimeType, 'document') => $this->extractWord($filePath),
            $mimeType === 'text/plain' => $this->extractText($filePath),
            str_starts_with($mimeType, 'image/') => $this->describeImage($filePath, $mimeType),
            default => throw new \Exception("Loại file không được hỗ trợ: {$mimeType}")
        };
        Log::channel('rag')->info("Extracted content type: $mimeType");
        return $content;
    }

    /**
     * Trích xuất text từ file PDF
     * Sử dụng thư viện smalot/pdfparser
     */
    private function extractPdf(string $filePath): string
    {
        // Kiểm tra thư viện đã cài đặt chưa
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new \Exception('Thư viện smalot/pdfparser chưa được cài đặt. Chạy: composer require smalot/pdfparser');
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            // Làm sạch text (loại bỏ khoảng trắng thừa)
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            return $text;
        } catch (\Exception $e) {
            Log::error("Lỗi trích xuất PDF: " . $e->getMessage());
            throw new \Exception("Không thể trích xuất text từ PDF: " . $e->getMessage());
        }
    }

    /**
     * Trích xuất text từ file Word (.doc, .docx)
     * Sử dụng thư viện phpoffice/phpword
     */
    private function extractWord(string $filePath): string
    {
        // Kiểm tra thư viện đã cài đặt chưa
        if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
            throw new \Exception('Thư viện phpoffice/phpword chưa được cài đặt. Chạy: composer require phpoffice/phpword');
        }

        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';

            // Lặp qua các sections và elements để lấy text
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= $this->extractWordElement($element);
                }
            }

            // Làm sạch text
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            return $text;
        } catch (\Exception $e) {
            Log::error("Lỗi trích xuất Word: " . $e->getMessage());
            throw new \Exception("Không thể trích xuất text từ Word: " . $e->getMessage());
        }
    }

    /**
     * Helper method để trích xuất text từ Word element
     */
    private function extractWordElement($element): string
    {
        $text = '';

        if (method_exists($element, 'getText')) {
            $text .= $element->getText() . ' ';
        }

        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $childElement) {
                $text .= $this->extractWordElement($childElement);
            }
        }

        return $text;
    }

    /**
     * Đọc nội dung file TXT
     */
    private function extractText(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File không tồn tại: {$filePath}");
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new \Exception("Không thể đọc file: {$filePath}");
        }

        // Detect và convert encoding nếu cần
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        return trim($content);
    }

    /**
     * Mô tả hình ảnh sử dụng Gemini Vision API
     * Gửi hình ảnh đến Gemini và nhận mô tả chi tiết
     */
    private function describeImage(string $filePath, string $mimeType): string
    {
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            throw new \Exception('GEMINI_API_KEY chưa được cấu hình trong .env');
        }

        try {
            // Đọc file và encode base64
            $imageData = file_get_contents($filePath);
            if ($imageData === false) {
                throw new \Exception("Không thể đọc file hình ảnh: {$filePath}");
            }
            $base64Image = base64_encode($imageData);

            // Gọi Gemini Vision API
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=' . $apiKey;

            $response = Http::timeout(60)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'Hãy mô tả chi tiết nội dung trong hình ảnh này bằng tiếng Việt. Bao gồm: đối tượng chính, văn bản (nếu có), màu sắc, bố cục, và bất kỳ chi tiết quan trọng nào. Mô tả cần đầy đủ để có thể sử dụng trong tìm kiếm và trả lời câu hỏi.'
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $base64Image
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Gemini Vision API Error: ' . $response->body());
                throw new \Exception('Lỗi gọi Gemini Vision API: ' . $response->status());
            }

            $data = $response->json();

            // Trích xuất text từ response
            $description = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($description)) {
                throw new \Exception('Không nhận được mô tả từ Gemini Vision');
            }

            return trim($description);

        } catch (\Exception $e) {
            Log::error("Lỗi mô tả hình ảnh: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Kiểm tra xem MIME type có được hỗ trợ không
     */
    public function isSupported(string $mimeType): bool
    {
        $allowedTypes = config('rag.upload.allowed_mime_types', []);
        return in_array($mimeType, $allowedTypes);
    }
}
