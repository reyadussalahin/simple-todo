<?php declare(strict_types=1);

namespace App\Templating;

use App\Contracts\Templating\ViewsStoreInterface;


class ViewsStore implements ViewsStoreInterface
{
    private $views_base;

    public function __construct(string $views_base)
    {
        $this->views_base = $views_base;
    }

    public function getViewFilepath(string $view_name)
    {
        $view_name = trim(trim($view_name), "/");
        $view_name = str_replace(".", DIRECTORY_SEPARATOR, $view_name);
        $view_path = $this->views_base
            . DIRECTORY_SEPARATOR
            . $view_name
            . ".el.php";
        return $view_path;
    }
}