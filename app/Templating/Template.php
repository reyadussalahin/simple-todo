<?php declare(strict_types=1);

namespace App\Templating;

use App\Contracts\Templating\TemplateInterface;


class Template implements TemplateInterface
{
    private $views;
    private $root_view;
    private $added_vals;
    private $added_views;

    public function __construct(ViewsStore $views, string $root_view_name)
    {
        $this->added_views = [];
        $this->added_vals = [];
        $this->views = $views;
        $this->root_view_name = $root_view_name;
    }

    public function addView(string $view_key, string $view_name)
    {
        $this->added_views[$view_key] = $view_name;
    }

    public function addVar(string $var_key, $var_val)
    {
        $this->added_vals[$var_key] = $var_val;
    }

    public function existsViewKey(string $view_key)
    {
        return isset($this->added_views[$view_key]);
    }

    public function existsValKey(string $val_key)
    {
        return isset($this->added_vals[$val_key]);
    }

    public function getViewName(string $view_key)
    {
        if($this->existsViewKey($view_key)) {
            return $this->added_views[$view_key];
        }
        return "";
    }

    public function getValue(string $val_key)
    {
        if($this->existsValKey($val_key)) {
            return $this->added_vals[$val_key];
        }
        return "";
    }

    public function getRootView()
    {
        return $this->root_view_name;
    }

    public function getAddedViews()
    {
        return $this->added_views;
    }

    public function getViewPath(string $view_name)
    {
        return $this->views->getViewFilepath($view_name);
    }
}
