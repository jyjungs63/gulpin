// 어휘게임: 가운데 한자와 관련된 어휘 카드를 고르는 게임
// 이미지: assets/gulpin/programs/jj/page-{star}/level-{level}/word-game/{그룹}-{번호}.jpg
//   {그룹}-1 = 한자 카드, {그룹}-2~4 = 어휘 카드 (Game.xlsx 참조)

const GAMES = {
    mjplay: {
        bg: '#C7E59D',
        accent: '#7CB342',
        levels: {
            '01': [
                { reading: '일', words: ['한일', '일등', '일층', '일주일'] },
                { reading: '월', words: ['달월', '매월', '삼월', '월요일'] },
                { reading: '인', words: ['사람인', '인사', '인형', '한국인'] },
                { reading: '산', words: ['메산', '등산', '화산', '백두산'] },
                { reading: '일', words: ['날일', '일기', '매일', '생일'] },
                { reading: '목', words: ['나무목', '목요일', '회전목마', '식목일'] },
                { reading: '동', words: ['동녘동', '동해', '동대문', '동서남북'] },
            ],
        },
    },
    gj: {
        bg: '#FDD08D',
        accent: '#F0962E',
        levels: {
            '01': [
                { reading: '래', words: ['올래', '내일', '미래', '외래어'] },
                { reading: '휴', words: ['쉴휴', '휴식', '휴교', '휴게소'] },
                { reading: '림', words: ['수풀림', '밀림', '산림', '산림욕'] },
                { reading: '미', words: ['아닐미', '미안', '미정', '미완성'] },
                { reading: '말', words: ['끝말', '주말', '월말', '연말'] },
                { reading: '본', words: ['근본본', '기본', '본명', '본심'] },
                { reading: '명', words: ['밝을명', '발명', '조명', '투명'] },
            ],
        },
    },
};

// 카드 위치 (1번 왼쪽 위부터 시계방향, 화면 대비 %)
const POSITIONS = [
    [26.5, 35.5], [50, 18], [73.5, 35.5],
    [73.5, 68.5], [50, 82], [26.5, 68.5],
];
const CARD_COUNT = POSITIONS.length;
const COVER_MS = 2500;
const REVEAL_DELAY_MS = 1000;

let audioContext;

// 정답은 '딩동' 두 음, 오답은 낮은 '땡' 한 음으로 구분한다.
function playFeedbackSound(isCorrect) {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (!AudioContext) return;

    audioContext ??= new AudioContext();
    if (audioContext.state === 'suspended') audioContext.resume().catch(() => {});

    const startAt = audioContext.currentTime;
    const notes = isCorrect
        ? [
            { frequency: 1046.5, offset: 0, duration: 0.28, volume: 0.18 },
            { frequency: 783.99, offset: 0.18, duration: 0.42, volume: 0.2 },
        ]
        : [{ frequency: 196, offset: 0, duration: 0.48, volume: 0.28 }];

    notes.forEach(({ frequency, offset, duration, volume }) => {
        const oscillator = audioContext.createOscillator();
        const gain = audioContext.createGain();
        const noteStart = startAt + offset;
        const noteEnd = noteStart + duration;

        oscillator.type = isCorrect ? 'sine' : 'square';
        oscillator.frequency.setValueAtTime(frequency, noteStart);
        if (!isCorrect) oscillator.frequency.exponentialRampToValueAtTime(165, noteEnd);
        gain.gain.setValueAtTime(0.0001, noteStart);
        gain.gain.exponentialRampToValueAtTime(volume, noteStart + 0.01);
        gain.gain.exponentialRampToValueAtTime(0.0001, noteEnd);
        oscillator.connect(gain).connect(audioContext.destination);
        oscillator.start(noteStart);
        oscillator.stop(noteEnd);
    });
}

const params = new URLSearchParams(location.search);
const star = GAMES[params.get('star')] ? params.get('star') : 'mjplay';
const level = String(parseInt(params.get('level'), 10) || 1).padStart(2, '0');
const game = GAMES[star];
const groups = game.levels[level];
const baseDir = `../../assets/gulpin/programs/jj/page-${star}`;

// 뒤로가기: 호출한 페이지(말잼별/글잼별)로 이동
document.querySelector('.back-btn').addEventListener('click', () => {
    const fromSite = document.referrer && new URL(document.referrer).origin === location.origin;
    if (fromSite && history.length > 1) history.back();
    else location.href = `../../index.php#page-${star}`;
});
const imgPath = (group, no) => `${baseDir}/level-${level}/word-game/${group + 1}-${no}.jpg`;

