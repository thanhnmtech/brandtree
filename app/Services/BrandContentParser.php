<?php

namespace App\Services;

class BrandContentParser
{
    // map keyword cho từng output của AI
    public static $AGENT_KEYWORDS = [
        'root1' => [
            'Purpose' => 'Purpose \(Mục đích\)',
            'Core Values' => 'Core Values \(Giá trị cốt lõi\)',
            'Expected Behaviors' => 'Expected Behaviors \(Hành vi kỳ vọng\)',
            'Symbols' => 'Symbols \(Biểu tượng\)',
            'Rules/Decisions' => 'Rules\/Decisions \(Quy tắc & Quyết định\)',
        ],
        'root2' => [
            'Tổng quan Thị trường' => 'Tổng quan Thị trường',
            'Chân dung & Insight' => 'Chân dung & Insight khách hàng',
            'Đối thủ & Khoảng trống' => 'Đối thủ & Khoảng trống',
            'Cơ hội Tăng trưởng' => 'Cơ hội Tăng trưởng',
            'Định hướng Tiếp theo' => 'Định hướng Tiếp theo',
        ],
        'root3' => [
            'Customer Profile' => 'Hồ sơ Khách hàng \(Customer Profile\)',
            'Value Map' => 'Bản đồ giá trị \(Value Map\)',
            'Value Proposition' => 'Tuyên bố giá trị \(Value Proposition Statements\)',
        ],
        'trunk1' => [
            'Brand Name' => 'Tên thương hiệu \(Brand Name\)',
            'Tagline' => 'Khẩu hiệu|Tagline',
            'Positioning Statement' => 'Tuyên bố Định vị',
            'Reasons to Believe' => 'Lý do tin tưởng \(Reasons to Believe\)',
            'Brand Personality' => 'Tính cách thương hiệu \(Brand Personality\)',
            'Tone of Voice' => 'Giọng điệu \(Tone of Voice\)',
            'Brand Promise' => 'Lời hứa thương hiệu \(Brand Promise\)',
        ],
        'trunk2' => [
            'Định nghĩa giọng nói' => 'Định nghĩa|Nomenclature',
            'Đặc điểm giọng điệu' => 'Kho tàng Từ vựng \(Lexicon\)',
            'Giọng nói nên tránh' => 'Blacklist|cấm kỵ',
            'Cách ứng dụng' => 'Quy tắc Cú pháp & Giao diện',
            'Tone map' => 'Mẫu thực chiến',
            'Ví dụ cụ thể' => 'Caption|Phản hồi',
        ],
    ];

    /**
     * Parse content từ AI agent theo keyword
     *
     * @param  string  $agentType  (root1, root2, root3, trunk1, trunk2)
     * @param  string  $content  Full content từ agent
     * @return array ['item_key' => 'content']
     */
    public static function parseContent(string $agentType, string $content): array
    {
        if (! isset(self::$AGENT_KEYWORDS[$agentType])) {
            return ['full_content' => $content];
        }

        $keywords = self::$AGENT_KEYWORDS[$agentType];
        $result = [];

        foreach ($keywords as $itemKey => $pattern) {
            $extracted = self::extractByKeyword($content, $pattern);
            $result[$itemKey] = $extracted;
        }

        return $result;
    }

    /**
     * Extract content by keyword pattern
     */
    private static function extractByKeyword(string $content, string $pattern): string
    {
        // Tìm pattern trong content - with safer matching
        if (preg_match('/'.$pattern.'[:\s]+(.+?)(?=\n\n|\n[A-Z]|$)/is', $content, $matches)) {
            // Safer access to matches array
            if (isset($matches[1])) {
                $extracted = trim($matches[1]);
                // Xóa các ký tự dư thừa
                $extracted = trim($extracted, "\n\t ");
                return $extracted;
            }
        }

        return '';
    }

    /**
     * Rebuild content từ các item (khi save)
     */
    public static function rebuildContent(string $agentType, array $items): string
    {
        if (! isset(self::$AGENT_KEYWORDS[$agentType])) {
            return $items['full_content'] ?? '';
        }

        $content = '';
        foreach ($items as $key => $value) {
            if (! empty($value)) {
                $content .= "\n\n**{$key}:**\n{$value}";
            }
        }

        return trim($content);
    }

    /**
     * Get item structure cho agent type (for UI)
     */
    public static function getItemStructure(string $agentType): array
    {
        $keywords = self::$AGENT_KEYWORDS[$agentType] ?? [];
        $result = [];

        foreach (array_keys($keywords) as $key) {
            $result[$key] = [
                'label' => $key,
                'content' => '',
                'hasData' => false,
            ];
        }

        return $result;
    }
}
