
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>WARTEN GARTEN</title>
<!-- fontawesome icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
@font-face {
  font-family: myFont;
  src: url(fonts/WartenGartenKern03-Regular.woff2);
}

html, body {
  margin: 0; padding: 0;
  width: 100%; height: 100%;
  font-family: myFont, Arial, sans-serif;
  overflow: hidden;
  background: #95127C;
}

/* Tour iframe */
#tourFrame {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  border: none;
  opacity: 0;
  transition: opacity 0.8s ease;
  background-color: black;
  z-index: 1;
}

/* Overlay intro */
#overlayTitle {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  text-align: center;
  cursor: pointer;
  line-height: 0.8;
  font-size: 48px;
  color: #C0CE28;
  opacity: 0;
  transform: scale(0.9);
  transition: opacity 1s ease, transform 1s ease;
  z-index: 1000;
}
#overlayTitle.show { opacity: 1; transform: scale(1); }
#overlayTitle.hide { opacity: 0; pointer-events: none; }

/* Bottom bar */
.bottom-bar {
  position: absolute;
  bottom: 0; left: 0;
  width: 100%;
  display: flex;
  justify-content: space-evenly;
  padding: 10px 20px;
  background: rgba(0,0,0,0);
  z-index: 10;
  flex-wrap: wrap;
  transition: opacity 0.4s ease;
}
.bottom-bar.hidden { opacity: 0; pointer-events: none; }

.tab-container { position: relative; }
.tab-button {
  background: #C0CE28;
  color: #95127C;
  border: none;
  border-radius: 15px;
  padding: 5px 10px;
  cursor: pointer;
  font-size: 26px;
  transition: background 0.2s ease;
}
.tab-button:hover,
.tab-button[aria-expanded="true"] {
  background: #95127C;
  color: #C0CE28;
}
.panel {
  position: absolute;
  bottom: 50px;
  left: 0;
  transform-origin: bottom left;
  transform: scale(0.95) translateY(10px);
  opacity: 0;
  width: 80vw;
  max-width: 700px;
  max-height: 70vh;
  background: #95127C;
  color: #C0CE28;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.panel.show { transform: scale(1) translateY(0); opacity: 1; }
.panel.right-align { left: auto; right: 0; transform-origin: bottom right; }
.panel-content { padding: 15px; overflow-y: auto; flex-grow: 1; background: rgba(255,255,255,0.05); }
.close-panel { cursor: pointer; font-size: 40px; float: right; }

/* Hotspot */
#audio-container {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  z-index: 500;
  pointer-events: none;
}
.hotspot {
  pointer-events: auto;
  position: absolute;
  top: 14%;
  left: 10%;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #C0CE28;
  border: 2px solid white;
  cursor: pointer;
  transform: translate(-50%, -50%);
  transition: transform 0.2s ease, background 0.3s;
  z-index: 501;
}
.hotspot:hover { transform: translate(-50%, -50%) scale(1.1); background: #95127C; }
.hotspot.active { background: #95127C; }

/* Pulse animation when audio is playing */
@keyframes hotspotPulse {
  0% { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 0 0 rgba(192,206,40,0.6); }
  70% { transform: translate(-50%, -50%) scale(1.1); box-shadow: 0 0 20px 10px rgba(192,206,40,0); }
  100% { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 0 0 rgba(192,206,40,0); }
}

.hotspot.pulsing {
  animation: hotspotPulse 2s infinite ease-in-out;
}

/* Audio UI */
#audio-ui {
  position: fixed;
  left: 0; right: 0;
  bottom: -240px;
  display: flex;
  gap: 16px;
  padding: 16px;
  z-index: 1000;
  transition: bottom 0.45s ease, opacity 0.35s ease;
  opacity: 0;
  pointer-events: none;
}
#audio-ui.active {
  bottom: 0;
  opacity: 1;
  pointer-events: auto;
}

