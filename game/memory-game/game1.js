// if (getUser() == "" || getUser() == undefined)
//     window.location.href = "../../index.php";

const searchParams = new URLSearchParams(location.search);
var clas = searchParams.get("clas");
var sor = searchParams.get("sor");
var vol = searchParams.get("vol");
if (vol == undefined) vol = "v1";
if (clas == "" || clas == null) clas = "A";
// if (sor == "" || sor == null) sor = "sentens";

const assetBasePath = "../../assets/game/memory-game";

const selectors = {
  boardContainer: document.querySelector(".board-container"),
  board: document.querySelector(".board"),
  moves: document.querySelector(".moves"),
  timer: document.querySelector(".timer"),
  start: document.querySelector("button"),
  win: document.querySelector(".win"),
};

const state = {
  gameStarted: false,
  flippedCards: 0,
  totalFlips: 0,
  totalTime: 0,
  loop: null,
  revealTimeout: null,
};

const shuffle = (array) => {
  const clonedArray = [...array];
  for (let index = clonedArray.length - 1; index > 0; index--) {
    const randomIndex = Math.floor(Math.random() * (index + 1));
    const original = clonedArray[index];
    clonedArray[index] = clonedArray[randomIndex];
    clonedArray[randomIndex] = original;
  }
  return clonedArray;
};

const pickRandom = (array, items) => {
  const clonedArray = [...array];
  const randomPicks = [];
  for (let index = 0; index < items; index++) {
    const randomIndex = Math.floor(Math.random() * clonedArray.length);
    randomPicks.push(clonedArray[randomIndex]);
    clonedArray.splice(randomIndex, 1);
  }
  return randomPicks;
};

const generateGame = () => {
  const dimensions = selectors.board.getAttribute("data-dimension");
  if (dimensions % 2 !== 0) {
    throw new Error("The dimension of the board must be an even number.");
  }

  let emojis = ["PH-1.png", "PH-2.png", "PH-3.png", "PH-4.png"];
  let emojis1 = ["PH-5.png", "PH-6.png", "PH-7.png", "PH-8.png"];
  if (clas == "B") {
    emojis = ["PS-1.png", "PS-2.png", "PS-3.png", "PS-4.png"];
    emojis1 = ["PS-5.png", "PS-6.png", "PS-7.png", "PS-8.png"];
  }
  let classpath=""
  if (clas == "T") classpath="/memory";
  const picks = pickRandom(emojis, (dimensions * dimensions) / 4);
  const pickst = picks.map(name => name.replace('.png', "t.png"));
  const items = shuffle([...picks, ...pickst]);
  const cards = `
            ${items
              .map(
                (item, index) => `
                <div class="card">  
                <div class="card-front" style="color: white; font-size: 3rem; justify-content: center;align-items: center;display: flex">${index + 1}</div>                                   
                    <div class="card-back"><img style="width:100%; height: 100%; pointer-events: none;" class="img-responsive" src=${assetBasePath}/${vol}/${clas}/${classpath}/${item}></img></div>
                </div>
            `
              )
              .join("")}
    `;
  const picks1 = pickRandom(emojis1, (dimensions * dimensions) / 4);
  const pickst1st = picks1.map(name => name.replace('.png', "t.png"));
  const items1 = shuffle([...picks1, ...pickst1st]);
  const cards1 = `
            ${items1
              .map(
                (item, index) => `
                <div class="card">  
                <div class="card-front1" style="color: white; font-size: 3rem; justify-content: center;align-items: center;display: flex">${index + 1}</div>                                   
                    <div class="card-back1"><img style="width:100%; height: 100%; pointer-events: none;" class="img-responsive" src=${assetBasePath}/${vol}/${clas}/${classpath}/${item}></img></div>
                </div>
            `
              )
              .join("")}
    `;

  const mycard = ` <div class="board" style="grid-template-columns: repeat(${dimensions}, auto)"> 
    ${cards} ${cards1}
    </div>
    `;
  const parser = new DOMParser().parseFromString(mycard, "text/html");
  selectors.board.replaceWith(parser.querySelector(".board"));
};

const revealAllCards = () => {
  const allCards = document.querySelectorAll(".card");
  allCards.forEach(card => {
    card.classList.add("flipped");
  });
  state.revealTimeout = setTimeout(() => {
    allCards.forEach(card => {
      if (!card.classList.contains("matched")) {
        card.classList.remove("flipped");
      }
    });
    startGameTimer();
  }, 3000);
};

