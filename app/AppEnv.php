<?php declare(strict_types=1);

namespace App;

use App\Contracts\AppEnvInterface;


class AppEnv implements AppEnvInterface
{
    public function getenv($env, $default="")
    {
        $ret = getenv($env);
        if($ret === false) {
            return $default;
        }
        return $ret;
    }
}
