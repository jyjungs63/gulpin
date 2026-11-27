// 한자게임(메모리게임): 같은 번호의 그림(N-1)과 한자(N-2)를 짝 맞추기
// 유치원 이미지: assets/gulpin/programs/jj/page-{star}/level-{level}/hanja-game/{N}-1.jpg, {N}-2.jpg
// 유치원 사용: index.html?star=mjplay|gj&level=01
// 홈스쿨 이미지: assets/gulpin/programs/homeschool/level-{level}/... (images.php 가 과정 폴더의 짝 목록을 돌려줌)
// 홈스쿨 사용: index.html?program=homeschool&level=01|02&unit=A~E&course=1-A1|알아보기

const GAMES = {
    mjplay: { accent: '#84B84C', levels: { '01': 7 } },
    gj: { accent: '#F39730', levels: { '01': 7 } },
}
const HOMESCHOOL_ACCENTS = { '01': '#52AA44', '02': '#F39830' }
const HOMESCHOOL_DIR = '../../assets/gulpin/programs/homeschool'
const MAX_PAIRS = 6 // 4 x 3 판에 최대 6쌍, 더 많으면 랜덤으로 6쌍 선택
const BOARD_SIZE = MAX_PAIRS * 2
const EMPTY_EMOJI = '🈳' // 짝이 모자라 남는 칸 (선택 불가)
const COVER_MS = 2500
const PREVIEW_MS = 5000
const CARD_FLIP_MS = 600

const searchParams = new URLSearchParams(location.search)
const isHomeschool = searchParams.get('program') === 'homeschool'
const star = GAMES[searchParams.get('star')] ? searchParams.get('star') : 'mjplay'
const level = String(parseInt(searchParams.get('level'), 10) || 1).padStart(2, '0')
const game = GAMES[star]
const baseDir = `../../assets/gulpin/programs/jj/page-${star}`

// 판에 쓸 이미지: 짝 번호 목록과 그 폴더/확장자 (홈스쿨은 loadPairs 에서 채움)
let pairNumbers = Array.from({ length: game.levels[level] || 0 }, (_, i) => i + 1)
let imageDir = `${baseDir}/level-${level}/hanja-game`
let imageExt = 'jpg'

// 홈스쿨: 단계/호수/과정 폴더에 있는 짝 목록을 서버에서 받아옴
const loadPairs = async () => {
    if (!isHomeschool) return
    pairNumbers = []
    const query = new URLSearchParams({
        level,
        unit: searchParams.get('unit') || '',
        course: searchParams.get('course') || '',
    })
    try {
        const response = await fetch(`images.php?${query}`)
        const data = await response.json()
        pairNumbers = data.pairs
        imageDir = `${HOMESCHOOL_DIR}/${data.dir}`
        imageExt = 'png'
    } catch {}
}

// 뒤로가기: 호출한 페이지(말잼별/글잼별/홈스쿨)로 이동
document.querySelector('.back-btn').addEventListener('click', () => {
    const fromSite = document.referrer && new URL(document.referrer).origin === location.origin
    if (fromSite && history.length > 1) history.back()
    else location.href = isHomeschool ? '../../simple/index.php' : `../../index.php#page-${star}`
})

const selectors = {
    cover: document.querySelector('.cover'),
    game: document.querySelector('.game'),
    boardContainer: document.querySelector('.board-container'),
    board: document.querySelector('.board'),
    moves: document.querySelector('.moves'),
    timer: document.querySelector('.timer'),
    start: document.querySelector('.start'),
    newGame: document.querySelector('.new'),
    win: document.querySelector('.win'),
    notice: document.querySelector('.notice'),
}

const state = {
    gameStarted: false,
    flippedCards: 0,
    totalFlips: 0,
    totalTime: 0,
    loop: null,
    previewing: false,
    previewTimeout: null,
    previewFinishTimeout: null,
}

let audioContext

