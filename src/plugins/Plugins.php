<?php

namespace ProminentColor\Plugin;



use Exception;

class Plugins {
    private static array $color_plugins = [];



    /**
     * @throws Exception
     */
    static function register_color_plugin(string $color_space, ColorPlugin $plugin): void {
        if (isset(self::$color_plugins[$color_space])) {
            throw new Exception("Cannot register color plugin. Color space '$color_space' is already assigned to different plugin");
        }

        self::$color_plugins[$color_space] = $plugin;
    }



    static function color_plugin(string $color_space): ColorPlugin|null {
        return self::$color_plugins[$color_space] ?? null;
    }
}