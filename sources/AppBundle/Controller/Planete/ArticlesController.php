<?php

declare(strict_types=1);

namespace AppBundle\Controller\Planete;

use PlanetePHP\DisplayableFeedArticle;
use PlanetePHP\FeedArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ArticlesController
{
    /** @var FeedArticleRepository */
    private $feedArticleRepository;

    public function __construct(
        FeedArticleRepository $feedArticleRepository
    ) {
        $this->feedArticleRepository = $feedArticleRepository;
    }

    public function __invoke(Request $request): Response
    {
        $perPage = 20;
        $page = (int) $request->query->get("page", 1);

        if ($page < 1) {
            $page = 1;
        }

        $totalCount = $this->feedArticleRepository->countRelevant();
        $articles = $this->feedArticleRepository->findLatest($page - 1, DATE_RSS, $perPage);

        $data = [];

        foreach ($articles as $article) {
            $data[] = [
                'title' => $article->getTitle(),
                'url' => $this->getArticleUrl($article),
                'date' => $article->getUpdate(),
                'author' => $article->getAuthor(),
                'content' => $article->getContent(),
                'feed' => [
                    'name' => $article->getFeedName(),
                    'url' => $article->getFeedUrl(),
                ],
            ];
        }

        return new Response(
            json_encode($data),
            200,
            [
                'Content-Type' => 'application/json',
                'X-Pagination-Total' => $totalCount,
                'X-Pagination-Per-Page' => $perPage,
                'X-Pagination-Has-Next-Page' => json_encode($totalCount > $page * $perPage),
            ]
        );
    }

    private function getArticleUrl(DisplayableFeedArticle $article): string
    {
        if (substr($article->getUrl(), 0, 4) !== 'http') {
            $feedUrl = rtrim($article->getFeedUrl(), '/');
            $articleUrl = ltrim($article->getUrl(), '/');

            return implode('/', [$feedUrl, $articleUrl]);
        }

        return $article->getUrl();
    }
}