const stage = document.getElementById('stage');
const cover = document.getElementById('cover');
const linesEl = stage.querySelector('.lines');
const centerEl = stage.querySelector('.center-card');
const cardsEl = stage.querySelector('.cards');
const nextBtn = stage.querySelector('.next-btn');
const progressEl = stage.querySelector('.progress');
const noticeEl = stage.querySelector('.notice');

const MARK_O = '<svg class="mark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="38" fill="none" stroke="#1E6FE8" stroke-width="12"/></svg>';
const MARK_X = '<svg class="mark" viewBox="0 0 100 100"><path d="M18 18L82 82M82 18L18 82" stroke="#E53935" stroke-width="14" stroke-linecap="round"/></svg>';

let order = [];
let round = 0;

function shuffle(arr) {
    const a = arr.slice();
    for (let i = a.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
}

function startGame() {
    order = shuffle(groups.map((_, i) => i));
    round = 0;
    showRound();
}

function showRound() {
    const group = order[round];
    const { reading } = groups[group];

    // 정답 어휘(같은 한자의 2~4번) + 다른 한자의 어휘로 채움
    // 같은 음이 들어간 다른 한자 어휘(예: 一 문제에 '매일')는 헷갈리므로 제외
    const answers = [2, 3, 4].map(no => ({ group, no, correct: true }));
    const others = [];
    groups.forEach((g, gi) => {
        if (gi === group) return;
        [2, 3, 4].forEach(no => {
            if (!g.words[no - 1].includes(reading)) others.push({ group: gi, no, correct: false });
        });
    });
    const picks = shuffle(answers.concat(shuffle(others).slice(0, CARD_COUNT - answers.length)));

    progressEl.textContent = `${round + 1} / ${order.length}`;
    nextBtn.classList.remove('show');
    centerEl.innerHTML = `<img src="${imgPath(group, 1)}" alt="${groups[group].words[0]}">`;
    linesEl.innerHTML = POSITIONS.map(([x, y]) => `<line x1="50" y1="50" x2="${x}" y2="${y}"/>`).join('');
    cardsEl.innerHTML = '';

    let remaining = answers.length;
    picks.forEach((pick, i) => {
        const card = document.createElement('div');
        card.className = 'card';
        card.style.left = `${POSITIONS[i][0]}%`;
        card.style.top = `${POSITIONS[i][1]}%`;
        card.innerHTML = `<span class="num">${i + 1}</span><img src="${imgPath(pick.group, pick.no)}" alt="${groups[pick.group].words[pick.no - 1]}">`;
        card.addEventListener('click', () => {
            if (card.classList.contains('done') || remaining === 0) return;
            card.classList.add('done');
            card.insertAdjacentHTML('beforeend', pick.correct ? MARK_O : MARK_X);
            playFeedbackSound(pick.correct);
            if (pick.correct && --remaining === 0) setTimeout(() => reveal(picks, reading), REVEAL_DELAY_MS);
        });
        cardsEl.appendChild(card);
    });
}

// 모두 찾으면 정답만 남기고 가운데에 음을 보여줌
function reveal(picks, reading) {
    const cards = cardsEl.querySelectorAll('.card');
    const lines = linesEl.querySelectorAll('line');
    picks.forEach((pick, i) => {
        cards[i].querySelector('.mark')?.remove();
        if (!pick.correct) {
            cards[i].classList.add('hidden');
            lines[i].classList.add('hidden');
        }
    });
    centerEl.innerHTML = `<span class="reading">${reading}</span>`;
    const last = round === order.length - 1;
    nextBtn.textContent = last ? '처음부터 다시' : '다음 문제 ▶';
    nextBtn.classList.add('show');
}

nextBtn.addEventListener('click', () => {
    if (round === order.length - 1) {
        startGame();
    } else {
        round++;
        showRound();
    }
});

function hideCover() {
    cover.classList.add('hide');
}

document.body.style.setProperty('--bg', game.bg);
document.body.style.setProperty('--accent', game.accent);
document.body.style.setProperty('--logo', `url('${baseDir}/log.png')`);
cover.querySelector('img').src = `${baseDir}/word-cover.jpg`;
cover.addEventListener('click', hideCover);
setTimeout(hideCover, COVER_MS);

if (groups) {
    startGame();
} else {
    centerEl.style.display = 'none';
    noticeEl.textContent = `${Number(level)}단계 어휘게임은 준비 중입니다.`;
    noticeEl.classList.add('show');
}
