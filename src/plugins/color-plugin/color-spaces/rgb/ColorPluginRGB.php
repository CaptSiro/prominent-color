<?php

namespace ProminentColor\ColorSpaces\rgb;

use KMean\Point;
use ProminentColor\Plugin\Color;
use ProminentColor\Plugin\ColorPlugin;
use ProminentColor\Plugin\ColorPluginInfo;
use function KMean\point_distance;

require_once __DIR__ . "/../../Color.php";
require_once __DIR__ . "/../../ColorPlugin.php";
require_once __DIR__ . "/../../ColorPluginInfo.php";
require_once __DIR__ . "/ColorRGB.php";



class RGBColorPlugin implements ColorPlugin {
    function info(): ColorPluginInfo {
        return new ColorPluginInfo(3);
    }



    function from_int(int $color, int $x, int $y): Color & Point {
        return new ColorRGB(
            ($color >> 16) & 0xFF,
            ($color >> 8) & 0xFF,
            $color & 0xFF,
            $x,
            $y
        );
    }



    function distance($a, $b): float {
        return point_distance($a, $b, 3);
    }



    function duplicate_threshold(): float {
        return 20;
    }
}