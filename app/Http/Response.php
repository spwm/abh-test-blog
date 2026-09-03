<?php

namespace App\Http;

/**
 * Immutable HTTP response: a body and a status code, sent to the client via send().
 */
final class Response
{
    /**
     * @param string $body Response body to output.
     * @param int $status HTTP status code.
     */
    public function __construct(
        public readonly string $body,
        public readonly int $status = 200,
    ) {
    }

    /**
     * Sets the HTTP status code and echoes the body.
     */
    public function send(): void
    {
        http_response_code($this->status);
        echo $this->body;
    }
}
