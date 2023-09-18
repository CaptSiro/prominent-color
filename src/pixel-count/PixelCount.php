<?php

namespace ProminentColor\PixelCount;



interface PixelCount {
    function count(int $width, int $height): int;
}