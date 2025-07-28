<?php
/**
 * WinShirt Next-Gen customizer template
 */

$vars = $GLOBALS['winshirt_customizer_vars'] ?? [];
$front_url = $vars['default_front'] ?? '';
$back_url  = $vars['default_back'] ?? '';

$default_front = 'https://winshirt.fr/wp-content/uploads/2025/06/White-Tshirt-Recto.png';
$default_back  = 'https://winshirt.fr/wp-content/uploads/2025/06/White-Tshirt-Verso.png';

if (!$front_url) $front_url = $default_front;
if (!$back_url) $back_url = $default_back;
?>
<link rel="stylesheet" href="/wp-content/plugins/winshirt-plugin/assets/css/winshirt-nextgen.css?v=<?php echo time(); ?>" />
<div class="ws-configurator">
  <header class="ws-header">
    <div class="ws-logo"><img src="/wp-content/uploads/2025/07/Fichier%202@2x.png" alt="WinShirt" height="36"></div>
    <nav class="ws-nav">
      <button class="ws-nav-btn" aria-label="Retour">←</button>
      <button class="ws-nav-btn" aria-label="Annuler">⟲</button>
      <button class="ws-nav-btn" aria-label="Rétablir">⟳</button>
      <button class="ws-nav-btn" aria-label="Sauvegarder">💾</button>
    </nav>
  </header>
  <main class="ws-main">
    <aside class="ws-sidebar">
      <button class="ws-tab-btn active" data-tab="product">🛍 Produit</button>
      <button class="ws-tab-btn" data-tab="images">🖼 Images</button>
      <button class="ws-tab-btn" data-tab="text">✏️ Texte</button>
      <button class="ws-tab-btn" data-tab="layers">⧉ Calques</button>
      <button class="ws-tab-btn" data-tab="cliparts">💖 Cliparts</button>
    </aside>
    <section class="ws-design-area">
      <div class="ws-face-switcher">
        <button class="ws-face-btn active">Recto</button>
        <button class="ws-face-btn">Verso</button>
      </div>
      <div class="ws-product-preview">
        <img src="<?php echo esc_url($front_url); ?>" 
          data-front="<?php echo esc_url($front_url); ?>" 
          data-back="<?php echo esc_url($back_url); ?>" 
          alt="Mockup produit" class="ws-mockup-img" />
        <div class="ws-print-area" tabindex="0" aria-label="Zone d'impression"></div>
      </div>
      <div class="ws-size-selector">
        <button class="ws-size-btn active">A4</button>
        <button class="ws-size-btn">A3</button>
        <button class="ws-size-btn">Coeur</button>
      </div>
    </section>
  </main>
  <nav class="ws-mobile-toolbar">
    <button class="ws-toolbar-btn" data-tab="product">🛍</button>
    <button class="ws-toolbar-btn" data-tab="images">🖼</button>
    <button class="ws-toolbar-btn" data-tab="text">✏️</button>
    <button class="ws-toolbar-btn" data-tab="layers">⧉</button>
    <button class="ws-toolbar-btn" data-tab="cliparts">💖</button>
  </nav>
</div>
