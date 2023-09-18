<?php

use KMean\Centroid;
use ProminentColor\Image;
use ProminentColor\PixelCount\WidthPixelCount;
use ProminentColor\ProminentColor;



// in real application, this could be accomplished with absol's import function:
// require_once __DIR__ . "/../absol/import.php";
// import("prominent-color");
require_once __DIR__ . "/../absol_modules/index.php";



$image_source = "../test-images/7.jpg";

// scale any image to use maximum of 64 pixels for width
$pixel_count = new WidthPixelCount(64);

try {
    $image = Image::create($image_source, $pixel_count);

    /** @var Centroid[] $groups */
    $groups = ProminentColor::generate($image, 9);
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}