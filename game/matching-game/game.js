let correct = 0;
let total = 0;
const totalDraggableItems = 8;
const totalMatchingPairs = 8; // Should be <= totalDraggableItems

const scoreSection = document.querySelector(".score");
const correctSpan = document.querySelector(".correct");
const totalSpan = document.querySelector(".total");
const playAgainBtn = document.querySelector("#play-again-btn");

var draggableItems = document.querySelectorAll('[class^="drag-"]');
var matchingPairs = document.querySelectorAll('[class^="match-"]');
let draggableElements;
let droppableElements;

let randomNumber = 1;
let path;

var step = "A";
var vol = "v1";

const searchParams = new URLSearchParams(location.search);
step = searchParams.get("step") || "A";
vol = searchParams.get("vol") || "v1";

let colormap = [
  "green",
  "red",
  "blue",
  "yellow",
  "pink",
  "brown",
  "steelblue",
  "gold",
];

var sql = `select path, cover, name from ? where step= "${step}"`;
var res = alasql(sql, [items]);

if (res.length == 0) {
  sql = 'select path, cover, name from ? where step= "A"';
  res = alasql(sql, [items]);
}

path = res[0]["path"] + vol + "/" + step + "/";
if (step == "T") 
  path = res[0]["path"] + vol + "/" + step + "/" + "matching/";
// 디바이스 감지 함수 개선: 태블릿도 터치 기반으로 처리
function getDeviceType() {
  const parser = new UAParser();
  const result = parser.getResult();
  const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

  if (result.device.type === "mobile" || result.device.type === "tablet" || isTouchDevice) {
    return "touch";
  } else {
    return "pc";
  }
}

console.log("This device is a " + getDeviceType());

initiateGame();

function initiateGame() {
  const randomDraggableBrands = generateRandomItemsArray(totalDraggableItems, res[0]["name"]);
  const randomDroppableBrands =
    totalMatchingPairs < totalDraggableItems
      ? generateRandomItemsArray(totalMatchingPairs, randomDraggableBrands)
      : randomDraggableBrands;
  const alphaRandomDropBrand = [...randomDroppableBrands].sort((a, b) =>
    a.imageName.toLowerCase().localeCompare(b.imageName.toLowerCase())
  );

  var i = 1;
  draggableItems.forEach((elem) => {
    var img = document.createElement("img");
    img.id = `${randomDraggableBrands[i - 1].imageName}`;
    img.src = `${path}${randomDraggableBrands[i - 1].imageName}.png`;
    img.alt = "Image " + i;
    img.classList.add("img-responsive", "draggable", "image-container");
    img.setAttribute("draggable", "true");
    img.setAttribute("touch-action", "none");
    img.width = 200;
    img.height = 200;
    document.getElementById("drag-" + i).appendChild(img);
    i++;
  });

  const matchingPairs = document.querySelectorAll('[id^="match-"]');
  for (let i = 0; i < alphaRandomDropBrand.length; i++) {
    var img = document.createElement("img");
    img.id = `${alphaRandomDropBrand[i].textName}`;
    img.src = `${path}${alphaRandomDropBrand[i].textName}.png`;
    img.classList.add("droppable");
    img.setAttribute("data-brand", `${alphaRandomDropBrand[i].textName}`);
    img.width = 200;
    img.height = 200;
    document.getElementById("match-" + (i + 1)).appendChild(img);
  }

  draggableElements = document.querySelectorAll(".draggable");
  droppableElements = document.querySelectorAll(".droppable");

  var activeEvent = "";
  var originalX = "";
  var originalY = "";

  // 디바이스 유형에 따라 이벤트 바인딩
  if (getDeviceType() === "touch") {
    draggableElements.forEach((elem) => {
      elem.addEventListener("touchstart", handleTouchStart, { passive: false });
      elem.addEventListener("touchmove", handleTouchMove, { passive: false });
      elem.addEventListener("touchend", handleTouchEnd, { passive: false });
    });
  } else {
    draggableElements.forEach((elem) => {
      elem.addEventListener("dragstart", dragStart);
    });
    droppableElements.forEach((elem) => {
      elem.addEventListener("dragenter", dragEnter);
      elem.addEventListener("dragover", dragOver);
      elem.addEventListener("dragleave", dragLeave);
      elem.addEventListener("drop", drop);
    });
  }
}

