jQuery(function($){
  var frame;

  function idsToArray(ids){
    if(!ids) return [];
    return ids.split(',').map(function(x){ return parseInt(x,10); }).filter(function(x){ return !isNaN(x) && x>0; });
  }
  function arrayToIds(arr){ return arr.join(','); }

  function syncIds(){
    var arr = [];
    $('#rg_gallery_preview .rg-thumb').each(function(){
      arr.push(parseInt($(this).data('id'),10));
    });
    $('#rg_gallery_ids').val(arrayToIds(arr));
  }

  function refreshSortable(){
    $('#rg_gallery_preview').sortable({
      items: '.rg-thumb',
      update: function(){ syncIds(); }
    });
  }

  refreshSortable();

  $('#rg_add_gallery').on('click', function(e){
    e.preventDefault();
    if(frame){ frame.open(); return; }

    frame = wp.media({
      title: 'Roboter-Galerie – Bilder auswählen',
      button: { text: 'Zur Galerie hinzufügen' },
      multiple: true
    });

    frame.on('select', function(){
      var selection = frame.state().get('selection');
      var current = idsToArray($('#rg_gallery_ids').val());

      selection.each(function(att){
        var id = att.get('id');
        if(current.indexOf(id) === -1) current.push(id);
      });

      $('#rg_gallery_ids').val(arrayToIds(current));

      var $wrap = $('#rg_gallery_preview');
      $wrap.empty();
      current.forEach(function(id){
        var att = wp.media.attachment(id);
        att.fetch().then(function(){
          var url = (att.get('sizes') && att.get('sizes').thumbnail) ? att.get('sizes').thumbnail.url : att.get('url');
          var $t = $('<div class="rg-thumb" />').attr('data-id', id);
          $t.append($('<img />').attr('src', url));
          $t.append($('<button type="button" class="rg-remove" title="Entfernen">×</button>'));
          $wrap.append($t);
          refreshSortable();
        });
      });
    });

    frame.open();
  });

  $('#rg_clear_gallery').on('click', function(e){
    e.preventDefault();
    $('#rg_gallery_ids').val('');
    $('#rg_gallery_preview').empty();
  });

  $(document).on('click', '.rg-admin-gallery .rg-remove', function(e){
    e.preventDefault();
    $(this).closest('.rg-thumb').remove();
    syncIds();
  });
});