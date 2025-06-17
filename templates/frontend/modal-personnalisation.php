<div id="winshirt-modal" class="winshirt-modal" data-default-front="<?php echo esc_attr( $default_front ?? '' ); ?>" data-default-back="<?php echo esc_attr( $default_back ?? '' ); ?>">
  <div class="winshirt-modal-content">
    <span class="winshirt-close">&times;</span>
    <ul class="winshirt-tab-links">
      <li class="active"><a href="#winshirt-tab-galerie">🖼 Galerie</a></li>
      <li><a href="#winshirt-tab-texte">🔤 Texte</a></li>
      <li><a href="#winshirt-tab-ia">🤖 IA</a></li>
      <li><a href="#winshirt-tab-svg">🖌️ SVG</a></li>
    </ul>
    <div class="winshirt-tab" id="winshirt-tab-galerie">
      <button class="winshirt-upload button">Uploader un visuel</button>
      <input type="file" class="winshirt-upload-input" style="display:none" accept="image/*" />
      <p>Choisissez un design dans la galerie.</p>
    </div>
    <div class="winshirt-tab" id="winshirt-tab-texte">
      <button class="winshirt-upload button">Uploader un visuel</button>
      <input type="file" class="winshirt-upload-input" style="display:none" accept="image/*" />
      <textarea id="winshirt-text-input" rows="3" style="width:100%;" placeholder="Votre texte..."></textarea>
    </div>
    <div class="winshirt-tab" id="winshirt-tab-ia">
      <button class="winshirt-upload button">Uploader un visuel</button>
      <input type="file" class="winshirt-upload-input" style="display:none" accept="image/*" />
      <p>Générez une image grâce à l'IA (bientôt disponible).</p>
    </div>
    <div class="winshirt-tab" id="winshirt-tab-svg">
      <button class="winshirt-upload button">Uploader un visuel</button>
      <input type="file" class="winshirt-upload-input" style="display:none" accept="image/*" />
      <p>Bibliothèque d'icônes vectorielles.</p>
    </div>
    <div class="winshirt-preview-buttons">
      <button id="winshirt-front-btn" class="button">Recto</button>
      <button id="winshirt-back-btn" class="button">Verso</button>
    </div>
    <div class="winshirt-preview">
      <div id="winshirt-preview-front" style="display:none;position:relative;">
        <img src="<?php echo esc_url( $default_front ?? '' ); ?>" alt="Preview front" />
        <span class="winshirt-text"></span>
      </div>
      <div id="winshirt-preview-back" style="display:none;position:relative;">
        <img src="<?php echo esc_url( $default_back ?? '' ); ?>" alt="Preview back" />
        <span class="winshirt-text"></span>
      </div>
    </div>
  </div>
</div>
