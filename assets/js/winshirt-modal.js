jQuery(function($){
  function init(){
  var $modal = $('#winshirt-customizer-modal');
  if(!$modal.length){
    console.error('WinShirt: modal #winshirt-customizer-modal not found');
    return false;
  }
  $('body').append($modal);
  var state = { side: 'front', color: null, zone: 0, zoneSel: { front: 0, back: 0 } };
  var $canvas = $('#ws-canvas');
  var $previewImg = $modal.find('.ws-preview-img');
  var initialFront = $modal.data('default-front');
  var initialBack  = $modal.data('default-back');
  var $sidebar = $modal.find('.ws-sidebar');
  var $scaleInput = $('#ws-prop-scale');
  var $rotateInput = $('#ws-prop-rotate');
  var $colorInput = $('#ws-prop-color');
  var $deleteBtn = $('#ws-prop-delete');
  var $removeBgBtn = $('#ws-remove-bg');
  var colors = $modal.data('colors') || [];
  var zones  = $modal.data('zones') || [];
  var $colorsWrap = $modal.find('.ws-colors');
  var $formatSelect = $('#ws-format-select');
  var $zonesWrap = $('#ws-print-zones');
  var $zoneButtons = $('#ws-zone-buttons');
  var $left = $modal.find('.ws-left');
  var $right = $modal.find('.ws-right');
  var $tabSelect = $('#ws-tab-select');
  var $debug = $('#ws-debug');
  var $prodField = $('#winshirt-production-image-field');
  var $frontField = $('#winshirt-front-image-field');
  var $backField = $('#winshirt-back-image-field');
  var $customField = $('#winshirt-custom-data-field');
  var $extraField = $('#winshirt-extra-price-field');
  var $priceEl = $('.summary .price, .product .price').first();
  var basePrice = parseFloat($modal.data('base-price'));
  if(isNaN(basePrice) && $priceEl.length){
    var num = $priceEl.text().replace(/[^0-9,.-]/g,'').replace(',','.');
    basePrice = parseFloat(num);
  }
  if(isNaN(basePrice)) basePrice = null;

  var saveTimeout = null;

  function computeExtraPrice(){
    if(!$extraField.length) return 0;
    var used = {front:false, back:false};
    $canvas.children('.ws-item').each(function(){
      used[$(this).data('side')||'front'] = true;
    });
    var extra = 0;
    ['front','back'].forEach(function(side){
      if(!used[side]) return;
      var idx = state.zoneSel[side];
      if(typeof idx === 'undefined') return;
      var z = zones[idx];
      if(z && (z.side||'front') === side){
        extra += parseFloat(z.price || 0);
      }
    });
    $extraField.val(extra.toFixed(2));
    return extra;
  }

  function updateDisplayedPrice(){
    if(basePrice===null || !$priceEl.length) return;
    var extra = computeExtraPrice();
    var total = basePrice + extra;
    var formatted = total.toLocaleString('fr-FR',{minimumFractionDigits:2,maximumFractionDigits:2});
    var $amount = $priceEl.find('.amount, bdi').first();
    if($amount.length){
      $amount.html(formatted+'\u00a0€');
    }else{
      $priceEl.text(formatted+'\u00a0€');
    }
  }

  function showCustomPreview(){
    var front = localStorage.getItem('winshirt_front_image');
    var back  = localStorage.getItem('winshirt_back_image');
    if(!front && !back) return;
    var $wrap = $('#ws-custom-preview');
    if(!$wrap.length){
      $wrap = $('<div id="ws-custom-preview" class="ws-custom-preview winshirt-theme-inherit"><h3>Votre T-Shirt personnalisé</h3><div class="ws-custom-images"></div></div>');
      $('.woocommerce-product-gallery').after($wrap);
    }
    var $imgs = $wrap.find('.ws-custom-images');
    $imgs.empty();
    if(front){ $imgs.append('<img src="'+front+'" alt="Recto" class="ws-custom-img" />'); }
    if(back){ $imgs.append('<img src="'+back+'" alt="Verso" class="ws-custom-img" />'); }
  }

  $(document).on('click', '#ws-custom-preview img', function(){
    window.open($(this).attr('src'), '_blank');
  });
  var $prodLocal = $('#winshirt-production-image');
  var $frontLocal = $('#winshirt-front-image');
  var $backLocal = $('#winshirt-back-image');

  if($prodField.length){
    var prevImg = localStorage.getItem('winshirt_production_image');
    if(prevImg){ $prodField.val(prevImg); if($prodLocal.length){$prodLocal.val(prevImg);} }
  }
  if($frontField.length){
    var prevFront = localStorage.getItem('winshirt_front_image');
    if(prevFront){ $frontField.val(prevFront); if($frontLocal.length){$frontLocal.val(prevFront);} }
  }
  if($backField.length){
    var prevBack = localStorage.getItem('winshirt_back_image');
    if(prevBack){ $backField.val(prevBack); if($backLocal.length){$backLocal.val(prevBack);} }
  }
  function loadFont(f){
    var id = 'ws-font-'+f.replace(/\s+/g,'-').toLowerCase();
    if(!document.getElementById(id)){
      $('head').append('<link id="'+id+'" rel="stylesheet" href="https://fonts.googleapis.com/css2?family='+f.replace(/\s+/g,'+')+':wght@400;700&display=swap">');
    }
  }
  $('#ws-font-select option').each(function(){
    loadFont($(this).val());
  });
  var formatHeights = { A3:0.467 };
  formatHeights.A4 = formatHeights.A3 / Math.sqrt(2);
  formatHeights.A5 = formatHeights.A4 / Math.sqrt(2);
  formatHeights.A6 = formatHeights.A5 / Math.sqrt(2);
  formatHeights.A7 = formatHeights.A6 / Math.sqrt(2);
  var formatOrder = ['A3','A4','A5','A6','A7'];
  var activeItem = null;
  var activeTab  = 'gallery';
  function saveAiImages(){
    var local = aiImages.filter(function(u){ return serverAiGallery.indexOf(u) === -1; });
    localStorage.setItem('ws_ai_images', JSON.stringify(local));
  }
  function loadAiImages(){
    var stored = [];
    try{ stored = JSON.parse(localStorage.getItem('ws_ai_images')) || []; }
    catch(e){ stored = []; }
    aiImages = serverAiGallery.concat(stored.filter(function(u){ return serverAiGallery.indexOf(u) === -1; }));
  }
  function renderAiGallery(){
    $aiGallery.empty();
    aiImages.forEach(function(url){
      var $w = $('<div class="ws-ai-thumb-wrap" />');
      var $img = $('<img class="ws-ai-thumb ws-gallery-thumb" />').attr('src',url);
      var $lab = $('<span class="ws-ai-label">IA</span>');
      $w.append($img,$lab);
      if(serverAiGallery.indexOf(url) === -1){
        $w.append('<span class="ws-ai-del">🗑️</span>');
      }
      $aiGallery.append($w);
    });
  }

  function showTooltip(text){
    if(!$debug.length) return;
    $debug.text(text).addClass('show');
    clearTimeout($debug.data('to'));
    $debug.data('to', setTimeout(function(){ $debug.removeClass('show'); }, 800));
  }

  function uploadMockup(){
    if(!window.html2canvas) return;
    html2canvas($modal.find('.ws-preview')[0], {backgroundColor:null,scale:1}).then(function(canvas){
      canvas.toBlob(function(blob){
        if(!blob) return;
        var fd = new FormData();
        fd.append('image', blob, 'mockup.png');
        fetch(winshirtAjax.rest+'upload-mockup', {
          method:'POST',
          credentials:'same-origin',
          headers:{'X-WP-Nonce':winshirtAjax.nonce},
          body:fd
        }).then(function(r){return r.json();}).then(function(res){
          if(res && res.url){
            localStorage.setItem('winshirt_mockup', res.url);
          }
        });
      }, 'image/png');
    });
  }

  function captureSide(side){
    return new Promise(function(resolve){
      if(!window.html2canvas){ resolve(); return; }
      var prev = state.side;
      var $hidden = $modal.find('.ws-print-zone, .ws-zone-label, .ws-zone-resize, .ws-remove');
      $hidden.css('visibility','hidden');
      if(side!==prev) switchSide(side);
      html2canvas($modal.find('.ws-preview')[0], {backgroundColor:null,scale:1,useCORS:true,allowTaint:true}).then(function(canvas){
        $hidden.css('visibility','');
        canvas.toBlob(function(blob){
          if(!blob){
            if(side!==prev) switchSide(prev);
            resolve();
            return;
          }
          var fd = new FormData();
          fd.append('image', blob, side+'.png');
          fd.append('side', side);
          fetch(winshirtAjax.rest+'upload-custom-side', {
            method:'POST',
            credentials:'same-origin',
            headers:{'X-WP-Nonce':winshirtAjax.nonce},
            body:fd
          }).then(function(r){return r.json();}).then(function(res){
            if(res && res.url){
              if(side==='front'){
                $frontField.val(res.url); if($frontLocal.length){$frontLocal.val(res.url);} localStorage.setItem('winshirt_front_image', res.url);
              }else{
                $backField.val(res.url); if($backLocal.length){$backLocal.val(res.url);} localStorage.setItem('winshirt_back_image', res.url);
              }
            }
            if(side!==prev) switchSide(prev);
            resolve();
          });
        }, 'image/png');
      });
    });
  }

  function captureAllSides(){
    var promise = captureSide('front');
    if($modal.data('default-back')){
      promise = promise.then(function(){ return captureSide('back'); });
    }
    return promise;
  }

  function updateDebug($it){
    if(!$debug.length) return;
    var pos = $it.position();
    var info = 'x:'+Math.round(pos.left)+' y:'+Math.round(pos.top)+' w:'+Math.round($it.width())+' h:'+Math.round($it.height())+' ('+detectFormat($it)+')';
    $debug.text(info);
  }

  function applyClip(){
    var $z = $modal.find('.ws-print-zone[data-index="'+state.zone+'"]');
    if($z.length){
      var pos = $z.position();
      var w = $z.parent().width();
      var h = $z.parent().height();
      var clip = 'inset(' + pos.top + 'px ' + (w - (pos.left + $z.width())) + 'px ' + (h - (pos.top + $z.height())) + 'px ' + pos.left + 'px)';
      $canvas.css('clip-path', clip);
    } else {
      $canvas.css('clip-path','none');
    }
  }

  function checkMobile(){
    if(window.innerWidth <= 768){
      $modal.addClass('ws-mobile winshirt-personnalisation-mobile');
      updatePreviewHeight();
    } else {
      $modal.removeClass('ws-mobile winshirt-personnalisation-mobile');
      $modal.find('.ws-right').removeClass('show');
      $modal.find('.ws-preview').css('max-height','');
    }
    if($zoneButtons.parent()[0] === $modal.find('.ws-preview')[0]){
      $zoneButtons.insertAfter($modal.find('.ws-preview'));
    }
  }

  function updatePreviewHeight(){
    $modal.find('.ws-preview').css('max-height', '');
  }

  function debugHiddenElements(){
    var hidden = [];
    $modal.find('*').each(function(){
      var $el = $(this);
      if(!$el.is(':visible')){
        var desc = this.tagName.toLowerCase();
        if(this.id) desc += '#' + this.id;
        if(this.className) desc += '.' + this.className.trim().replace(/\s+/g,'.');
        hidden.push(desc);
      }
    });
    if(hidden.length){
      console.debug('WinShirt hidden elements:', hidden);
    }
  }

  function syncState(){
    if(!winshirtAjax.loggedIn) return;
    var data = localStorage.getItem('winshirt_custom');
    if(!data) return;
    fetch(winshirtAjax.rest+'save-customization', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'X-WP-Nonce': winshirtAjax.nonce,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        data: data,
        front: localStorage.getItem('winshirt_front_image') || '',
        back: localStorage.getItem('winshirt_back_image') || '',
        product: parseInt($modal.data('product-id') || 0)
      })
    });
  }

  checkMobile();
  if(localStorage.getItem('wsDebug')==='1'){ $debug.addClass('show'); }
  $(document).on('keydown', function(e){
    if(e.key.toLowerCase()==='d' && e.ctrlKey){
      e.preventDefault();
      if($debug.hasClass('show')){ $debug.removeClass('show'); localStorage.setItem('wsDebug','0'); }
      else { $debug.addClass('show'); localStorage.setItem('wsDebug','1'); }
    }
  });

  function saveState(){
    var items = [];
    $canvas.children('.ws-item').each(function(){
      var $it = $(this);
      var pos = {
        left: parseFloat($it.attr('data-x') || 0),
        top:  parseFloat($it.attr('data-y') || 0)
      };
      items.push({
        type: $it.data('type'),
        side: $it.data('side'),
        position: { x:(pos.left / $canvas.width()).toFixed(4), y:(pos.top / $canvas.height()).toFixed(4) },
        scale: parseFloat($it.attr('data-scale') || 1),
        rotation: parseInt($it.attr('data-rotation') || 0,10),
        color: $it.attr('data-color') || null,
        align: $it.attr('data-align') || null,
        outlineColor: $it.attr('data-outline-color') || null,
        outlineWidth: parseInt($it.attr('data-outline-width') || 0,10),
        width: ($it.width() / $canvas.width()).toFixed(4),
        height: ($it.height() / $canvas.height()).toFixed(4),
        content: (function(){
          if($it.data('type')==='text') return $it.find('.ws-text').text();
          if($it.data('type')==='svg') return $it.find('svg')[0].outerHTML;
          return $it.find('img').attr('src');
        })()
      });
    });
    var data = {
      items: items,
      color: state.color,
      defaultFront: $modal.data('default-front') || initialFront,
      defaultBack: $modal.data('default-back') || initialBack,
      side: state.side,
      zoneSel: state.zoneSel
    };
    localStorage.setItem('winshirt_custom', JSON.stringify(data));
    updateDisplayedPrice();
    if(saveTimeout){ clearTimeout(saveTimeout); }
    saveTimeout = setTimeout(function(){
      uploadMockup();
    }, 500);
  }

  function loadState(){
    var raw = localStorage.getItem('winshirt_custom');
    if(!raw) return;
    try{ raw = JSON.parse(raw); }catch(e){ return; }
    $canvas.empty();
    if(raw.defaultFront){ $modal.data('default-front', raw.defaultFront); }
    if(raw.defaultBack){ $modal.data('default-back', raw.defaultBack); }
    if(raw.zoneSel){
      state.zoneSel = raw.zoneSel;
    }
    if(raw.color){
      $('.ws-color-overlay').css('background-color', raw.color);
      state.color = raw.color;
    }
    switchSide(raw.side || 'front');
    if(Array.isArray(raw.items)){
      raw.items.forEach(function(it){
        var $new = addItem(it.type, it.content);
        $new.attr('data-side', it.side || 'front');
        $new.attr('data-scale', it.scale || 1);
        $new.attr('data-rotation', it.rotation || 0);
        var posX = parseFloat(it.position.x) * $canvas.width();
        var posY = parseFloat(it.position.y) * $canvas.height();
        $new.attr('data-x', posX).attr('data-y', posY);
        $new.css({
          width: parseFloat(it.width) * $canvas.width(),
          height: parseFloat(it.height) * $canvas.height(),
          left:0,
          top:0
        });
        if(it.color){
          $new.attr('data-color', it.color);
          if(it.type === 'text'){
            $new.find('.ws-text').css('color', it.color);
          } else if(it.type === 'svg'){
            $new.find('svg').css('color', it.color);
          }
        }
        if(it.align){
          $new.attr('data-align', it.align);
          $new.find('.ws-text').css('text-align', it.align);
        }
        if(it.outlineColor){
          $new.attr('data-outline-color', it.outlineColor);
          $new.attr('data-outline-width', it.outlineWidth || 0);
          $new.find('.ws-text').css('-webkit-text-stroke', (it.outlineWidth||0)+'px '+it.outlineColor);
        }
        updateItemTransform($new);
      });
    }
    updateDisplayedPrice();
  }

  var gallery = $modal.data('gallery') || [];
  var $gallery = $modal.find('.ws-gallery');
  var $cats   = $modal.find('.ws-gallery-cats');
  var $aiGallery = $('#ws-ai-gallery');
  var serverAiGallery = $modal.data('ai-gallery') || [];
  var aiImages = [];
  var cats = [];
  gallery.forEach(function(g){
    var cat = g.category || '';
    if(cat && cats.indexOf(cat) === -1) cats.push(cat);
    var $img = $('<img class="ws-gallery-thumb" />')
      .attr('src', g.url)
      .attr('data-id', g.id)
      .attr('alt', g.title || '')
      .attr('data-cat', cat);
    $gallery.append($img);
  });
  if(cats.length){
    $cats.append('<button class="ws-cat-btn active" data-cat="all">Tous</button>');
    cats.forEach(function(c){
      $cats.append('<button class="ws-cat-btn" data-cat="'+c+'">'+c+'</button>');
    });
  }
  $cats.on('click', '.ws-cat-btn', function(){
    var cat = $(this).data('cat');
    $cats.find('.ws-cat-btn').removeClass('active');
    $(this).addClass('active');
    if(cat==='all'){
      $gallery.children().show();
    }else{
      $gallery.children().hide().filter('[data-cat="'+cat+'"]').show();
    }
  });

  $gallery.on('click', '.ws-gallery-thumb', function(){
    $gallery.find('.ws-gallery-thumb').removeClass('selected');
    $(this).addClass('selected');
    addItem('image', $(this).attr('src'));
  });

  function initAi(){
    loadAiImages();
    renderAiGallery();
  }
  initAi();

  $aiGallery.on('click', '.ws-ai-thumb', function(){
    addItem('image', $(this).attr('src'));
  });
  $aiGallery.on('click', '.ws-ai-del', function(e){
    e.stopPropagation();
    var url = $(this).siblings('img').attr('src');
    aiImages = aiImages.filter(function(u){ return u !== url; });
    saveAiImages();
    renderAiGallery();
  });
  $('#ws-ai-generate').on('click', function(e){
    e.preventDefault();
    var prompt = $('#ws-ai-prompt').val().trim();
    if(!prompt) return;
    var limit = 3;
    var count = parseInt(localStorage.getItem('ws_ai_count')||'0',10);
    if(count >= limit){
      alert('Limite de generation atteinte');
      return;
    }
    $('#ws-ai-loading').show();
    fetch('/wp-json/winshirt/v1/generate-image', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ prompt: prompt })
    }).then(function(r){return r.json();}).then(function(data){
      $('#ws-ai-loading').hide();
      if(data && data.imageUrl){
        if(aiImages.indexOf(data.imageUrl) === -1){
          aiImages.unshift(data.imageUrl);
        }
        saveAiImages();
        localStorage.setItem('ws_ai_count', count+1);
        renderAiGallery();
      }else{
        alert('Erreur de génération IA');
      }
    }).catch(function(){
      $('#ws-ai-loading').hide();
      alert('Erreur de génération IA');
    });
  });

  colors.forEach(function(c,idx){
    var $b = $('<button class="ws-color-btn" />').css('background-color', c.code || '#fff').attr('data-index', idx);
    $colorsWrap.append($b);
  });

  $colorsWrap.on('click', '.ws-color-btn', function(){
    var col = colors[$(this).data('index')];
    if(!col) return;
    $colorsWrap.find('.ws-color-btn').removeClass('active');
    $(this).addClass('active');
    $('.ws-color-overlay').css('background-color', col.code || '#ffffff');
    state.color = col.code || null;
    saveState();
  });

  $zonesWrap.empty();
  $zoneButtons.empty();
  zones.forEach(function(z, idx){
    var $z = $('<div class="ws-print-zone" />')
      .attr('data-side', z.side || 'front')
      .attr('data-index', idx)
      .css({top:z.top+'%', left:z.left+'%', width:z.width+'%', height:z.height+'%'});
    var $lab = $('<div class="ws-zone-label" />').text(z.name || '');
    $z.append($lab);
    $zonesWrap.append($z);
    var $btn = $('<button class="ws-zone-btn" />')
      .text(z.name || ('Zone '+(idx+1)))
      .attr('data-index', idx)
      .attr('data-side', z.side || 'front');
    $zoneButtons.append($btn);
  });
  $zoneButtons.on('click', '.ws-zone-btn', function(){
    selectZone(parseInt($(this).data('index'),10));
  });
  var firstZone = zones.findIndex(function(z){ return z.side === state.side; });
  if(firstZone < 0) firstZone = 0;
  state.zoneSel[state.side] = firstZone;
  selectZone(firstZone);
  updateZoneButtons();
  applyClip();

  function getContainment(){
    var $z = $modal.find('.ws-print-zone[data-index="'+state.zone+'"]');
    if($z.length && $z.width()>0 && $z.height()>0){
      return $z;
    }
    return '.ws-preview';
  }

  function getBaseHeight(){
    var cont = $(getContainment());
    return cont.height() || $('.ws-preview').height();
  }

  function detectFormat($it){
    var h = $it.height();
    var base = getBaseHeight();
    var fmt = formatOrder[formatOrder.length-1];
    for(var i=0;i<formatOrder.length;i++){
      var f = formatOrder[i];
      var ratio = formatHeights[f];
      if(h >= base * ratio){ fmt = f; break; }
    }
    return fmt;
  }

  function selectZone(index){
    state.zone = index;
    state.zoneSel[state.side] = index;
    $zoneButtons.find('.ws-zone-btn').removeClass('active selected');
    $zoneButtons.find('.ws-zone-btn[data-index="'+index+'"]').addClass('active selected');
    $modal.find('.ws-print-zone').removeClass('active').hide();
    $modal.find('.ws-print-zone[data-index="'+index+'"]').show().addClass('active');
    updateDisplayedPrice();
    applyClip();
    if(activeItem){ updateDebug(activeItem); }
    saveState();
  }

  function updateZoneButtons(){
    $zoneButtons.find('.ws-zone-btn').each(function(){
      var idx = parseInt($(this).data('index'),10);
      var side = zones[idx] && zones[idx].side ? zones[idx].side : 'front';
      if(side === state.side){ $(this).show(); }
      else { $(this).hide(); }
    });
  }

  function updateFormatUIFromItem($it){
    var ratio = $it.height() / getBaseHeight();
    var closest = {fmt:'A7', diff:Infinity};
    formatOrder.forEach(function(f){
      var d = Math.abs(ratio - formatHeights[f]);
      if(d < closest.diff){ closest = {fmt:f, diff:d}; }
    });
    if($formatSelect.length){ $formatSelect.val(closest.fmt); }
  }

  function updateFormatUI(fmt){
    if($formatSelect.length){ $formatSelect.val(fmt); }
  }

  function applyFormat($it, fmt){
    var $zone = $(getContainment());
    var zpos = $zone.position();
    var zw = $zone.width();
    var zh = $zone.height();
    var base = getBaseHeight();
    var ratio = formatHeights[fmt] || 0;
    var h = Math.min(base * ratio, zh);
    var w = h / 1.414;
    if(w > zw){
      w = zw;
      h = w * 1.414;
    }
    var left = zpos.left + (zw - w)/2;
    var top  = zpos.top + (zh - h)/2;
    $it.css({width:w, height:h});
    $it.attr('data-x', left).attr('data-y', top);
    updateItemTransform($it);
    updateFormatUI(fmt);
  }

  function updateItemTransform($it){
    var x = parseFloat($it.attr('data-x')||0);
    var y = parseFloat($it.attr('data-y')||0);
    var scale = parseFloat($it.attr('data-scale') || 1);
    var rot = parseInt($it.attr('data-rotation') || 0, 10);
    $it.css({transform:'translate('+x+'px,'+y+'px) rotate('+rot+'deg) scale('+scale+')'});
  }

  function applyTextStyles($it){
    var font = $('#ws-font-select').val() || 'Arial';
    var bold = $('#ws-bold-btn').hasClass('active');
    var italic = $('#ws-italic-btn').hasClass('active');
    var underline = $('#ws-underline-btn').hasClass('active');
    var col = $('#ws-color-picker').val() || '#000000';
    var align = $('#ws-text-align').val() || 'center';
    var strokeColor = $('#ws-outline-color').val() || '#000000';
    var strokeWidth = parseInt($('#ws-outline-width').val() || 0,10);
    var scale = parseFloat($('#ws-scale-range').val() || 1);
    var rot = parseInt($('#ws-rotate-range').val() || 0,10);
    $it.attr('data-scale', scale);
    $it.attr('data-rotation', rot);
    $it.attr('data-color', col);
    $it.attr('data-align', align);
    $it.attr('data-outline-color', strokeColor);
    $it.attr('data-outline-width', strokeWidth);
    var $txt = $it.find('.ws-text');
    $txt.css({
      'font-family': font,
      'font-weight': bold ? '700' : '400',
      'font-style': italic ? 'italic' : 'normal',
      'text-decoration': underline ? 'underline' : 'none',
      'color': col,
      'text-align': align,
      '-webkit-text-stroke': strokeWidth+'px '+strokeColor
    });
    updateItemTransform($it);
  }

