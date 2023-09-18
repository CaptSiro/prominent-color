<?php

namespace ProminentColor;



use KMean\Centroid;

function centroid_merge(Centroid $a, Centroid $b): Centroid {
    $point = [];

    $ap = $a->point;
    $ac = count($a->connections);

    $bp = $b->point;
    $bc = count($b->connections);

    for ($i = 0; $i < count($ap); $i++) {
        $point[$i] = ($ap[$i] * $ac + $bp[$i] * $bc) / ($ac + $bc);
    }

    return new Centroid($point, array_merge($a->connections, $b->connections));
}