.audio-left, .audio-right {
  background: #95127C;
  color: #C0CE28;
  border-radius: 14px;
  box-shadow: 0 6px 16px rgba(0,0,0,0.35);
  padding: 14px 16px;
}

/* Left panel */
.audio-left  { flex: 0 0 40%; position: relative; display: flex; flex-direction: column; gap: 10px; }
.audio-title-row { display: flex; align-items: center; gap: 12px; }
.audio-title { font-size: 22px; }
.audio-download {
  color: #95127C;
  background: #C0CE28;
  text-decoration: none;
  padding: 6px 10px;
  border-radius: 10px;
  font-weight: 700;
}
.audio-meta { opacity: 0.9; font-size: 14px; }
.audio-close {
  position: absolute; top: 10px; right: 12px;
  background: transparent; border: none; color: #C0CE28;
  font-size: 28px; cursor: pointer;
}

/* Right panel */
.audio-right { flex: 1 1 60%; display: flex; flex-direction: column; gap: 10px; }
.player-row { display: flex; align-items: center; gap: 8px; }
.icon-btn {
  background: transparent;
  color: #C0CE28;
  border: none;
  font-size: 22px;
  cursor: pointer;
  line-height: 1;
  padding: 6px 10px;
}
.icon-btn:hover { color: #fff; }

.time { margin-left: auto; font-variant-numeric: tabular-nums; }


/* --- Unified Styling for Seek & Volume Sliders --- */
:root {
  --accent: #C0CE28;
  --accent-bright: #E8F55A; /* slightly brighter green for progress */
  --track: rgba(255,255,255,0.25);
}

/* Base track */
#seek, #vol {
  -webkit-appearance: none;
  appearance: none;
  width: 100%;
  height: 8px; /* Thicker track for stronger contrast */
  border-radius: 10px;
  outline: none;
  background: linear-gradient(to right,
    var(--accent-bright) 0%,
    var(--accent-bright) 0%,
    var(--track) 0%,
    var(--track) 100%);
  transition: height 0.2s ease;
}

/* --- Tooltip above seek thumb --- */
#seek-tooltip {
  position: absolute;
  bottom: 110px; /* just above the player bar */
  left: 0;
  transform: translateX(-50%);
  padding: 4px 8px;
  background: rgba(149,18,124,0.9);
  color: #C0CE28;
  font-size: 14px;
  border-radius: 6px;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.15s ease, transform 0.15s ease;
  z-index: 1200;
}
#seek:hover + #seek-tooltip {
  opacity: 1;
  transform: translateX(-50%) translateY(-3px);
}


#vol {
  max-width: 160px;
  height: 6px; /* slightly thinner volume bar */
  background: linear-gradient(to right,
    var(--accent) 0%,
    var(--accent) 0%,
    var(--track) 0%,
    var(--track) 100%);
}

/* --- Seek Thumb: rectangular, flat design --- */
#seek::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 12px;
  border-radius: 3px;
  background: var(--accent);
  border: none;
  cursor: pointer;
  margin-top: -2px;
  box-shadow: 0 0 6px rgba(192,206,40,0.4);
  transition: transform 0.15s ease, background 0.2s ease, box-shadow 0.2s ease;
}
#seek::-webkit-slider-thumb:hover {
  transform: scale(1.2);
  background: var(--accent-bright);
  box-shadow: 0 0 10px rgba(192,206,40,0.6);
}
#seek::-moz-range-thumb {
  width: 20px;
  height: 12px;
  border-radius: 3px;
  background: var(--accent);
  border: none;
  cursor: pointer;
  transition: transform 0.15s ease, background 0.2s ease, box-shadow 0.2s ease;
}
#seek::-moz-range-thumb:hover {
  transform: scale(1.2);
  background: var(--accent-bright);
  box-shadow: 0 0 10px rgba(192,206,40,0.6);
}

