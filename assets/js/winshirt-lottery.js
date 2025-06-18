jQuery(function($){
  var $select = $('#winshirt-lottery-select');
  if(!$select.length) return;
  var $info = $('#winshirt-lottery-info');

  function update(){
    var $opt = $select.find('option:selected');
    var data = $opt.data('info');
    if(!data){
      $info.empty();
      return;
    }
    if(typeof data === 'string'){
      try{ data = JSON.parse(data); } catch(e){ data = {}; }
    }

    var percent = data.goal ? Math.min(100, (data.participants / data.goal) * 100) : 0;
    var draw = data.drawDate ? '<p style="margin-top:1rem;">\ud83d\udcc5 Tirage le '+data.drawDate+'</p>' : '';
    var img = data.image ? '<img src="'+data.image+'" alt="'+(data.name||$opt.text())+'" />' : '';

    var html = '<div class="lottery-card">'+
      img+
      '<h3>'+(data.name||$opt.text())+'</h3>'+
      '<p>\ud83c\udf39 +'+data.tickets+' tickets</p>'+
      '<p>'+data.participants+' / '+data.goal+' participants</p>'+
      '<div class="lottery-progress"><div class="lottery-progress-bar" style="width:'+percent+'%"></div></div>'+
      draw+
      '</div>';
    $info.html(html);
  }

  $select.on('change', update).trigger('change');
});
