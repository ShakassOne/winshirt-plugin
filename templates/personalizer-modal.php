<div id="winshirt-customizer-modal" class="ws-modal hidden winshirt-theme-inherit"
  data-default-front="<?php echo esc_attr( $default_front ?? '' ); ?>"
  data-default-back="<?php echo esc_attr( $default_back ?? '' ); ?>"
  data-colors='<?php echo esc_attr( $ws_colors ?? '[]' ); ?>'
  data-zones='<?php echo esc_attr( $ws_zones ?? '[]' ); ?>'
  data-gallery='<?php echo esc_attr( $ws_gallery ?? '[]' ); ?>'
  data-product-id="<?php echo esc_attr( $pid ); ?>">
  
  <div class="ws-modal-content winshirt-theme-inherit">

    <div class="ws-left winshirt-theme-inherit">
      <div class="ws-preview mockup-fixed winshirt-theme-inherit">
        <img src="<?php echo esc_url( $default_front ?? '' ); ?>" alt="Mockup" class="ws-preview-img" />
        <div class="ws-color-overlay winshirt-theme-inherit"></div>
        <div id="ws-canvas" class="ws-canvas"></div>
        <div id="ws-print-zones"></div>
        <div id="ws-zone-buttons" class="ws-zone-buttons winshirt-theme-inherit"></div>
      </div>
      <div class="ws-context-actions winshirt-theme-inherit">
        <button id="ws-remove-bg" class="ws-remove-bg winshirt-theme-inherit hidden" type="button" title="Supprimer le fond">🧼 Supprimer le fond</button>
        <button id="ws-prop-delete" class="ws-delete winshirt-theme-inherit" type="button" title="Supprimer l'élément">🗑️ Supprimer</button>
        <label class="ws-format-label">📐
          <select id="ws-format-select" class="ws-format-select winshirt-theme-inherit">
            <option value="A3">A3</option>
            <option value="A4">A4</option>
            <option value="A5">A5</option>
            <option value="A6">A6</option>
            <option value="A7">A7</option>
          </select>
        </label>
        <div class="ws-toggle winshirt-theme-inherit">
          <button id="winshirt-front-btn" class="ws-side-btn active winshirt-theme-inherit">Recto</button>
          <button id="winshirt-back-btn" class="ws-side-btn winshirt-theme-inherit">Verso</button>
        </div>
      </div>
    </div>

    <div class="ws-right winshirt-theme-inherit">
      <div class="ws-tabs-header winshirt-theme-inherit">
        <button class="ws-tab-button active winshirt-theme-inherit" data-tab="gallery">🖼 Galerie</button>
        <button class="ws-tab-button winshirt-theme-inherit" data-tab="text">🔤 Texte</button>
        <button class="ws-tab-button winshirt-theme-inherit" data-tab="ai">🤖 IA</button>
        <button class="ws-tab-button winshirt-theme-inherit" data-tab="svg">✒️ SVG</button>
        <button id="ws-reset-visual" class="ws-reset winshirt-theme-inherit">Réinitialiser ↺</button>
        <button id="winshirt-close-modal" class="ws-close ws-ml-auto winshirt-theme-inherit">Fermer ✖️</button>
      </div>

      <select id="ws-tab-select" class="ws-tab-select select winshirt-theme-inherit">
        <option value="gallery">Galerie</option>
        <option value="text">Texte</option>
        <option value="ai">IA</option>
        <option value="svg">SVG</option>
      </select>

      <button class="ws-accordion-header winshirt-theme-inherit" data-tab="gallery">🖼 Galerie</button>
      <div class="ws-tab-content" id="ws-tab-gallery">
        <p>Choisissez un design dans la galerie.</p>
        <div class="ws-gallery-cats winshirt-theme-inherit"></div>
        <div class="ws-gallery winshirt-theme-inherit"></div>
        <button id="ws-upload-trigger" class="ws-upload-btn winshirt-theme-inherit">Uploader un visuel</button>
        <input type="file" id="ws-upload-input" accept="image/*" class="hidden winshirt-theme-inherit" />
      </div>

      <button class="ws-accordion-header winshirt-theme-inherit" data-tab="text">🔤 Texte</button>
      <div class="ws-tab-content hidden" id="ws-tab-text">
        <input type="text" id="ws-text-content" class="ws-input input-text winshirt-theme-inherit" placeholder="Votre texte..." />
        <select id="ws-font-select" class="ws-select select winshirt-theme-inherit">
          <?php
          $fonts = [
            'Arial',
            'Georgia',
            'Courier New',
            'Times New Roman',
            'Comic Sans MS',
            'Impact',
            'Tahoma',
            'Verdana',
            'Trebuchet MS',
            'Lucida Console',
          ];
          foreach ( $fonts as $font ) :
            ?>
            <option value="<?php echo esc_attr( $font ); ?>" style="font-family: '<?php echo esc_attr( $font ); ?>';">
              <?php echo esc_html( $font ); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="ws-formatting winshirt-theme-inherit">
          <button type="button" id="ws-bold-btn" class="winshirt-theme-inherit">B</button>
          <button type="button" id="ws-italic-btn" class="winshirt-theme-inherit">I</button>
          <button type="button" id="ws-underline-btn" class="winshirt-theme-inherit">U</button>
          <input type="color" id="ws-color-picker" class="winshirt-theme-inherit" value="#000000" />
        </div>
        <label class="winshirt-theme-inherit"><?php esc_html_e('Taille', 'winshirt'); ?>
          <input type="range" id="ws-scale-range" class="winshirt-theme-inherit" min="0.5" max="2" step="0.1" value="1">
        </label>
        <label class="winshirt-theme-inherit"><?php esc_html_e('Rotation', 'winshirt'); ?>
          <input type="range" id="ws-rotate-range" class="winshirt-theme-inherit" min="0" max="360" step="1" value="0">
        </label>
        <button class="ws-upload-btn winshirt-theme-inherit" id="ws-add-text">Ajouter</button>
      </div>

      <button class="ws-accordion-header winshirt-theme-inherit" data-tab="ai">🤖 IA</button>
      <div class="ws-tab-content hidden" id="ws-tab-ai">
        <div class="ws-ai-form winshirt-theme-inherit">
          <input type="text" id="ws-ai-prompt" class="ws-input input-text winshirt-theme-inherit" placeholder="Décris le visuel que tu veux créer" />
          <button type="button" id="ws-ai-generate" class="ws-upload-btn winshirt-theme-inherit">Générer</button>
          <div id="ws-ai-loading" style="display:none;margin-top:.5rem;">Chargement...</div>
        </div>
        <div id="ws-ai-gallery" class="ws-ai-gallery winshirt-theme-inherit"></div>
      </div>

      <button class="ws-accordion-header winshirt-theme-inherit" data-tab="svg">✒️ SVG</button>
      <div class="ws-tab-content hidden" id="ws-tab-svg">
        <p>Bibliothèque d’icônes vectorielles (SVG).</p>
      </div>


      <div class="ws-sidebar hidden winshirt-theme-inherit">
        <h3><?php esc_html_e( 'Édition', 'winshirt' ); ?></h3>
        <label class="winshirt-theme-inherit">📐 <?php esc_html_e( 'Taille', 'winshirt' ); ?>
          <input type="range" id="ws-prop-scale" class="winshirt-theme-inherit" min="0.5" max="2" step="0.1" value="1">
        </label>
        <label class="winshirt-theme-inherit">↻ <?php esc_html_e( 'Rotation', 'winshirt' ); ?>
          <input type="range" id="ws-prop-rotate" class="winshirt-theme-inherit" min="0" max="360" step="1" value="0">
        </label>
        <label class="ws-color-field winshirt-theme-inherit">🎨 <?php esc_html_e( 'Couleur', 'winshirt' ); ?>
          <input type="color" id="ws-prop-color" class="winshirt-theme-inherit" value="#000000">
        </label>
      </div>

      <div class="ws-colors winshirt-theme-inherit"></div>
      <input type="hidden" id="winshirt-custom-data" value="" />

        <div class="ws-actions winshirt-theme-inherit">
          <small class="ws-size-note">Taille réelle estimée sur un visuel 1500x1500px – affichage à titre indicatif.</small>
          <button id="btn-valider-personnalisation" class="ws-validate winshirt-theme-inherit">Valider la personnalisation</button>
        </div>

    </div>
  <div id="ws-debug" class="ws-debug"></div>
  <div class="ws-tools winshirt-theme-inherit">
    <button class="ws-tool-btn" data-tab="gallery">📷</button>
    <button class="ws-tool-btn" id="ws-upload-tool">⬆</button>
    <button class="ws-tool-btn" data-tab="ai">🤖</button>
    <button class="ws-tool-btn" data-tab="text">✏</button>
    <button class="ws-tool-btn" data-tab="svg">📄</button>
  </div>
</div>
</div>
