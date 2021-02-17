<?php declare(strict_types=1);

namespace App\Http\Response;

use App\Http\Response\AbstractResponse;


class TextResponse extends AbstractResponse
{
    private $buf;

    public function __construct(string $msg) 
    {
        $this->buf = $msg;
    }
    public function sendResponse()
    {
        echo $this->buf;
    }
}
