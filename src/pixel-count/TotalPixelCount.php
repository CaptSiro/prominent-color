<?php

namespace ProminentColor\PixelCount;

require_once __DIR__ . "/PixelCount.php";



class TotalPixelCount implements PixelCount {
    private int $totalPixelCount;



    public function __construct(int $totalPixelCount) {
        $this->totalPixelCount = $totalPixelCount;
    }



    function count(int $width, int $height): int {
        return $this->totalPixelCount;
    }
}