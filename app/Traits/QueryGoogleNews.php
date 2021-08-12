<?php

namespace App\Traits;

trait QueryGoogleNews
{
    private string $baseUrl = 'https://news.google.com/rss';

    private array $defaultTopics = [
        'WORLD',
        'NATION',
        'BUSINESS',
        'TECHNOLOGY',
        'ENTERTAINMENT',
        'SCIENCE',
        'SPORTS',
        'HEALTH',
    ];

    private function getGoogleNews($url): array
    {
        $items = [];

        try {
            $xml = simplexml_load_file($url);
            $items = ((array) $xml->channel)['item'] ?? [];

            usort($items, function ($a, $b) {
                return strtotime((string) $b->pubDate) <=> strtotime((string) $a->pubDate);
            });
        } catch (\Exception $e) {
        }

        return array_chunk($items, 10);
    }

    private function buildGoogleNewsURL(array $params): string
    {
        $url = $this->baseUrl;
        $topic = $params['topic'] ?? '';
        $lang = strtolower($params['lang'] ?? 'en');
        $country = strtoupper($params['country'] ?? 'us');

        if (!empty($params['q'])) {
            $url .= "/search?q={$params['q']}+when:7d&";
        } elseif (!empty($topic) && in_array(strtoupper($topic), $this->defaultTopics)) {
            $url .= "/headlines/section/topic/".strtoupper($topic)."?";
        } elseif (!empty($topic) && !in_array(strtoupper($topic), $this->defaultTopics)) {
            $url .= "/topics/{$topic}?";
        } else {
            $url .= "?";
        }

        return "{$url}hl={$lang}-{$country}&gl={$country}&ceid={$country}:{$lang}";
    }
}
