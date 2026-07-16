function formatDate() {
  const date = new Date();
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");

  return `${year}-${month}-${day}`;
}

function formatMonth() {
  const date = new Date();
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");

  return `${year}-${month}`;
}

function formatDate2() {
  return formatDate();
}

function cvtCurrency(amount) {
  return amount.toLocaleString("ko-KR");
}

function execDaumPostcode(zip = "idZip", addrs = "idAddr") {
  new daum.Postcode({
    oncomplete: function (data) {
      // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다.

      // 각 주소의 노출 규칙에 따라 주소를 조합한다.
      // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
      let addr = ""; // 주소 변수

      //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
      if (data.userSelectedType === "R") {
        // 사용자가 도로명 주소를 선택했을 경우
        addr = data.roadAddress;
      } else {
        // 사용자가 지번 주소를 선택했을 경우(J)
        addr = data.jibunAddress;
      }

      const zipInput = document.getElementById(zip);
      const addrInput = document.getElementById(addrs);

      if (zipInput) zipInput.value = data.zonecode;
      if (addrInput) {
        addrInput.value = addr;
        addrInput.focus();
      }
    },
  }).open();
}

function getServerUrl() {
  const host = window.location.host;

  if (host.includes("chaitalkkid")) return "https://chaitalkkid.co.kr/Server/";
  // if (host.includes("chaitalkkid")) return "https://chaitalkkid.co.kr/Server/";
  if (host.includes("eplat")) return "https://eplat.co.kr/Server/";

  return `${window.location.protocol}//${host}/Server/`;
}

function getServerDbType() {
  const searchParams = new URLSearchParams(window.location.search);
  const dbType = searchParams.get("db") || localStorage.getItem("serverDb") || "mariadb";

  return String(dbType).toLowerCase() === "sqlite" ? "sqlite" : "mariadb";
}

function setServerDbType(dbType) {
  const nextDbType = String(dbType).toLowerCase() === "sqlite" ? "sqlite" : "mariadb";
  localStorage.setItem("serverDb", nextDbType);
}

function resolveServerMethod(fucName) {
  if (getServerDbType() !== "sqlite") return fucName;

  return fucName.replace(/(^|\/)SMethods\.php(\?|$)/, "$1SMethodsSQLite.php$2");
}

function logServerDbConnection(fucName, resolvedName) {
  if (window.__serverDbLogged) return;

  window.__serverDbLogged = true;
  console.log(`[DB] connected: ${getServerDbType()} (${resolvedName || fucName})`);
}

function appendFormParam(params, key, value) {
  if (value === undefined) return;

  const isFile = typeof File !== "undefined" && value instanceof File;
  if (value !== null && typeof value === "object" && !isFile) {
    Object.keys(value).forEach((childKey) => {
      appendFormParam(params, `${key}[${childKey}]`, value[childKey]);
    });
    return;
  }

  params.append(key, value === null ? "" : value);
}

function toRequestBody(data) {
  if (data instanceof FormData) return data;

  const params = new URLSearchParams();
  Object.keys(data || {}).forEach((key) => appendFormParam(params, key, data[key]));
  return params;
}

function setProgress(progressBar, percent) {
  if (!progressBar) return;

  const element = progressBar instanceof Element ? progressBar : progressBar[0];
  if (!element) return;

  element.style.width = `${percent}%`;
  element.textContent = `${percent}%`;
}

async function createFetchError(response, responseText) {
  let responseJSON = null;

  try {
    responseJSON = responseText ? JSON.parse(responseText) : null;
  } catch (e) {
    responseJSON = null;
  }

  return {
    status: response.status,
    statusText: response.statusText,
    responseText,
    responseJSON,
  };
}

async function requestJson(fucName, fntype = "POST", options, retFn, errFn, config = {}) {
  const method = String(fntype).toUpperCase();
  const headers = {};
  const fetchOptions = { method, headers };
  const body = toRequestBody(options);
  const controller = config.timeout ? new AbortController() : null;
  let timeoutId = null;
  const resolvedName = resolveServerMethod(fucName);
  let url = getServerUrl() + resolvedName;

  logServerDbConnection(fucName, resolvedName);

  if (controller) {
    fetchOptions.signal = controller.signal;
    timeoutId = setTimeout(() => controller.abort(), config.timeout);
  }

  if (method === "GET") {
    const query = body.toString();
    if (query) url += (url.includes("?") ? "&" : "?") + query;
  } else {
    fetchOptions.body = body;
    if (!(body instanceof FormData)) {
      headers["Content-Type"] = "application/x-www-form-urlencoded;charset=UTF-8";
    }
  }

  try {
    if (config.progressBar) {
      setProgress(config.progressBar, 0);
    }

    const response = await fetch(url, fetchOptions);
    const responseText = await response.text();

    if (!response.ok) {
      throw await createFetchError(response, responseText);
    }

    if (config.progressBar) {
      setProgress(config.progressBar, 100);
    }

    if (typeof retFn === "function") {
      retFn(responseText ? JSON.parse(responseText) : null);
    }
  } catch (error) {
    if (typeof errFn === "function") {
      errFn(error);
    }
  } finally {
    if (timeoutId) clearTimeout(timeoutId);
  }
}

