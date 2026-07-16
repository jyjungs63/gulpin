/**
 * ========================================
 * EPLAT Ebook - 오디오 플레이어 (Duration Bar 초기화 수정)
 * ========================================
 */

document.addEventListener("DOMContentLoaded", () => {
  audio = document.getElementById("audio");
  const playBtn = document.getElementById("playBtn");
  const volumeBtn = document.getElementById("volumeBtn");
  const progressContainer = document.getElementById("progressContainer");
  const progressBar = document.getElementById("progressBar");
  const progressHandle = document.getElementById("progressHandle");
  const currentTimeEl = document.getElementById("currentTime");
  const durationEl = document.getElementById("duration");

  let isDragging = false;
  let isLoading = false;

  // ========================================
  // 오디오 프리로드 설정
  // ========================================
  audio.preload = "metadata";

  // ========================================
  // 오디오 초기화 함수 (중요!)
  // ========================================
  function resetAudioUI() {
    playBtn.textContent = "▶";
    progressBar.style.width = "0%";
    progressHandle.style.left = "0%";
    currentTimeEl.textContent = "0:00";
    durationEl.textContent = "0:00";
    hideLoadingIndicator();
  }

  // ========================================
  // 오디오 소스 변경 시 초기화 (전역 함수)
  // ========================================
  window.resetAudioPlayer = function() {
    // 오디오 일시정지
    if (!audio.paused) {
      audio.pause();
    }
    
    // 현재 시간 리셋
    audio.currentTime = 0;
    
    // UI 리셋
    resetAudioUI();
  };

  // ========================================
  // 재생/일시정지 기능
  // ========================================
  playBtn.addEventListener("click", () => {
    if (isLoading) {
      showLoadingIndicator();
      return;
    }

    if (audio.paused) {
      // 오디오가 로드되지 않았다면 먼저 로드
      if (audio.readyState < 2) {
        showLoadingIndicator();
        audio.load();
        audio.addEventListener('canplay', () => {
          hideLoadingIndicator();
          audio.play();
          playBtn.textContent = "⏸";
        }, { once: true });
      } else {
        audio.play();
        playBtn.textContent = "⏸";
      }
    } else {
      audio.pause();
      playBtn.textContent = "▶";
    }
  });

  // ========================================
  // 볼륨 음소거/해제
  // ========================================
  volumeBtn.addEventListener("click", () => {
    if (audio.muted) {
      audio.muted = false;
      volumeBtn.textContent = "🔊";
    } else {
      audio.muted = true;
      volumeBtn.textContent = "🔇";
    }
  });

  // ========================================
  // 오디오 이벤트 리스너
  // ========================================
  
  // 시간 업데이트
  audio.addEventListener("timeupdate", updateProgress);

  // 메타데이터 로드 완료
  audio.addEventListener("loadedmetadata", () => {
    if (audio.duration && !isNaN(audio.duration) && isFinite(audio.duration)) {
      durationEl.textContent = formatTime(audio.duration);
    } else {
      durationEl.textContent = "0:00";
    }
  });

  // 로딩 시작
  audio.addEventListener("loadstart", () => {
    isLoading = true;
    showLoadingIndicator();
    // UI 초기화
    progressBar.style.width = "0%";
    progressHandle.style.left = "0%";
    currentTimeEl.textContent = "0:00";
    durationEl.textContent = "0:00";
  });

  // 데이터 로드 중
  audio.addEventListener("progress", () => {
    if (audio.buffered.length > 0) {
      const buffered = audio.buffered.end(0);
      const duration = audio.duration;
      if (duration && !isNaN(duration)) {
        const bufferedPercent = (buffered / duration) * 100;
        // console.log(`버퍼링: ${bufferedPercent.toFixed(1)}%`);
      }
    }
  });

  // 재생 가능 상태
  audio.addEventListener("canplay", () => {
    isLoading = false;
    hideLoadingIndicator();
    
    // duration 업데이트
    if (audio.duration && !isNaN(audio.duration) && isFinite(audio.duration)) {
      durationEl.textContent = formatTime(audio.duration);
    }
  });

  // 로딩 중 대기
  audio.addEventListener("waiting", () => {
    showLoadingIndicator();
  });

  // 재생 가능 상태로 복귀
  audio.addEventListener("playing", () => {
    hideLoadingIndicator();
  });

  // 재생 완료
  audio.addEventListener("ended", () => {
    playBtn.textContent = "▶";
    progressBar.style.width = "0%";
    progressHandle.style.left = "0%";
    currentTimeEl.textContent = "0:00";
    audio.currentTime = 0;
  });

  // 오류 처리
  audio.addEventListener("error", (e) => {
    hideLoadingIndicator();
    resetAudioUI();
    console.error("오디오 로드 오류:", e);
  });

  // 오디오 소스 변경 감지
  audio.addEventListener("emptied", () => {
    // 소스가 변경되거나 비워졌을 때
    resetAudioUI();
  });

  // ========================================
  // 진행 바 이벤트
  // ========================================
  
  // 진행 바 클릭
  progressContainer.addEventListener("click", setProgress);

  // 드래그 시작 (마우스)
  progressHandle.addEventListener("mousedown", startDrag);

  // 드래그 시작 (터치)
  progressHandle.addEventListener("touchstart", startDrag, { passive: false });

  // ========================================
  // 드래그 함수들
  // ========================================
  
  function startDrag(e) {
    isDragging = true;
    e.preventDefault();

    if (e.type === "touchstart") {
      document.addEventListener("touchmove", drag, { passive: false });
      document.addEventListener("touchend", stopDrag);
    } else {
      document.addEventListener("mousemove", drag);
      document.addEventListener("mouseup", stopDrag);
    }
  }

  function drag(e) {
    if (!isDragging) return;

    const rect = progressContainer.getBoundingClientRect();
    let clientX;

    if (e.type === "touchmove") {
      clientX = e.touches[0].clientX;
    } else {
      clientX = e.clientX;
    }

    const pos = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
    setProgressPosition(pos);
  }

  function stopDrag() {
    if (!isDragging) return;

    isDragging = false;
    document.removeEventListener("mousemove", drag);
    document.removeEventListener("mouseup", stopDrag);
    document.removeEventListener("touchmove", drag);
    document.removeEventListener("touchend", stopDrag);

    const width = progressBar.style.width;
    const percent = parseFloat(width) / 100;
    
    if (audio.duration && !isNaN(audio.duration)) {
      audio.currentTime = percent * audio.duration;
    }
  }

  // ========================================
  // 유틸리티 함수
  // ========================================
  
  // 시간 포맷팅
  function formatTime(seconds) {
    if (!seconds || isNaN(seconds) || !isFinite(seconds)) {
      return "0:00";
    }
    
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${minutes}:${secs < 10 ? "0" + secs : secs}`;
  }

  // 진행 상태 업데이트
  function updateProgress() {
    if (isDragging) return;

    const { currentTime, duration } = audio;
    
    if (!duration || isNaN(duration) || !isFinite(duration)) return;

    const progressPercent = (currentTime / duration) * 100;
    progressBar.style.width = `${progressPercent}%`;
    progressHandle.style.left = `${progressPercent}%`;
    currentTimeEl.textContent = formatTime(currentTime);
  }

  // 진행 바 클릭 처리
  function setProgress(e) {
    if (isDragging) return;
    if (!audio.duration || isNaN(audio.duration)) return;

    const rect = progressContainer.getBoundingClientRect();
    let clientX;

    if (e.type === "touchstart" || e.type === "touchend") {
      clientX = e.changedTouches[0].clientX;
    } else {
      clientX = e.clientX;
    }

    const pos = (clientX - rect.left) / rect.width;
    setProgressPosition(pos);
    
    audio.currentTime = pos * audio.duration;
  }

  // 진행 위치 설정
  function setProgressPosition(pos) {
    const percent = Math.max(0, Math.min(1, pos)) * 100;
    progressBar.style.width = `${percent}%`;
    progressHandle.style.left = `${percent}%`;
  }

  // 로딩 인디케이터 표시
  function showLoadingIndicator() {
    playBtn.style.opacity = "0.5";
    playBtn.style.cursor = "wait";
  }

  // 로딩 인디케이터 숨기기
  function hideLoadingIndicator() {
    playBtn.style.opacity = "1";
    playBtn.style.cursor = "pointer";
  }

  // ========================================
  // 초기화
  // ========================================
  resetAudioUI();
  
  if (audio.readyState >= 1 && audio.duration && isFinite(audio.duration)) {
    durationEl.textContent = formatTime(audio.duration);
  }
});

// ========================================
// 전역 오디오 프리로드 함수
// ========================================
window.preloadAudio = function(audioSrc) {
  const tempAudio = new Audio();
  tempAudio.preload = "auto";
  tempAudio.src = audioSrc;
  tempAudio.load();
  
  return tempAudio;
};
