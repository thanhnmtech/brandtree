<?php

namespace App\Services;

class BriefContentParser
{
    /**
     * Parse raw markdown content into structured items based on config.
     *
     * @param string $agentType (e.g., 'root1', 'trunk2')
     * @param string $content (raw markdown content)
     * @return array
     */
    public function parse(string $agentType, ?string $content): array
    {
        if (empty($content)) {
            return [];
        }

        $configItems = config("agent_brief.{$agentType}", []);
        if (empty($configItems)) {
            return [];
        }

        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", $content);

        // Chuẩn hoá string thành không dấu, viết thường, xoá kí tự đặc biệt để ép so sánh
        $normalize = function ($str) {
            $str = mb_strtolower($str, 'UTF-8');
            $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
            $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
            $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
            $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
            $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
            $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
            $str = preg_replace('/(đ)/', 'd', $str);
            // Xoá mọi thứ không phải chữ cái a-z hoặc số
            return preg_replace('/[^a-z0-9]/', '', $str);
        };

        $normalizedMap = [];
        foreach ($configItems as $index => $item) {
            foreach ($item['match_keywords'] as $kw) {
                $normalizedMap[$index][] = $normalize($kw);
            }
        }

        $parsedMap = [];
        $activeConfigIndex = -1;
        $activeContent = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                if ($activeConfigIndex !== -1) {
                    $activeContent[] = $line;
                }
                continue;
            }

            // Loại bỏ #, *, -, số thứ tự ở đầu dòng để lấy chữ thuần
            $cleanLineForMatch = preg_replace('/^([#\*\-\s\d\.]+)/', '', $trimmed);
            // Lấy 30 char đầu tiên để so sánh xem có phải là thẻ tiêu đề không
            $startOfLine = mb_substr($cleanLineForMatch, 0, 30);
            $normalizedStart = $normalize($startOfLine);

            $matchedIndex = -1;

            foreach ($normalizedMap as $idx => $nKeywords) {
                foreach ($nKeywords as $nkw) {
                    if (str_starts_with($normalizedStart, $nkw)) {
                        $matchedIndex = $idx;
                        break 2;
                    }
                }
            }

            if ($matchedIndex !== -1) {
                // Đã tìm ra mục mới!
                if ($activeConfigIndex !== -1) {
                    $parsedMap[$configItems[$activeConfigIndex]['title']] = trim(implode("\n", $activeContent));
                }

                $activeConfigIndex = $matchedIndex;
                $activeContent = [];

                // Trim nội dung trùng với tiêu đề
                $pos = mb_strpos($trimmed, ':');
                if ($pos !== false) {
                    $rest = mb_substr($trimmed, $pos + 1);
                    $rest = trim(preg_replace('/^[\*\s]+/', '', $rest));
                    if (!empty($rest)) {
                        $activeContent[] = $rest;
                    }
                } else {
                    // force so sánh
                    $origKw = $configItems[$matchedIndex]['match_keywords'][0];
                    $ireplace = str_ireplace($origKw, '', $trimmed);

                    if (mb_strlen($ireplace) < mb_strlen($trimmed) - 2) {
                        $rest = preg_replace('/^[\*: \-\n\r]+/', '', trim($ireplace));
                        if (!empty($rest)) {
                            $activeContent[] = $rest;
                        }
                    }
                }
            } else {
                if ($activeConfigIndex !== -1) {
                    $activeContent[] = $line;
                }
            }
        }

        // Lưu mục cuối
        if ($activeConfigIndex !== -1) {
            $parsedMap[$configItems[$activeConfigIndex]['title']] = trim(implode("\n", $activeContent));
        }

        // Generate final array
        $finalParsedList = [];
        foreach ($configItems as $itemConfig) {
            $title = $itemConfig['title'];
            $contentStr = $parsedMap[$title] ?? '';

            $finalParsedList[] = [
                'title' => $title,
                'content' => $contentStr,
                'short_content' => !empty($contentStr)
                    ? \Illuminate\Support\Str::limit(strip_tags($contentStr), 100)
                    : '',
            ];
        }

        return $finalParsedList;
    }
}
