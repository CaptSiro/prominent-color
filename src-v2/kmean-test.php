<?php

  // result [{"x":274,"y":173},{"x":787,"y":511}]
  
  use KMean\Centroid;
  use KMean\Point;
  use KMean\PointClosestPoints;
  
  require_once __DIR__ . "/KMean/core.php";
  require_once __DIR__ . "/KMean/Centroid.php";
  require_once __DIR__ . "/KMean/Point.php";
  require_once __DIR__ . "/KMean/PointClosestPoints.php";
  
  class P implements Point, Centroid {
    public int $x, $y;
    
    private array $centroidPoints;
    
    public function __construct(int $x, int $y) {
      $this->x = $x;
      $this->y = $y;
    }
  
    /**
     * @param P $point
     * @return float
     */
    function distanceTo($point): float {
      return sqrt(($point->x - $this->x) ** 2 + ($point->y - $this->y) ** 2);
    }
  
    use PointClosestPoints;
  
    function connectedPoints(): array {
      return $this->centroidPoints;
    }
  
    /**
     * @param P[] $points
     * @return Centroid
     */
    static function new(array $points): Centroid {
      $sumX = $sumY = 0;
      $count = count($points);
      
      for ($i = 0; $i < $count; $i++) {
        $sumX += $points[$i]->x;
        $sumY += $points[$i]->y;
      }
      
      $new = new self($sumX / $count, $sumY / $count);
      $new->centroidPoints = $points;
      
      return $new;
    }
  
    function intoPoint(): Point {
      return $this;
    }
  }
  
  $points = json_decode(file_get_contents(__DIR__ . "/points.json"));
  foreach ($points as $i => $point) {
    $points[$i] = new P($point->x, $point->y);
  }
  
  $centroids = [new P(0, 0), new P(0, 0)];
  
  $groups = findGroups($points, $centroids, P::class);
  usort($groups, function (P $a, P $b) { // ASC sort
    $countA = count($a->connectedPoints());
    $countB = count($b->connectedPoints());
    
    if ($countA === $countB) {
      return 0;
    }
    
    if ($countA < $countB) {
      return -1;
    }
    
    return 1;
  });
  
  $json = [];
  foreach ($groups as $c) {
    $obj = new stdClass();
    $obj->x = round($c->x);
    $obj->y = round($c->y);
    
    var_dump(count($c->connectedPoints()));
    
    $json[] = $obj;
  }
  
  $string = json_encode($json);
  $res = file_get_contents(__DIR__ . "/result.json");
  
  echo "$string === $res";
  
  if ($string === $res) {
    echo "<br><span style='background-color: mediumseagreen; color: white'>Success</span>";
  } else {
    echo "<br><span style='background-color: crimson; color: white'>Failure</span>";
  }