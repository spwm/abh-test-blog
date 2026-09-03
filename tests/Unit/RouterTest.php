<?php

namespace Tests\Unit;

use App\Routing\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function test_matches_static_route(): void
    {
        $router = new Router();
        $router->get('', ['HomeController', 'index']);

        $result = $router->match('GET', '/');

        $this->assertSame(['HomeController', 'index'], $result['handler']);
        $this->assertSame([], $result['params']);
    }

    public function test_matches_route_with_slug_parameter(): void
    {
        $router = new Router();
        $router->get('category/{slug}', ['CategoryController', 'show']);

        $result = $router->match('GET', '/category/frontend');

        $this->assertSame(['CategoryController', 'show'], $result['handler']);
        $this->assertSame(['slug' => 'frontend'], $result['params']);
    }

    public function test_ignores_query_string_when_matching(): void
    {
        $router = new Router();
        $router->get('category/{slug}', ['CategoryController', 'show']);

        $result = $router->match('GET', '/category/frontend?sort=views&page=2');

        $this->assertSame(['slug' => 'frontend'], $result['params']);
    }

    public function test_returns_null_for_unmatched_route(): void
    {
        $router = new Router();
        $router->get('category/{slug}', ['CategoryController', 'show']);

        $this->assertNull($router->match('GET', '/does-not-exist'));
    }

    public function test_does_not_match_wrong_http_method(): void
    {
        $router = new Router();
        $router->get('category/{slug}', ['CategoryController', 'show']);

        $this->assertNull($router->match('POST', '/category/frontend'));
    }
}
