let correct = 0;
let total = 0;
const totalDraggableItems = 6;
const totalMatchingPairs = 6; // Should be <= totalDraggableItems

const scoreSection = document.querySelector(".score");
const correctSpan = document.querySelector(".correct");
const totalSpan = document.querySelector(".total");
const playAgainBtn = document.querySelector("#play-again-btn");

var draggableItems = document.querySelectorAll('[class^="drag-"]');
var matchingPairs = document.querySelectorAll('[class^="match-"]');
let draggableElements;
let droppableElements;

let radomNumber = 1;
let path;

const searchParams = new URLSearchParams(location.search);
var step = searchParams.get("step");
var vol = searchParams.get("vol");

step = "A";

var sql =
  'select path, cover, name from ? where vol="' +
  vol +
  '" and step= "' +
  step +
  '"';
var res = alasql(sql, [items]);

if (res.length == 0) {
  sql = 'select path, cover, name from ? where vol="v1" and step= "A"';
  res = alasql(sql, [items]);
}

// switch (1) {
//   case 1:
//     path = "../../assets/game/paza-game/v3/y4/";
//     break;
//   case 2:
//     path = "../../assets/game/memory-game/v1/y5/";
//     break;
//   case 3:
//     path = "../../assets/game/memory-game/v1/y6/";
//     break;
//   case 4:
//     path = "../../assets/game/memory-game/v1/y7/";
//     break;
// }

path = res[0]["path"];

function getDeviceType() {
  const parser = new UAParser();
  const result = parser.getResult();
  const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

  if (result.device.type === "mobile" || result.device.type === "tablet" || isTouchDevice) {
    return "mobile";
  } else {
    return "pc";
  }
}

console.log("This device is a " + getDeviceType());

initiateGame();

function initiateGame() {
  //const randomDraggableBrands = generateRandomItemsArray(totalDraggableItems, items[radomNumber-1]['name']);
  const randomDraggableBrands = generateRandomItemsArray(
    totalDraggableItems,
    res[0]["name"]
  );
  const randomDroppableBrands =
    totalMatchingPairs < totalDraggableItems
      ? generateRandomItemsArray(totalMatchingPairs, randomDraggableBrands)
      : randomDraggableBrands;
  const alphaRandomDropBrand = [...randomDroppableBrands].sort((a, b) =>
    a.imageName.toLowerCase().localeCompare(b.imageName.toLowerCase())
  );
  //const alphaRandomDropBrand = [...randomDroppableBrands].sort((a,b) => a.imageName.localeCompare(b.imageName));

  var i = 1;
  draggableItems.forEach((elem) => {
    var img = document.createElement("img"); // 이미지 요소 생성
    img.id = `${randomDraggableBrands[i - 1].imageName}`;
    img.src = `${path}${randomDraggableBrands[i - 1].imageName}.png`; // 이미지 경로 설정
    img.alt = "Image " + i; // 대체 텍스트 설정
    img.classList.add("img-responsive");
    img.classList.add("draggable");
    img.classList.add("image-container");
    img.setAttribute("draggable", "true");
    img.setAttribute("touch-action", "none");
    img.width = "200";
    img.height = "200";
    document.getElementById("drag-" + i).appendChild(img);
    i++;
  });

  var img = document.createElement("img"); // 이미지 요소 생성

  img.src = `${path}${res[0].cover}`; // 이미지 경로 설정
  img.id = "imgcover";
  img.classList.add("img-responsive");

  document.getElementById("idcover").appendChild(img);

  // Create "matching-pairs" and append to DOM
  const matchingPairs = document.querySelectorAll('[id^="match-"]');

  for (let i = 0; i < alphaRandomDropBrand.length; i++) {
    var img = document.createElement("img");

    img.id = `${alphaRandomDropBrand[i].textName}`;
    img.src = `${path}${alphaRandomDropBrand[i].textName}.png`; // 이미지 경로 설정

    img.classList.add("droppable");
    img.setAttribute("data-brand", `${alphaRandomDropBrand[i].textName}`);
    img.width = "200";
    img.height = "200";
    //span.textContent = `${alphaRandomDropBrand[i].imageName}`
    document.getElementById("match-" + (i + 1)).appendChild(img);
  }

  draggableElements = document.querySelectorAll(".draggable");
  droppableElements = document.querySelectorAll(".droppable");

  var activeEvent = "";
  var originalX = "";
  var originalY = "";

  if (getDeviceType() == "mobile") {
    draggableElements.forEach((elem) => {
      elem.addEventListener("touchstart", handleTouchStart, false);
      elem.addEventListener("touchmove", handleTouchMove, false);
      elem.addEventListener("touchend", handleTouchEnd, false);
    });
    droppableElements.forEach((elem) => {
      elem.addEventListener("touchstart", handleTouchStart, false);
    });
  } else {
    draggableElements.forEach((elem) => {
      elem.addEventListener("dragstart", dragStart);
      elem.addEventListener("mouseenter", mouseEnter);
    });
    droppableElements.forEach((elem) => {
      elem.addEventListener("dragenter", dragEnter);
      elem.addEventListener("dragover", dragOver);
      elem.addEventListener("dragleave", dragLeave);
      elem.addEventListener("drop", drop);
    });
  }
}

