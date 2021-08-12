<?php

namespace App\Http\Resources\V1;

use Fusonic\OpenGraph\Consumer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\HttpClient\Psr18Client;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $item = (array) $this->resource;
        $ogMetaTags = $this->getOGMetaTagsUrlFromMetaTags($item['link']);
        $url = $ogMetaTags->url ?? $item['link'];

        preg_match(
            '/[(http(s)?):\/\/(www\.)?a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}/',
            $url,
            $matches
        );

        return [
            'title' => $item['title'],
            'description' => $ogMetaTags->description ?? '',
            'image' => $ogMetaTags->images[0]->url ?? '',
            'url' => $ogMetaTags->url ?? $item['link'],
            'published_at' => date('Y-m-d H:i:s', strtotime($item['pubDate'])),
            'source' => [
                'name' => $item['source'],
                'url' => $matches[0] ?? ''
            ],
        ];
    }

    private function getOGMetaTagsUrlFromMetaTags($url): object
    {
        $data = (object) [];
        try {
            $client = new Psr18Client(new NativeHttpClient([
                "headers" => [
                    "User-Agent" => "facebookexternalhit/1.1"
                ]
            ]));
            $consumer = new Consumer($client, $client);

            $data = $consumer->loadUrl($url);
        } catch ( ClientExceptionInterface $e ) {
        }

        return $data;
    }
}
