<div id="winshirt-customizer-modal" class="ws-modal hidden" data-default-front="<?php echo esc_attr( $default_front ?? '' ); ?>" data-default-back="<?php echo esc_attr( $default_back ?? '' ); ?>">
  <div class="ws-modal-content">
    <div class="ws-tabs-header">
      <button class="ws-tab-button active" data-tab="gallery">🖼 Galerie</button>
      <button class="ws-tab-button" data-tab="text">🔤 Texte</button>
      <button class="ws-tab-button" data-tab="ai">🤖 IA</button>
      <button class="ws-tab-button" data-tab="svg">✒️ SVG</button>
      <button id="winshirt-close-modal" class="ws-close ws-ml-auto">Fermer ✖️</button>
    </div>

    <div class="ws-tab-content" id="ws-tab-gallery">
      <p>Choisissez un design dans la galerie.</p>
      <div class="ws-gallery"></div>
      <button class="ws-upload-btn">Uploader un visuel</button>
      <input type="file" id="ws-upload-input" accept="image/*" class="hidden" />
    </div>

    <div class="ws-tab-content hidden" id="ws-tab-text">
      <textarea id="winshirt-text-input" placeholder="Ajoutez votre texte ici..." class="ws-textarea"></textarea>
    </div>

    <div class="ws-tab-content hidden" id="ws-tab-ai">
      <p>Générez une image grâce à l’IA (bientôt disponible).</p>
    </div>

    <div class="ws-tab-content hidden" id="ws-tab-svg">
      <p>Bibliothèque d’icônes vectorielles (SVG).</p>
    </div>

    <div class="ws-preview">
      <img src="<?php echo esc_url( $default_front ?? '' ); ?>" alt="Mockup" class="ws-preview-img" />
      <div id="design-zone" class="ws-design-zone">
        <button class="ws-remove" title="Supprimer">×</button>
        <div class="ws-handle ws-handle-nw"></div>
        <div class="ws-handle ws-handle-ne"></div>
        <div class="ws-handle ws-handle-sw"></div>
        <div class="ws-handle ws-handle-se"></div>
      </div>
    </div>

    <div class="ws-actions">
      <div class="ws-toggle">
        <button id="winshirt-front-btn" class="ws-side-btn active">Recto</button>
        <button id="winshirt-back-btn" class="ws-side-btn">Verso</button>
      </div>
      <button id="winshirt-validate" class="ws-validate">Valider la personnalisation</button>
    </div>
  </div>
</div>
