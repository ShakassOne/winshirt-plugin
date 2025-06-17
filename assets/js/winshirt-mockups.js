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

    function saveZonePosition($z){
        var idx = $z.data('index');
        var $row = $('.zone-row[data-index='+idx+']');
        var $canvas = $z.closest('.mockup-canvas');
        var img = $canvas.find('img')[0];
        if(!img) return;
        var pos = $z.position();
        $row.find('.zone-top').val((pos.top / img.offsetHeight * 100).toFixed(2));
        $row.find('.zone-left').val((pos.left / img.offsetWidth * 100).toFixed(2));
        $row.find('.zone-width').val(($z.width() / img.offsetWidth * 100).toFixed(2));
        $row.find('.zone-height').val(($z.height() / img.offsetHeight * 100).toFixed(2));
    }

    function initZone($z){
        var side = $z.data('side');
        var cont = side === 'back' ? '#mockup-canvas-back' : '#mockup-canvas-front';
        $z.draggable({ containment: cont, stop: function(){ saveZonePosition($z); } });
        $z.resizable({ containment: cont, handles:'n,e,s,w,ne,se,sw,nw', stop: function(){ saveZonePosition($z); } });
        saveZonePosition($z);
    }

    function createZone(index){
        var $row = $('.zone-row[data-index='+index+']');
        var side = $row.find('.zone-side').val();
        var fmt = $row.find('.zone-format').val();
        var top = parseFloat($row.find('.zone-top').val()) || 10;
        var left = parseFloat($row.find('.zone-left').val()) || 10;
        var width = parseFloat($row.find('.zone-width').val()) || 20;
        var height = parseFloat($row.find('.zone-height').val()) || 20;
        var $canvas = side === 'back' ? $('#mockup-canvas-back') : $('#mockup-canvas-front');
        var $zone = $('<div class="print-zone" data-index="'+index+'" data-side="'+side+'" data-format="'+fmt+'">'+fmt+'</div>');
        $zone.css({top:top+'%',left:left+'%',width:width+'%',height:height+'%'});
        $canvas.append($zone);
        initZone($zone);
    }

    function moveZone(index, side){
        var $zone = $('.print-zone[data-index='+index+']');
        var cont = side === 'back' ? '#mockup-canvas-back' : '#mockup-canvas-front';
        $zone.attr('data-side', side).appendTo($(cont));
        $zone.draggable('option','containment', cont);
        $zone.resizable('option','containment', cont);
        saveZonePosition($zone);
    }

    var drawing = false;
    var drawingIndex = null;
    var startPos = null;

    function startDrawing(index){
        drawing = true;
        drawingIndex = index;
        $('.print-zone[data-index='+index+']').remove();
        $('.mockup-canvas').addClass('drawing');
    }

    $('#add-zone').on('click', function(e){
        e.preventDefault();
        var index = $('#zones-container .zone-row').length;
        var tpl = $('#zone-template').html().replace(/%i%/g, index);
        $('#zones-container').append(tpl);
        startDrawing(index);
    });

    $(document).on('mousedown', '.mockup-canvas.drawing', function(e){
        if(!drawing) return;
        e.preventDefault();
        var $canvas = $(this);
        var side = $canvas.attr('id') === 'mockup-canvas-back' ? 'back' : 'front';
        var $row = $('.zone-row[data-index='+drawingIndex+']');
        $row.find('.zone-side').val(side);
        var fmt = $row.find('.zone-format').val();
        var offset = $canvas.offset();
        startPos = {x:e.pageX - offset.left, y:e.pageY - offset.top, offset:offset};
        var $zone = $('<div class="print-zone drawing" data-index="'+drawingIndex+'" data-side="'+side+'" data-format="'+fmt+'">'+fmt+'</div>');
        $canvas.append($zone);
        $(document).on('mousemove.drawZone', function(ev){
            var x = ev.pageX - startPos.offset.left;
            var y = ev.pageY - startPos.offset.top;
            var left = Math.min(startPos.x, x);
            var top = Math.min(startPos.y, y);
            var width = Math.abs(x - startPos.x);
            var height = Math.abs(y - startPos.y);
            $zone.css({left:left, top:top, width:width, height:height});
        });
        $(document).on('mouseup.drawZone', function(){
            $(document).off('.drawZone');
            $zone.removeClass('drawing');
            initZone($zone);
            saveZonePosition($zone);
            $('.mockup-canvas').removeClass('drawing');
            drawing = false;
            drawingIndex = null;
        });
    });

    $(document).on('click', '.remove-zone', function(e){
        e.preventDefault();
        var $row = $(this).closest('.zone-row');
        var idx = $row.data('index');
        $('.print-zone[data-index='+idx+']').remove();
        $row.remove();
    });

    $(document).on('change', '.zone-side', function(){
        var $row = $(this).closest('.zone-row');
        moveZone($row.data('index'), $(this).val());
    });

    $(document).on('change', '.zone-format', function(){
        var $row = $(this).closest('.zone-row');
        var idx = $row.data('index');
        $('.print-zone[data-index='+idx+']').text($(this).val()).attr('data-format', $(this).val());
    });

    $('.zone-row').each(function(){
        createZone($(this).data('index'));
    });

    $('#mockup-form').on('submit', function(){
        $('.zone-row').each(function(){
            var $row = $(this);
            var idx = $row.data('index');
            var side = $row.find('.zone-side').val();
            var $canvas = side === 'back' ? $('#mockup-canvas-back') : $('#mockup-canvas-front');
            var img = $canvas.find('img')[0];
            if(!img) return;
            var $z = $('.print-zone[data-index='+idx+']');
            var pos = $z.position();
            var pct = {
                top: (pos.top / img.offsetHeight * 100).toFixed(2),
                left: (pos.left / img.offsetWidth * 100).toFixed(2),
                width: ($z.width() / img.offsetWidth * 100).toFixed(2),
                height: ($z.height() / img.offsetHeight * 100).toFixed(2)
            };
            $row.find('.zone-top').val(pct.top);
            $row.find('.zone-left').val(pct.left);
            $row.find('.zone-width').val(pct.width);
            $row.find('.zone-height').val(pct.height);
        });
    });
});
