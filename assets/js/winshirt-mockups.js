jQuery(function($){
    $('#add-color').on('click', function(e){
        e.preventDefault();
        var index = $('#colors-container .color-row').length;
        var template = $('#color-template').html();
        template = template.replace(/%i%/g, index);
        $('#colors-container').append(template);
    });

    $(document).on('click', '.remove-color', function(e){
        e.preventDefault();
        $(this).closest('.color-row').remove();
    });

    // Gestion visuelle des zones en admin
    $('.admin-add-zone').on('click', function(){
        var $zone = $('<div class="admin-mockup-zone"></div>');
        var $resize = $('<div class="admin-zone-resize"></div>');
        var $remove = $('<div class="admin-remove-zone">×</div>');
        var $price = $('<input type="number" class="admin-zone-price" min="0" step="0.01" placeholder="Prix" />');
        $zone.append($resize).append($remove).append($price);
        $zone.css({width:120, height:120, left:60, top:60});
        $('.admin-mockup-container').append($zone);

        var isDragging = false, dragStart = {};
        $zone.on('mousedown touchstart', function(e){
            if($(e.target).is('.admin-remove-zone, .admin-zone-resize, .admin-zone-price')) return;
            isDragging = true;
            var oe = (e.type === 'touchstart') ? e.originalEvent.touches[0] : e;
            dragStart.x = oe.clientX;
            dragStart.y = oe.clientY;
            dragStart.left = parseInt($zone.css('left'),10);
            dragStart.top = parseInt($zone.css('top'),10);
            $(window).on('mousemove touchmove', dragMove);
            $(window).on('mouseup touchend', dragEnd);
            $zone.addClass('selected');
        });
        function dragMove(e){
            if(!isDragging) return;
            var oe = (e.type === 'touchmove') ? e.originalEvent.touches[0] : e;
            var dx = oe.clientX - dragStart.x;
            var dy = oe.clientY - dragStart.y;
            var parentW = $zone.parent().width(), parentH = $zone.parent().height();
            var zoneW = $zone.width(), zoneH = $zone.height();
            var newLeft = Math.min(Math.max(0, dragStart.left + dx), parentW - zoneW);
            var newTop  = Math.min(Math.max(0, dragStart.top + dy), parentH - zoneH);
            $zone.css({left:newLeft, top:newTop});
        }
        function dragEnd(e){
            isDragging = false;
            $(window).off('mousemove touchmove', dragMove);
            $(window).off('mouseup touchend', dragEnd);
        }

        // RESIZE
        $resize.on('mousedown touchstart', function(e){
            e.stopPropagation();
            var oe = (e.type === 'touchstart') ? e.originalEvent.touches[0] : e;
            var resizeStart = {
                x: oe.clientX,
                y: oe.clientY,
                w: $zone.width(),
                h: $zone.height()
            };
            $(window).on('mousemove touchmove', resizeMove);
            $(window).on('mouseup touchend', resizeEnd);
            function resizeMove(e2){
                var oe2 = (e2.type === 'touchmove') ? e2.originalEvent.touches[0] : e2;
                var dx = oe2.clientX - resizeStart.x;
                var dy = oe2.clientY - resizeStart.y;
                var parentW = $zone.parent().width(), parentH = $zone.parent().height();
                var newW = Math.max(32, resizeStart.w + dx);
                var newH = Math.max(32, resizeStart.h + dy);
                newW = Math.min(newW, parentW - parseInt($zone.css('left'),10));
                newH = Math.min(newH, parentH - parseInt($zone.css('top'),10));
                $zone.css({width:newW, height:newH});
            }
            function resizeEnd(){
                $(window).off('mousemove touchmove', resizeMove);
                $(window).off('mouseup touchend', resizeEnd);
            }
        });

        // SUPPR
        $remove.on('click', function(){
            $zone.remove();
        });

        // SÉLECTION
        $zone.on('mousedown touchstart', function(e){
            $('.admin-mockup-zone').removeClass('selected');
            $zone.addClass('selected');
        });

        // (Optionnel) : Enregistre chaque changement si besoin
        $price.on('change', function(){
            // enregistre le prix pour cette zone si tu veux l'envoyer au back
        });
    });
});
