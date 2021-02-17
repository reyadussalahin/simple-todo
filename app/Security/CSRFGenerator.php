<?php declare(strict_types=1);

namespace App\Security;

use App\Contracts\Security\CSRFGeneratorInterface;


class CSRFGenerator implements CSRFGeneratorInterface
{
    public function generateToken()
    {
        return bin2hex(random_bytes(64));
    }
}
