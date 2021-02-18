<?php declare(strict_types=1);

namespace App\Templating;

use App\Contracts\Templating\TemplatingEngineInterface;
use App\Contracts\Templating\TemplateInterface;
use App\Contracts\AppInterface;
use App\Http\Server\Server;
use App\Http\Server\Session;
use App\Security\CSRFGenerator;


class TemplatingEngine implements TemplatingEngineInterface
{
    private $app;
    private $globals;
    private $template;
    private $server;
    private $session;

    public function __construct(AppInterface $app, TemplateInterface $template)
    {
        $this->app = $app;
        $this->globals  = $this->app->bs()->globals();
        $this->template = $template;
        $this->server = new Server($this->globals);
        $this->session = new Session($this->globals);
    }

    private function csrfTokenTemplator()
    {
        $_csrf = function() {
            if($this->session->has('csrfmiddlewaretoken') === false) {
                $csrfgen = new csrfGenerator();
                $this->session->set('csrfmiddlewaretoken', $csrfgen->generateToken());
            }
            $token = $this->session->get('csrfmiddlewaretoken');
            return "<input type='hidden' name='csrfmiddlewaretoken' id='csrfmiddlewaretoken' value='" . $token . "'>";
        };
        return $_csrf;
    }

    private function valueTemplator()
    {
        $_val = function($val_key) {
            if($this->template->existsValKey($val_key)) {
                return $this->template->getValue($val_key);
            }
        };
        return $_val;
    }

    public function staticTemplator()
    {
        $_static = function($resource_path) {
            return $this->server->domain() . "/" . trim($resource_path, "/");
        };
        return $_static;
    }

    public function processTemplate()
    {
        $_static = $this->staticTemplator();
        $_val = $this->valueTemplator();
        $_csrf = $this->csrfTokenTemplator();
        // defining view templator
        // note: all other templator should be bound
        //       to the view templators closure
        //       so that each template included using
        //       the include statement should find those
        //       method
        //       we've to do this using this certain method
        //       cause, closure's scoping problem
        //       nothing local by default is accessible
        //       to closure's body
        //       we've to bind everything explicitly
        $_view = function($view_key) use(&$_view, $_val, $_csrf) {
            if($this->template->existsViewKey($view_key)) {
                include $this->template->getViewPath(
                    $this->template->getViewName($view_key)
                );
            }
        };
        include $this->template->getViewPath($this->template->getRootView());
    }
}
