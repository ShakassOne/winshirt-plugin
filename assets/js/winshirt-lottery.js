jQuery(function($){
  $('.winshirt-lottery-select').each(function(){
    var $select = $(this);
    var $info = $select.next('.winshirt-lottery-info');

    function render(){
      var $opt = $select.find('option:selected');
      var data = $opt.data('info');

      if(!data){
        $info.empty();
        return;
      }

      if(typeof data === 'string'){
        try{ data = JSON.parse(data); }catch(e){ data = {}; }
      }

      var percent = data.goal ? Math.min(100, (data.participants / data.goal) * 100) : 0;
      var img = data.image ? '<div class="lottery-image"><img src="' + data.image + '" alt=""/></div>' : '';
      var badge = data.active ? '<span class="lottery-badge lottery-active">Active</span>' : '<span class="lottery-badge lottery-inactive">Terminée</span>';
      var badgeF = data.featured ? '<span class="lottery-badge badge-featured">En vedette</span>' : '';
      var value = data.value ? '<p class="lottery-value">Valeur : ' + data.value + '€</p>' : '';
      var draw = data.drawDate ? '<p class="lottery-draw">Tirage le ' + data.drawDate + '</p>' : '';

      var html = '<div class="ws-lottery-card" data-end="' + data.drawDate + '">' +
        badge + badgeF +
        img +
        '<h3 class="lottery-title">' + (data.name || $opt.text()) + '</h3>' +
        value +
        '<div class="lottery-timer"></div>' +
        '<div class="lottery-progress"><div class="lottery-progress-bar" data-progress="' + percent + '" style="width:' + percent + '%"></div></div>' +
        '<p class="lottery-count">' + data.participants + ' participants / Objectif : ' + data.goal + '</p>' +
        draw +
        '</div>';

      $info.html(html);

      if(window.initWinshirtLotteryCard){
        window.initWinshirtLotteryCard($info.find('.ws-lottery-card')[0]);
      }
      if(window.WinshirtLotteryCard){
        window.WinshirtLotteryCard.initCard($info.find('.ws-lottery-card')[0], {interval:60000});
      }
    }

    $select.on('change', render);
    render();
  });
});
