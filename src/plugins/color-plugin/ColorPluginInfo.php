<?php

namespace ProminentColor\Plugin;




readonly class ColorPluginInfo {
    public function __construct(
        public int $fixed_buffer_size
    ) {}
}