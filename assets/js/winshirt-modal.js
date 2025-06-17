jQuery(function($){
  var $modal = $('#winshirt-customizer-modal');
  if(!$modal.length) return;

  var state = {side:'front'};
  var $canvas = $('#ws-canvas');
  var $previewImg = $modal.find('.ws-preview-img');
  var colors = $modal.data('colors') || [];
  var zones  = $modal.data('zones') || [];
  var $colorsWrap = $modal.find('.ws-colors');
  var activeItem = null;

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
    return $zone.length ? $zone : '.ws-preview';
  }

  function openModal(){
    $modal.removeClass('hidden').addClass('open');
  }
  function closeModal(){
    $modal.removeClass('open');
    setTimeout(function(){ $modal.addClass('hidden'); }, 300);
  }

  $('#winshirt-open-modal').on('click', function(e){
    e.preventDefault();
    openModal();
  });
  $('#winshirt-close-modal').on('click', closeModal);
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
    var $item = $('<div class="ws-item" />').attr('data-type', type).attr('data-side', state.side);
    if(type === 'image'){
      $item.append('<img src="'+content+'" alt="" />');
    }else{
      $item.append('<span class="ws-text">'+content+'</span>');
    }
    $item.append('<button class="ws-remove" title="Supprimer">×</button>');
    $canvas.append($item);
    var cont = getContainment();
    $item.draggable({containment:cont,snap:cont,snapTolerance:10});
    $item.resizable({handles:'n, e, s, w, ne, se, sw, nw',containment:cont,snap:cont,snapTolerance:10});
    return $item;
  }

  $(document).on('click', '.ws-remove', function(e){
    e.preventDefault();
    $(this).closest('.ws-item').remove();
  });

  $(document).on('mousedown', '.ws-item', function(e){
    if($(e.target).is('.ws-remove')) return;
    selectItem($(this));
  });

  function selectItem($it){
    $('.ws-item').removeClass('ws-selected');
    activeItem = $it;
    if($it) $it.addClass('ws-selected');
  }

  function applyTextStyles($it){
    if(!$it) return;
    $it.find('.ws-text').css({
      'font-family': $('#ws-font-select').val(),
      'color': $('#ws-color-picker').val()
    });
    $it.toggleClass('bold', $('#ws-bold-btn').hasClass('active'));
    $it.toggleClass('italic', $('#ws-italic-btn').hasClass('active'));
    $it.toggleClass('underline', $('#ws-underline-btn').hasClass('active'));
    var sc = parseFloat($('#ws-scale-range').val());
    var rot = parseInt($('#ws-rotate-range').val(),10);
    $it.css('transform','scale('+sc+') rotate('+rot+'deg)');
  }

  $('#ws-font-select,#ws-color-picker,#ws-scale-range,#ws-rotate-range,#ws-text-content').on('input change', function(){
    applyTextStyles(activeItem);
  });
  $('#ws-bold-btn,#ws-italic-btn,#ws-underline-btn').on('click', function(){
    $(this).toggleClass('active');
    applyTextStyles(activeItem);
  });

  function switchSide(side){
    state.side = side;
    if(side === 'back'){
      $('#winshirt-back-btn').addClass('active');
      $('#winshirt-front-btn').removeClass('active');
      $previewImg.attr('src', $modal.data('default-back'));
      $canvas.children('.ws-item').hide().filter('[data-side="back"]').show();
      $modal.find('.ws-print-zone').hide().filter('[data-side="back"]').show();
    }else{
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
      var pos = $it.position();
      items.push({
        type: $it.data('type'),
        side: $it.data('side'),
        left: (pos.left / $canvas.width()).toFixed(4),
        top: (pos.top / $canvas.height()).toFixed(4),
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