/* --- Volume Thumb: smaller, circular --- */
#vol::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--accent);
  border: 2px solid #fff;
  cursor: pointer;
  margin-top: -4px;
  transition: transform 0.15s ease;
}
#vol::-webkit-slider-thumb:hover {
  transform: scale(1.2);
}
#vol::-moz-range-thumb {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--accent);
  border: 2px solid #fff;
  cursor: pointer;
  transition: transform 0.15s ease;
}
#vol::-moz-range-thumb:hover {
  transform: scale(1.2);
}



@media (max-width: 900px) {
  #audio-ui { flex-direction: column; }
  .audio-left, .audio-right { flex: 1 1 auto; }
}
</style>
</head>

<body>

<!-- Overlay -->
<div id="overlayTitle">
  <span style="font-size:115px;">EXPLORE</span>
  <span style="font-size:160px;">WARTEN</span>
  <span style="font-size:200px;">GARTEN</span>
</div>

<!-- Tour -->
<iframe id="tourFrame" src="vtour/index.htm" allowfullscreen></iframe>

<!-- Hotspot -->
<div id="audio-container">
  <div class="hotspot" id="hs1"></div>
</div>

<!-- Bottom Bar -->
<div class="bottom-bar">
  <div class="tab-container">
    <button class="tab-button">HERBARIUM</button>
    <div class="panel"><div class="panel-content"><span class="close-panel">&times;</span><p>Herbarium details here.</p></div></div>
  </div>

  <div class="tab-container">
    <button class="tab-button">ABOUT</button>
    <div class="panel"><div class="panel-content"><span class="close-panel">&times;</span><p>About section content.</p></div></div>
  </div>

  <div class="tab-container">
    <button class="tab-button">IMPRINT</button>
    <div class="panel"><div class="panel-content"><span class="close-panel">&times;</span><p>Imprint text here.</p></div></div>
  </div>

  <div class="tab-container">
    <button class="tab-button">EXERCISES</button>
    <div class="panel" role="region" aria-label="Exercises">
      <div class="panel-content">

        <div style="display:left; justify-content:space-between; align-items:center; margin-bottom:10px;">
          <!-- download -->
          <a href="media/sample.pdf" download class="download-btn" aria-label="Download PDF"
             style="margin-right:25px; background:#C0CE28; color:#95127C; padding:8px 14px; border-radius:12px; text-decoration:none; font-weight:bold;">
             <i class="fa-solid fa-arrow-down"></i>
          </a>
          <!-- fullscreen -->
          <a class="download-btn" aria-label="View fullscreen"
             style="background:#C0CE28; color:#95127C; padding:8px 14px; border-radius:12px; text-decoration:none; font-weight:bold;"
             onclick="document.querySelector('.pdf-viewer').requestFullscreen()">
            <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
          </a>
                
          <span class="close-panel" style="font-size:36px; cursor:pointer;">&times;</span>
        </div>

        <!-- pdf viewer -->
        <iframe class="pdf-viewer"
                style="width:100%; height:80vh; border:none; border-radius:10px;"
                src="media/sample.pdf#toolbar=0&navpanes=0&zoom=FitH" allowfullscreen></iframe>
      </div>
    </div>
  </div>






</div>

<!-- Audio UI -->
<div id="audio-ui">
  <div class="audio-left">
    <button class="audio-close">✕</button>
    <div class="audio-title-row">
      <div class="audio-title" id="audio-title">Now playing</div>
      
        <!-- download -->
          <a id="btn-download" class="audio-download" download aria-label="Download audio"
             style="background:#C0CE28; color:#95127C; padding:8px 14px; border-radius:12px; text-decoration:none; font-weight:bold;">
             <i class="fa-solid fa-arrow-down"></i>
          </a>

    </div>
    <div class="audio-meta" id="audio-meta">—</div>
  </div>

  <div class="audio-right">
    <div class="player-row">
      <button id="btn-back" class="icon-btn">⏮</button>
      <button id="btn-play" class="icon-btn">▶</button>
      <button id="btn-pause" class="icon-btn" hidden>⏸</button>
      <button id="btn-forward" class="icon-btn">⏭</button>
      <span class="time"><span id="time-elapsed">0:00</span> / <span id="time-total">0:00</span></span>
    </div>
    <input id="seek" type="range" min="0" max="100" value="0" step="0.1">
    <div id="seek-tooltip">0:00</div>
    <div class="player-row" style="display:none;">
      <label>Vol</label>
      <input id="vol" type="range" min="0" max="1" step="0.01" value="1">
    </div>
  </div>

  <audio id="custom-audio" preload="auto"></audio>
