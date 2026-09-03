<?php

namespace App\Controllers;

use App\Http\Response;
use App\View\SmartyView;
use Smarty\Exception;

/**
 * Base controller providing template rendering and common error responses.
 */
abstract class Controller
{
    public function __construct(protected SmartyView $view)
    {
    }

    /**
     * Renders a template into a 200 OK response.
     *
     * @param string $template Template filename, relative to the template dir.
     * @param array<string, mixed> $data Variables to make available in the template.
     * @throws Exception
     */
    protected function render(string $template, array $data = []): Response
    {
        return new Response($this->view->render($template, $data));
    }

    /**
     * Builds a 404 Not Found response.
     *
     * @param string $message Body text shown to the client.
     */
    protected function notFound(string $message = 'Страница не найдена'): Response
    {
        return new Response($message, 404);
    }
}
