<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\View\SmartyView;
use Smarty\Exception;

/**
 * Renders the home page: every category with its latest posts.
 */
final class HomeController extends Controller
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
        private readonly PostRepositoryInterface $posts,
        SmartyView $view,
    ) {
        parent::__construct($view, $categories);
    }

    /**
     * @param Request $request Current HTTP request.
     * @param array<string, string> $params Route parameters (unused for this action).
     * @throws Exception
     */
    public function index(Request $request, array $params): Response
    {
        $sections = [];
        foreach ($this->categories->withPosts() as $category) {
            $sections[] = [
                'category' => $category,
                'posts' => $this->posts->latestByCategory($category->id, 3),
            ];
        }

        return $this->render('home/index.tpl', ['sections' => $sections]);
    }
}