function mouseEnter(event) {
  event.preventDefault();
}
// Drag and Drop Functions

//Events fired on the drag target

function dragStart(event) {
  let sound = `${path}${event.target.id}.mp3`;
  var audio = new Audio(sound);

  audio.play();
  event.dataTransfer.setData("text", event.target.id); // or "text/plain"
}

//Events fired on the drop target

function dragEnter(event) {
  if (
    event.target.classList &&
    event.target.classList.contains("droppable") &&
    !event.target.classList.contains("dropped")
  ) {
    event.target.classList.add("droppable-hover");
  }
}

function dragOver(event) {
  if (
    event.target.classList &&
    event.target.classList.contains("droppable") &&
    !event.target.classList.contains("dropped")
  ) {
    event.preventDefault();
  }
}

function dragLeave(event) {
  if (
    event.target.classList &&
    event.target.classList.contains("droppable") &&
    !event.target.classList.contains("dropped")
  ) {
    event.target.classList.remove("droppable-hover");
  }
}
var intervalId;
function changeCover() {
  var oldimg = document.getElementById("imgcover");
  document.getElementById("idcover").removeChild(oldimg);

  var img = document.createElement("img"); // 이미지 요소 생성
  img.src = `${path}${res[0].cover}`; // 이미지 경로 설정
  img.id = "imgcover";
  img.classList.add("img-responsive");

  document.getElementById("idcover").appendChild(img);
  clearInterval(intervalId);
}

function drop(event) {
  event.preventDefault();
  event.target.classList.remove("droppable-hover");
  const draggableElementBrand = event.dataTransfer.getData("text");
  const droppableElementBrand = event.target.getAttribute("data-brand");
  //const isCorrectMatching = draggableElementBrand===droppableElementBrand;
  const isCorrectMatching =
    draggableElementBrand.slice(0, 2) === droppableElementBrand.slice(0, 2);
  total++;
  if (isCorrectMatching) {
    const draggableElement = document.getElementById(draggableElementBrand);
    event.target.classList.add("dropped");
    draggableElement.classList.add("dragged");
    draggableElement.setAttribute("draggable", "false");
    draggableElement.removeEventListener("mouseenter", mouseEnter);
    let image = `<img  style=" margin-right: 10px" class="img-responsive draggable dragged" 
      src=${path}${draggableElementBrand}.png  draggable="true" ></img>`;

    //event.target.innerHTML = image;
    event.target.classList.add("dragged");
    event.target.src = `${path}${draggableElementBrand}.png`;

    var oldimg = document.getElementById("imgcover");
    document.getElementById("idcover").removeChild(oldimg);

    var img = document.createElement("img"); // 이미지 요소 생성
    img.id = "imgcover";
    img.src = `${path}${droppableElementBrand}-T.png`; // 이미지 경로 설정
    img.classList.add("img-responsive");

    document.getElementById("idcover").appendChild(img);

    // let sound = `${path}${draggableElementBrand}.mp3`
    // var audio = new Audio(sound);
    // audio.play();
    correct++;
  }
  scoreSection.style.opacity = 0;
  setTimeout(() => {
    correctSpan.textContent = correct;
    totalSpan.textContent = total;
    scoreSection.style.opacity = 1;
  }, 200);
  if (correct === Math.min(totalMatchingPairs, totalDraggableItems)) {
    // Game Over!!
    playAgainBtn.style.display = "block";
    setTimeout(() => {
      //playAgainBtn.classList.add("play-again-btn-entrance");
      finish();
    }, 200);
  }
}

function handleTouchStart(e) {
  e.preventDefault();
  originalX = e.target.offsetLeft + "px";
  originalY = e.target.offsetTop + "px";
  activeEvent = "start";

  if (!/^[a-zA-Z]$/.test(e.target.id.charAt(2))) {
    let sound = `${path}${e.target.id}.mp3`;
    var audio = new Audio(sound);
    audio.play();
  }
}

