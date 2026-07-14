// GitHub View — heatmap dia × hora (canvas).
// Porte de app/javascript/controllers/heatmap_controller.js +
// app/javascript/lib/playback_controller.js (github-visualize, Stimulus) para
// ES vanilla, sem dependências. Auto-inicializa em [data-gh-heatmap]: lê o JSON
// embutido, desenha uma frame-fantasma e roda o "sweep" (as células acendem em
// ordem cronológica, o contador sobe junto) quando ~20% entra na viewport.
// Respeita prefers-reduced-motion (pula direto pro frame final).

// Base replayável (porte de PlaybackController).
class PlaybackChart {
  constructor(root, { duration = 3000 } = {}) {
    this.root = root;
    this.duration = duration;
    this.progress = 0;
  }

  // Chamado pela subclasse DEPOIS de preparar seus campos (canvas, dados).
  start() {
    this.onResize = () => this.render(this.progress);
    window.addEventListener("resize", this.onResize);
    this.render(0);
    this.playWhenVisible();
  }

  playWhenVisible() {
    this.observer = new IntersectionObserver(
      (entries) => {
        if (entries.some((entry) => entry.intersectionRatio >= 0.2)) {
          this.observer.disconnect();
          this.replay();
        }
      },
      { threshold: 0.2 }
    );
    this.observer.observe(this.root);
  }

  replay() {
    cancelAnimationFrame(this.frame);
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      this.progress = 1;
      this.render(1);
      return;
    }
    const start = performance.now();
    const tick = (now) => {
      this.progress = Math.min((now - start) / this.duration, 1);
      this.render(this.progress);
      if (this.progress < 1) this.frame = requestAnimationFrame(tick);
    };
    this.frame = requestAnimationFrame(tick);
  }

  // eslint-disable-next-line no-unused-vars
  render(progress) {}
}

// Rampa de calor roxo→amarelo (mesma do heatmap_controller.js).
const HEAT_STOPS = [
  [45, 27, 78], [126, 34, 206], [192, 38, 211],
  [236, 72, 153], [249, 115, 22], [250, 204, 21],
];
const FADE_SPAN = 30; // quantas células estão acendendo ao mesmo tempo no sweep.

class HeatmapChart extends PlaybackChart {
  constructor(root) {
    super(root);
    this.canvas = root.querySelector("[data-gh-heatmap-canvas]");
    this.counter = root.querySelector("[data-gh-heatmap-counter]");
    this.numberFormat = new Intl.NumberFormat();

    const dataEl = root.querySelector("[data-gh-heatmap-data]");
    try {
      this.data = JSON.parse(dataEl ? dataEl.textContent : "{}");
    } catch {
      this.data = { rows: [], max: 0, total: 0 };
    }

    const replayBtn = root.querySelector("[data-gh-heatmap-replay]");
    if (replayBtn) replayBtn.addEventListener("click", () => this.replay());

    if (this.canvas) this.start();
  }

  render(progress) {
    const { rows, max } = this.data;
    if (!rows || !rows.length || !this.canvas) return;

    const canvas = this.canvas;
    const dpr = window.devicePixelRatio || 1;
    const width = canvas.clientWidth;
    const labelWidth = 64;
    const headerHeight = 18;
    const gap = 3;
    const cellWidth = (width - labelWidth - gap * 23) / 24;
    const cellHeight = 13;
    const height = headerHeight + rows.length * (cellHeight + gap);

    canvas.width = width * dpr;
    canvas.height = height * dpr;
    canvas.style.height = `${height}px`;
    const ctx = canvas.getContext("2d");
    ctx.scale(dpr, dpr);
    ctx.clearRect(0, 0, width, height);

    ctx.fillStyle = "#525252";
    ctx.font = "10px ui-monospace, monospace";
    for (const hour of [0, 6, 12, 18]) {
      ctx.fillText(this.hourLabel(hour), labelWidth + hour * (cellWidth + gap), 10);
    }

    const totalCells = rows.length * 24;
    const sweep = progress * (totalCells + FADE_SPAN);
    let revealedCommits = 0;

    rows.forEach((row, rowIndex) => {
      const y = headerHeight + rowIndex * (cellHeight + gap);
      ctx.fillStyle = "#737373";
      ctx.fillText(row.label, 0, y + cellHeight - 3);

      row.counts.forEach((count, hour) => {
        const index = rowIndex * 24 + hour;
        const alpha = Math.min(Math.max((sweep - index) / FADE_SPAN, 0), 1);
        if (alpha <= 0) return;
        if (alpha >= 0.5) revealedCommits += count;

        ctx.globalAlpha = alpha;
        ctx.fillStyle = this.heatColor(count, max);
        ctx.beginPath();
        ctx.roundRect(labelWidth + hour * (cellWidth + gap), y, cellWidth, cellHeight, 2);
        ctx.fill();
        ctx.globalAlpha = 1;
      });
    });

    this.updateCounter(progress, revealedCommits);
  }

  updateCounter(progress, revealedCommits) {
    if (!this.counter) return;
    const total = this.data.total ?? 0;
    this.counter.textContent = this.numberFormat.format(progress >= 1 ? total : revealedCommits);
  }

  hourLabel(hour) {
    if (hour === 0) return "12am";
    if (hour === 12) return "12pm";
    return hour < 12 ? `${hour}am` : `${hour - 12}pm`;
  }

  heatColor(value, max) {
    if (value === 0 || max === 0) return "#17131f";
    const t = Math.sqrt(value / max) * (HEAT_STOPS.length - 1);
    const index = Math.min(Math.floor(t), HEAT_STOPS.length - 2);
    const fraction = t - index;
    const channel = (i) =>
      Math.round(HEAT_STOPS[index][i] + (HEAT_STOPS[index + 1][i] - HEAT_STOPS[index][i]) * fraction);
    return `rgb(${channel(0)}, ${channel(1)}, ${channel(2)})`;
  }
}

function initGitHubHeatmaps() {
  document.querySelectorAll("[data-gh-heatmap]").forEach((root) => {
    if (root.dataset.ghInit) return;
    root.dataset.ghInit = "1";
    new HeatmapChart(root);
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initGitHubHeatmaps);
} else {
  initGitHubHeatmaps();
}
