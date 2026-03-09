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
                'purpose' => ['Purpose (Mục đích)', 'Purpose', 'Mục đích', 'Mục đích doanh nghiệp'],
                'core_values' => ['Giá trị cốt lõi', 'Core Values (Giá trị cốt lõi)', 'Core Values', 'Giá trị'],
                'behaviors' => ['Hành vi đặc trưng', 'Behaviors', 'Hành vi'],
                'promise' => ['Lời cam kết', 'Promise', 'Cam kết'],
                'spirit' => ['Tinh thần đội ngũ', 'Spirit', 'Tinh thần'],
            ],
            'root2' => [
                'core_customer' => ['Khách hàng trọng tâm', 'Core Customer', 'Khách hàng'],
                'pain_points' => ['Nỗi đau lớn nhất', 'Pain Points', 'Nỗi đau'],
                'customer_desires' => ['Khao khát của khách hàng', 'Customer Desires', 'Khao khát'],
                'purchase_barriers' => ['Rào cản mua hàng', 'Purchase Barriers', 'Rào cản'],
                'market_opportunity' => ['Cơ hội thị trường', 'Market Opportunity', 'Cơ hội'],
            ],
            'root3' => [
                'unique_character' => ['Điểm khác biệt độc nhất', 'Điểm khác biệt', 'khác biệt', 'độc nhất'],
                'core_solution' => ['Giải pháp cốt lõi', 'Core Solution', 'Giải pháp'],
                'rational_benefits' => ['Lợi ích lý tính', 'Rational Benefits', 'Lợi ích'],
                'emotional_benefits' => ['Lợi ích cảm xúc', 'Emotional Benefits', 'Cảm xúc'],
            ],
            'trunk1' => [
                'brand_name' => ['Tên thương hiệu', 'Brand Name'],
                'brand_slogan' => ['Thông điệp chính', 'Thông điệp', 'slogan'],
                'personality' => ['Tính cách thương hiệu', 'Brand Personality', 'Tính cách'],
                'representative' => ['Hình mẫu đại diện', 'Representative', 'Hình mẫu'],
                'brand_promise' => ['Lời hứa thương hiệu', 'Brand Promise', 'Lời hứa'],
            ],
            'trunk2' => [
                'forms_of_address' => ['Cách xưng hô', 'Forms of Address', 'Cách xưng hô'],
                'core_tone' => ['Giọng văn chủ đạo', 'Core Tone', 'Giọng văn', 'chủ đạo'],
                'delivery' => ['Cảm xúc truyền tải', 'Delivery', 'truyền tải', 'Cảm xúc'],
                'unique_keyword' => ['Từ khóa đặc trưng', 'Unique Keyword', 'Từ khóa', 'đặc trưng'],
                'forbidden_words' => ['Ngôn từ cần tránh', 'Từ khóa cấm', 'Forbidden Words', 'cấm', 'Tránh', 'Ngôn từ'],
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
        foreach ($keywords as $keyword) {
            $escaped = preg_quote($keyword, '/');
            // Match with or without markdown bold, followed by a colon or newline, capturing until the next bold heading or end
            $pattern = '/(?:^|\n)(?:\*\*|)\s*' . $escaped . '\s*(?:\*\*|)\s*[:\-]*\s*(.+?)(?=\n\s*(?:\*\*|)[A-ZĐÁÀẢÃẠÂẤẦẨẪẬĂẮẰẲẴẶÊẾỀỂỄỆÔỐỒỔỖỘƠỚỜỞỠỢƯỨỪỬỮỰÍÌỈĨỊÚÙỦŨỤÝỲỶỸỴ][a-zđáàảãạâấầẩẫậăắằẳẵặêếềểễệôốồổỗộơớờởỡợưứừửữựíìỉĩịúùủũụýỳỷỹỵA-Z\s]+(?:\*\*|)\s*:|$)/is';
            
            if (preg_match($pattern, $content, $matches)) {
                $section = trim($matches[1]);
                $section = ltrim($section, ':- \t\n');
                $section = rtrim($section, ':- \t\n');
                return $section;
            }
            
            // Fallback simpler pattern if the strict one fails
            $patternFallback = '/(?:^|\n)(?:\*\*|)\s*' . $escaped . '\s*(?:\*\*|)\s*[:\-]*\s*(.+?)(?=\n\n|$)/is';
            if (preg_match($patternFallback, $content, $matches)) {
                $section = trim($matches[1]);
                $section = ltrim($section, ':- \t\n');
                $section = rtrim($section, ':- \t\n');
                return $section;
            }
        }

        return '';
    }
}
