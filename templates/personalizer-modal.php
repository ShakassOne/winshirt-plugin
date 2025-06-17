<div id="winshirt-customizer-modal" class="ws-modal hidden" data-default-front="<?php echo esc_attr( $default_front ?? '' ); ?>" data-default-back="<?php echo esc_attr( $default_back ?? '' ); ?>" data-colors='<?php echo esc_attr( $ws_colors ?? '[]' ); ?>' data-zones='<?php echo esc_attr( $ws_zones ?? '[]' ); ?>' data-gallery='<?php echo esc_attr( $ws_gallery ?? '[]' ); ?>'>
  <div class="ws-modal-content">
    <div class="ws-tabs-header">
      <button class="ws-tab-button active" data-tab="gallery">🖼 Galerie</button>
      <button class="ws-tab-button" data-tab="text">🔤 Texte</button>
      <button class="ws-tab-button" data-tab="ai">🤖 IA</button>
      <button class="ws-tab-button" data-tab="svg">✒️ SVG</button>
      <button id="ws-reset-btn" class="ws-reset">🗑 Réinitialiser</button>
      <button id="winshirt-close-modal" class="ws-close ws-ml-auto">Fermer ✖️</button>
    </div>

    <div class="ws-body">

    <div class="ws-tab-content" id="ws-tab-gallery">
      <p>Choisissez un design dans la galerie.</p>
      <select id="ws-category-select" class="ws-select" style="margin-bottom:.5rem;"></select>
      <div class="ws-gallery"></div>
      <button class="ws-upload-btn">Uploader un visuel</button>
      <input type="file" id="ws-upload-input" accept="image/*" class="hidden" />
    </div>

    <div class="ws-tab-content hidden" id="ws-tab-text">
      <input type="text" id="ws-text-content" class="ws-input" placeholder="Votre texte..." />
      <select id="ws-font-select" class="ws-select">
        <option value="Arial">Arial</option>
        <option value="Georgia">Georgia</option>
        <option value="Courier New">Courier New</option>
        <option value="Times New Roman">Times</option>
      </select>
      <div class="ws-formatting">
        <button type="button" id="ws-bold-btn">B</button>
        <button type="button" id="ws-italic-btn">I</button>
        <button type="button" id="ws-underline-btn">U</button>
        <input type="color" id="ws-color-picker" value="#000000" />
      </div>
      <label><?php esc_html_e('Taille', 'winshirt'); ?> <input type="range" id="ws-scale-range" min="0.5" max="2" step="0.1" value="1"></label>
      <label><?php esc_html_e('Rotation', 'winshirt'); ?> <input type="range" id="ws-rotate-range" min="0" max="360" step="1" value="0"></label>
      <button class="ws-upload-btn" id="ws-add-text">Ajouter</button>
    </div>

    <div class="ws-tab-content hidden" id="ws-tab-ai">
      <p>Générez une image grâce à l’IA (bientôt disponible).</p>
    </div>

    <div class="ws-tab-content hidden" id="ws-tab-svg">
      <p>Bibliothèque d’icônes vectorielles (SVG).</p>
    </div>

    <div class="ws-preview">
      <img src="<?php echo esc_url( $default_front ?? '' ); ?>" alt="Mockup" class="ws-preview-img" />
      <div id="ws-canvas" class="ws-canvas"></div>
      <div class="ws-print-zone" data-side="front"></div>
      <div class="ws-print-zone" data-side="back"></div>
    </div>
    <div class="ws-sidebar hidden">
      <h3><?php esc_html_e( 'Édition', 'winshirt' ); ?></h3>
      <label><?php esc_html_e( 'Taille', 'winshirt' ); ?>
        <input type="range" id="ws-prop-scale" min="0.5" max="2" step="0.1" value="1">
      </label>
      <label><?php esc_html_e( 'Rotation', 'winshirt' ); ?>
        <input type="range" id="ws-prop-rotate" min="0" max="360" step="1" value="0">
      </label>
      <label class="ws-color-field">
        <?php esc_html_e( 'Couleur', 'winshirt' ); ?>
        <input type="color" id="ws-prop-color" value="#000000">
      </label>
      <button id="ws-prop-delete" class="ws-delete" type="button"><?php esc_html_e( 'Supprimer', 'winshirt' ); ?></button>
    </div>
    <div class="ws-colors"></div>

    <input type="hidden" id="winshirt-custom-data" value="" />

    <div class="ws-actions">
      <div class="ws-toggle">
        <button id="winshirt-front-btn" class="ws-side-btn active">Recto</button>
        <button id="winshirt-back-btn" class="ws-side-btn">Verso</button>
      </div>
      <button id="winshirt-validate" class="ws-validate">Valider la personnalisation</button>
    </div>
    </div>
  </div>
</div>
