(function(window){
  'use strict';

  function pad(n){return n<10?'0'+n:n;}

  function initTilt(card){
    var img = card.querySelector('[data-tilt] img, img');
    if(window.VanillaTilt && img){
      window.VanillaTilt.init(img, {max:8, speed:400, scale:1.05});
    } else {
      card.addEventListener('mousemove', function(e){
        var rect = card.getBoundingClientRect();
        var x = (e.clientX - rect.left - rect.width/2)/20;
        var y = (e.clientY - rect.top - rect.height/2)/20;
        card.style.transform = 'rotateX('+y+'deg) rotateY('+x+'deg) scale(1.05)';
      });
      card.addEventListener('mouseleave', function(){
        card.style.transform = '';
      });
    }
  }

  function initCountdown(card, end, interval){
    if(!end) return;
    var timer = card.querySelector('.lottery-timer');
    if(!timer) return;
    var endDate = new Date(end);
    interval = interval || 60000;
    function update(){
      var diff = endDate - new Date();
      if(diff <= 0){
        timer.textContent = 'Terminé';
        clearInterval(id);
        return;
      }
      var d = Math.floor(diff/86400000);
      var h = Math.floor((diff%86400000)/3600000);
      var m = Math.floor((diff%3600000)/60000);
      if(interval === 1000){
        var s = Math.floor((diff%60000)/1000);
        timer.textContent = d+'j '+pad(h)+'h '+pad(m)+'m '+pad(s)+'s';
      } else {
        timer.textContent = d+' JOURS - '+h+' HEURES - '+m+' MINUTES';
      }
    }
    update();
    var id = setInterval(update, interval);
  }

  function initCard(card, opts){
    opts = opts || {};
    initTilt(card);
    initCountdown(card, card.dataset.end, opts.interval);
  }

  window.WinshirtLotteryCard = {
    initTilt: initTilt,
    initCountdown: initCountdown,
    initCard: initCard
  };
})(window);