// 짝이 맞으면 '딩동' 두 음
const playMatchSound = () => {
    const AudioContext = window.AudioContext || window.webkitAudioContext
    if (!AudioContext) return

    audioContext ??= new AudioContext()
    if (audioContext.state === 'suspended') audioContext.resume().catch(() => {})

    const startAt = audioContext.currentTime
    const notes = [
        { frequency: 1046.5, offset: 0, duration: 0.28, volume: 0.18 },
        { frequency: 783.99, offset: 0.18, duration: 0.42, volume: 0.2 },
    ]

    notes.forEach(({ frequency, offset, duration, volume }) => {
        const oscillator = audioContext.createOscillator()
        const gain = audioContext.createGain()
        const noteStart = startAt + offset
        const noteEnd = noteStart + duration

        oscillator.type = 'sine'
        oscillator.frequency.setValueAtTime(frequency, noteStart)
        gain.gain.setValueAtTime(0.0001, noteStart)
        gain.gain.exponentialRampToValueAtTime(volume, noteStart + 0.01)
        gain.gain.exponentialRampToValueAtTime(0.0001, noteEnd)
        oscillator.connect(gain).connect(audioContext.destination)
        oscillator.start(noteStart)
        oscillator.stop(noteEnd)
    })
}

// 다 맞추면 화면 아래 양쪽에서 축포(색종이)를 쏨
const fireConfetti = () => {
    const canvas = document.createElement('canvas')
    canvas.className = 'confetti'
    document.body.appendChild(canvas)
    const ctx = canvas.getContext('2d')
    const width = canvas.width = innerWidth
    const height = canvas.height = innerHeight
    const colors = ['#FF5252', '#FFD740', '#69F0AE', '#40C4FF', '#E040FB', '#FF9100']

    const pieces = []
    const burst = () => {
        for (const fromLeft of [true, false]) {
            for (let i = 0; i < 80; i++) {
                const angle = (fromLeft ? -60 : -120) * Math.PI / 180 + (Math.random() - .5) * .8
                const speed = height * (.025 + Math.random() * .02)
                pieces.push({
                    x: fromLeft ? 0 : width,
                    y: height,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed,
                    size: height * (.008 + Math.random() * .01),
                    rotation: Math.random() * Math.PI,
                    spin: (Math.random() - .5) * .3,
                    color: colors[Math.floor(Math.random() * colors.length)],
                })
            }
        }
    }
    burst()
    setTimeout(burst, 600)
    setTimeout(burst, 1200)

    const startedAt = performance.now()
    const draw = now => {
        ctx.clearRect(0, 0, width, height)
        for (const p of pieces) {
            p.vy += height * .0006
            p.vx *= .99
            p.x += p.vx
            p.y += p.vy
            p.rotation += p.spin
            ctx.save()
            ctx.translate(p.x, p.y)
            ctx.rotate(p.rotation)
            ctx.fillStyle = p.color
            ctx.fillRect(-p.size / 2, -p.size / 4, p.size, p.size / 2)
            ctx.restore()
        }
        if (now - startedAt < 5000) requestAnimationFrame(draw)
        else canvas.remove()
    }
    requestAnimationFrame(draw)
}

const shuffle = array => {
    const clonedArray = [...array]

    for (let index = clonedArray.length - 1; index > 0; index--) {
        const randomIndex = Math.floor(Math.random() * (index + 1))
        const original = clonedArray[index]

        clonedArray[index] = clonedArray[randomIndex]
        clonedArray[randomIndex] = original
    }

    return clonedArray
}

const generateGame = () => {
    const pairs = shuffle(pairNumbers).slice(0, MAX_PAIRS)
    const items = []
    for (const pair of pairs) {
        items.push({ pair, src: encodeURI(`${imageDir}/${pair}-1.${imageExt}`) })
        items.push({ pair, src: encodeURI(`${imageDir}/${pair}-2.${imageExt}`) })
    }

    selectors.board.innerHTML = shuffle(items).map((item, index) => `
        <div class="card" data-pair="${item.pair}">
            <div class="card-front">${index + 1}</div>
            <div class="card-back"><img src="${item.src}" alt=""></div>
        </div>
    `).join('') + `<div class="card-empty">${EMPTY_EMOJI}</div>`.repeat(Math.max(0, BOARD_SIZE - items.length))

    selectors.board.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', () => flipCard(card))
    })
}

const updateStats = () => {
    selectors.moves.innerText = `${state.totalFlips} moves`
    selectors.timer.innerText = `time : ${state.totalTime}sec`
}

const startGame = () => {
    if (state.gameStarted || state.previewing) return
    state.gameStarted = true
    selectors.start.classList.add('disabled')
    selectors.newGame.classList.add('show')

    state.loop = setInterval(() => {
        state.totalTime++
        updateStats()
    }, 1000)
}

