jQuery(function($){
  var $button  = $('.single_add_to_cart_button');
  var $selects = $('.winshirt-lottery-select');
  var $custom  = $('#winshirt-custom-data');

  if(!$button.length){
    return;
  }

  var requiresLottery = $selects.length > 0;
  var requiresCustom  = $custom.length > 0;
  if(!requiresLottery && !requiresCustom){
    return;
  }

  var $warning = $('<p class="winshirt-lottery-warning winshirt-theme-inherit"></p>');
  $warning.insertAfter($button.first());

  function anySelected(){
    var ok = false;
    $selects.each(function(){
      var v = $(this).val();
      if(v && v !== '0'){ ok = true; return false; }
    });
    return ok;
  }

  function customValid(){
    return !requiresCustom || ($custom.val() && $custom.val().length > 2);
  }

  function updateState(){
    var okLottery = !requiresLottery || anySelected();
    var okCustom  = customValid();

    if(okLottery && okCustom){
      $button.prop('disabled', false);
      $warning.hide();
    }else{
      $button.prop('disabled', true);
      var parts = [];
      if(requiresLottery && !okLottery){ parts.push('une loterie'); }
      if(requiresCustom && !okCustom){ parts.push('votre personnalisation'); }
      $warning.text('Veuillez sélectionner '+parts.join(' et ')+' avant d\u2019ajouter au panier.');
      $warning.show();
    }
  }

  $selects.on('change', updateState);
  $custom.on('change', updateState);
  $('#winshirt-validate').on('click', function(){
    setTimeout(updateState, 100);
  });

  updateState();
});
