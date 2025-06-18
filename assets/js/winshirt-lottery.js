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
    var html = '<div class="winshirt-lottery-info">';
    if(data.img) html += '<img src="'+data.img+'" alt="" />';
    html += '<h3>'+$opt.text()+'</h3>';
    if(data.tickets) html += '<p>+'+data.tickets+' tickets</p>';
    if(data.max){
      var percent = data.max > 0 ? Math.min(100, (data.count/data.max)*100) : 0;
      html += '<p>'+data.count+' / '+data.max+' participants</p>';
      html += '<div class="winshirt-lottery-progress"><div class="bar" style="width:'+percent+'%;background:'+(percent>80?'#c00':(percent>50?'#e67e00':'#2ecc71'))+'"></div></div>';
      if(data.count >= data.max) html += '<p class="winshirt-lottery-full">Loterie complète</p>';
    }else if(typeof data.count !== 'undefined'){
      html += '<p>'+data.count+' participants</p>';
    }
    html += '</div>';
    $info.html(html);
  }

  $select.on('change', update);
});
