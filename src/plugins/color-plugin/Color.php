<?php

namespace ProminentColor\Plugin;

interface Color {
    function to_rgb(): RGB;

    function x(): int;

    function y(): int;
}