jQuery(function($){
  $('.ws-lottery-card').each(function(){
    var $card = $(this);
    var $img = $card.find('img');
    if($img.length && window.VanillaTilt){
      VanillaTilt.init($img[0], {max:8, speed:400, scale:1.05});
    }
    var end = $card.data('end');
    if(end){
      end = new Date(end);
      function update(){
        var diff = end - new Date();
        if(diff <= 0){
          $card.find('.lottery-timer').text('Terminé');
          clearInterval(timer);
          return;
        }
        var d = Math.floor(diff/86400000);
        var h = Math.floor((diff%86400000)/3600000);
        var m = Math.floor((diff%3600000)/60000);
        $card.find('.lottery-timer').text(d+' JOURS - '+h+' HEURES - '+m+' MINUTES');
      }
      update();
      var timer = setInterval(update,60000);
    }
  });
});
