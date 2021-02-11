<?php

namespace App\Controller;

class startController
{
    public function print(): bool
    {
        echo 'Cześć jesteś w tym miejscu: <strong>'.__METHOD__.'</strong>';
        nl2br(print_r($_SERVER));
        echo phpinfo();
        return true;
    }
}