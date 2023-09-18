<?php

namespace ProminentColor\Plugin;

readonly class RGB {
    public function __construct(
        public int $red,
        public int $green,
        public int $blue
    ) {}
}