var marX = screen.width / 2 - 150;
var marY = screen.height / 2 - 150 - 75;
var mw = "75";
var mh = "75";

if (screen.width > 767) {
  mw = "150";
  mh = "150";
  var marX = screen.width / 2 - 300;
  var marY = screen.height / 2 - 300 - 150;
}

function handleTouchMove(e) {
  e.preventDefault();
  var touchLocation = e.targetTouches[0];
  var pageX = touchLocation.pageX - marX + "px";
  var pageY = touchLocation.pageY - marY + "px";

  e.target.style.position = "absolute";
  e.target.style.left = pageX;
  e.target.style.top = pageY;
  if (screen.width > 767) {
    e.target.style.width = "150";
    e.target.style.height = "150";
  } else {
    e.target.style.width = "75";
    e.target.style.height = "75";
  }

  activeEvent = "move";
}

function handleTouchEnd(e) {
  e.preventDefault();

  if (activeEvent === "move") {
    var pageX = parseInt(e.target.style.left) + marX;
    var pageY = parseInt(e.target.style.top) + marY;
    let dragid = e.target.getAttribute("id");

    let dropobj = finDropObect(pageX, pageY);

    if (dropobj != null) {
      //if (dropobj.id.includes(dragid))
      let targetid = dropobj.id;
      if (dropobj.id.slice(0, 2) == dragid.slice(0, 2)) {
        total++;
        const draggableElement = document.getElementById(dragid);
        e.target.classList.add("dropped");
        draggableElement.classList.add("dragged");
        draggableElement.setAttribute("draggable", "false");
        draggableElement.remove();
        //dropobj.classList.add("img-responsive");
        dropobj.width = mw;
        dropobj.height = mh;
        dropobj.classList.add("dragged");
        dropobj.src = `${path}${draggableElement.id}.png`;

        var oldimg = document.getElementById("imgcover");
        document.getElementById("idcover").removeChild(oldimg);

        var img = document.createElement("img"); // 이미지 요소 생성
        img.id = "imgcover";
        img.src = `${path}${targetid}-T.png`; // 이미지 경로 설정
        img.classList.add("img-responsive");

        document.getElementById("idcover").appendChild(img);

        correct++;
      } else {
        // e.target.classList.remove("droppable-hover");
        e.target.style.left = originalX;
        e.target.style.top = originalY;
        e.target.style.width = mw;
        e.target.style.height = mh;
      }
    } else {
      e.target.style.left = originalX;
      e.target.style.top = originalY;
      e.target.style.width = mw;
      e.target.style.height = mh;
    }
  }
  scoreSection.style.opacity = 0;
  setTimeout(() => {
    correctSpan.textContent = correct;
    totalSpan.textContent = total;
    scoreSection.style.opacity = 1;
  }, 200);
  if (correct === Math.min(totalMatchingPairs, totalDraggableItems)) {
    // Game Over!!
    playAgainBtn.style.display = "block";
    setTimeout(() => {
      finish();
    }, 200);
  }
}
function finish() {
  var audio = new Audio("../../assets/game/memory-game/success1.mp3");

  audio.play();

  playAgainBtn.classList.add("play-again-btn-entrance");
}
function finDropObect(x, y) {
  res = null;
  droppableElements.forEach((elem) => {
    var rect = elem.getBoundingClientRect();
    console.log(rect);
    let x0 = rect.left;
    let x1 = rect.right;
    let y0 = rect.top;
    let y1 = rect.bottom;

    if (x >= x0 && x < x1 && y >= y0 && y < y1) res = elem;
  });
  return res;
}
function detectTouchEnd(x1, y1, x2, y2, w, h) {
  //Very simple detection here
  if (x2 - x1 > w) return false;
  if (y2 - y1 > h) return false;
  return true;
}

// Other Event Listeners
playAgainBtn.addEventListener("click", playAgainBtnClick);
function playAgainBtnClick() {
  location.reload();
}

// Auxiliary functions
function generateRandomItemsArray(n, originalArray) {
  let res = [];
  let clonedArray = [...originalArray];
  if (n > clonedArray.length) n = clonedArray.length;
  for (let i = 1; i <= n; i++) {
    const randomIndex = Math.floor(Math.random() * clonedArray.length);
    res.push(clonedArray[randomIndex]);
    clonedArray.splice(randomIndex, 1);
  }
  return res;
}
