import {findGroups} from "./KMean/core";
import {groupImagePixels} from "./ImageKMean";

const imageSRC = "../test-images/0.jpg";
const groups = groupImagePixels(imageSRC, 5, 50_000);

const row = document.querySelector(".row");

groups.then(g => {
  for (let i = 0; i < g.length; i++) {
    const cell = document.createElement("div");
    cell.style.backgroundColor = g[i].intoPoint().toString();
    cell.style.color = g[i].intoPoint().l > 0.5
      ? "black"
      : "white";
    
    cell.textContent = g[i].intoPoint().toString();
    
    row.append(cell);
  }
});