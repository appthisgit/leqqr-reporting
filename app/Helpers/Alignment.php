<?php

namespace App\Helpers;

enum Alignment: int
{
    case left = STR_PAD_RIGHT;
    case center = STR_PAD_BOTH;
    case right = STR_PAD_LEFT;


}
