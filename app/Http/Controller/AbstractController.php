<?php declare(strict_types=1);

namespace App\Http\Controller;

use App\Contracts\Http\Controller\ControllerInterface;
use App\Contracts\AppInterface;
use App\Contracts\Http\Request\RequestInterface;
use App\Http\Response\TextResponse;
use App\Http\Response\JsonResponse;
use App\Http\Response\View;


abstract class AbstractController implements ControllerInterface
{
    protected $app;
    protected $request;

    public function __construct(AppInterface $app, RequestInterface $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    public function db()
    {
        return $this->app->bs()->db();
    }

    public function request()
    {
        return $this->request;
    }

    public function text(string $msg)
    {
        return new TextResponse($msg);
    }

    public function json(array $arr)
    {
        return new JsonResponse($arr);
    }

    public function view(string $view_name)
    {
        return new View($this->app, $view_name);
    }
}
