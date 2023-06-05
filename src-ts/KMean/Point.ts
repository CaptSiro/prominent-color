export interface Point {
  distanceTo(point: Point): number;
  
  closest(points: Point[]): number;
}
