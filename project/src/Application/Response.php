<?php

declare(strict_types=1);

namespace PS\Application;

class Response
{
    /**
     * @var string
     */
    private string $body;

    /**
     * Response constructor.
     *
     * @param $body
     */
    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
