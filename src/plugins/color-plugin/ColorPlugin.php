<?php

namespace ProminentColor\Plugin;

use KMean\Point;

require_once __DIR__ . "/ColorPluginInfo.php";
require_once __DIR__ . "/Color.php";



interface ColorPlugin {
    function info(): ColorPluginInfo;

    function from_int(int $color, int $x, int $y): Point & Color;

    function distance(array $a, array $b): float;

    function duplicate_threshold(): float;
}