jQuery(function($){
  var $modal = $('#winshirt-customizer-modal');
  if(!$modal.length) return;

  var state = JSON.parse(localStorage.getItem('winshirt_state')) || {front:'', back:'', text:'', side:'front'};
  var $previewImg = $modal.find('.ws-preview-img');

  function saveState(){
    localStorage.setItem('winshirt_state', JSON.stringify(state));
  }

  function loadState(){
    var frontDefault = $modal.data('default-front') || '';
    var backDefault  = $modal.data('default-back') || '';
    if(state.side === 'back'){
      $('#winshirt-back-btn').addClass('active');
      $('#winshirt-front-btn').removeClass('active');
      $previewImg.attr('src', state.back || backDefault);
    }else{
      $('#winshirt-front-btn').addClass('active');
      $('#winshirt-back-btn').removeClass('active');
      $previewImg.attr('src', state.front || frontDefault);
    }
    if(state.text){
      $('#winshirt-text-input').val(state.text);
    }
  }

  function switchSide(side){
    state.side = side;
    loadState();
    saveState();
  }

  $('#winshirt-open-modal').on('click', function(e){
    e.preventDefault();
    $modal.removeClass('hidden');
    loadState();
  });

  $('#winshirt-close-modal').on('click', function(){
    $modal.addClass('hidden');
  });

  $('.ws-tab-button').on('click', function(){
    var tab = $(this).data('tab');
    $('.ws-tab-button').removeClass('active');
    $(this).addClass('active');
    $('.ws-tab-content').removeClass('active').addClass('hidden');
    $('#ws-tab-'+tab).addClass('active').removeClass('hidden');
  });

  $('.ws-upload-btn').on('click', function(){
    $('#ws-upload-input').trigger('click');
  });

  $('#ws-upload-input').on('change', function(){
    var input = this;
    if(!input.files.length) return;
    var reader = new FileReader();
    reader.onload = function(e){
      if(state.side === 'back'){
        state.back = e.target.result;
      }else{
        state.front = e.target.result;
      }
      loadState();
      saveState();
    };
    reader.readAsDataURL(input.files[0]);
  });

  $('#winshirt-text-input').on('input', function(){
    state.text = $(this).val();
    saveState();
  });

  $('#winshirt-front-btn').on('click', function(){ switchSide('front'); });
  $('#winshirt-back-btn').on('click', function(){ switchSide('back'); });

  // Draggable + resizable zone
  $('#design-zone').draggable({containment:'.ws-preview'}).resizable({containment:'.ws-preview'});

  loadState();
});
