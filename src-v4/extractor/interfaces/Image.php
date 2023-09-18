<?php
  
  namespace Extractor;
  
  use Generator;

  interface Image {
    function getWidth(): int;
    function getHeight(): int;
    function getScale(): float;
    
    function pixels(): Generator;
    
    function randomPixels(int $count): array;
  
    /**
     * @param int $count
     * @return Pixel[]
     */
    function evenDistribution(int $count): array;
    
    function release(): void;
  }