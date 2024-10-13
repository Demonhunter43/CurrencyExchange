<?php

namespace App\Http;

class HttpResponse
{
    private string $data;
    private int $code;

    /**
     * @param string $data
     * @param int $code
     */
    public function __construct(string $data, int $code)
    {
        $this->data = $data;
        $this->code = $code;
    }
}