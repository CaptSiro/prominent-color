import {Point} from "./Point";

export interface Centroid {
  connectedPoints(): Point[];
  
  intoPoint(): Point;
}

export type CentroidBuilder = (points: Point[]) => Centroid;