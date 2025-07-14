jQuery(function($){
  var $selects   = $('.winshirt-lottery-select');
  if(!$selects.length){
    return; // nothing to manage if no selectors present
  }
  var $containers = $('.loteries-container');
  if(!$containers.length){
    return;
  }
  if($containers.length > 1){
    $containers.slice(1).remove();
  }
  var $container = $containers.first();

  function getSelectedLotteries(){
    var lots = [];
    $selects.each(function(index){
      var $select = $(this);
      var $opt    = $select.find('option:selected');
      var lid     = $opt.val();
      if(!lid){
        return;
      }
      var data = $opt.data('info');
      if(typeof data === 'string'){
        try{ data = JSON.parse(data); }catch(e){ data = {}; }
      }
      data = data || {};
      lots.push({ id: lid, data: data, selectIndex: index, $select: $select, text: $opt.text() });
    });
    return lots;
  }

  function getUniqueLoteries(loteries){
    return loteries.filter(function(lot, idx, arr){
      return arr.findIndex(function(l){ return l.id === lot.id; }) === idx;
    });
  }

  function renderLoteries(loteries){
    $container.empty();
    loteries.forEach(function(lot, cardIndex){
      var percent = lot.data.goal ? Math.min(100, Math.round((lot.data.participants / lot.data.goal) * 100)) : 0;
      var badge   = lot.data.featured ? '<span class="loterie-badge">BEST</span>' : (lot.data.active ? '<span class="loterie-badge">NOUVEAU</span>' : '');
      var price   = lot.data.value ? '<span class="loterie-price">'+lot.data.value+'€</span>' : '';
      var html    = '<div class="loterie-card winshirt-theme-inherit" data-index="'+cardIndex+'" data-lottery="'+lot.id+'">'+
        badge+
        '<button type="button" class="loterie-remove winshirt-theme-inherit" aria-label="Retirer">&times;</button>'+
        (lot.data.image ? '<img class="loterie-img winshirt-theme-inherit" src="'+lot.data.image+'" alt="" />' : '')+
        '<div class="loterie-info winshirt-theme-inherit">'+
          '<span class="loterie-title">'+(lot.data.name || lot.text)+'</span>'+
          '<div class="loterie-meta">'+
            price+
            '<span class="loterie-participants">'+lot.data.participants+' / '+lot.data.goal+' participants</span>'+
          '</div>'+
          '<div class="loterie-bar-bg">'+
            '<div class="loterie-bar" style="width:'+percent+'%"></div>'+
            '<div class="loterie-tooltip">'+percent+'% rempli ('+lot.data.participants+' sur '+lot.data.goal+')</div>'+
          '</div>'+
        '</div>'+
      '</div>';

      var $card = $(html).appendTo($container);
      $card.find('.loterie-remove').on('click', function(e){
        e.preventDefault();
        removeLoterie(lot.id);
      });
    });
  }

  function removeLoterie(id){
    $selects.each(function(){
      var $select = $(this);
      if($select.val() == id){
        $select.val('');
        if($select.data('select2')){
          $select.trigger('change');
        }else{
          $select.trigger('input').trigger('change');
        }
      }
    });
    renderAll();
  }

  function renderAll(){
    var selected = getSelectedLotteries();
    var unique   = getUniqueLoteries(selected);
    renderLoteries(unique);
  }

  $selects.on('change', renderAll);
  renderAll();
});
