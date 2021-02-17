<?php declare(strict_types=1);

namespace App\Http\Response;

use App\Http\Response\AbstractResponse;
use App\Templating\TemplatingEngine;
use App\Templating\Template;
use App\Contracts\AppInterface;


class View extends AbstractResponse
{
    private $app;
    private $template;

    public function __construct(AppInterface $app, string $view_name)
    {
        $this->app = $app;
        $this->template = new Template($app->bs()->views(), $view_name);
    }

    public function extendWithView(string $key, string $view_name)
    {
        $this->template->addView($key, $view_name);
        return $this;
    }

    public function addKeyVal(string $key, $val)
    {
        $this->template->addVar($key, $val);
        return $this;
    }

    public function sendResponse()
    {
        $templatingEngine = new TemplatingEngine($this->app, $this->template);
        $templatingEngine->processTemplate();
    }
}