function openModal(){
  checkMobile();
  updatePreviewHeight();
  loadState();
  loadAiImages();
  renderAiGallery();
  if(state.color){ $('.ws-color-overlay').css('background-color', state.color); }
  $modal.removeClass('hidden').addClass('open active');
  if (!$modal.hasClass('ws-mobile')) {
    setTimeout(function(){ $modal.find('.ws-right').addClass('show'); }, 10);
  }
  openTab('gallery');
  if(activeItem){ updateDebug(activeItem); }
  applyClip();
  updateDisplayedPrice();
  debugHiddenElements();
}

  window.openWinShirtModal = function(productId){
    openModal();
  };

  function openTab(tab){
    activeTab = tab;
    $('.ws-panel-btn').removeClass('active');
    $('.ws-panel-btn[data-tab="'+tab+'"]').addClass('active');
    $('.ws-tab-content').addClass('hidden').removeClass('active');
    $('#ws-tab-'+tab).removeClass('hidden').addClass('active');
    if($tabSelect.length){ $tabSelect.val(tab); }
  }
  function closeModal(){
    $modal.removeClass('open active');
    setTimeout(function(){
      $modal.addClass('hidden');
      $modal.find('.ws-right').removeClass('show');
      $('.ws-tab-content').addClass('hidden').removeClass('active');
      activeTab = 'gallery';
      showCustomPreview();
      updateDisplayedPrice();
    }, 300);
  }

