jQuery(function($){
  var $containers = $('.loteries-container');
  if(!$containers.length){
    return;
  }
  if($containers.length > 1){
    $containers.slice(1).remove();
  }
  var $container = $containers.first();

  // Ici, ton code de traitement des loteries continue...

  $('.winshirt-lottery-select').each(function(index){
    var $select = $(this);

    function render(){
      var $opt = $select.find('option:selected');
      var data = $opt.data('info');
      var card = $container.find('.loterie-card[data-index="'+index+'"]');

      if(!$opt.val()){
        card.remove();
        return;
      }

      if(typeof data === 'string'){
        try{ data = JSON.parse(data); }catch(e){ data = {}; }
      }
      data = data || {};

      var percent = data.goal ? Math.min(100, Math.round((data.participants / data.goal) * 100)) : 0;
      var badge = data.featured ? '<span class="loterie-badge">BEST</span>' : (data.active ? '<span class="loterie-badge">NOUVEAU</span>' : '');
      var price = data.value ? '<span class="loterie-price">'+data.value+'€</span>' : '';
      var html = '<div class="loterie-card" data-index="'+index+'">'+
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

      if(card.length){
        card.replaceWith(html);
        card = $container.find('.loterie-card[data-index="'+index+'"]');
      }else{
        $container.append(html);
        card = $container.find('.loterie-card[data-index="'+index+'"]');
      }

      card.find('.loterie-remove').on('click', function(e){
        e.preventDefault();
        $select.val('');
        $select.trigger('change');
      });
    }

    $select.on('change', render);
    render();
  });
});
