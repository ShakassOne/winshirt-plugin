(function($){

  window.initWinshirtLotteryCard = function(card){
    var $card = $(card);
    var $img = $card.find('[data-tilt] img, .img[data-tilt], .lottery-image[data-tilt] img');

    if ($img.length && window.VanillaTilt) {
      VanillaTilt.init($img[0], {
        max: 8,
        speed: 400,
        scale: 1.05
      });
    }

    $card.addClass('fade-in-up card-shine');

    var end = $card.data('end');
    if (end) {
      end = new Date(end);
      var $timer = $card.find('.lottery-timer');

      function update() {
        var diff = end - new Date();
        if (diff <= 0) {
          $timer.text('Terminé');
          clearInterval(timer);
          return;
        }

        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);

        $timer.text(d + ' JOURS - ' + h + ' HEURES - ' + m + ' MINUTES');
      }

      update();
      var timer = setInterval(update, 60000);
    }

    var $progress = $card.find('.lottery-progress-bar');
    if ($progress.length) {
      var p = parseFloat($progress.data('progress'));

      if (!isNaN(p)) {
        var w = $progress.width();
        var pw = $progress.parent().width();
        p = pw ? (w / pw) * 100 : 0;

        $progress.css('--progress', p + '%').addClass('animate-progress');
      }
    }
  };

  $(function(){
    $('.ws-lottery-card').each(function(){
      if (window.initWinshirtLotteryCard) {
        window.initWinshirtLotteryCard(this);
      }
    });
  });

})(jQuery);
