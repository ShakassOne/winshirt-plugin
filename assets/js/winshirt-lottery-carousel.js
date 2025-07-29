(function($){
  function init($w){
    var $c = $w.find('.winshirt-lottery-carousel');
    $w.find('.carousel-prev').on('click', function(){
      $c[0].scrollBy({left:-$c[0].offsetWidth, behavior:'smooth'});
    });
    $w.find('.carousel-next').on('click', function(){
      $c[0].scrollBy({left:$c[0].offsetWidth, behavior:'smooth'});
    });
  }
  $(function(){
    $('.winshirt-lottery-carousel-wrapper').each(function(){
      init($(this));
    });
  });
})(jQuery);