const clearPreviewTimers = () => {
    clearTimeout(state.previewTimeout)
    clearTimeout(state.previewFinishTimeout)
    state.previewTimeout = null
    state.previewFinishTimeout = null
}

const previewCards = () => {
    clearPreviewTimers()
    state.previewing = true
    selectors.start.classList.add('disabled')
    selectors.newGame.classList.add('show')

    const cards = selectors.board.querySelectorAll('.card')
    cards.forEach(card => card.classList.add('flipped'))

    state.previewTimeout = setTimeout(() => {
        cards.forEach(card => card.classList.remove('flipped'))

        state.previewFinishTimeout = setTimeout(() => {
            state.previewing = false
            startGame()
        }, CARD_FLIP_MS)
    }, PREVIEW_MS)
}

const resetGame = () => {
    clearInterval(state.loop)
    clearPreviewTimers()
    Object.assign(state, {
        gameStarted: false,
        flippedCards: 0,
        totalFlips: 0,
        totalTime: 0,
        loop: null,
        previewing: false,
    })
    updateStats()
    selectors.boardContainer.classList.remove('flipped')
    generateGame()
    previewCards()
}

const flipBackCards = () => {
    document.querySelectorAll('.card:not(.matched)').forEach(card => {
        card.classList.remove('flipped', 'shake')
    })

    state.flippedCards = 0
}

const flipCard = card => {
    if (!state.gameStarted || card.classList.contains('flipped') || state.flippedCards >= 2) return

    state.flippedCards++
    state.totalFlips++
    updateStats()

    card.classList.add('flipped')

    if (state.flippedCards === 2) {
        const flippedCards = document.querySelectorAll('.card.flipped:not(.matched)')

        if (flippedCards[0].dataset.pair === flippedCards[1].dataset.pair) {
            flippedCards[0].classList.add('matched')
            flippedCards[1].classList.add('matched')
            playMatchSound()
        } else {
            setTimeout(() => {
                flippedCards[0].classList.add('shake')
                flippedCards[1].classList.add('shake')
            }, 400)
        }

        setTimeout(() => {
            flipBackCards()
        }, 1000)
    }

    // 모든 짝을 맞추면 승리
    if (!document.querySelectorAll('.card:not(.flipped)').length) {
        clearInterval(state.loop)
        setTimeout(() => {
            selectors.boardContainer.classList.add('flipped')
            selectors.win.innerHTML = `
                <span class="win-text">
                    You won!<br />
                    with <span class="highlight">${state.totalFlips}</span> moves<br />
                    under <span class="highlight">${state.totalTime}</span> seconds
                </span>
            `
            new Audio('../../assets/game/memory-game/success1.mp3').play().catch(() => {})
            fireConfetti()
        }, 1000)
    }
}

const hideCover = () => {
    if (selectors.cover.classList.contains('hide')) return

    selectors.cover.classList.add('hide')
    if (pairNumbers.length) previewCards()
}

const homeschoolLevelDir = `${HOMESCHOOL_DIR}/level-${level}`
document.body.style.setProperty('--accent', isHomeschool ? HOMESCHOOL_ACCENTS[level] : game.accent)
document.querySelector('.stage').style.setProperty('--bg', isHomeschool
    ? `url('${homeschoolLevelDir}/background${Number(level)}.png')`
    : `url('${baseDir}/background.jpg')`)
selectors.cover.querySelector('img').src = isHomeschool
    ? `${homeschoolLevelDir}/cover${Number(level)}.png`
    : `${baseDir}/hanja-cover.jpg`

// 이미지 목록을 받은 뒤 표지를 넘길 수 있게 함
loadPairs().then(() => {
    selectors.cover.addEventListener('click', hideCover)
    setTimeout(hideCover, COVER_MS)

    if (pairNumbers.length) {
        selectors.start.addEventListener('click', startGame)
        selectors.newGame.addEventListener('click', resetGame)
        generateGame()
        selectors.start.classList.add('disabled')
    } else {
        selectors.game.style.display = 'none'
        selectors.notice.textContent = `${Number(level)}${isHomeschool ? '단계 한자게임' : '호 게임2'}은 준비 중입니다.`
        selectors.notice.classList.add('show')
    }
})
