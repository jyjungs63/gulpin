import { lessons } from './lessons.js';

const select = (selector) => document.querySelector(selector);
const accountButtons = document.querySelectorAll('[data-account]');
const stageButtons = document.querySelectorAll('[data-stage]');
const accountDialog = select('#account-dialog');
const accountForm = select('#account-form');
const accountError = select('#account-error');
const lessonDialog = select('#lesson-dialog');
const lessonContent = select('#lesson-content');

let stage = 'kindergarten';
let account = null;
let completed = [];
let registering = false;
let accountLoading = false;

function element(tag, content, className) {
  const node = document.createElement(tag);
  node.textContent = content;
  if (className) node.className = className;
  return node;
}

function action(label, style, run) {
  const button = element('button', label, `button ${style}`);
  button.type = 'button';
  button.addEventListener('click', run);
  return button;
}

async function api(path, body) {
  const options = body === undefined ? {} : {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  };
  let response;
  try {
    response = await fetch(`/api/${path}`, options);
  } catch {
    throw new Error('서버에 연결할 수 없어요. 잠시 후 다시 시도해 주세요.');
  }
  const data = await response.json().catch(() => {
    throw new Error('서버 응답을 확인할 수 없어요. 잠시 후 다시 시도해 주세요.');
  });
  if (!response.ok) throw new Error(data.error, { cause: response.status });
  return data;
}

function renderLessons() {
  const cards = lessons.filter((lesson) => lesson.stage === stage).map((lesson) => {
    const card = element('article', '', `lesson-card ${lesson.theme}`);
    const header = element('header', '');
    const mark = element('span', lesson.mark, 'lesson-mark');
    mark.setAttribute('aria-hidden', 'true');
    header.append(mark, element('span', lesson.subject));
    const done = completed.includes(lesson.id);
    const button = action(done ? '다시 해보기 ↗' : '활동 시작 ↗', 'quiet', () => startLesson(lesson));
    button.dataset.lesson = lesson.id;
    button.setAttribute('aria-label', `${lesson.title} ${done ? '다시 해보기' : '시작'}`);
    card.append(header, element('h3', lesson.title), element('p', lesson.description), button);
    return card;
  });
  select('#lesson-grid').replaceChildren(...cards);
}

stageButtons.forEach((button) => button.addEventListener('click', () => {
  stage = button.dataset.stage;
  stageButtons.forEach((item) => item.setAttribute('aria-pressed', String(item === button)));
  renderLessons();
}));

function updateAccount(data) {
  account = data.account;
  completed = data.completed;
  accountButtons.forEach((button) => {
    button.textContent = account ? '내 학습 기록' : '보호자 로그인';
  });
  renderLessons();
}

function renderAccount() {
  select('#account-title').textContent = account ? '내 학습 기록' : registering ? '보호자 계정 만들기' : '보호자 로그인';
  accountForm.hidden = Boolean(account);
  select('#account-panel').hidden = !account;
  if (account) {
    select('#account-email').textContent = account.email;
    select('#progress-list').replaceChildren(...(completed.length
      ? lessons.filter((lesson) => completed.includes(lesson.id)).map((lesson) => element('li', lesson.title))
      : [element('li', '아직 완료한 활동이 없어요.')]));
    return;
  }
  select('#account-intro').textContent = registering ? '보호자 이메일로 계정을 만들고 학습 기록을 저장하세요.' : '로그인하고 아이의 학습 기록을 확인하세요.';
  select('#password').autocomplete = registering ? 'new-password' : 'current-password';
  select('#password').minLength = registering ? 10 : 1;
  select('#password-help').hidden = !registering;
  select('#account-submit').textContent = registering ? '계정 만들기' : '로그인';
  select('#switch-mode').textContent = registering ? '로그인으로 돌아가기' : '보호자 계정 만들기';
}

function openAccount() {
  registering = false;
  accountForm.reset();
  accountError.textContent = '';
  renderAccount();
  accountDialog.showModal();
  select(account ? '#logout' : '#email').focus();
}