</div>

<script>
const overlay = document.getElementById("overlayTitle");
const iframe = document.getElementById("tourFrame");
const bottomBar = document.querySelector(".bottom-bar");
const audioUI = document.getElementById("audio-ui");
const audioEl = document.getElementById("custom-audio");
const hotspot = document.getElementById("hs1");

const titleEl = document.getElementById("audio-title");
const metaEl = document.getElementById("audio-meta");
const btnDownload = document.getElementById("btn-download");

const btnPlay = document.getElementById("btn-play");
const btnPause = document.getElementById("btn-pause");
const btnBack = document.getElementById("btn-back");
const btnForward = document.getElementById("btn-forward");
const btnClose = document.querySelector(".audio-close");
const seek = document.getElementById("seek");
const vol = document.getElementById("vol");
const timeElapsed = document.getElementById("time-elapsed");
const timeTotal = document.getElementById("time-total");

/* Hotspot data */
const hotspots = {
  hs1: {
    title: "Field Recording: Morning Garden",
    file: "media/test1.mp3",
    meta: "Warten Garten • 2025 • CC-BY"
  }
};

/* Helpers */
function fmtTime(s) {
  if (!isFinite(s)) return "0:00";
  const m = Math.floor(s / 60);
  const ss = Math.floor(s % 60).toString().padStart(2, "0");
  return `${m}:${ss}`;
}
function setSliderFill(el, percent) {
  const p = Math.min(100, Math.max(0, percent));
  el.style.background = `linear-gradient(to right, var(--accent) 0%, var(--accent) ${p}%, var(--track) ${p}%, var(--track) 100%)`;
}

/* Show/Hide Player */
function openAudioUI() {
  bottomBar.classList.add("hidden");
  audioUI.classList.add("active");
  hotspot.classList.add("active");
  

}
function closeAudioUI() {
  audioUI.classList.remove("active");
  bottomBar.classList.remove("hidden");
  hotspot.classList.remove("active");
  

}

/* Show player */
function showPlayer(id) {
  const hs = hotspots[id];
  if (!hs) return;
  titleEl.textContent = hs.title;
  metaEl.textContent = hs.meta;
  btnDownload.href = hs.file;
  audioEl.src = hs.file;
  openAudioUI();
  audioEl.play().then(() => {
    btnPlay.hidden = true;
    btnPause.hidden = false;
    hotspot.classList.add("pulsing");
  });
}
function hidePlayer() {
  audioEl.pause();
  btnPlay.hidden = false;
  btnPause.hidden = true;
  closeAudioUI();
  hotspot.classList.remove("pulsing");
}

/* Hotspot click */
hotspot.addEventListener("click", () => showPlayer("hs1"));

/* Controls */
btnClose.addEventListener("click", hidePlayer);
btnPlay.addEventListener("click", () => { audioEl.play(); btnPlay.hidden = true; btnPause.hidden = false; });
btnPause.addEventListener("click", () => { audioEl.pause(); btnPlay.hidden = false; btnPause.hidden = true; });

