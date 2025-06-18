jQuery(function($){
  var $containers = $('.loteries-container');
  if(!$containers.length){
    return;
  }
  if($containers.length > 1){
    $containers.slice(1).remove();
  }
  var $container = $containers.first();
  var $selects   = $('.winshirt-lottery-select');

  function renderAll(){
    $container.empty();
    var shown = {};

    $selects.each(function(index){
      var $select = $(this);
      var $opt    = $select.find('option:selected');
      var lid     = $opt.val();
      if(!lid || shown[lid]){
        return;
      }
      shown[lid] = true;

      var data = $opt.data('info');
      if(typeof data === 'string'){
        try{ data = JSON.parse(data); }catch(e){ data = {}; }
      }
      data = data || {};

      var percent = data.goal ? Math.min(100, Math.round((data.participants / data.goal) * 100)) : 0;
      var badge   = data.featured ? '<span class="loterie-badge">BEST</span>' : (data.active ? '<span class="loterie-badge">NOUVEAU</span>' : '');
      var price   = data.value ? '<span class="loterie-price">'+data.value+'€</span>' : '';
      var html    = '<div class="loterie-card" id="loterie-card-'+index+'" data-index="'+index+'" data-lottery="'+lid+'">'+
        badge+
        '<button type="button" class="loterie-remove" aria-label="Retirer">&times;</button>'+
        (data.image ? '<img class="loterie-img" src="'+data.image+'" alt="" />' : '')+
        '<div class="loterie-info">'+
          '<span class="loterie-title">'+(data.name || $opt.text())+'</span>'+
          '<div class="loterie-meta">'+
            price+
            '<span class="loterie-participants">'+data.participants+' / '+data.goal+' participants</span>'+
          '</div>'+
          '<div class="loterie-bar-bg">'+
            '<div class="loterie-bar" style="width:'+percent+'%"></div>'+
            '<div class="loterie-tooltip">'+percent+'% rempli ('+data.participants+' sur '+data.goal+')</div>'+
          '</div>'+
        '</div>'+
      '</div>';

      $container.append(html);
      var $card = $container.find('#loterie-card-'+index);
      $card.find('.loterie-remove').on('click', function(e){
        e.preventDefault();
        $select.val('');
        renderAll();
      });
    });
  }

  $selects.on('change', renderAll);
  renderAll();
});
