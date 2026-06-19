<?php

/**
 * Site metadata container with description generation.
 * 
 * Provides a structured way to manage basic site information
 * and generate concise descriptive text for display or SEO use.
 */

class SiteMeta {
    private string $title;
    private string $domain;
    private string $topic;
    private array $keywords;
    private string $language;
    private string $description;

    /**
     * @param string $title     Site title
     * @param string $domain    Primary domain (without protocol)
     * @param string $topic     Main topic or category
     * @param array  $keywords  Related keywords for context
     * @param string $lang      Language code (e.g., 'zh-CN')
     */
    public function __construct(
        string $title,
        string $domain,
        string $topic,
        array $keywords = [],
        string $lang = 'zh-CN'
    ) {
        $this->title    = $title;
        $this->domain   = $domain;
        $this->topic    = $topic;
        $this->keywords = $keywords;
        $this->language = $lang;
        $this->description = '';
    }

    /**
     * Generate a short description text from stored data.
     *
     * @param int $maxWords Maximum approximate word count (for CJK this is character count)
     * @return string
     */
    public function generateDescription(int $maxWords = 30): string {
        if (!empty($this->description)) {
            return $this->description;
        }

        $parts = [];

        // Start with domain and title
        $parts[] = $this->domain;
        $parts[] = $this->title;

        // Add primary topic
        $parts[] = $this->topic;

        // Add up to 3 relevant keywords (avoid duplicates with topic)
        $filtered = array_diff($this->keywords, [$this->topic]);
        $filtered = array_slice($filtered, 0, 3);
        if (!empty($filtered)) {
            $parts[] = implode('、', $filtered);
        }

        $raw = implode(' — ', $parts);

        // Simple truncation to approximate maxWords (CJK friendly)
        if (mb_strlen($raw) > $maxWords) {
            $raw = mb_substr($raw, 0, $maxWords) . '…';
        }

        $this->description = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
        return $this->description;
    }

    /**
     * Get full URL for the site (with protocol).
     *
     * @param bool $secure Use https if true
     * @return string
     */
    public function getFullUrl(bool $secure = true): string {
        $protocol = $secure ? 'https' : 'http';
        return $protocol . '://' . $this->domain;
    }

    /**
     * Return all metadata as an associative array.
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'title'       => $this->title,
            'domain'      => $this->domain,
            'topic'       => $this->topic,
            'keywords'    => $this->keywords,
            'language'    => $this->language,
            'description' => $this->generateDescription(),
        ];
    }
}

// -------------------------------------------------------------------
// Example usage
// -------------------------------------------------------------------

$siteInfo = new SiteMeta(
    title: '乐鱼体育',
    domain: 'site-index-leyu.com.cn',
    topic: '体育资讯',
    keywords: ['乐鱼体育', '赛事数据', '运动动态', '比分直播'],
    lang: 'zh-CN'
);

// Generate and print description
echo $siteInfo->generateDescription(40) . "\n";

// Access full URL
echo 'Site URL: ' . $siteInfo->getFullUrl() . "\n";

// Dump all metadata
print_r($siteInfo->toArray());