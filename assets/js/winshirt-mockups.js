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

    function initZones(){
        $('.print-zone').each(function(){
            var $z = $(this);
            $z.draggable({ containment: '#mockup-canvas' });
            $z.resizable({ containment: '#mockup-canvas' });
        });
    }
    initZones();

    $('#mockup-form').on('submit', function(){
        var img = $('#mockup-canvas img')[0];
        if(!img) return;
        $('.print-zone').each(function(){
            var $z = $(this);
            var f = $z.data('format');
            var pos = $z.position();
            var pct = {
                top: (pos.top / img.offsetHeight * 100).toFixed(2),
                left: (pos.left / img.offsetWidth * 100).toFixed(2),
                width: ($z.width() / img.offsetWidth * 100).toFixed(2),
                height: ($z.height() / img.offsetHeight * 100).toFixed(2)
            };
            $('#area_'+f+'_top').val(pct.top);
            $('#area_'+f+'_left').val(pct.left);
            $('#area_'+f+'_width').val(pct.width);
            $('#area_'+f+'_height').val(pct.height);
        });
    });
});
