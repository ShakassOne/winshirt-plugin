jQuery(function($){
  $('.ws-lottery-card').each(function(){
    var $card = $(this);
    var end = $card.data('end');
    if(end){
      function update(){
        var diff = new Date(end) - new Date();
        if(diff <= 0){
          $card.find('.lottery-timer').text('Terminé');
          clearInterval(int);
          return;
        }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        $card.find('.lottery-timer').text(d+'j '+h+'h '+m+'m '+s+'s');
      }
      update();
      var int = setInterval(update,1000);
    }

    $card.on('mousemove', function(e){
      var w = $card.outerWidth();
      var h = $card.outerHeight();
      var x = e.offsetX - w/2;
      var y = e.offsetY - h/2;
      var rx = (y/h)*6;
      var ry = -(x/w)*6;
      $card.css('transform','rotateX('+rx+'deg) rotateY('+ry+'deg) scale(1.03)');
    }).on('mouseleave', function(){
      $card.css('transform','');
    });
  });
});
