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

    function initZone($z){
        var side = $z.data('side');
        var cont = side === 'back' ? '#mockup-canvas-back' : '#mockup-canvas-front';
        $z.draggable({ containment: cont });
        $z.resizable({ containment: cont });
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
    }

    $('#add-zone').on('click', function(e){
        e.preventDefault();
        var index = $('#zones-container .zone-row').length;
        var tpl = $('#zone-template').html().replace(/%i%/g, index);
        $('#zones-container').append(tpl);
        createZone(index);
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
