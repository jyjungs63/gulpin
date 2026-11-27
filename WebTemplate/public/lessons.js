export const lessons = [
  {
    id: 'letter-play', stage: 'kindergarten', subject: '한글', theme: 'letters', mark: '가',
    title: '첫소리 찾기', description: '그림을 보고 알맞은 첫 글자를 찾아요.',
    questions: [
      { prompt: '🍎 사과의 첫 글자는 무엇일까요?', options: ['아', '사', '과'], answer: 1 },
      { prompt: '🍌 바나나의 첫 글자는 무엇일까요?', options: ['바', '나', '다'], answer: 0 },
      { prompt: '🦋 나비의 첫 글자는 무엇일까요?', options: ['다', '비', '나'], answer: 2 }
    ]
  },
  {
    id: 'count-play', stage: 'kindergarten', subject: '수학', theme: 'math', mark: '123',
    title: '하나, 둘, 셋!', description: '하나씩 손으로 짚으며 개수를 세어요.',
    questions: [
      { prompt: '🍓 🍓 🍓 딸기가 몇 개 있나요?', options: ['2개', '3개', '4개'], answer: 1 },
      { prompt: '⭐ ⭐ 별이 몇 개 있나요?', options: ['2개', '3개', '5개'], answer: 0 },
      { prompt: '🐥 🐥 🐥 🐥 병아리가 몇 마리 있나요?', options: ['2마리', '3마리', '4마리'], answer: 2 }
    ]
  },
  {
    id: 'nature-play', stage: 'kindergarten', subject: '관찰 놀이', theme: 'observe', mark: '✿',
    title: '자연을 살펴봐요', description: '하늘과 연못, 나무 주변을 살펴봐요.',
    questions: [
      { prompt: '연못에서 헤엄치는 친구는 누구일까요?', options: ['🐱 고양이', '🐟 물고기', '🐰 토끼'], answer: 1 },
      { prompt: '밤하늘에서 볼 수 있는 것은 무엇일까요?', options: ['🌙 달', '🍄 버섯', '🐚 조개'], answer: 0 },
      { prompt: '나무에 달리는 열매를 찾아볼까요?', options: ['🪨 돌', '☁️ 구름', '🍎 사과'], answer: 2 }
    ]
  },
  {
    id: 'reading-time', stage: 'elementary', subject: '한글', theme: 'letters', mark: '가',
    title: '짧은 글을 읽어요', description: '문장 속 단서를 찾아 질문에 답해요.',
    questions: [
      { prompt: '“지우는 우산을 쓰고 집에 갔어요.” 지우는 무엇을 썼나요?', options: ['모자', '우산', '안경'], answer: 1 },
      { prompt: '“민준이는 도서관에서 책을 빌렸어요.” 민준이는 어디에 갔나요?', options: ['도서관', '놀이터', '문구점'], answer: 0 },
      { prompt: '“유나는 할머니와 시장에서 딸기를 샀어요.” 유나는 누구와 갔나요?', options: ['친구', '동생', '할머니'], answer: 2 }
    ]
  },
  {
    id: 'number-time', stage: 'elementary', subject: '수학', theme: 'math', mark: '123',
    title: '더하고 빼요', description: '간단한 덧셈과 뺄셈을 풀어요.',
    questions: [
      { prompt: '연필 5자루에 3자루를 더하면 모두 몇 자루인가요?', options: ['7자루', '8자루', '9자루'], answer: 1 },
      { prompt: '사과 10개 중 4개를 먹으면 몇 개가 남나요?', options: ['6개', '5개', '4개'], answer: 0 },
      { prompt: '7 + 6은 얼마인가요?', options: ['11', '12', '13'], answer: 2 }
    ]
  },
  {
    id: 'season-time', stage: 'elementary', subject: '관찰 놀이', theme: 'observe', mark: '✿',
    title: '계절의 변화를 찾아요', description: '우리 주변 풍경을 보고 계절을 알아맞혀요.',
    questions: [
      { prompt: '벚꽃이 피고 새싹이 돋는 계절은 언제인가요?', options: ['여름', '봄', '가을'], answer: 1 },
      { prompt: '단풍이 들고 낙엽이 떨어지는 계절은 언제인가요?', options: ['가을', '봄', '겨울'], answer: 0 },
      { prompt: '눈이 오고 물이 얼기도 하는 계절은 언제인가요?', options: ['여름', '봄', '겨울'], answer: 2 }
    ]
  }
];
