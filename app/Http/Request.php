<?php

namespace App\Http;

/**
 * Immutable value object describing the current HTTP request.
 */
final class Request
{
    /**
     * @param string $method HTTP method, e.g. "GET".
     * @param string $uri Raw request URI, including any query string.
     * @param array<string, string> $query Parsed query string parameters.
     */
    public function __construct(
        public readonly string $method,
        public readonly string $uri,
        public readonly array $query,
    ) {
    }

    /**
     * Builds a Request from PHP's superglobals.
     */
    public static function fromGlobals(): self
    {
        return new self(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            uri: $_SERVER['REQUEST_URI'] ?? '/',
            query: $_GET,
        );
    }

    /**
     * Reads a query string parameter, returning $default when it is not present.
     *
     * @param string $name Query parameter name.
     * @param string|null $default Value returned when the parameter is missing.
     */
    public function queryParam(string $name, ?string $default = null): ?string
    {
        return isset($this->query[$name]) ? (string) $this->query[$name] : $default;
    }
}
