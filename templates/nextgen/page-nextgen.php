<?php
/**
 * WinShirt Next-Gen customizer template
 */
?>

<!-- PAGE WINSHIRT DÉDIÉE PERSONNALISATION (Lumise-like) -->
<div class="ws-configurator">

  <!-- HEADER -->
  <header class="ws-header">
    <div class="ws-logo">WinShirt</div>
    <nav class="ws-nav">
      <button class="ws-nav-btn" aria-label="Retour">←</button>
      <button class="ws-nav-btn" aria-label="Annuler">⟲</button>
      <button class="ws-nav-btn" aria-label="Rétablir">⟳</button>
      <button class="ws-nav-btn" aria-label="Sauvegarder">💾</button>
    </nav>
  </header>

  <!-- CONTENU PRINCIPAL -->
  <main class="ws-main">
    <!-- SIDEBAR (desktop uniquement) -->
    <aside class="ws-sidebar">
      <button class="ws-tab-btn active" data-tab="product">🛍 Produit</button>
      <button class="ws-tab-btn" data-tab="images">🖼 Images</button>
      <button class="ws-tab-btn" data-tab="text">✏️ Texte</button>
      <button class="ws-tab-btn" data-tab="layers">⧉ Calques</button>
      <button class="ws-tab-btn" data-tab="cliparts">💖 Cliparts</button>
    </aside>
    <!-- ZONE DE DESIGN PRINCIPALE -->
    <section class="ws-design-area">
      <div class="ws-face-switcher">
        <button class="ws-face-btn active">Recto</button>
        <button class="ws-face-btn">Verso</button>
      </div>
      <div class="ws-product-preview">
        <img src="/mockups/tshirt.png" alt="Mockup produit" class="ws-mockup-img" />
        <div class="ws-print-area" tabindex="0" aria-label="Zone d'impression"></div>
      </div>
      <div class="ws-size-selector">
        <button class="ws-size-btn active">A4</button>
        <button class="ws-size-btn">A3</button>
        <button class="ws-size-btn">Coeur</button>
      </div>
    </section>
    <aside class="ws-rightbar"></aside>
  </main>

  <!-- TOOLBAR MOBILE (scrollable horizontal) -->
  <nav class="ws-mobile-toolbar">
    <button class="ws-toolbar-btn" data-tab="product">🛍</button>
    <button class="ws-toolbar-btn" data-tab="images">🖼</button>
    <button class="ws-toolbar-btn" data-tab="text">✏️</button>
    <button class="ws-toolbar-btn" data-tab="layers">⧉</button>
    <button class="ws-toolbar-btn" data-tab="cliparts">💖</button>
    <button class="ws-toolbar-btn" data-tab="ai">🤖</button>
    <button class="ws-toolbar-btn" data-tab="qrcode">#️⃣</button>
  </nav>

  <!-- PANELS CONTEXTUELS -->
  <section class="ws-panel" data-panel="product"></section>
  <section class="ws-panel" data-panel="images"></section>
  <section class="ws-panel" data-panel="text"></section>
  <section class="ws-panel" data-panel="layers"></section>
  <section class="ws-panel" data-panel="cliparts"></section>
  <section class="ws-panel" data-panel="ai"></section>
  <section class="ws-panel" data-panel="qrcode"></section>
</div>
