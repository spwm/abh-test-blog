<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Support\RelatedPostsRanker;
use App\View\SmartyView;
use Smarty\Exception;

/**
 * Renders a single post page: content, updated view count, and related posts.
 */
final class PostController extends Controller
{
    public function __construct(
        private readonly PostRepositoryInterface $posts,
        private readonly RelatedPostsRanker $ranker,
        CategoryRepositoryInterface $categories,
        SmartyView $view,
    ) {
        parent::__construct($view, $categories);
    }

    /**
     * @param Request $request Current HTTP request.
     * @param array<string, string> $params Route parameters; must contain "slug".
     * @throws Exception
     */
    public function show(Request $request, array $params): Response
    {
        $post = $this->posts->findBySlug($params['slug']);
        if ($post === null) {
            return $this->notFound('Статья не найдена');
        }

        $views = $this->posts->incrementViews($post->id);

        $candidates = $this->posts->findCandidatesSharingCategory($post);
        $related = $this->ranker->rank($post, $candidates, 3);

        return $this->render('post/show.tpl', [
            'post' => $post,
            'views' => $views,
            'related' => $related,
        ]);
    }
}
