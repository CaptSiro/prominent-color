<?php

namespace ProminentColor\ColorSpaces\rgb;

use KMean\Point;
use ProminentColor\Plugin\Color;
use ProminentColor\Plugin\RGB;

require_once __DIR__ . "/../../RGB.php";



readonly class ColorRGB implements Point, Color {
    private array $buffer;



    public function __construct(
        public int $red,
        public int $green,
        public int $blue,
        public int $x,
        public int $y,
    ) {
        $this->buffer = [$red, $green, $blue];
    }



    function data(): array {
        return $this->buffer;
    }



    function to_rgb(): RGB {
        return new RGB(
            $this->red,
            $this->green,
            $this->blue
        );
    }



    function x(): int {
        return $this->x;
    }



    function y(): int {
        return $this->y;
    }
}