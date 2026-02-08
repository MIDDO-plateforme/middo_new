<?php

namespace App\Bundle\MIDDOGeniusProBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MIDDOGeniusProBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}

