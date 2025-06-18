jQuery(function($){
  $('.ws-lottery-card').each(function(){
    if(window.WinshirtLotteryCard){
      window.WinshirtLotteryCard.initCard(this, {interval:60000});
    }
  });
});
