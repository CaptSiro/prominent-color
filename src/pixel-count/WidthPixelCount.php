<?php

namespace ProminentColor\PixelCount;

require_once __DIR__ . "/PixelCount.php";



class WidthPixelCount implements PixelCount {
    private int $resizedWidth;



    public function __construct(int $resizedWidth) {
        $this->resizedWidth = $resizedWidth;
    }



    function count(int $width, int $height): int {
        return round(($height * pow($this->resizedWidth, 2)) / $width);
    }
}