accountButtons.forEach((button) => button.addEventListener('click', openAccount));
select('#switch-mode').addEventListener('click', () => {
  if (accountLoading) return;
  registering = !registering;
  select('#password').value = '';
  accountError.textContent = '';
  renderAccount();
  select('#email').focus();
});

async function accountAction(button, run) {
  if (accountLoading) return;
  accountLoading = true;
  button.disabled = true;
  accountError.textContent = '';
  try {
    await run();
  } catch (error) {
    accountError.textContent = error.message;
  } finally {
    accountLoading = false;
    button.disabled = false;
  }
}

accountForm.addEventListener('submit', (event) => {
  event.preventDefault();
  accountAction(select('#account-submit'), async () => {
    const credentials = Object.fromEntries(new FormData(accountForm));
    updateAccount(await api(registering ? 'register' : 'login', credentials));
    accountForm.reset();
    renderAccount();
    if (accountDialog.open) select('#logout').focus();
  });
});

select('#logout').addEventListener('click', () => accountAction(select('#logout'), async () => {
  await api('logout', {});
  updateAccount({ account: null, completed: [] });
  registering = false;
  accountDialog.close();
}));

function startLesson(lesson) {
  const answers = [];
  const title = select('#lesson-title');
  const step = select('#lesson-step');
  title.textContent = lesson.title;

  function renderQuestion() {
    const question = lesson.questions[answers.length];
    step.textContent = `${lesson.subject} · ${answers.length + 1} / ${lesson.questions.length}`;
    const prompt = element('p', question.prompt, 'question');
    prompt.tabIndex = -1;
    const choices = element('div', '', 'answers');
    const feedback = element('p', '', 'feedback');
    feedback.setAttribute('role', 'status');
    let correct = false;
    const next = action('다음으로 →', 'primary', () => {
      answers.push(question.answer);
      if (answers.length < lesson.questions.length) renderQuestion();
      else renderResult();
    });
    next.hidden = true;
    if (answers.length + 1 === lesson.questions.length) next.textContent = '활동 마치기 →';

    question.options.forEach((option, index) => {
      const button = action(option, 'outline', () => {
        if (correct) return;
        correct = index === question.answer;
        feedback.textContent = correct ? '정답이에요!' : '한 번 더 생각해 볼까요?';
        if (correct) {
          button.setAttribute('aria-pressed', 'true');
          next.hidden = false;
        }
      });
      choices.append(button);
    });
    lessonContent.replaceChildren(prompt, choices, feedback, next);
    if (lessonDialog.open) prompt.focus();
  }

  function renderResult() {
    step.textContent = '활동 완료';
    const result = element('p', '모든 문제를 풀었어요! 🎉', 'question');
    result.tabIndex = -1;
    const message = element('p', account ? '학습 기록에 이 활동을 저장할 수 있어요.' : '다음에 학습 기록을 남기려면 보호자 계정으로 로그인해 주세요.', 'feedback');
    message.setAttribute('role', 'status');
    const actions = element('div', '', 'result-actions');
    const close = action('활동 목록으로', 'outline', () => lessonDialog.close());
    if (account) {
      const save = action('기록 저장', 'primary', async () => {
        save.disabled = true;
        try {
          updateAccount(await api('progress', { lessonId: lesson.id, answers }));
          message.textContent = '학습 기록을 저장했어요.';
          if (lessonDialog.open) close.focus();
          save.remove();
        } catch (error) {
          message.textContent = error.message;
          save.disabled = false;
        }
      });
      actions.append(save);
    }
    actions.append(close);
    lessonContent.replaceChildren(result, message, actions);
    result.focus();
  }

  renderQuestion();
  lessonDialog.showModal();
  lessonDialog.addEventListener('close', () => {
    select(`[data-lesson="${lesson.id}"]`)?.focus();
  }, { once: true });
  lessonContent.querySelector('.question').focus();
}

renderLessons();
api('session').then(updateAccount).catch(() => {
  // Public activities remain available during a database outage.
});
