<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Support\Paginator;
use App\View\SmartyView;

/**
 * Renders a single category page: its posts, sorted and paginated.
 */
final class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
        private readonly PostRepositoryInterface $posts,
        private readonly int $perPage,
        SmartyView $view,
    ) {
        parent::__construct($view, $categories);
    }

    /**
     * @param Request $request Current HTTP request; "sort" and "page" query params are read.
     * @param array<string, string> $params Route parameters; must contain "slug".
     */
    public function show(Request $request, array $params): Response
    {
        $category = $this->categories->findBySlug($params['slug']);
        if ($category === null) {
            return $this->notFound('Категория не найдена');
        }

        $sort = $this->resolveSort($request);
        $requestedPage = $this->resolveRequestedPage($request);

        $total = $this->posts->countByCategory($category->id);
        $paginator = new Paginator($requestedPage, $this->perPage, $total);

        $posts = $this->posts->paginatedByCategory($category->id, $sort, $paginator->offset, $this->perPage);

        return $this->render('category/show.tpl', [
            'category' => $category,
            'posts' => $posts,
            'sort' => $sort,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Reads the "sort" query param, defaulting to date-based sorting for any value other than "views".
     */
    private function resolveSort(Request $request): string
    {
        return $request->queryParam('sort', 'date') === 'views' ? 'views' : 'date';
    }

    /**
     * Reads the "page" query param as an integer, defaulting to 1.
     */
    private function resolveRequestedPage(Request $request): int
    {
        return (int) $request->queryParam('page', '1');
    }
}
