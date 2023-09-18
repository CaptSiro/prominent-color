<?php

use ProminentColor\ColorSpaces\rgb\RGBColorPlugin;
use ProminentColor\Plugin\Plugins;

require_once __DIR__ . "/Plugins.php";
require_once __DIR__ . "/color-plugin/color-spaces/rgb/ColorPluginRGB.php";

try {
    Plugins::register_color_plugin("rgb", new RGBColorPlugin());
} catch (Exception) {}