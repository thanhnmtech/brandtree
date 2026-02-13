<?php

namespace App\Services;

/**
 * Service chia text thành các chunks nhỏ
 * 
 * Sử dụng recursive character text splitter với overlap
 * để đảm bảo context không bị mất ở ranh giới chunks
 */
class ChunkingService
{
    /**
     * Chia text thành các chunks
     * 
     * @param string $text Text cần chia
     * @param int|null $maxTokens Số tokens tối đa mỗi chunk (null = lấy từ config)
     * @param int|null $overlap Số tokens overlap giữa các chunk (null = lấy từ config)
     * @return array<string> Mảng các chunks
     */
    public function chunk(string $text, ?int $maxTokens = null, ?int $overlap = null): array
    {
        $maxTokens = $maxTokens ?? config('rag.chunking.max_tokens', 500);
        $overlap = $overlap ?? config('rag.chunking.overlap', 50);

        // Ước lượng: 1 token ≈ 4 ký tự (cho tiếng Việt có thể khác)
        $charsPerToken = 4;
        $maxChars = $maxTokens * $charsPerToken;
        $overlapChars = $overlap * $charsPerToken;

        // Các ký tự phân tách theo thứ tự ưu tiên
        $separators = ["\n\n", "\n", ". ", "! ", "? ", "; ", ", ", " "];

        return $this->recursiveSplit($text, $maxChars, $overlapChars, $separators);
    }

    /**
     * Recursive character text splitter
     * 
     * @param string $text Text cần chia
     * @param int $maxChars Số ký tự tối đa mỗi chunk
     * @param int $overlapChars Số ký tự overlap
     * @param array $separators Các ký tự phân tách
     * @return array<string> Mảng các chunks
     */
    private function recursiveSplit(string $text, int $maxChars, int $overlapChars, array $separators): array
    {
        // Nếu text đủ ngắn, trả về luôn
        if (mb_strlen($text) <= $maxChars) {
            return [trim($text)];
        }

        // Nếu hết separator, cắt cứng theo độ dài
        if (empty($separators)) {
            return $this->splitByLength($text, $maxChars, $overlapChars);
        }

        $separator = array_shift($separators);
        $parts = explode($separator, $text);

        // Nếu không thể split với separator này, thử separator tiếp theo
        if (count($parts) <= 1) {
            return $this->recursiveSplit($text, $maxChars, $overlapChars, $separators);
        }

        $chunks = [];
        $currentChunk = '';

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part))
                continue;

            // Khôi phục separator cho các phần (trừ phần cuối)
            $partWithSep = $part . $separator;

            // Nếu thêm part vào chunk hiện tại vẫn trong giới hạn
            if (mb_strlen($currentChunk . $partWithSep) <= $maxChars) {
                $currentChunk .= $partWithSep;
            } else {
                // Lưu chunk hiện tại nếu có
                if (!empty(trim($currentChunk))) {
                    $chunks[] = trim($currentChunk);
                }

                // Nếu part đơn lẻ quá dài, cần split tiếp
                if (mb_strlen($partWithSep) > $maxChars) {
                    $subChunks = $this->recursiveSplit($partWithSep, $maxChars, $overlapChars, $separators);
                    $chunks = array_merge($chunks, $subChunks);
                    $currentChunk = '';
                } else {
                    // Bắt đầu chunk mới với overlap từ chunk trước
                    if (!empty($chunks)) {
                        $lastChunk = end($chunks);
                        $overlapText = $this->getOverlapText($lastChunk, $overlapChars);
                        $currentChunk = $overlapText . $partWithSep;
                    } else {
                        $currentChunk = $partWithSep;
                    }
                }
            }
        }

        // Lưu chunk cuối cùng
        if (!empty(trim($currentChunk))) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    /**
     * Cắt text theo độ dài cố định (fallback khi không có separator phù hợp)
     */
    private function splitByLength(string $text, int $maxChars, int $overlapChars): array
    {
        $chunks = [];
        $start = 0;
        $textLength = mb_strlen($text);

        while ($start < $textLength) {
            $chunk = mb_substr($text, $start, $maxChars);
            $chunks[] = trim($chunk);

            // Di chuyển với overlap
            $start += $maxChars - $overlapChars;
        }

        return $chunks;
    }

    /**
     * Lấy đoạn cuối của text để làm overlap
     */
    private function getOverlapText(string $text, int $overlapChars): string
    {
        $textLength = mb_strlen($text);

        if ($textLength <= $overlapChars) {
            return $text;
        }

        return mb_substr($text, $textLength - $overlapChars);
    }

    /**
     * Ước lượng số tokens của text
     * Sử dụng quy tắc đơn giản: 1 token ≈ 4 ký tự
     */
    public function estimateTokens(string $text): int
    {
        return (int) ceil(mb_strlen($text) / 4);
    }
}