// WinShirt: ouverture du modal de personnalisation

// Délégation d’événement = fonctionne même si le bouton est injecté après coup
// Sur certains thémès mobiles, l'événement "click" peut ne pas se déclencher
// correctement. On écoute également "touchstart" pour garantir l'ouverture du
// modal sur tous les navigateurs mobiles.
$(document).on('click touchstart', '.btn-personnaliser, #btn-personnaliser', function(e){
  e.preventDefault();
  e.stopPropagation();
  if (e.stopImmediatePropagation) e.stopImmediatePropagation();
  try {
    openModal();
  } catch(err) {
    console.error('WinShirt: ouverture du modal impossible', err);
  }
});

// Debug visuel (optionnel) : log si bouton absent au DOM load
if (!$('.btn-personnaliser, #btn-personnaliser').length) {
  console.warn('WinShirt: bouton de personnalisation introuvable au DOMContentLoaded');
}

  $('#winshirt-close-modal').on('click', closeModal);
  $('.ws-modal-close-btn').on('click', closeModal);
  $('#ws-reset-visual').on('click', function(){
    $canvas.children('.ws-item[data-type="image"]').remove();
    $modal.data('default-front', initialFront);
    $modal.data('default-back', initialBack);
    $previewImg.attr('src', state.side === 'back' ? initialBack : initialFront);
    $colorsWrap.find('.ws-color-btn').removeClass('active');
    $('.ws-color-overlay').css('background-color', 'transparent');
    state.color = null;
    selectItem(null);
    saveState();
    updateDisplayedPrice();
  });
  $modal.on('click', function(e){ if($(e.target).is('.ws-modal')) closeModal(); });
  $(document).on('keyup', function(e){ if(e.key === 'Escape') closeModal(); });
  $(window).on('resize', function(){
    checkMobile();
    updatePreviewHeight();
  });



  // Interaction principale : clic sur les boutons du panneau latéral
  $('.ws-panel-btn[data-tab]').on('click', function(){
    openTab($(this).data('tab'));
  });
  $('#ws-upload-panel').on('click', function(){
    $('#ws-upload-trigger').trigger('click');
  });
  $tabSelect.on('change', function(){
    openTab($(this).val());
  });

  $('#ws-upload-trigger').on('click', function(e){
    e.preventDefault();
    $('#ws-upload-input').trigger('click');
  });
  $('#ws-upload-input').on('change', function(){
    var file = this.files[0];
    if(!file) return;
    var reader = new FileReader();
    reader.onload = function(e){ addItem('image', e.target.result); };
    reader.readAsDataURL(file);
    $(this).val('');
  });

  $('#ws-svg-upload-trigger').on('click', function(e){
    e.preventDefault();
    $('#ws-svg-upload-input').trigger('click');
  });
  $('#ws-svg-upload-input').on('change', function(){
    var file = this.files[0];
    if(!file) return;
    var reader = new FileReader();
    reader.onload = function(e){ addSvgItem(e.target.result); };
    reader.readAsText(file);
    $(this).val('');
  });
  $('#ws-svg-color-picker').on('input change', function(){
    if(activeItem && activeItem.data('type')==='svg'){
      activeItem.attr('data-color', $(this).val());
      activeItem.find('svg').css('color', $(this).val());
      $colorInput.val($(this).val());
      saveState();
    }
  });

  var typingItem = null;
  var textTimer = null;
  $('#ws-text-content').on('input', function(){
    var txt = $(this).val();
    if(!typingItem){
      if(!txt.trim()) return;
      typingItem = addItem('text', txt);
      selectItem(typingItem);
    }
    if(typingItem){
      typingItem.find('.ws-text').text(txt || ' ');
      clearTimeout(textTimer);
      textTimer = setTimeout(function(){
        applyTextStyles(typingItem);
        saveState();
      }, 150);
    }
  });

  $('#ws-add-text').on('click', function(e){
    e.preventDefault();
    typingItem = null;
    $('#ws-text-content').val('');
  });

  $('#ws-font-select').on('change', function(){
    loadFont($(this).val());
    if(activeItem && activeItem.data('type')==='text'){
      applyTextStyles(activeItem);
      saveState();
    }
  });
  $('#ws-bold-btn, #ws-italic-btn, #ws-underline-btn').on('click', function(){
    $(this).toggleClass('active');
    if(activeItem && activeItem.data('type')==='text'){
      applyTextStyles(activeItem);
      saveState();
    }
  });
  $('#ws-color-picker, #ws-scale-range, #ws-rotate-range, #ws-text-align, #ws-outline-color, #ws-outline-width').on('input change', function(){
    if(activeItem && activeItem.data('type')==='text'){
      applyTextStyles(activeItem);
      saveState();
    }
  });
  $formatSelect.on('change', function(){
    if(!activeItem) return;
    applyFormat(activeItem, $(this).val());
  });


  function addItem(type, content){
    // Supprime l'image existante sur la même face si besoin
    if(type === 'image') {
      $canvas.children('.ws-item[data-type="image"][data-side="'+state.side+'"]').remove();
    }

    var $zone = $(getContainment());
    var zoneW = $zone.width();
    var zoneH = $zone.height();
    var initSize = Math.min(zoneW, zoneH) * 0.6;
    if(!initSize || isNaN(initSize)) initSize = 120;

    var $item = $('<div class="ws-item" />')
      .attr('data-type', type)
      .attr('data-side', state.side)
      .attr('data-scale','1')
      .attr('data-rotation','0')
      .attr('data-x','0').attr('data-y','0')
      .css({
        width: initSize,
        height: initSize,
        left: 0,
        top: 0,
        maxWidth: '100%',
        maxHeight: '100%'
      });

    if(type === 'image'){
      $item.append('<img src="'+content+'" alt="" style="width:100%;height:100%;pointer-events:none;"/>');
    } else if(type === 'svg'){
      $item.append('<div class="ws-svg-wrap">'+content+'</div>');
      var col = $('#ws-svg-color-picker').val() || '#000000';
      $item.attr('data-color', col);
      $item.find('svg').css('color', col);
    } else {
      $item.append('<span class="ws-text">'+content+'</span>');
      var col = $('#ws-color-picker').val() || '#000000';
      var align = $('#ws-text-align').val() || 'center';
      var strokeColor = $('#ws-outline-color').val() || '#000000';
      var strokeWidth = parseInt($('#ws-outline-width').val() || 0,10);
      $item.attr('data-color', col)
           .attr('data-align', align)
           .attr('data-outline-color', strokeColor)
           .attr('data-outline-width', strokeWidth);
      $item.find('.ws-text').css({'color':col,'text-align':align,'-webkit-text-stroke':strokeWidth+'px '+strokeColor});
    }

    $item.append('<div class="ws-zone-resize"></div>');
    $item.append('<button class="ws-remove" title="Supprimer">×</button>');
    $canvas.append($item);

    // Centre dans la zone d'impression
    var zonePos = $zone.position();
    var itemW = $item.width();
    var itemH = $item.height();
    $item.attr('data-x', zonePos.left + (zoneW - itemW)/2)
         .attr('data-y', zonePos.top + (zoneH - itemH)/2);
    $item.css({left:0, top:0});
    updateItemTransform($item);

    // --- DRAG & DROP ---
    var isDragging = false, dragStart = {};
    $item.on('mousedown touchstart', function(e){
      if($(e.target).is('.ws-remove, .ws-zone-resize')) return;
      isDragging = true;
      var oe = (e.type === 'touchstart') ? e.originalEvent.touches[0] : e;
      dragStart.x = oe.clientX;
      dragStart.y = oe.clientY;
      dragStart.itemX = parseFloat($item.attr('data-x'));
      dragStart.itemY = parseFloat($item.attr('data-y'));
      $(window).on('mousemove touchmove', dragMove);
      $(window).on('mouseup touchend', dragEnd);
      $item.addClass('ws-selected');
    });

    function dragMove(e){
      if(!isDragging) return;
      var oe = (e.type === 'touchmove') ? e.originalEvent.touches[0] : e;
      var dx = oe.clientX - dragStart.x;
      var dy = oe.clientY - dragStart.y;
      var zonePos = $zone.position();
      var zoneW = $zone.width(), zoneH = $zone.height();
      var scale = parseFloat($item.attr('data-scale') || 1);
      var itemW = $item.width() * scale, itemH = $item.height() * scale;
      var newX = Math.min(Math.max(zonePos.left, dragStart.itemX + dx), zonePos.left + zoneW - itemW);
      var newY = Math.min(Math.max(zonePos.top, dragStart.itemY + dy), zonePos.top + zoneH - itemH);
      $item.attr('data-x', newX).attr('data-y', newY);
      updateItemTransform($item);
    }
    function dragEnd(e){
      if(!isDragging) return;
      isDragging = false;
      $(window).off('mousemove touchmove', dragMove);
      $(window).off('mouseup touchend', dragEnd);
      saveState();
    }

    // --- RESIZE ---
    $item.find('.ws-zone-resize').on('mousedown touchstart', function(e){
      e.stopPropagation();
      var oe = (e.type === 'touchstart') ? e.originalEvent.touches[0] : e;
      var resizeStart = {
        x: oe.clientX,
        y: oe.clientY,
        w: $item.width(),
        h: $item.height()
      };
      $(window).on('mousemove touchmove', resizeMove);
      $(window).on('mouseup touchend', resizeEnd);
      function resizeMove(e2){
        var oe2 = (e2.type === 'touchmove') ? e2.originalEvent.touches[0] : e2;
        var dx = oe2.clientX - resizeStart.x;
        var dy = oe2.clientY - resizeStart.y;
        var newW = Math.max(32, resizeStart.w + dx);
        var newH = Math.max(32, resizeStart.h + dy);
        var zonePos = $zone.position();
        var zoneW = $zone.width();
        var zoneH = $zone.height();
        var itemX = parseFloat($item.attr('data-x')) - zonePos.left;
        var itemY = parseFloat($item.attr('data-y')) - zonePos.top;
        newW = Math.min(newW, zoneW - itemX);
        newH = Math.min(newH, zoneH - itemY);
        $item.css({width: newW, height: newH});
        updateItemTransform($item);
      }
      function resizeEnd(){
        $(window).off('mousemove touchmove', resizeMove);
        $(window).off('mouseup touchend', resizeEnd);
        saveState();
      }
    });

    // --- SUPPRESSION ---
    $item.find('.ws-remove').on('click', function(e){
      e.preventDefault();
      $item.remove();
      saveState();
    });

    // --- SELECTION ---
    $item.on('mousedown touchstart', function(e){
      $('.ws-item').removeClass('ws-selected');
      $item.addClass('ws-selected');
    });

  updateItemTransform($item);
  saveState();
  return $item;
  }

  function sanitizeSvg(text){
    text = text.replace(/<rect[^>]+fill="(white|#fff|#ffffff)"[^>]*>.*?<\/rect>/ig,'');
    text = text.replace(/style="[^"]*fill[^"]*"/ig,'');
    text = text.replace(/<path[^>]+>/ig,function(t){
      t = t.replace(/fill="[^"]*"/ig,'');
      return t.replace(/<path/,'<path fill="currentColor"');
    });
    if(!/viewBox=/i.test(text)){
      var w=text.match(/width="(\d+)/i); var h=text.match(/height="(\d+)/i);
      if(w&&h){ text=text.replace(/<svg/i,'<svg viewBox="0 0 '+w[1]+' '+h[1]+'"'); }
    }
    return text;
  }

  function addSvgItem(text){
    var clean = sanitizeSvg(text);
    var $it = addItem('svg', clean);
    selectItem($it);
  }

  $(document).on('click', '.ws-remove', function(e){
    e.preventDefault();
    $(this).closest('.ws-item').remove();
    saveState();
  });

  $(document).on('mousedown', '.ws-item', function(e){
    if($(e.target).is('.ws-remove')) return;
    selectItem($(this));
  });
  $canvas.on('touchstart', '.ws-item', function(e){ e.stopPropagation(); });
  $canvas.on('touchmove', '.ws-item', function(e){ e.preventDefault(); });

  function selectItem($it){
    $('.ws-item').removeClass('ws-selected');
    activeItem = $it && $it.length ? $it : null;
    if(activeItem){
      activeItem.addClass('ws-selected');
      $scaleInput.val(activeItem.attr('data-scale') || 1);
      $rotateInput.val(activeItem.attr('data-rotation') || 0);
      if(activeItem.data('type') === 'text' || activeItem.data('type') === 'svg'){
        var col = activeItem.attr('data-color') || '#000000';
        $colorInput.val(col);
        if(activeItem.data('type') === 'svg'){ $('#ws-svg-color-picker').val(col); }
        $colorInput.closest('label').show();
        $removeBgBtn.addClass('hidden');
        if(activeItem.data('type') === 'text'){
          $('#ws-text-align').val(activeItem.attr('data-align') || 'center');
          $('#ws-outline-color').val(activeItem.attr('data-outline-color') || '#000000');
          $('#ws-outline-width').val(activeItem.attr('data-outline-width') || 0);
        }
      } else {
        $colorInput.closest('label').hide();
        if(activeItem.data('type') === 'image'){
          $removeBgBtn.removeClass('hidden');
        } else {
          $removeBgBtn.addClass('hidden');
        }
      }
      updateFormatUIFromItem(activeItem);
      updateDebug(activeItem);
      $sidebar.addClass('show');
    } else {
      $sidebar.removeClass('show');
      if($formatSelect.length){ $formatSelect.val('A3'); }
      $removeBgBtn.addClass('hidden');
    }
  }

  $scaleInput.on('input change', function(){
    if(!activeItem) return;
    activeItem.attr('data-scale', $(this).val());
    updateItemTransform(activeItem);
    updateFormatUIFromItem(activeItem);
    updateDebug(activeItem);
    showTooltip('Taille estimée : '+detectFormat(activeItem));
    saveState();
  });
  $rotateInput.on('input change', function(){
    if(!activeItem) return;
    activeItem.attr('data-rotation', $(this).val());
    updateItemTransform(activeItem);
    updateDebug(activeItem);
    showTooltip('Taille estimée : '+detectFormat(activeItem));
    saveState();
  });
  $colorInput.on('input change', function(){
    if(!activeItem) return;
    if(activeItem.data('type')==='text'){
      activeItem.attr('data-color', $(this).val());
      activeItem.find('.ws-text').css('color', $(this).val());
    } else if(activeItem.data('type')==='svg'){
      activeItem.attr('data-color', $(this).val());
      activeItem.find('svg').css('color', $(this).val());
      $('#ws-svg-color-picker').val($(this).val());
    } else {
      return;
    }
    saveState();
  });
  $deleteBtn.on('click', function(){
    if(activeItem){
      activeItem.remove();
      activeItem=null;
      $sidebar.removeClass('show');
      $debug.text('');
      saveState();
    }
  });

  function removeBackground(url, cb){
    var img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = function(){
      var c = document.createElement('canvas');
      c.width = img.width;
      c.height = img.height;
      var ctx = c.getContext('2d');
      ctx.drawImage(img,0,0);
      var imageData = ctx.getImageData(0,0,c.width,c.height);
      var d = imageData.data;
      var w = c.width, h = c.height;
      var r=0,g=0,b=0,count=0;
      for(var x=0;x<w;x++){ var i=(0*w+x)*4; r+=d[i]; g+=d[i+1]; b+=d[i+2]; count++; }
      for(var x=0;x<w;x++){ var i=((h-1)*w+x)*4; r+=d[i]; g+=d[i+1]; b+=d[i+2]; count++; }
      for(var y=1;y<h-1;y++){ var i=(y*w)*4; r+=d[i]; g+=d[i+1]; b+=d[i+2]; count++; }
      for(var y=1;y<h-1;y++){ var i=(y*w+(w-1))*4; r+=d[i]; g+=d[i+1]; b+=d[i+2]; count++; }
      var r0=Math.round(r/count), g0=Math.round(g/count), b0=Math.round(b/count);
      var tol=60;
      for(var i=0;i<d.length;i+=4){
        var diff=Math.abs(d[i]-r0)+Math.abs(d[i+1]-g0)+Math.abs(d[i+2]-b0);
        if(diff<tol){ d[i+3]=0; }
      }
      ctx.putImageData(imageData,0,0);
      cb(c.toDataURL('image/png'));
    };
    img.src=url;
  }

  $removeBgBtn.on('click', function(){
    if(!activeItem || activeItem.data('type')!=='image') return;
    var src = activeItem.find('img').attr('src');
    removeBackground(src, function(newUrl){
      activeItem.find('img').attr('src', newUrl);
      saveState();
    });
  });

  $('#ws-reset-btn').on('click', function(e){
    e.preventDefault();
    $canvas.empty();
    $modal.data('default-front', initialFront);
    $modal.data('default-back', initialBack);
    $previewImg.attr('src', state.side==='back' ? initialBack : initialFront);
    $colorsWrap.find('.ws-color-btn').removeClass('active');
    selectItem(null);
    saveState();
  });

  function switchSide(side){
    state.side = side;
    if(side === 'back'){
      $('#winshirt-back-btn').addClass('active');
      $('#winshirt-front-btn').removeClass('active');
      $previewImg.attr('src', $modal.data('default-back'));
      $canvas.children('.ws-item').hide().filter('[data-side="back"]').show();
    } else {
      $('#winshirt-front-btn').addClass('active');
      $('#winshirt-back-btn').removeClass('active');
      $previewImg.attr('src', $modal.data('default-front'));
      $canvas.children('.ws-item').hide().filter('[data-side="front"]').show();
    }
    var next = zones.findIndex(function(z){ return z.side === side; });
    if(next >= 0){
      if(typeof state.zoneSel[side] === 'undefined') state.zoneSel[side] = next;
      selectZone(state.zoneSel[side]);
    }
    updateZoneButtons();
    if(activeItem){ updateFormatUIFromItem(activeItem); }
    applyClip();
    if(activeItem){ updateDebug(activeItem); }
    saveState();
  }
  $('#winshirt-front-btn').on('click', function(){ switchSide('front'); });
  $('#winshirt-back-btn').on('click', function(){ switchSide('back'); });

  // Validation finale : enregistre puis ferme la modale
  $('#btn-valider-personnalisation').on('click', function(){
    var items = [];
    $canvas.children('.ws-item').each(function(){
      var $it = $(this);
      var pos = {left: parseFloat($it.attr('data-x') || 0), top: parseFloat($it.attr('data-y') || 0)};
      items.push({
        type: $it.data('type'),
        side: $it.data('side'),
        position: { x: (pos.left / $canvas.width()).toFixed(4), y: (pos.top / $canvas.height()).toFixed(4) },
        scale: parseFloat($it.attr('data-scale') || 1),
        rotation: parseInt($it.attr('data-rotation') || 0,10),
        color: $it.attr('data-color') || null,
        width: ($it.width() / $canvas.width()).toFixed(4),
        height: ($it.height() / $canvas.height()).toFixed(4),
        content: $it.data('type') === 'text' ? $it.find('.ws-text').text() : $it.find('img').attr('src')
      });
    });
    var json = JSON.stringify(items);
    $('#winshirt-custom-data').val(json);
    if($customField.length){ $customField.val(json); }
    console.log('WinShirt data', JSON.stringify(items));
    saveState();
    syncState();
    captureAllSides().then(function(){
      if(window.dataLayer){ dataLayer.push({event:'customize_completed', product_id:$modal.data('product-id')}); }
      closeModal();
    });
  });

  // Test capture sans fermer la modale
  $('#btn-test-capture').on('click', function(){
    captureAllSides().then(function(){
      alert('Capture enregistrée');
    });
  });
  switchSide('front');
  openTab('gallery');
  if(localStorage.getItem('winshirt_resume') === '1'){
    localStorage.removeItem('winshirt_resume');
    openModal();
  }
  return true;
}

  if(!init()){
    var observer = new MutationObserver(function(){ if(init()){ observer.disconnect(); } });
    observer.observe(document.body,{childList:true,subtree:true});
  }
});
jQuery(function($){
  var $basic = $('#winshirt-modal');
  if(!$basic.length) return;

  function showSection(id){
    $basic.find('.winshirt-tab').removeClass('active');
    $basic.find('#'+id).addClass('active');
    $basic.find('.winshirt-btn').removeClass('active');
    $basic.find('.winshirt-btn[data-target="'+id+'"]').addClass('active');
  }

  $basic.on('click', '.winshirt-btn', function(e){
    e.preventDefault();
    showSection($(this).data('target'));
  });

  var first = $basic.find('.winshirt-btn').first().data('target');
  if(first){ showSection(first); }
});