btnBack.addEventListener("click", () => {
  audioEl.currentTime = Math.max(0, audioEl.currentTime - 15);
  // Fix: reflect real playback state
  if (audioEl.paused) {
    btnPlay.hidden = false;
    btnPause.hidden = true;
  } else {
    btnPlay.hidden = true;
    btnPause.hidden = false;
  }
});
btnForward.addEventListener("click", () => {
  if (isFinite(audioEl.duration)) {
    audioEl.currentTime = Math.min(audioEl.duration, audioEl.currentTime + 15);
    if (audioEl.currentTime >= audioEl.duration) audioEl.dispatchEvent(new Event("ended"));
  }
});

audioEl.addEventListener("loadedmetadata", () => { timeTotal.textContent = fmtTime(audioEl.duration); });
audioEl.addEventListener("timeupdate", () => {
  timeElapsed.textContent = fmtTime(audioEl.currentTime);
  if (audioEl.duration > 0) {
    const pct = (audioEl.currentTime / audioEl.duration) * 100;
    seek.value = pct;
    setSliderFill(seek, pct);
  }
});

/* Seek bar fix */
seek.addEventListener("input", () => {
  if (isFinite(audioEl.duration) && audioEl.duration > 0) {
    const pct = parseFloat(seek.value);
    audioEl.currentTime = (pct / 100) * audioEl.duration;
    setSliderFill(seek, pct);
    if (audioEl.paused) {
      btnPlay.hidden = false;
      btnPause.hidden = true;
    } else {
      btnPlay.hidden = true;
      btnPause.hidden = false;
    }
  }

});

/* Tooltip position + time display */
const tooltip = document.getElementById("seek-tooltip");

seek.addEventListener("mousemove", e => {
  if (!isFinite(audioEl.duration)) return;

  const rect = seek.getBoundingClientRect();
  const percent = (e.clientX - rect.left) / rect.width;
  const clamped = Math.min(Math.max(percent, 0), 1);
  const time = clamped * audioEl.duration;
  tooltip.textContent = fmtTime(time);
  tooltip.style.left = `${e.clientX}px`;
});

seek.addEventListener("mouseenter", () => { tooltip.style.opacity = 1; });
seek.addEventListener("mouseleave", () => { tooltip.style.opacity = 0; });


/* Volume slider */
vol.addEventListener("input", () => {
  audioEl.volume = parseFloat(vol.value);
  setSliderFill(vol, vol.value * 100);
});
setSliderFill(vol, vol.value * 100);

/* End of track handling */
audioEl.addEventListener("ended", () => {
  btnPause.hidden = true;
  audioEl.currentTime = 0;
  timeElapsed.textContent = fmtTime(0);
  seek.value = 0;
  setSliderFill(seek, 0);
  btnPlay.hidden = false;
});

/* Overlay intro + iframe fade */
const PRELOAD_DELAY = 1000;
window.addEventListener("load", () => {
  setTimeout(() => {
    iframe.style.opacity = 1;
    overlay.classList.add("show");
  }, PRELOAD_DELAY);
});
overlay.addEventListener("click", () => overlay.classList.add("hide"));

/* Bottom-bar panel logic */
document.querySelectorAll(".tab-container").forEach(container => {
  const button = container.querySelector(".tab-button");
  const panel  = container.querySelector(".panel");
  const closeX = container.querySelector(".close-panel");

  button.addEventListener("click", () => {
    document.querySelectorAll(".panel").forEach(p => p.classList.remove("show"));
    document.querySelectorAll(".tab-button").forEach(tb => tb.setAttribute("aria-expanded", "false"));
    const rect = button.getBoundingClientRect();
    const midpoint = window.innerWidth / 2;
    if (rect.left > midpoint) panel.classList.add("right-align");
    else panel.classList.remove("right-align");
    button.setAttribute("aria-expanded", "true");
    panel.classList.add("show");
  });

  closeX.addEventListener("click", () => {
    button.setAttribute("aria-expanded", "false");
    panel.classList.remove("show");
    setTimeout(() => panel.classList.remove("right-align"), 300);
  });
});

</script>
</body>
</html>
