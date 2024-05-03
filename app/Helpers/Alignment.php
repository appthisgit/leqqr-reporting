<?php

namespace App\Helpers;

enum Alignment: int
{
    case left = 1;   // STR_PAD_RIGHT
    case center = 2; // STR_PAD_BOTH
    case right = 0;  // STR_PAD_LEFT
}
