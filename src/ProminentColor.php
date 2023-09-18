<?php

namespace ProminentColor;

require_once __DIR__ . "/../absol/import.php";
import("k-means");

use Exception;
use KMean\Centroid;
use ProminentColor\Plugin\ColorPlugin;
use ProminentColor\Plugin\Plugins;
use function KMean\kmeans;
use function KMean\kmeans_sort;
use function KMean\point_distance;
use const KMean\KMEAN_SORT_DESC;

require_once __DIR__ . "/plugins/Plugins.php";
require_once __DIR__ . "/plugins/color-space-loader.php";
require_once __DIR__ . "/centroid_merge.php";



class ProminentColor {
    /**
     * @param Image $image
     * @param int $colors
     * @param "rgb"|string $color_space
     * @return array
     * @throws Exception
     */
    static function generate(Image $image, int $colors, string $color_space = "rgb"): array {
        $color_plugin = Plugins::color_plugin($color_space);

        $points = $image->pixels($color_plugin);
        $centroids = $image->even_distribution($colors, $color_plugin);
        $point_dim = $color_plugin->info()->fixed_buffer_size;

        $groups = self::sanitize_colors(
            kmeans($points, $centroids, $point_dim),
            $color_plugin
        );

        kmeans_sort($groups, KMEAN_SORT_DESC);

        return $groups;
    }



    /**
     * @param Centroid[] $centroids
     * @param ColorPlugin $color_plugin
     * @return array
     */
    private static function sanitize_colors(array $centroids, ColorPlugin $color_plugin): array {
        $merged = [];
        $dims = $color_plugin->info()->fixed_buffer_size;

        $count = count($centroids);
        for ($i = 0; $i < $count; $i++) {
            if (!isset($centroids[$i]->connections[0])) {
                continue;
            }

            $merged_count = count($merged);

            for ($j = 0; $j < $merged_count; $j++) {
                $dist = point_distance($centroids[$i]->point, $merged[$j]->point, $dims);

                if ($dist > $color_plugin->duplicate_threshold()) {
                    continue;
                }

                $merged[$j] = centroid_merge($centroids[$i], $merged[$j]);
                continue 2;
            }

            $merged[] = $centroids[$i];
        }

        $stack = [];
        $min_count = array_reduce($merged, fn($carry, Centroid $c) => $carry + count($c->connections), 0) * 0.005;

        /** @var Centroid $centroid */
        foreach ($merged as $centroid) {
            if (count($centroid->connections) > $min_count) {
                $stack[] = $centroid;
            }
        }

        return $stack;
    }
}