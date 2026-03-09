<?php

namespace App\Services;

class BriefContentParser
{
    /**
     * Map agent type -> keywords để extract từ brief content
     */
    public static function getKeywordMap(): array
    {
        return [
            'root1' => [
                'purpose' => ['Purpose (Mục đích)', 'Purpose', 'Mục đích'],
                'core_values' => ['Core Values (Giá trị cốt lõi)', 'Core Values', 'Giá trị cốt lõi'],
                'behaviors' => ['Expected Behaviors (Hành vi kỳ vọng)', 'Expected Behaviors', 'Hành vi'],
                'symbols' => ['Symbols (Biểu tượng)', 'Symbols', 'Biểu tượng'],
                'rules' => ['Rules/Decisions (Quy tắc & Quyết định)', 'Rules/Decisions', 'Quy tắc'],
            ],
            'root2' => [
                'market_overview' => ['Tổng quan Thị trường'],
                'customer_insight' => ['Chân dung & Insight khách hàng', 'Chân dung & Insight'],
                'competitor_analysis' => ['Đối thủ & Khoảng trống', 'Phân tích Đối thủ'],
                'growth_opportunity' => ['Cơ hội Tăng trưởng'],
                'next_direction' => ['Định hướng Tiếp theo', 'Định hướng'],
            ],
            'root3' => [
                'project_context' => ['Tổng quan dự án', 'Project Context'],
                'customer_profile' => ['Hồ sơ Khách hàng', 'Customer Profile', 'Chân dung Khách hàng'],
                'value_map' => ['Bản đồ giá trị', 'Value Map'],
                'value_proposition' => ['Tuyên bố giá trị', 'Value Proposition'],
                'next_steps' => ['Lời khuyên & bước tiếp theo', 'Next Steps'],
            ],
            'trunk1' => [
                'brand_name' => ['Tên thương hiệu', 'Brand Name'],
                'personality' => ['Tính cách thương hiệu', 'Brand Personality'],
                'tone_voice' => ['Giọng điệu', 'Tone of Voice'],
                'reasons_believe' => ['Lý do tin tưởng', 'Reasons to Believe'],
                'brand_promise' => ['Lời hứa thương hiệu', 'Brand Promise'],
            ],
            'trunk2' => [
                'nomenclature' => ['Định danh', 'Nomenclature'],
                'lexicon' => ['Kho tàng Từ vựng', 'Lexicon'],
                'rule_syntax' => ['Quy tắc Cú pháp', 'Cú pháp & Giao diện'],
                'examples' => ['Mẫu thực chiến', 'Mẫu'],
            ],
        ];
    }

    /**
     * Parse brief content theo keywords
     * Trả về array: [item_key => content]
     */
    public static function parseBriefContent(string $agentType, string $briefContent): array
    {
        $keywordMap = self::getKeywordMap();

        if (!isset($keywordMap[$agentType])) {
            return [];
        }

        $itemMap = $keywordMap[$agentType];
        $result = [];
        $content = trim($briefContent);

        foreach ($itemMap as $itemKey => $keywords) {
            $result[$itemKey] = self::extractSection($content, $keywords);
        }

        return $result;
    }

    /**
     * Extract section content từ brief based on keywords
     */
    private static function extractSection(string $content, array $keywords): string
    {
        // Build regex pattern từ keywords
        $pattern = '';
        foreach ($keywords as $keyword) {
            $escaped = preg_quote($keyword, '/');
            $pattern .= $escaped . '|';
        }
        $pattern = rtrim($pattern, '|');

        // Case-insensitive search
        if (!preg_match('/(' . $pattern . ')/i', $content, $matches)) {
            return '';
        }

        $startPos = strpos($content, $matches[0]);
        $startPos += strlen($matches[0]);

        // Find next keyword position or end of content
        $nextKeywordPos = PHP_INT_MAX;

        foreach ($keywords as $keyword) {
            $pos = stripos($content, $keyword, $startPos);
            if ($pos !== false && $pos > $startPos) {
                $nextKeywordPos = min($nextKeywordPos, $pos);
            }
        }

        // Extract section content
        if ($nextKeywordPos === PHP_INT_MAX) {
            $section = substr($content, $startPos);
        } else {
            $section = substr($content, $startPos, $nextKeywordPos - $startPos);
        }

        // Clean up
        $section = trim($section);
        $section = ltrim($section, ':- \t\n');
        $section = trim($section);

        return $section;
    }
}
