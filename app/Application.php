<?php

namespace App;

use App\Controllers\CategoryController;
use App\Controllers\Controller;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Routing\Router;
use App\Support\RelatedPostsRanker;
use App\View\SmartyView;

/**
 * HTTP application kernel: matches the current request against the router and dispatches it to a controller.
 */
final class Application
{
    /**
     * @param Router $router Routes registered for this application.
     * @param CategoryRepositoryInterface $categories Shared category repository, injected into controllers.
     * @param PostRepositoryInterface $posts Shared post repository, injected into controllers.
     * @param SmartyView $view Shared template renderer, injected into controllers.
     * @param int $perPage Number of posts per page, used by CategoryController.
     */
    public function __construct(
        private readonly Router $router,
        private readonly CategoryRepositoryInterface $categories,
        private readonly PostRepositoryInterface $posts,
        private readonly SmartyView $view,
        private readonly int $perPage,
    ) {
    }

    /**
     * Handles the current global HTTP request and sends the resulting response.
     */
    public function run(): void
    {
        $this->handle(Request::fromGlobals())->send();
    }

    /**
     * Matches $request against the router and dispatches it to the resolved controller action.
     *
     * @param Request $request Request to handle.
     */
    public function handle(Request $request): Response
    {
        $match = $this->router->match($request->method, $request->uri);
        if ($match === null) {
            return new Response('404 Not Found', 404);
        }

        [$controllerClass, $action] = $match['handler'];
        $controller = $this->resolveController($controllerClass);
        if ($controller === null) {
            return new Response('404 Not Found', 404);
        }

        return $controller->$action($request, $match['params']);
    }

    /**
     * Builds a controller instance for $controllerClass with its required dependencies.
     *
     * @param string $controllerClass Fully qualified controller class name.
     * @return Controller|null Null when $controllerClass is not a known controller.
     */
    private function resolveController(string $controllerClass): ?Controller
    {
        return match ($controllerClass) {
            HomeController::class => new HomeController($this->categories, $this->posts, $this->view),
            CategoryController::class => new CategoryController(
                $this->categories,
                $this->posts,
                $this->perPage,
                $this->view
            ),
            PostController::class => new PostController($this->posts, new RelatedPostsRanker(), $this->view),
            default => null,
        };
    }
}
