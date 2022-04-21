<?php

namespace App\Service;

class StringHelper
{
    public function mytrim($str)
    {
        return preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $str);
    }
}
