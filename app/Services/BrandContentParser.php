<?php

namespace App\Services;

class BrandContentParser
{
    // map keyword cho từng output của AI
    public static $AGENT_KEYWORDS = [
        'root1' => [
            'Mục đích của thương hiệu' => 'Purpose \(Mục đích của thương hiệu\)',
            'Giá trị cốt lõi' => 'Core Values \(Giá trị cốt lõi\)',
            'Hành vi' => 'Behaviors \(Hành vi\)',
            'Biểu tượng' => 'Symbols \(Biểu tượng\)',
            'Các quy tắc của thương hiệu' => 'Rules \(Các quy tắc của thương hiệu\)',
        ],
        'root2' => [
            'Tổng quan Thị trường' => 'Tổng quan Thị trường',
            'Chân dung & Insight' => 'Chân dung & Insight khách hàng',
            'Phân tích đối thủ & Khoảng trống cạnh tranh' => 'Phân tích đối thủ & Khoảng trống cạnh tranh',
            'Cơ hội Tăng trưởng' => 'Cơ hội Tăng trưởng',
            'Định hướng Tiếp theo' => 'Định hướng Tiếp theo',
        ],
        'root3' => [
            'Chân dung Khách hàng' => 'Chân dung Khách hàng \(Customer Profile\)',
            'Bản đồ giá trị Giải pháp' => 'Bản đồ giá trị Giải pháp \(Value Map\)',
            'Tuyên bố giá trị' => 'Tuyên bố giá trị \(Value Proposition Statements\)',
        ],
        'trunk1' => [
            'Tên thương hiệu' => 'Tên thương hiệu \(Brand Name\)',
            'Khẩu hiệu' => 'Khẩu hiệu \(Tagline\)',
            'Tuyên bố Định vị' => 'Tuyên bố Định vị \(Positioning Statement\)',
            'Lý do để người dùng tin tưởng thương hiệu' => 'Lý do để người dùng tin tưởng thương hiệu \(Reasons to Believe\)',
            'Tính cách thương hiệu' => 'Tính cách thương hiệu \(Brand Personality\)',
            'Giọng điệu Thương hiệu' => 'Giọng điệu Thương hiệu \(Tone of Voice\)',
            'Lời hứa thương hiệu' => 'Lời hứa thương hiệu \(Brand Promise\)',
        ],
        'trunk2' => [
            'Định nghĩa giọng nói' => 'Định nghĩa giọng nói \(Nomenclature\)',
            'Đặc điểm giọng điệu' => 'Đặc điểm giọng điệu \(Tone of Voice\)',
            'Giọng nói Thương hiệu nên tránh' => 'Giọng nói Thương hiệu nên tránh \(Brand Voice to Avoid\)',
            'Cách ứng dụng' => 'Cách ứng dụng \(How to Apply\)',
            'Tone map' => 'Tone map \(Tone Map\)',
            'Ví dụ cụ thể' => 'Ví dụ cụ thể \(Examples\)',
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
        if (!isset(self::$AGENT_KEYWORDS[$agentType])) {
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
        if (preg_match('/' . $pattern . '[:\s]+(.+?)(?=\n\n|\n[A-Z]|$)/is', $content, $matches)) {
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
        if (!isset(self::$AGENT_KEYWORDS[$agentType])) {
            return $items['full_content'] ?? '';
        }

        $content = '';
        foreach ($items as $key => $value) {
            if (!empty($value)) {
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
