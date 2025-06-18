jQuery(function($){
  var $select = $('#winshirt-lottery-select');
  if(!$select.length) return;
  var $info = $('#winshirt-lottery-info');

  function initCard($card, end){
    $card.on('mousemove', function(e){
      var rect = this.getBoundingClientRect();
      var x = (e.clientX - rect.left - rect.width / 2) / 20;
      var y = (e.clientY - rect.top - rect.height / 2) / 20;
      this.style.transform = 'rotateX('+y+'deg) rotateY('+x+'deg) scale(1.05)';
    }).on('mouseleave', function(){
      this.style.transform = '';
    });

    if(end){
      var $timer = $card.find('.lottery-timer');
      function updateTimer(){
        var diff = new Date(end) - new Date();
        if(diff <= 0){
          $timer.text('Terminé');
          clearInterval(int);
          return;
        }
        var d = Math.floor(diff/86400000);
        var h = Math.floor((diff%86400000)/3600000);
        var m = Math.floor((diff%3600000)/60000);
        $timer.text(d+' JOURS - '+h+' HEURES - '+m+' MINUTES');
      }
      updateTimer();
      var int = setInterval(updateTimer,60000);
    }
  }

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
    var img = data.image ? '<div class="lottery-image"><img src="'+data.image+'" alt="" /></div>' : '';
    var badge = '<span class="lottery-badge">'+(data.active ? 'Active' : 'Terminée')+'</span>';
    var badgeF = data.featured ? '<span class="lottery-badge badge-featured">En vedette</span>' : '';
    var value = data.value ? '<p class="lottery-value">Valeur : '+data.value+' €</p>' : '';
    var draw = data.drawDate ? '<p class="lottery-draw">Tirage le '+data.drawDate+'</p>' : '';
    var html = '<div class="ws-lottery-card" data-end="'+(data.drawDate||'')+'">'+
      badge+badgeF+
      img+
      '<h3 class="lottery-title">'+(data.name||$opt.text())+'</h3>'+
      value+
      '<div class="lottery-timer"></div>'+
      '<div class="lottery-progress"><div class="lottery-progress-bar" style="width:'+percent+'%"></div></div>'+
      '<p class="lottery-count">'+data.participants+' participants / Objectif : '+data.goal+'</p>'+
      draw+
      '</div>';
    $info.html(html);
    initCard($info.find('.ws-lottery-card'), data.drawDate);
  }

  $select.on('change', render);
  render();
});
