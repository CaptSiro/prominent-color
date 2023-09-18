<?php

use KMean\Centroid;
use ProminentColor\Image;
use ProminentColor\PixelCount\WidthPixelCount;
use ProminentColor\Plugin\Color;
use ProminentColor\ProminentColor;

require_once __DIR__ . "/../../absol_modules/index.php";

$start = time();

$src = __DIR__ . "/test-images/7.jpg";
$pixel_count = new WidthPixelCount(64);

try {
    $image = Image::create($src, $pixel_count);

    /** @var Centroid[] $groups */
    $groups = ProminentColor::generate($image, 9);
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

$end = time();

$canvas = imagecreatetruecolor(ceil($image->width / $image->scale), ceil($image->height / $image->scale));

foreach ($groups as $centroid) {
    $rgb = array_map(fn($x) => (int)round($x), $centroid->point);
    $color = imagecolorallocate($canvas, $rgb[0], $rgb[1], $rgb[2]);

    /** @var Color $pixel */
    foreach ($centroid->connections as $pixel) {
        imagesetpixel($canvas, $pixel->x(), $pixel->y(), $color);
    }
}

imagepng($canvas, __DIR__ . "/image.png");

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Prominent color</title>

    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            display: grid;
            grid-template-rows: 1fr 80px;
            background-color: black;
        }

        img {
            width: 100vw;
            height: calc(100vh - 80px);
            object-fit: contain;
            image-rendering: pixelated;
        }

        .row {
            width: 100vw;
            height: 80px;
            display: grid;
            grid-template-columns: repeat(<?= count($groups) + 1 ?>, 1fr);
        }
    </style>
</head>
<body>
    <img src="./image.png" alt="pog">
    <div class="row">
        <?php
        foreach ($groups as $centroid) {
            $point = array_map(fn($x) => (int)round($x), $centroid->point);
            $rgb = "rgb(". join(", ", $point) .")";
            echo "
                    <span
                        style='
                            background-color: $rgb;
                            display: grid;
                            place-items: center;
                            text-align: center;
                            color: ". (array_sum($point) > 128*3 ? "black" : "white") ."
                        '
                    >$rgb: ". count($centroid->connections) ."</span>
                ";
        }
        ?>
        <span style="
            color: white;
            background-color: black;
            display: grid;
            place-items: center;
            text-align: center;
        ">Time to generate: <?= $end - $start ?> seconds<br>Total pixels: <?= $image->pixel_count->count($image->width, $image->height) ?></span>
    </div>
</body>
</html>