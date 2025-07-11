jQuery(function($){
  var $form    = $('form.cart');
  var $button  = $form.find('.single_add_to_cart_button');
  var $selects = $('.winshirt-lottery-select');
  var $custom  = $('#winshirt-custom-data');
  var productId = parseInt($form.find('input[name="add-to-cart"]').val()||0,10);

  if(!$button.length){
    return;
  }

  var requiresLottery = $selects.length > 0;
  var requiresCustom  = $custom.length > 0;
  if(!requiresLottery && !requiresCustom){
    return;
  }

  var $tooltip = $('<div class="winshirt-lottery-tooltip winshirt-theme-inherit"></div>').hide();
  $button.after($tooltip);
  var timeout = null;

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

  $form.on('submit', function(e){
    var okLottery = !requiresLottery || anySelected();
    var okCustom  = customValid();
    if(!(okLottery && okCustom)){
      e.preventDefault();
      var parts = [];
      if(requiresLottery && !okLottery){ parts.push('une loterie'); }
      if(requiresCustom && !okCustom){ parts.push('votre personnalisation'); }
      $tooltip.text('Veuillez sélectionner '+parts.join(' et ')+' avant d\u2019ajouter au panier.')
             .fadeIn(120);
      clearTimeout(timeout);
      timeout = setTimeout(function(){ $tooltip.fadeOut(200); }, 4000);
    } else {
      if(window.dataLayer){ dataLayer.push({event:'add_to_cart', product_id:productId, customization:$custom.val()}); }
    }
  });
});
