jQuery(function($){
  var $selects = $('.winshirt-lottery-select');
  var $button = $('.single_add_to_cart_button');
  if(!$selects.length || !$button.length){
    return;
  }

  var warningText = 'Aucune loterie s\xC3\xA9lectionn\xC3\xA9e : vous serez automatiquement inscrit(e) \xC3\xA0 la loterie la plus proche du tirage.';
  var $warning = $('<p class="winshirt-lottery-warning"></p>').text(warningText);
  $warning.insertAfter($button.first());

  function anySelected(){
    var ok = false;
    $selects.each(function(){
      var v = $(this).val();
      if(v && v !== '0'){ ok = true; return false; }
    });
    return ok;
  }

  function updateState(){
    if(anySelected()){
      $button.prop('disabled', false);
      $warning.hide();
    }else{
      $button.prop('disabled', true);
      $warning.show();
    }
  }

  function parseData($opt){
    var data = $opt.data('info');
    if(!data) return {};
    if(typeof data === 'string'){
      try{ data = JSON.parse(data); }catch(e){ data = {}; }
    }
    return data || {};
  }

  function getBestLottery(){
    var best = null;
    var bestRatio = -1;
    var $options = $selects.eq(0).find('option');
    $options.each(function(){
      var $opt = $(this);
      var val = $opt.val();
      if(!val || val === '0') return;
      var data = parseData($opt);
      var goal = parseInt(data.goal || 0, 10);
      var count = parseInt(data.participants || 0, 10);
      if(goal > 0 && count >= goal) return;
      var ratio = goal > 0 ? count / goal : 0;
      if(ratio > bestRatio){
        bestRatio = ratio;
        best = val;
      }
    });
    return best;
  }

  $selects.on('change', updateState);
  updateState();

  var submitting = false;
  $('form.cart').on('submit', function(e){
    if(submitting || anySelected()) return;
    e.preventDefault();
    var best = getBestLottery();
    if(best){
      $selects.each(function(){
        if(!$(this).val()){
          $(this).val(best).trigger('change');
        }
      });
      submitting = true;
      setTimeout(() => { $(this).trigger('submit'); }, 0);
    }
  });
});
