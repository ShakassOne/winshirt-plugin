jQuery(function($){
  var $modal = $('#winshirt-customizer-modal');
  if(!$modal.length) return;
  $('body').append($modal);

  var state = {side:'front'};
  var $canvas = $('#ws-canvas');
  var $previewImg = $modal.find('.ws-preview-img');
  var initialFront = $modal.data('default-front');
  var initialBack  = $modal.data('default-back');
  var $sidebar = $modal.find('.ws-sidebar');
  var $scaleInput = $('#ws-prop-scale');
  var $rotateInput = $('#ws-prop-rotate');
  var $colorInput = $('#ws-prop-color');
  var $deleteBtn = $('#ws-prop-delete');
  var colors = $modal.data('colors') || [];
  var zones  = $modal.data('zones') || [];
  var $colorsWrap = $modal.find('.ws-colors');
  var $formatWrap = $modal.find('.ws-format-buttons');
  var $formatBtns = $formatWrap.find('.ws-format-btn');
  var $formatLabel = $('#ws-current-format');
  var $zonesWrap = $('#ws-print-zones');
  var $tabSelect = $('#ws-tab-select');
  var $debug = $('#ws-debug');
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

  function showTooltip(text){
    if(!$debug.length) return;
    $debug.text(text).addClass('show');
    clearTimeout($debug.data('to'));
    $debug.data('to', setTimeout(function(){ $debug.removeClass('show'); }, 800));
  }

  function updateDebug($it){
    if(!$debug.length) return;
    var pos = $it.position();
    var info = 'x:'+Math.round(pos.left)+' y:'+Math.round(pos.top)+' w:'+Math.round($it.width())+' h:'+Math.round($it.height())+' ('+detectFormat($it)+')';
    $debug.text(info);
  }

  function applyClip(){
    var $z = $modal.find('.ws-print-zone[data-side="'+state.side+'"]').eq(0);
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
      $modal.addClass('ws-mobile');
    } else {
      $modal.removeClass('ws-mobile');
      $modal.find('.ws-right').removeClass('show');
    }
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
      var pos = $it.position();
      items.push({
        type: $it.data('type'),
        side: $it.data('side'),
        position: { x:(pos.left / $canvas.width()).toFixed(4), y:(pos.top / $canvas.height()).toFixed(4) },
        scale: parseFloat($it.attr('data-scale') || 1),
        rotation: parseInt($it.attr('data-rotation') || 0,10),
        color: $it.attr('data-color') || null,
        width: ($it.width() / $canvas.width()).toFixed(4),
        height: ($it.height() / $canvas.height()).toFixed(4),
        content: $it.data('type')==='text' ? $it.find('.ws-text').text() : $it.find('img').attr('src')
      });
    });
    var data = {
      items: items,
      defaultFront: $modal.data('default-front') || initialFront,
      defaultBack: $modal.data('default-back') || initialBack,
      side: state.side
    };
    localStorage.setItem('winshirt_custom', JSON.stringify(data));
  }

  function loadState(){
    var raw = localStorage.getItem('winshirt_custom');
    if(!raw) return;
    try{ raw = JSON.parse(raw); }catch(e){ return; }
    $canvas.empty();
    if(raw.defaultFront){ $modal.data('default-front', raw.defaultFront); }
    if(raw.defaultBack){ $modal.data('default-back', raw.defaultBack); }
    switchSide(raw.side || 'front');
    if(Array.isArray(raw.items)){
      raw.items.forEach(function(it){
        var $new = addItem(it.type, it.content);
        $new.attr('data-side', it.side || 'front');
        $new.attr('data-scale', it.scale || 1);
        $new.attr('data-rotation', it.rotation || 0);
        $new.css({
          width: parseFloat(it.width) * $canvas.width(),
          height: parseFloat(it.height) * $canvas.height(),
          left: parseFloat(it.position.x) * $canvas.width(),
          top: parseFloat(it.position.y) * $canvas.height()
        });
        if(it.color){ $new.attr('data-color', it.color); $new.find('.ws-text').css('color', it.color); }
        updateItemTransform($new);
      });
    }
  }

  var gallery = $modal.data('gallery') || [];
  var $gallery = $modal.find('.ws-gallery');
  var $cats   = $modal.find('.ws-gallery-cats');
  var cats = [];
  gallery.forEach(function(g){
    var cat = g.category || g.type || '';
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
    addItem('image', $(this).attr('src'));
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
    if(col.front){ $modal.data('default-front', col.front); if(state.side==='front') $previewImg.attr('src', col.front); }
    if(col.back){ $modal.data('default-back', col.back); if(state.side==='back') $previewImg.attr('src', col.back); }
    saveState();
  });

  zones.forEach(function(z, idx){
    var $z = $('<div class="ws-print-zone" />')
      .attr('data-side', z.side || 'front')
      .attr('data-index', idx)
      .css({top:z.top+'%',left:z.left+'%',width:z.width+'%',height:z.height+'%'});
    $zonesWrap.append($z);
  });
  applyClip();

  function getContainment(){
    var $zones = $modal.find('.ws-print-zone[data-side="'+state.side+'"]');
    if($zones.length){
      var $z = $zones.eq(0);
      if($z.width()>0 && $z.height()>0){
        return $z;
      }
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

  function updateFormatUIFromItem($it){
    var ratio = $it.height() / getBaseHeight();
    var closest = {fmt:'A7', diff:Infinity};
    formatOrder.forEach(function(f){
      var d = Math.abs(ratio - formatHeights[f]);
      if(d < closest.diff){ closest = {fmt:f, diff:d}; }
    });
    $formatBtns.removeClass('active');
    $formatBtns.filter('[data-format="'+closest.fmt+'"]').addClass('active');
    if($formatLabel.length){
      if(closest.diff < 0.02){
        $formatLabel.text('Format actuel : '+closest.fmt);
      } else {
        $formatLabel.text('Personnalisé (≈ '+closest.fmt+')');
      }
    }
  }

  function updateFormatUI(fmt){
    $formatBtns.removeClass('active');
    $formatBtns.filter('[data-format="'+fmt+'"]').addClass('active');
    if($formatLabel.length){ $formatLabel.text('Format actuel : '+fmt); }
  }

  function applyFormat($it, fmt){
    var $zone = $(getContainment());
    var zpos = $zone.position();
    var zw = $zone.width();
    var zh = $zone.height();
    var base = getBaseHeight();
    var ratio = formatHeights[fmt] || 0;
    var h = base * ratio;
    var w = h / 1.414;
    var left = zpos.left + (zw - w)/2;
    var top  = zpos.top + (zh - h)/2;
    $it.css({width:w, height:h});
    $it.attr('data-x', left).attr('data-y', top);
    updateItemTransform($it);
    updateFormatUI(fmt);
  }

  function updateItemTransform($it){
    var sc = parseFloat($it.attr('data-scale') || 1);
    var rot = parseInt($it.attr('data-rotation') || 0,10);
    var x  = parseFloat($it.attr('data-x') || 0);
    var y  = parseFloat($it.attr('data-y') || 0);
    $it.css({
      'transform':'translate('+x+'px,'+y+'px) rotate('+rot+'deg) scale('+sc+')',
      'transform-origin':'center center'
    });
  }

  function applyTextStyles($it){
    var font = $('#ws-font-select').val() || 'Arial';
    var bold = $('#ws-bold-btn').hasClass('active');
    var italic = $('#ws-italic-btn').hasClass('active');
    var underline = $('#ws-underline-btn').hasClass('active');
    var col = $('#ws-color-picker').val() || '#000000';
    var scale = parseFloat($('#ws-scale-range').val() || 1);
    var rot = parseInt($('#ws-rotate-range').val() || 0,10);
    $it.attr('data-scale', scale);
    $it.attr('data-rotation', rot);
    $it.attr('data-color', col);
    var $txt = $it.find('.ws-text');
    $txt.css({
      'font-family': font,
      'font-weight': bold ? '700' : '400',
      'font-style': italic ? 'italic' : 'normal',
      'text-decoration': underline ? 'underline' : 'none',
      'color': col
    });
    updateItemTransform($it);
  }

function openModal(){
  checkMobile();
  loadState();
  $modal.removeClass('hidden').addClass('open');
  if (!$modal.hasClass('ws-mobile')) {
    setTimeout(function(){ $modal.find('.ws-right').addClass('show'); }, 10);
  }
  openTab('gallery');
  if(activeItem){ updateDebug(activeItem); }
  applyClip();
}

  function openTab(tab){
    if($modal.hasClass('ws-mobile')){
      var $c = $('#ws-tab-'+tab);
      var $h = $('.ws-accordion-header[data-tab="'+tab+'"]');
      if(activeTab === tab && $c.hasClass('active')){
        $c.addClass('hidden').removeClass('active');
        $h.removeClass('open');
        $modal.find('.ws-right').removeClass('show');
        activeTab = null;
        return;
      }
      activeTab = tab;
      $('.ws-accordion-header').removeClass('open');
      $('.ws-tab-content').addClass('hidden').removeClass('active');
      $h.addClass('open');
      $c.removeClass('hidden').addClass('active');
      $modal.find('.ws-right').addClass('show');
      if($tabSelect.length){ $tabSelect.val(tab); }
    } else {
      $('.ws-tab-button').removeClass('active');
      $('.ws-tab-button[data-tab="'+tab+'"]').addClass('active');
      $('.ws-tab-content').addClass('hidden').removeClass('active');
      $('#ws-tab-'+tab).removeClass('hidden').addClass('active');
      if($tabSelect.length){ $tabSelect.val(tab); }
    }
  }
  function closeModal(){
    $modal.removeClass('open');
    setTimeout(function(){
      $modal.addClass('hidden');
      $modal.find('.ws-right').removeClass('show');
      $('.ws-accordion-header').removeClass('open');
      $('.ws-tab-content').addClass('hidden').removeClass('active');
      activeTab = 'gallery';
    }, 300);
  }

  $('#winshirt-open-modal').on('click', function(e){ e.preventDefault(); openModal(); });
  $('#winshirt-close-modal').on('click', closeModal);
  $('#ws-reset-visual').on('click', function(){
    $canvas.children('.ws-item[data-type="image"]').remove();
    $modal.data('default-front', initialFront);
    $modal.data('default-back', initialBack);
    $previewImg.attr('src', state.side === 'back' ? initialBack : initialFront);
    $colorsWrap.find('.ws-color-btn').removeClass('active');
    selectItem(null);
    saveState();
  });
  $modal.on('click', function(e){ if($(e.target).is('.ws-modal')) closeModal(); });
  $(document).on('keyup', function(e){ if(e.key === 'Escape') closeModal(); });
  $(window).on('resize', checkMobile);

  $('.ws-tab-button').on('click', function(){
    openTab($(this).data('tab'));
  });
  $('.ws-accordion-header').on('click', function(){
    openTab($(this).data('tab'));
  });
  $tabSelect.on('change', function(){
    openTab($(this).val());
  });

  $('.ws-upload-btn').on('click', function(){ $('#ws-upload-input').trigger('click'); });
  $('#ws-upload-input').on('change', function(){
    var file = this.files[0];
    if(!file) return;
    var reader = new FileReader();
    reader.onload = function(e){ addItem('image', e.target.result); };
    reader.readAsDataURL(file);
    $(this).val('');
  });

  var typingItem = null;
  $('#ws-text-content').on('input', function(){
    var txt = $(this).val();
    if(!typingItem){
      if(!txt.trim()) return;
      typingItem = addItem('text', txt);
      selectItem(typingItem);
    }
    if(typingItem){
      typingItem.find('.ws-text').text(txt || ' ');
      applyTextStyles(typingItem);
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
  $('#ws-color-picker, #ws-scale-range, #ws-rotate-range').on('input change', function(){
    if(activeItem && activeItem.data('type')==='text'){
      applyTextStyles(activeItem);
      saveState();
    }
  });

  $formatWrap.on('click', '.ws-format-btn', function(){
    if(!activeItem) return;
    var fmt = $(this).data('format');
    applyFormat(activeItem, fmt);
  });

  function addItem(type, content){
    if(type === 'image') $canvas.children('.ws-item[data-type="image"]').remove();
    var $item = $('<div class="ws-item" />').attr('data-type', type).attr('data-side', state.side).attr('data-scale','1').attr('data-rotation','0').attr('data-x','0').attr('data-y','0').css({left:0,top:0});
    if(type === 'image'){
      $item.append('<img src="'+content+'" alt="" />');
      var cont = getContainment();
      var cw = $(cont).width();
      var ch = $(cont).height();
      if(!cw || !ch){
        cw = $canvas.width();
        ch = $canvas.height();
      }
      var size = Math.min(cw, ch) * 0.5;
      if(!size){ size = 100; }
      $item.css({width:size,height:size});
    } else {
      $item.append('<span class="ws-text">'+content+'</span>');
      var cont = getContainment();
      var cw = $(cont).width();
      var ch = $(cont).height();
      if(!cw || !ch){
        cw = $canvas.width();
        ch = $canvas.height();
      }
      var size = Math.min(cw, ch) * 0.3;
      if(!size){ size = 100; }
      $item.css({width:size,height:size});
      var col = $('#ws-color-picker').val() || '#000000';
      $item.attr('data-color', col);
      $item.find('.ws-text').css('color', col);
    }
    $item.append('<button class="ws-remove" title="Supprimer">×</button>');
    $canvas.append($item);
    applyClip();
    var cont = getContainment();
    var $cont = $(cont);
    var cpos = $cont.position();
    var cw = $cont.width();
    var ch = $cont.height();
    if(!cw || !ch){
      cw = $canvas.width();
      ch = $canvas.height();
      cpos = {left:0, top:0};
    }
    var left = cpos.left + (cw - $item.width())/2;
    var top  = cpos.top + (ch - $item.height())/2;
    $item.attr('data-x', left).attr('data-y', top);
    updateItemTransform($item);
    $item.draggable({
      containment: cont,
      start: function(e, ui){
        var $t = $(this);
        $t.data('dragStartX', parseFloat($t.attr('data-x')) || 0);
        $t.data('dragStartY', parseFloat($t.attr('data-y')) || 0);
        ui.position.left = 0;
        ui.position.top = 0;
      },
      drag: function(e, ui){
        var $t = $(this);
        var newX = $t.data('dragStartX') + ui.position.left;
        var newY = $t.data('dragStartY') + ui.position.top;
        $t.attr('data-x', newX);
        $t.attr('data-y', newY);
        updateItemTransform($t);
        ui.position.left = 0;
        ui.position.top = 0;
        updateDebug($t);
      },
      stop: function(e, ui){
        var $t = $(this);
        var finalX = $t.data('dragStartX') + ui.position.left;
        var finalY = $t.data('dragStartY') + ui.position.top;
        $t.attr('data-x', finalX);
        $t.attr('data-y', finalY);
        $t.css({left:0, top:0});
        updateItemTransform($t);
        updateDebug($t);
        showTooltip('Taille estimée : '+detectFormat($t));
        saveState();
      }
    });
    $item.resizable({ handles:'ne, se, sw, nw', containment:cont })
      .on('resizestart', function(e, ui){
        var $t = $(this);
        $t.data('resizeStartX', parseFloat($t.attr('data-x')) || 0);
        $t.data('resizeStartY', parseFloat($t.attr('data-y')) || 0);
        ui.position.left = 0;
        ui.position.top = 0;
      })
      .on('resize', function(e, ui){
        var $t = $(this);
        var newX = $t.data('resizeStartX') + ui.position.left;
        var newY = $t.data('resizeStartY') + ui.position.top;
        $t.attr('data-x', newX);
        $t.attr('data-y', newY);
        updateItemTransform($t);
        ui.position.left = 0;
        ui.position.top = 0;
        updateDebug($t);
      })
      .on('resizestop', function(e, ui){
        var $t = $(this);
        var finalX = $t.data('resizeStartX') + ui.position.left;
        var finalY = $t.data('resizeStartY') + ui.position.top;
        $t.attr('data-x', finalX);
        $t.attr('data-y', finalY);
        $t.css({left:0, top:0});
        clearTimeout($t.data('rt'));
        $t.data('rt', setTimeout(function(){
          updateFormatUIFromItem($t);
        }, 100));
        updateDebug($t);
        showTooltip('Taille estimée : '+detectFormat($t));
        saveState();
      });
    updateItemTransform($item);
    updateFormatUIFromItem($item);
    saveState();
    return $item;
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
      if(activeItem.data('type') === 'text'){
        $colorInput.val(activeItem.attr('data-color') || '#000000');
        $colorInput.closest('label').show();
      } else {
        $colorInput.closest('label').hide();
      }
      updateFormatUIFromItem(activeItem);
      updateDebug(activeItem);
      $sidebar.addClass('show');
    } else {
      $sidebar.removeClass('show');
      $formatBtns.removeClass('active');
      $formatLabel.text('');
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
    if(!activeItem || activeItem.data('type')!=='text') return;
    activeItem.attr('data-color', $(this).val());
    activeItem.find('.ws-text').css('color', $(this).val());
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
      $modal.find('.ws-print-zone').hide().filter('[data-side="back"]').show();
    } else {
      $('#winshirt-front-btn').addClass('active');
      $('#winshirt-back-btn').removeClass('active');
      $previewImg.attr('src', $modal.data('default-front'));
      $canvas.children('.ws-item').hide().filter('[data-side="front"]').show();
      $modal.find('.ws-print-zone').hide().filter('[data-side="front"]').show();
    }
    if(activeItem){ updateFormatUIFromItem(activeItem); }
    applyClip();
    if(activeItem){ updateDebug(activeItem); }
    saveState();
  }
  $('#winshirt-front-btn').on('click', function(){ switchSide('front'); });
  $('#winshirt-back-btn').on('click', function(){ switchSide('back'); });

  $('#winshirt-validate').on('click', function(){
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
    $('#winshirt-custom-data').val(JSON.stringify(items));
    console.log('WinShirt data', JSON.stringify(items));
    saveState();
    closeModal();
  });

  switchSide('front');
  openTab('gallery');
});
