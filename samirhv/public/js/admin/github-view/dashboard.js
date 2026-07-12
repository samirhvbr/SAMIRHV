// GitHub View — dashboard: toggle log/linear das barras + expandir "mostrar
// todos os repos". Porte de bar_scale_controller.js + reveal_controller.js
// (Stimulus) para JS vanilla. Sem dependências.
(function () {
  function initBarScale(root) {
    var label = root.querySelector("[data-gh-barscale-label]");
    var bars = root.querySelectorAll("[data-gh-bar]");
    if (!label || !bars.length) return;
    var linear = false;
    label.addEventListener("click", function () {
      linear = !linear;
      bars.forEach(function (bar) {
        bar.style.width = linear ? bar.dataset.linearWidth : bar.dataset.logWidth;
      });
      label.textContent = linear ? "(linear scale)" : "(log scale)";
    });
  }

  function initReveal(root) {
    var btn = root.querySelector("[data-gh-reveal-toggle]");
    var content = root.querySelector("[data-gh-reveal-content]");
    if (!btn || !content) return;
    btn.addEventListener("click", function () {
      var hidden = content.classList.toggle("is-hidden");
      btn.textContent = hidden ? btn.dataset.more : btn.dataset.less;
    });
  }

  function init() {
    document.querySelectorAll("[data-gh-bars]").forEach(function (root) {
      initBarScale(root);
      initReveal(root);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