CallAjax = (fucName, fntype = "POST", options, retFn, errFn) => {
  return requestJson(fucName, fntype, options, retFn, errFn);
};

CallAjax1 = (fucName, fntype = "POST", options, retFn, errFn) => {
  return requestJson(fucName, fntype, options, retFn, errFn);
};

CallAjax10 = (fucName, fntype = "POST", options, retFn, errFn, progressBar) => {
  return requestJson(fucName, fntype, options, retFn, errFn, {
    progressBar,
    timeout: 300000,
  });
};

CallAjax2 = (fucName, fntype = "POST", options, retFn, errFn) => {
  return requestJson(fucName, fntype, options, retFn, errFn);
};

CallToast = (message, stat) => {
  let toastContainer = document.getElementById("toast-container");

  if (!toastContainer) {
    toastContainer = document.createElement("div");
    toastContainer.id = "toast-container";
    toastContainer.style.position = "fixed";
    toastContainer.style.left = "16px";
    toastContainer.style.bottom = "16px";
    toastContainer.style.zIndex = "1080";
    toastContainer.style.display = "grid";
    toastContainer.style.gap = "8px";
    document.body.appendChild(toastContainer);
  }

  const toast = document.createElement("div");
  const isError = stat == "error";
  toast.textContent = message;
  toast.style.minWidth = "240px";
  toast.style.maxWidth = "360px";
  toast.style.padding = "12px 14px";
  toast.style.borderRadius = "6px";
  toast.style.boxShadow = "0 8px 24px rgba(0, 0, 0, 0.16)";
  toast.style.color = "#fff";
  toast.style.fontSize = "0.95rem";
  toast.style.backgroundColor = isError ? "#dc3545" : "#198754";
  toast.style.opacity = "0";
  toast.style.transition = "opacity 0.2s ease";

  toastContainer.appendChild(toast);
  requestAnimationFrame(() => {
    toast.style.opacity = "1";
  });

  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 200);
  }, 2000);
};

saveLocalStorage = (name, jsstr) => {
  localStorage.setItem(name, JSON.stringify(jsstr));
};

getLocalStorage = (name) => {
  return JSON.parse(localStorage.getItem(name));
};

deleteLocalStorage = (name) => {
  localStorage.removeItem(name);
};

getStoredUserField = (field) => {
  const sto = getLocalStorage("infochaitalk");
  return sto != undefined ? sto[field] : undefined;
};

getUser = () => {
  return getStoredUserField("user");
};

getName = () => {
  return getStoredUserField("name");
};

getOwner = () => {
  return getStoredUserField("owner");
};

getRole = () => {
  return getStoredUserField("role");
};

getStep = () => {
  return getStoredUserField("step");
};

getClass = () => {
  return getStoredUserField("clas");
};

cardWidgetManage = (widget, button) => {
  const widgetElement = widget instanceof Element ? widget : widget[0];
  const buttonElement = button instanceof Element ? button : button[0];
  if (!widgetElement || !buttonElement) return;

  widgetElement.addEventListener("collapsed.lte.cardwidget", function () {
    buttonElement.textContent = "펴기";
  });

  widgetElement.addEventListener("expanded.lte.cardwidget", function () {
    buttonElement.textContent = "접기";
  });
};

function generatePassword(length) {
  const charset =
    "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-+=";
  let password = "";

  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * charset.length);
    password += charset[randomIndex];
  }

  return password;
}

function getRandomColor() {
  // Generate random values for red, green, and blue
  const red = Math.floor(Math.random() * 256);
  const green = Math.floor(Math.random() * 256);
  const blue = Math.floor(Math.random() * 256);

  // Create the RGB color string
  const color = `rgba(${red},${green},${blue},1)`;

  return color;
}
function checkUserlogin() {
  if (getUser() == "" || getUser() == undefined) {
    window.location.href = "../index.php";
  }
}
const qi = (id) => document.getElementById(id);
const qs = (selector, root = document) => root.querySelector(selector);
const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));

window.addEventListener("beforeunload", function (e) {
  //deleteLocalStorage('infochaitalk')
});
