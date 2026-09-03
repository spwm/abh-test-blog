<?php

namespace App\Routing;

/**
 * Minimal HTTP router: registers routes with {placeholder} patterns and matches a method + URI against them.
 */
final class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: array{0: string, 1: string}}> */
    private array $routes = [];

    /**
     * Registers a GET route.
     *
     * @param string $pattern Route pattern, e.g. "category/{slug}"; no leading/trailing slash needed.
     * @param array{0: string, 1: string} $handler Controller class and method to invoke on match.
     */
    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    /**
     * Registers a route for an arbitrary HTTP method.
     *
     * @param array{0: string, 1: string} $handler Controller class and method to invoke on match.
     */
    public function add(string $method, string $pattern, array $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => trim($pattern, '/'),
            'handler' => $handler,
        ];
    }

    /**
     * Finds the first registered route matching $method and the path portion of $uri (query string ignored).
     *
     * @return array{handler: array{0: string, 1: string}, params: array<string, string>}|null
     *     Null when nothing matches.
     */
    public function match(string $method, string $uri): ?array
    {
        $path = $this->extractPath($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            $params = $this->matchPattern($route['pattern'], $path);
            if ($params !== null) {
                return ['handler' => $route['handler'], 'params' => $params];
            }
        }

        return null;
    }

    /**
     * Extracts and normalizes the path component of a URI, discarding the query string.
     */
    private function extractPath(string $uri): string
    {
        return trim((string) (parse_url($uri, PHP_URL_PATH) ?: ''), '/');
    }

    /**
     * Checks whether $path matches $pattern, extracting {placeholder} values on success.
     *
     * @return array<string, string>|null Named params on match, null otherwise.
     */
    private function matchPattern(string $pattern, string $path): ?array
    {
        ['regex' => $regex, 'paramNames' => $paramNames] = $this->compilePattern($pattern);

        if (!preg_match('#^' . $regex . '$#', $path, $matches)) {
            return null;
        }

        array_shift($matches);

        return array_combine($paramNames, $matches);
    }

    /**
     * Converts a route pattern into a matching regex, collecting the {placeholder} names in order of appearance.
     *
     * @return array{regex: string, paramNames: string[]}
     */
    private function compilePattern(string $pattern): array
    {
        $paramNames = [];

        $regex = preg_replace_callback('/\{(\w+)\}/', function (array $m) use (&$paramNames) {
            $paramNames[] = $m[1];

            return '([^/]+)';
        }, $pattern);

        return ['regex' => $regex, 'paramNames' => $paramNames];
    }
}
