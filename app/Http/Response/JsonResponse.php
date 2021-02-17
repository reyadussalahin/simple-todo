<?php declare(strict_types=1);

namespace App\Http\Response;

use App\Http\Response\AbstractResponse;


class JsonResponse extends AbstractResponse
{
    private $response;

    public function __construct(array $arr)
    {
        $this->response = json_encode($arr);
    }

    public function sendResponse()
    {
        echo $this->response;
    }
}
