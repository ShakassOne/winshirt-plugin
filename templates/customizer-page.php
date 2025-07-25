<?php
/**
 * Dedicated customization page.
 * Expect variables from winshirt_get_customizer_vars().
 */
?>
<div id="winshirt-customizer-modal" class="ws-modal open active ws-page winshirt-theme-inherit modal"
  data-default-front="<?php echo esc_attr( $default_front ?? '' ); ?>"
  data-default-back="<?php echo esc_attr( $default_back ?? '' ); ?>"
  data-colors='<?php echo esc_attr( $ws_colors ?? '[]' ); ?>'
  data-zones='<?php echo esc_attr( $ws_zones ?? '[]' ); ?>'
  data-gallery='<?php echo esc_attr( $ws_gallery ?? '[]' ); ?>'
  data-ai-gallery='<?php echo esc_attr( $ws_ai_gallery ?? '[]' ); ?>'
  data-product-id="<?php echo esc_attr( $pid ); ?>"
  data-base-price="<?php echo esc_attr( $product instanceof WC_Product ? $product->get_price() : 0 ); ?>">
  <div class="modal__overlay"></div>
  <div class="ws-modal-content winshirt-theme-inherit modal__container">
        <button id="winshirt-back-btn" class="ws-side-btn winshirt-theme-inherit side-btn" aria-label="Verso">Verso</button>
      </div>
      <div class="ws-colors winshirt-theme-inherit modal__color-picker"></div>
      <button type="button" class="ws-modal-close-btn modal__close" aria-label="Fermer">
        <svg viewBox="0 0 24 24"><line x1="4" y1="4" x2="20" y2="20"/><line x1="20" y1="4" x2="4" y2="20"/></svg>
      </button>
    </div>

    <div class="modal__content">
      <div class="ws-left winshirt-theme-inherit modal__mockup-area">
      <div class="ws-preview mockup-fixed ws-section winshirt-theme-inherit">
        <img src="<?php echo esc_url( $default_front ?? '' ); ?>" alt="Mockup" class="ws-preview-img" crossorigin="anonymous" />
        <div class="ws-color-overlay winshirt-theme-inherit"></div>
        <div id="ws-canvas" class="ws-canvas"></div>
        <div id="ws-print-zones"></div>
      </div>
      <div id="ws-zone-buttons" class="ws-zone-buttons winshirt-theme-inherit"></div>
    </div>

    <div class="ws-right winshirt-theme-inherit">
      <div class="ws-panel winshirt-theme-inherit">
        <button class="ws-panel-btn winshirt-theme-inherit" data-tab="gallery" aria-label="Galerie">🖼 Galerie</button>
        <button class="ws-panel-btn winshirt-theme-inherit" data-tab="text" aria-label="Texte">🔤 Texte</button>
        <button class="ws-panel-btn winshirt-theme-inherit" data-tab="qr" aria-label="QR Code">🔳 QRCode</button>
        <button class="ws-panel-btn winshirt-theme-inherit" data-tab="ai" aria-label="IA">🤖 IA</button>
        <button class="ws-panel-btn" id="ws-upload-panel" aria-label="Upload">⬆ Uploader</button>
      </div>

      <div class="ws-tab-content ws-section" id="ws-tab-gallery">
        <p>Choisissez un design dans la galerie.</p>
        <div class="ws-gallery-cats winshirt-theme-inherit"></div>
        <div class="ws-gallery winshirt-theme-inherit"></div>
        <button id="ws-upload-trigger" class="ws-upload-btn winshirt-theme-inherit" aria-label="Uploader un visuel">Uploader un visuel</button>
        <input type="file" id="ws-upload-input" accept="image/*" class="hidden winshirt-theme-inherit" />
      </div>

      <div class="ws-tab-content ws-section hidden" id="ws-tab-text">
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
        <label class="winshirt-theme-inherit">Alignement
          <select id="ws-text-align" class="ws-select winshirt-theme-inherit">
            <option value="left">Gauche</option>
            <option value="center" selected>Centre</option>
            <option value="right">Droite</option>
          </select>
        </label>
        <label class="winshirt-theme-inherit">Contour
          <input type="color" id="ws-outline-color" class="winshirt-theme-inherit" value="#000000" />
          <input type="number" id="ws-outline-width" class="winshirt-theme-inherit" min="0" max="10" step="1" value="0" style="width:60px;" />
        </label>
        <button class="ws-upload-btn winshirt-theme-inherit" id="ws-add-text" aria-label="Ajouter le texte">Ajouter</button>
      </div>

      <div class="ws-tab-content ws-section hidden" id="ws-tab-ai">
        <div class="ws-ai-form winshirt-theme-inherit">
          <input type="text" id="ws-ai-prompt" class="ws-input input-text winshirt-theme-inherit" placeholder="Décris le visuel que tu veux créer" />
          <button type="button" id="ws-ai-generate" class="ws-upload-btn winshirt-theme-inherit" aria-label="Générer via IA">Générer</button>
          <div id="ws-ai-loading" style="display:none;margin-top:.5rem;">Chargement...</div>
        </div>
        <div id="ws-ai-gallery" class="ws-ai-gallery winshirt-theme-inherit"></div>
      </div>

      <div class="ws-tab-content ws-section hidden" id="ws-tab-qr">
        <p>Générez un QR Code personnalisé.</p>
        <label class="winshirt-theme-inherit">Type
          <select id="ws-qr-type" class="ws-select winshirt-theme-inherit">
            <option value="url">URL</option>
            <option value="image">Image</option>
            <option value="vcard">vCard</option>
          </select>
        </label>
        <div id="ws-qr-url-wrap" class="ws-qr-field">
          <input type="text" id="ws-qr-url" class="ws-input input-text winshirt-theme-inherit" placeholder="https://..." />
        </div>
        <div id="ws-qr-image-wrap" class="ws-qr-field hidden">
          <input type="file" id="ws-qr-image" accept="image/*" class="winshirt-theme-inherit" />
        </div>
        <div id="ws-qr-vcard-wrap" class="ws-qr-field hidden">
          <input type="text" id="ws-qr-prenom" class="ws-input input-text winshirt-theme-inherit" placeholder="Prénom" />
          <input type="text" id="ws-qr-nom" class="ws-input input-text winshirt-theme-inherit" placeholder="Nom" />
          <input type="email" id="ws-qr-email" class="ws-input input-text winshirt-theme-inherit" placeholder="Email" />
          <input type="tel" id="ws-qr-tel" class="ws-input input-text winshirt-theme-inherit" placeholder="Téléphone" />
        </div>
        <button id="ws-generate-qr" class="ws-upload-btn winshirt-theme-inherit" aria-label="Générer le QR Code">Générer</button>
      </div>


      <div class="ws-sidebar ws-section hidden winshirt-theme-inherit">
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
        <div class="ws-context-actions winshirt-theme-inherit">
          <button id="ws-remove-bg" class="ws-remove-bg winshirt-theme-inherit hidden" type="button" title="Supprimer le fond" aria-label="Supprimer le fond">🧼 Supprimer le fond</button>
          <button id="ws-prop-delete" class="ws-delete winshirt-theme-inherit" type="button" title="Supprimer l'élément" aria-label="Supprimer l'élément">🗑️ Supprimer</button>
          <label class="ws-format-label">📐
            <select id="ws-format-select" class="ws-format-select winshirt-theme-inherit">
              <option value="A3">A3</option>
              <option value="A4">A4</option>
              <option value="A5">A5</option>
              <option value="A6">A6</option>
              <option value="A7">A7</option>
            </select>
          </label>
        </div>
      </div>


      <input type="hidden" id="winshirt-custom-data" value="" />
      <input type="hidden" id="winshirt-production-image" value="" />
      <input type="hidden" id="winshirt-front-image" value="" />
        <input type="hidden" id="winshirt-back-image" value="" />

      </div>

    </div> <!-- end modal__content -->

    <div class="ws-actions ws-section winshirt-theme-inherit modal__footer">
      <small class="ws-size-note">Taille réelle estimée sur un visuel 1500x1500px – affichage à titre indicatif.</small>
      <button id="ws-reset-visual" class="ws-reset winshirt-theme-inherit modal__reset" aria-label="Réinitialiser">Réinitialiser ↺</button>
      <button id="btn-valider-personnalisation" class="ws-validate winshirt-theme-inherit modal__save" aria-label="Valider la personnalisation">Valider la personnalisation</button>
      <button id="btn-test-capture" class="ws-validate winshirt-theme-inherit" aria-label="Test capture">Test Capture</button>
    </div>

    <div id="ws-debug" class="ws-debug"></div>
  </div>
</div>
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof window.openWinShirtModal==='function'){window.openWinShirtModal();}});</script>
