<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NewsCollection;
use Illuminate\Http\Request;
use App\Traits\QueryGoogleNews;

class NewsController extends Controller
{
    use QueryGoogleNews;

    public function index(Request $request): NewsCollection
    {
        $page = $this->getPage($request->query('page'));
        $url = $this->buildGoogleNewsURL([
            'lang' => $request->query('lang'),
            'country' => $request->query('country')
        ]);
        $news = $this->getGoogleNews($url);

        return NewsCollection::make( $news[$page - 1] ?? [] );
    }

    public function search(Request $request): NewsCollection
    {
        $url = $this->buildGoogleNewsURL([
            'q' => $request->query('q'),
            'lang' => $request->query('lang'),
            'country' => $request->query('country')
        ]);
        $page = $this->getPage($request->query('page'));
        $news = $this->getGoogleNews($url);

        return NewsCollection::make( $news[$page - 1] ?? [] );
    }

    public function topics(Request $request, string $topic): NewsCollection
    {
        $url = $this->buildGoogleNewsURL([
            'topic' => $topic,
            'lang' => $request->query('lang'),
            'country' => $request->query('country')
        ]);
        $page = $this->getPage($request->query('page'));
        $news = $this->getGoogleNews($url);

        return NewsCollection::make( $news[$page - 1] ?? [] );
    }

    private function getPage($page): int
    {
        return $page !== null && (int) $page > 0 ? (int) $page : 1;
    }
}
