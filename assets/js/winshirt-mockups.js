jQuery(function ($) {
  function openMedia(target, preview) {
    var frame = wp.media({
      title: 'Choisir une image',
      button: { text: 'Sélectionner' },
      multiple: false
    });
    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      $('#' + target).val(attachment.id);
      if (preview) {
        $(preview).html('<img src="' + attachment.sizes.thumbnail.url + '" />');
      }
    });
    frame.open();
  }

  $(document).on('click', '.winshirt-media-btn', function (e) {
    e.preventDefault();
    var target = $(this).data('target');
    var preview = $(this).data('preview');
    openMedia(target, preview);
  });
  $('#add-color').on('click', function (e) {
    e.preventDefault();
    var index = $('#colors-container .color-row').length;
    var template = $('#color-template').html();
    template = template.replace(/%i%/g, index);
    $('#colors-container').append(template);
  });

  $(document).on('click', '.remove-color', function (e) {
    e.preventDefault();
    $(this).closest('.color-row').remove();
  });

  function initZone($zone) {
    var idx = $zone.data('index');
    $zone.draggable({
      containment: 'parent',
      stop: function () { syncZone($zone); }
    }).resizable({
      handles: 'n,e,s,w,se,sw,ne,nw',
      containment: 'parent',
      stop: function () { syncZone($zone); }
    });
  }

  function syncZone($zone) {
    var idx = $zone.data('index');
    var $row = $('.zone-row[data-index="' + idx + '"]');
    if (!$row.length) return;
    var $canvas = $zone.parent();
    var cw = $canvas.width(), ch = $canvas.height();
    var pos = $zone.position();
    $row.find('.zone-top').val(((pos.top / ch) * 100).toFixed(2));
    $row.find('.zone-left').val(((pos.left / cw) * 100).toFixed(2));
    $row.find('.zone-width').val((($zone.width() / cw) * 100).toFixed(2));
    $row.find('.zone-height').val((($zone.height() / ch) * 100).toFixed(2));
  }

  $('.print-zone').each(function () { initZone($(this)); });

  $('#add-zone').on('click', function (e) {
    e.preventDefault();
    var index = $('#zones-container .zone-row').length;
    var tpl = $('#zone-template').html().replace(/%i%/g, index);
    $('#zones-container').append(tpl);
    var $row = $('.zone-row[data-index="' + index + '"]');
    var side = $row.find('.zone-side').val();
    var fmt = $row.find('.zone-format').val();
    var price = parseFloat($row.find('.zone-price').val() || 0);
    var $canvas = side === 'back' ? $('#mockup-canvas-back') : $('#mockup-canvas-front');
    var $zone = $('<div class="print-zone" data-index="' + index + '" data-side="' + side + '" data-format="' + fmt + '">' + fmt + '<span class="admin-zone-price">' + price + '€</span></div>')
      .css({ top: '10%', left: '10%', width: '20%', height: '20%' });
    $canvas.append($zone);
    syncZone($zone);
    initZone($zone);
  });

  $(document).on('click', '.remove-zone', function (e) {
    e.preventDefault();
    var $row = $(this).closest('.zone-row');
    var idx = $row.data('index');
    $('.print-zone[data-index="' + idx + '"]').remove();
    $row.remove();
  });

  $(document).on('change', '.zone-side', function () {
    var $row = $(this).closest('.zone-row');
    var idx = $row.data('index');
    var side = $(this).val();
    var $zone = $('.print-zone[data-index="' + idx + '"]').attr('data-side', side);
    var $canvas = side === 'back' ? $('#mockup-canvas-back') : $('#mockup-canvas-front');
    $zone.appendTo($canvas);
  });

  $(document).on('change', '.zone-format', function () {
    var $row = $(this).closest('.zone-row');
    var idx = $row.data('index');
    var fmt = $(this).val();
    var $pz = $('.print-zone[data-index="' + idx + '"]').attr('data-format', fmt);
    $pz.contents().filter(function(){return this.nodeType===3;}).first().replaceWith(fmt);
  });

  $(document).on('input change', '.zone-price', function(){
    var $row = $(this).closest('.zone-row');
    var idx = $row.data('index');
    var price = parseFloat($(this).val() || 0);
    $('.print-zone[data-index="' + idx + '"]').find('.admin-zone-price').text(price + '€');
  });
});