// Drag and Drop Functions
function dragStart(event) {
  let sound = `${path}${event.target.id.substring(0, 4)}.mp3`;
  var audio = new Audio(sound);
  if (step == "T") 
    console.log("below");
  else
    audio.play();
  event.dataTransfer.setData("text", event.target.id);
}

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

  var img = document.createElement("img");
  img.src = `${path}${res[0].cover}`;
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

  const isCorrectMatching =
    draggableElementBrand.slice(0, 4) === droppableElementBrand.slice(0, 4);
  total++;
  if (isCorrectMatching) {
    const draggableElement = document.getElementById(draggableElementBrand);
    event.target.classList.add("dropped");
    draggableElement.classList.add("dragged");
    draggableElement.setAttribute("draggable", "false");
    draggableElement.style.border = "3px solid " + colormap[total];
    let image = `<img style="margin-right: 10px" class="img-responsive draggable dragged" 
      src=${path}${draggableElementBrand}.png draggable="true"></img>`;

    event.target.style.border = "3px solid " + colormap[total];
    event.target.src = `${path}${draggableElementBrand}.png`;

    correct++;
    let sound = `${path}${event.target.id.substring(0, 4)}.mp3`;
    var audio = new Audio(sound);
    if (step == "T") 
      audio.play();
    else
      console.log("below");
  }
  scoreSection.style.opacity = 0;
  setTimeout(() => {
    correctSpan.textContent = correct;
    totalSpan.textContent = total;
    scoreSection.style.opacity = 1;
  }, 200);
  if (correct === Math.min(totalMatchingPairs, totalDraggableItems)) {
    playAgainBtn.style.display = "block";
    setTimeout(() => {
      finish();
    }, 200);
  }
}

// 화면 크기 동적 조정: 태블릿 포함
let mw, mh, marX, marY;
function setDimensions() {
  if (screen.width > 1024) {
    // PC
    mw = "150";
    mh = "150";
    marX = screen.width / 2 - 300;
    marY = screen.height / 2 - 300 - 150;
  } else if (screen.width > 767) {
    // 태블릿
    mw = "100";
    mh = "100";
    marX = screen.width / 2 - 200;
    marY = screen.height / 2 - 200 - 100;
  } else {
    // 모바일
    mw = "75";
    mh = "75";
    marX = screen.width / 2 - 150;
    marY = screen.height / 2 - 150 - 75;
  }
}
setDimensions();

function handleTouchStart(e) {
  e.preventDefault(); // 브라우저 기본 동작(스크롤 등) 방지
  originalX = e.target.offsetLeft + "px";
  originalY = e.target.offsetTop + "px";

  var pageX = parseInt(e.target.style.left || 0) + marX;
  var pageY = parseInt(e.target.style.top || 0) + marY;

  let dropobj = finDropObect(pageX, pageY);
  let sound = `${path}${e.target.id.substring(0, 4)}.mp3`;
  if (e.target.id[4] !== "t") {
    console.log("below");
  } else {
    var audio = new Audio(sound);
    audio.play();
  }

  activeEvent = "start";
}

function handleTouchMove(e) {
  e.preventDefault();
  var touchLocation = e.targetTouches[0];
  var pageX = touchLocation.pageX - marX + "px";
  var pageY = touchLocation.pageY - marY + "px";

  e.target.style.position = "absolute";
  e.target.style.left = pageX;
  e.target.style.top = pageY;
  e.target.style.width = mw;
  e.target.style.height = mh;

  activeEvent = "move";
}

function handleTouchEnd(e) {
  e.preventDefault();

  if (activeEvent === "move") {
    var pageX = parseInt(e.target.style.left || 0) + marX;
    var pageY = parseInt(e.target.style.top || 0) + marY;
    let dragid = e.target.getAttribute("id");

    let dropobj = finDropObect(pageX, pageY);

    if (dropobj != null) {
      let targetid = dropobj.id;
      if (dropobj.id.slice(0, 4) === dragid.slice(0, 4)) {
        total++;
        let color = Math.floor(Math.random() * 7);
        const draggableElement = document.getElementById(dragid);
        draggableElement.classList.add("dragged");
        draggableElement.setAttribute("draggable", "false");

        draggableElement.style.left = originalX;
        draggableElement.style.top = originalY;
        draggableElement.style.border = "3px solid " + colormap[total];

        dropobj.width = mw;
        dropobj.height = mh;
        dropobj.style.opacity = "0.2";
        dropobj.style.border = "3px solid " + colormap[total];
        dropobj.src = `${path}${draggableElement.id}.png`;

        correct++;
          let sound = `${path}${draggableElement.id.substring(0, 4)}.mp3`;
          var audio = new Audio(sound);
          if (step == "T") 
            audio.play();
          else
            console.log("below");
      } else {
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
    playAgainBtn.style.display = "block";
    setTimeout(() => {
      finish();
    }, 200);
  }
}

function finish() {
  total = 0;
  var audio = new Audio("../../assets/game/memory-game/success1.mp3");
  audio.play();
  playAgainBtn.classList.add("play-again-btn-entrance");
}

function finDropObect(x, y) {
  let res = null;
  droppableElements.forEach((elem) => {
    var rect = elem.getBoundingClientRect();
    let x0 = rect.left;
    let x1 = rect.right;
    let y0 = rect.top;
    let y1 = rect.bottom;

    if (x >= x0 && x < x1 && y >= y0 && y < y1) res = elem;
  });
  return res;
}

playAgainBtn.addEventListener("click", playAgainBtnClick);
function playAgainBtnClick() {
  location.reload();
}

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