<?php

namespace App\Controllers;

use App\Http\Response;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\View\SmartyView;
use Smarty\Exception;

/**
 * Base controller providing template rendering and common error responses.
 */
abstract class Controller
{
    /**
     * @param SmartyView $view Template renderer.
     * @param CategoryRepositoryInterface $navCategories Category repository used to populate the site-wide nav menu.
     */
    public function __construct(
        protected SmartyView $view,
        private readonly CategoryRepositoryInterface $navCategories,
    ) {
    }

    /**
     * Renders a template into a response, adding the site-wide nav menu categories.
     *
     * @param string $template Template filename, relative to the template dir.
     * @param array<string, mixed> $data Variables to make available in the template.
     * @param int $status HTTP status code for the response.
     * @throws Exception
     */
    protected function render(string $template, array $data = [], int $status = 200): Response
    {
        $data['navCategories'] = $this->navCategories->withPosts();

        return new Response($this->view->render($template, $data), $status);
    }

    /**
     * Renders the 404 page with the given message.
     *
     * @param string $message Text shown on the 404 page.
     * @throws Exception
     */
    protected function notFound(string $message = 'Страница не найдена'): Response
    {
        return $this->render('errors/404.tpl', ['message' => $message], 404);
    }
}
