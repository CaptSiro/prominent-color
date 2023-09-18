<?php

namespace ProminentColor;

use GdImage;



function create_gd_image(string $path): false|GdImage {
    if (!file_exists($path)) {
        return false;
    }

    $type = mime_content_type($path);

    ini_set("memory_limit","512M");

    return match ($type) {
        "image/png" => imagecreatefrompng($path),
        "image/jpg" => imagecreatefromjpeg($path),
        "image/gif" => imagecreatefromgif($path),
        default => imagecreatefromstring(file_get_contents($path)),
    };
}