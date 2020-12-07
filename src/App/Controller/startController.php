<?php

namespace App\Controller;

class startController
{
    public function print(): bool
    {
        echo 'Cześć jesteś w tym miejscu: <strong>'.__METHOD__.'</strong>';
        return true;
    }
}