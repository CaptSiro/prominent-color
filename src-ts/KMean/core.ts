import {Point} from "./Point";
import {Centroid, CentroidBuilder} from "./Centroid";



export function moveCentroids(points: Point[], centroids: Centroid[], centroidBuilder: CentroidBuilder): Centroid[] {
  const map = new Map<number, Point[]>();
  const intoPoints = centroids.map(c => c.intoPoint());
  
  for (let i = 0; i < points.length; i++) {
    const closest = points[i].closest(intoPoints);
    
    if (!map.has(closest)) {
      map.set(closest, [points[i]]);
      continue;
    }
    
    map.get(closest).push(points[i]);
  }
  
  const newCentroids = new Array(centroids.length);
  
  for (let i = 0; i < centroids.length; i++) {
    if (!map.has(i)) {
      newCentroids[i] = centroids[i];
      continue;
    }
    
    newCentroids[i] = centroidBuilder(map.get(i));
  }
  
  return newCentroids;
}



export function findGroups(points: Point[], centroids: Centroid[], centroidBuilder: CentroidBuilder): Centroid[] {
  let distance: number;
  do {
    const moved = moveCentroids(points, centroids, centroidBuilder);
    distance = 0;
  
    for (let i = 0; i < moved.length; i++) {
      distance += moved[i].intoPoint().distanceTo(centroids[i].intoPoint());
    }
    
    centroids = moved;
  } while(distance / centroids.length < 0.001);
  
  return centroids;
}