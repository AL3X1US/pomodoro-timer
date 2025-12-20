<?php

namespace App\Enums;

enum TimerPreset : int 
{
    case treSecondi = 3;
    case cinqueMinuti = 300;
    case dieciMinuti = 600;
    case quindiciMinuti = 900;
    case ventiMinuti = 1200;
    case trentaMinuti = 1800;
    case cinquantaMinuti = 3000;
    case novantaMinuti = 5400;
}
