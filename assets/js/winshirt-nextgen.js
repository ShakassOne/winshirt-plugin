(function(){
  function initPanels() {
    document.querySelectorAll('.ws-toolbar-btn, .ws-tab-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const tab = btn.dataset.tab;
        document.querySelectorAll('.ws-panel').forEach(panel => {
          if (panel.dataset.panel === tab) {
            panel.classList.add('active');
          } else {
            panel.classList.remove('active');
          }
        });
        document.querySelectorAll('.ws-toolbar-btn, .ws-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      });
    });

    document.addEventListener('click', function (e) {
      document.querySelectorAll('.ws-panel.active').forEach(panel => {
        if (!panel.contains(e.target) && !e.target.classList.contains('ws-toolbar-btn')) {
          panel.classList.remove('active');
          document.querySelectorAll('.ws-toolbar-btn, .ws-tab-btn').forEach(b => b.classList.remove('active'));
        }
      });
    });
  }

  function injectHandles(printArea) {
    const handleNames = ['close', 'rotate', 'resize', 'scale'];
    const handleIcons = ['✖', '⟳', '⤢', '↔'];
    handleNames.forEach((name, idx) => {
      const h = document.createElement('button');
      h.className = 'ws-handle ws-handle-' + name;
      h.type = 'button';
      h.setAttribute('aria-label', name);
      h.textContent = handleIcons[idx];
      printArea.appendChild(h);
      // TODO: add manipulation events
    });
  }

  function initHelp() {
    if (!localStorage.getItem('ws-help-shown')) {
      alert('Pincez pour zoomer, glissez pour déplacer, tapotez pour éditer !');
      localStorage.setItem('ws-help-shown', '1');
    }
  }

  function initFaceSwitcher() {
    const img = document.querySelector('.ws-mockup-img');
    const faceBtns = document.querySelectorAll('.ws-face-btn');
    if (!img || !faceBtns.length) return;
    faceBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        faceBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const isBack = btn.textContent.trim().toLowerCase() === 'verso';
        img.src = isBack ? img.dataset.back : img.dataset.front;
      });
    });
  }

  function init() {
    const printArea = document.querySelector('.ws-print-area');
    if (printArea) {
      injectHandles(printArea);
    }
    initPanels();
    initFaceSwitcher();
    initHelp();
  }

  document.addEventListener('DOMContentLoaded', init);
})();