const startGameTimer = () => {
  state.gameStarted = true;
  state.loop = setInterval(() => {
    state.totalTime++;
    selectors.moves.innerText = `${state.totalFlips} moves`;
    selectors.timer.innerText = `time: ${state.totalTime} sec`;
  }, 1000);
};

const startGame = () => {
  if (state.gameStarted) {
    clearInterval(state.loop);
    clearTimeout(state.revealTimeout);
  }
  state.gameStarted = false;
  state.flippedCards = 0;
  state.totalFlips = 0;
  state.totalTime = 0;
  selectors.start.classList.add("disabled");
  selectors.moves.innerText = "0 moves";
  selectors.timer.innerText = "time: 0 sec";
  selectors.boardContainer.classList.remove("flipped");
  selectors.win.innerHTML = ""; // Clear win message
  generateGame(); // Regenerate the board
  revealAllCards();
};

const flipBackCards = () => {
  document.querySelectorAll(".card:not(.matched)").forEach((card) => {
    card.classList.remove("flipped", "shake");
  });
  state.flippedCards = 0;
};

const flipCard = (card) => {
  if (!state.gameStarted) {
    return;
  }
  state.flippedCards++;
  state.totalFlips++;
  if (state.flippedCards <= 2) {
    card.classList.add("flipped");
  }
  if (state.flippedCards === 2) {
    const flippedCards = document.querySelectorAll(".flipped:not(.matched)");
    const mydom = new DOMParser().parseFromString(flippedCards[0].innerHTML, "text/html");
    let word1 = mydom.getElementsByTagName("img")[0].attributes["src"].value.match(/[^/]+$/)[0];
    const mydom1 = new DOMParser().parseFromString(flippedCards[1].innerHTML, "text/html");
    let word2 = mydom1.getElementsByTagName("img")[0].attributes["src"].value.match(/[^/]+$/)[0];
    if (word1.substr(0, 4) == word2.substr(0, 4)) {
      flippedCards[0].classList.add("matched");
      flippedCards[1].classList.add("matched");
      let sound = replaceFileExtension(mydom.getElementsByTagName("img")[0].attributes["src"].value.replace("t.png", ".png"), "mp3");
      var audio = new Audio(sound);
      audio.play();
    }
    setTimeout(() => {
      flippedCards[0].classList.add("shake");
      flippedCards[1].classList.add("shake");
    }, 400);
    setTimeout(() => {
      flipBackCards();
    }, 1000);
  }
  if (card.getElementsByTagName("img")[0]) {
    let sound = replaceFileExtension(
      card.getElementsByTagName("img")[0].attributes["src"].value.replace("t.png", ".png"),
      "mp3"
    );
    var audio = new Audio(sound);
    //audio.play();
  }
  if (!document.querySelectorAll(".card:not(.flipped)").length) {
    setTimeout(() => {
      selectors.boardContainer.classList.add("flipped");
      selectors.win.innerHTML = `
                <span class="win-text">
                    You won!<br />
                    with <span class="highlight">${state.totalFlips}</span> moves<br />
                    under <span class="highlight">${state.totalTime}</span> seconds
                </span>
            `;
      var audio = new Audio("../../assets/game/memory-game/success1.mp3");
      audio.play();
      selectors.start.classList.remove("disabled");
      clearInterval(state.loop);
      // Automatically restart the game after 3 seconds
      setTimeout(() => {
        startGame();
      }, 3000);
    }, 1000);
  }
};

function replaceFileExtension(fileName, newExtension) {
  var lastDotIndex = fileName.lastIndexOf(".");
  if (lastDotIndex !== -1 && lastDotIndex > 0) {
    var newFileName = fileName.substr(0, lastDotIndex) + "." + newExtension;
    return newFileName;
  } else {
    return fileName + "." + newExtension;
  }
}

const attachEventListeners = () => {
  document.addEventListener("click", (event) => {
    const eventTarget = event.target;
    const eventParent = eventTarget.closest(".card");
    if (eventParent && !eventParent.classList.contains("flipped") && state.gameStarted) {
      flipCard(eventParent);
    }
    if (eventTarget.nodeName === "BUTTON" && !eventTarget.className.includes("disabled")) {
      startGame();
      const btnNew = document.getElementById("idNew");
      btnNew.style.display = "inline-block";

    // New 버튼 클릭 이벤트 처리
    btnNew.addEventListener("click", () => {
      console.log("New Game 시작");
      location.reload();
      btnNew.style.display = "none";
    }, { once: true }); // 한번만 등록되도록 (중복 방지)
    }
  });
};

generateGame();
attachEventListeners();