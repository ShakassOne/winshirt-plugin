jQuery(function($){
  var $modal = $('#winshirt-customizer-modal');
  if(!$modal.length) return;

  var state = {side:'front'};
  var $canvas = $('#ws-canvas');
  var $previewImg = $modal.find('.ws-preview-img');

  function openModal(){
    $modal.removeClass('hidden');
  }
  function closeModal(){
    $modal.addClass('hidden');
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

  $('#winshirt-text-input').on('keydown', function(e){
    if(e.key === 'Enter'){
      e.preventDefault();
      var txt = $(this).val().trim();
      if(txt){
        addItem('text', txt);
        $(this).val('');
      }
    }
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
    $item.draggable({containment:'.ws-preview',snap:'.ws-preview',snapTolerance:10});
    $item.resizable({handles:'n, e, s, w, ne, se, sw, nw',containment:'.ws-preview',snap:'.ws-preview',snapTolerance:10});
  }

  $(document).on('click', '.ws-remove', function(e){
    e.preventDefault();
    $(this).closest('.ws-item').remove();
  });

  function switchSide(side){
    state.side = side;
    if(side === 'back'){
      $('#winshirt-back-btn').addClass('active');
      $('#winshirt-front-btn').removeClass('active');
      $previewImg.attr('src', $modal.data('default-back'));
      $canvas.children('.ws-item').hide().filter('[data-side="back"]').show();
    }else{
      $('#winshirt-front-btn').addClass('active');
      $('#winshirt-back-btn').removeClass('active');
      $previewImg.attr('src', $modal.data('default-front'));
      $canvas.children('.ws-item').hide().filter('[data-side="front"]').show();
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
