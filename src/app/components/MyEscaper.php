<?php

namespace App\Components;
use Phalcon\Escaper;

class MyEscaper extends Escaper
{
    public function sanitize($var)
    {
        $escaper = new Escaper();
        return $escaper->escapeHtml($var);
    }
}