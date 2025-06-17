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
  var activeItem = null;

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
  });

  zones.forEach(function(z){
    var $z = $modal.find('.ws-print-zone[data-side="'+z.side+'"]');
    $z.css({top:z.top+'%',left:z.left+'%',width:z.width+'%',height:z.height+'%'});
  });

  function getContainment(){
    var $zone = $modal.find('.ws-print-zone[data-side="'+state.side+'"]');
    if($zone.length && $zone.width() > 0 && $zone.height() > 0){
      return $zone;
    }
    return '.ws-preview';
  }

  function updateItemTransform($it){
    var sc = parseFloat($it.attr('data-scale') || 1);
    var rot = parseInt($it.attr('data-rotation') || 0,10);
    var x  = parseFloat($it.attr('data-x') || 0);
    var y  = parseFloat($it.attr('data-y') || 0);
    $it.css('transform','translate3d('+x+'px,'+y+'px,0) scale('+sc+') rotate('+rot+'deg)');
  }

  function openModal(){
    $modal.removeClass('hidden').addClass('open');
  }
  function closeModal(){
    $modal.removeClass('open');
    setTimeout(function(){ $modal.addClass('hidden'); }, 300);
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
  });
  $modal.on('click', function(e){ if($(e.target).is('.ws-modal')) closeModal(); });
  $(document).on('keyup', function(e){ if(e.key === 'Escape') closeModal(); });

  $('.ws-tab-button').on('click', function(){
    var tab = $(this).data('tab');
    $('.ws-tab-button').removeClass('active');
    $(this).addClass('active');
    $('.ws-tab-content').addClass('hidden').removeClass('active');
    $('#ws-tab-'+tab).removeClass('hidden').addClass('active');
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

  $('#ws-add-text').on('click', function(e){
    e.preventDefault();
    var txt = $('#ws-text-content').val().trim();
    if(!txt) return;
    var $it = addItem('text', txt);
    $('#ws-text-content').val('');
    applyTextStyles($it);
    selectItem($it);
  });

  function addItem(type, content){
    if(type === 'image') $canvas.children('.ws-item[data-type="image"]').remove();
    var $item = $('<div class="ws-item" />').attr('data-type', type).attr('data-side', state.side).attr('data-scale','1').attr('data-rotation','0').attr('data-x','0').attr('data-y','0').css({left:0,top:0});
    if(type === 'image'){
      $item.append('<img src="'+content+'" alt="" />');
      var cont = getContainment();
      var cw = $(cont).width();
      var ch = $(cont).height();
      var size = Math.min(cw, ch) * 0.5;
      $item.css({width:size,height:size});
    } else {
      $item.append('<span class="ws-text">'+content+'</span>');
      var col = $('#ws-color-picker').val() || '#000000';
      $item.attr('data-color', col);
      $item.find('.ws-text').css('color', col);
    }
    $item.append('<button class="ws-remove" title="Supprimer">×</button>');
    $canvas.append($item);
    var cont = getContainment();
    $item.draggable({ containment:cont });
    $item.resizable({ handles:'ne, se, sw, nw', containment:cont });
    updateItemTransform($item);
    return $item;
  }

  $(document).on('click', '.ws-remove', function(e){ e.preventDefault(); $(this).closest('.ws-item').remove(); });

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
      $sidebar.addClass('show');
    } else {
      $sidebar.removeClass('show');
    }
  }

  $scaleInput.on('input change', function(){
    if(!activeItem) return;
    activeItem.attr('data-scale', $(this).val());
    updateItemTransform(activeItem);
  });
  $rotateInput.on('input change', function(){
    if(!activeItem) return;
    activeItem.attr('data-rotation', $(this).val());
    updateItemTransform(activeItem);
  });
  $colorInput.on('input change', function(){
    if(!activeItem || activeItem.data('type')!=='text') return;
    activeItem.attr('data-color', $(this).val());
    activeItem.find('.ws-text').css('color', $(this).val());
  });
  $deleteBtn.on('click', function(){
    if(activeItem){ activeItem.remove(); activeItem=null; $sidebar.removeClass('show'); }
  });

  $('#ws-reset-btn').on('click', function(e){
    e.preventDefault();
    $canvas.empty();
    $modal.data('default-front', initialFront);
    $modal.data('default-back', initialBack);
    $previewImg.attr('src', state.side==='back' ? initialBack : initialFront);
    $colorsWrap.find('.ws-color-btn').removeClass('active');
    selectItem(null);
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
    closeModal();
  });

  switchSide('front');
});
