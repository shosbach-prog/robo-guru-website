jQuery(function($){
  var frame;

  // ------------------------------
  // Media attachments (legacy)
  // ------------------------------
  function syncMediaIds(){
    var arr = [];
    $('#rg_doc_list .rg-doc').each(function(){
      arr.push(parseInt($(this).data('id'),10));
    });
    $('#rg_doc_ids').val(arr.join(','));
  }

  // ------------------------------
  // CPT document posts
  // ------------------------------
  function syncDocPostIds(){
    var arr = [];
    $('#rg_doc_post_list .rg-doc').each(function(){
      arr.push(parseInt($(this).data('id'),10));
    });
    $('#rg_doc_post_ids').val(arr.join(','));
  }

  function hasId($wrap, id){
    var found = false;
    $wrap.find('.rg-doc').each(function(){
      if (parseInt($(this).data('id'),10) === id) { found = true; return false; }
    });
    return found;
  }

  // ------------------------------
  // BuddyBoss documents
  // ------------------------------
  function syncBbIds(){
    var arr = [];
    $('#rg_bb_doc_list .rg-doc').each(function(){
      arr.push(parseInt($(this).data('id'),10));
    });
    $('#rg_bb_doc_ids').val(arr.join(','));
  }


function syncBbFolderIds(){
  var arr = [];
  $('#rg_bb_folder_list .rg-doc').each(function(){
    arr.push(parseInt($(this).data('id'),10));
  });
  $('#rg_bb_folder_ids').val(arr.join(','));
}

  // Sortable lists
  if ($.fn.sortable){
    $('#rg_doc_list').sortable({ handle: '.rg-doc__drag', update: syncMediaIds });
    $('#rg_doc_post_list').sortable({ handle: '.rg-doc__drag', update: syncDocPostIds });
    $('#rg_bb_doc_list').sortable({ handle: '.rg-doc__drag', update: syncBbIds });
    $('#rg_bb_folder_list').sortable({ handle: '.rg-doc__drag', update: syncBbFolderIds });
  }

  // Media selection (WP Media frame)
  $('#rg_add_docs').on('click', function(e){
    e.preventDefault();
    if (frame) { frame.open(); return; }

    frame = wp.media({
      title: 'Dokument(e) auswählen',
      button: { text: 'Hinzufügen' },
      multiple: true,
      library: { type: [ 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ] }
    });

    frame.on('select', function(){
      var selection = frame.state().get('selection');
      selection.each(function(attachment){
        var a = attachment.toJSON();
        var id = parseInt(a.id,10);
        if (!id) return;

        if (hasId($('#rg_doc_list'), id)) return;

        var title = a.title ? a.title : ('Datei #' + id);
        var ext = '';
        if (a.filename && a.filename.indexOf('.') > -1){
          ext = a.filename.split('.').pop().toUpperCase();
        }

        var $row = $('<div class="rg-doc" data-id="'+id+'">\
          <span class="rg-doc__drag">↕</span>\
          <span class="rg-doc__name"></span>\
          '+(ext ? '<span class="rg-doc__meta">'+ext+'</span>' : '')+'\
          <a href="#" class="button button-small rg-doc__remove">Entfernen</a>\
        </div>');
        $row.find('.rg-doc__name').text(title);
        $('#rg_doc_list').append($row);
      });
      syncMediaIds();
    });

    frame.open();
  });

  $('#rg_clear_docs').on('click', function(e){
    e.preventDefault();
    $('#rg_doc_ids').val('');
    $('#rg_doc_list').empty();
  });

  // Add CPT doc post from dropdown
  $('#rg_add_doc_post').on('click', function(e){
    e.preventDefault();
    var id = parseInt($('#rg_doc_post_select').val(), 10);
    if (!id) return;

    if (hasId($('#rg_doc_post_list'), id)) return;

    var title = $('#rg_doc_post_select option:selected').text() || ('Dokument #' + id);

    var $row = $('<div class="rg-doc rg-doc--post" data-id="'+id+'">\
      <span class="rg-doc__drag">↕</span>\
      <span class="rg-doc__name"></span>\
      <a href="#" class="button button-small rg-doc__remove">Entfernen</a>\
    </div>');
    $row.find('.rg-doc__name').text(title);
    $('#rg_doc_post_list').append($row);
    syncDocPostIds();
  });

  $('#rg_clear_doc_posts').on('click', function(e){
    e.preventDefault();
    $('#rg_doc_post_ids').val('');
    $('#rg_doc_post_list').empty();
  });

  // Remove for both lists
  $(document).on('click', '.rg-doc__remove', function(e){
    e.preventDefault();
    var $row = $(this).closest('.rg-doc');
    var $parent = $row.parent();
    $row.remove();
    if ($parent.attr('id') === 'rg_doc_list') syncMediaIds();
    if ($parent.attr('id') === 'rg_doc_post_list') syncDocPostIds();
    if ($parent.attr('id') === 'rg_bb_doc_list') syncBbIds();
    if ($parent.attr('id') === 'rg_bb_folder_list') syncBbFolderIds();
  });

  // --- BuddyBoss search + add ---
  var bbTimer = null;
  function renderBbResults(docs){
    var $r = $('#rg_bb_results');
    $r.empty();
    if (!docs || !docs.length){
      $r.append('<div class="rg-bb-empty">Keine Treffer.</div>');
      return;
    }
    docs.forEach(function(doc){
      var id = parseInt(doc.id,10);
      if (!id) return;
      var folder = doc.folder ? doc.folder : '';
      var ext = doc.ext ? doc.ext : '';
      var html = '<div class="rg-bb-row" data-id="'+id+'" data-title="'+$('<div/>').text(doc.title||('Dokument #'+id)).html()+'" data-folder="'+$('<div/>').text(folder).html()+'" data-ext="'+$('<div/>').text(ext).html()+'">'
        + '<div class="rg-bb-row__meta">'
        +   '<div class="rg-bb-row__title">'+$('<div/>').text(doc.title||('Dokument #'+id)).html()+'</div>'
        +   (folder ? '<div class="rg-bb-row__folder">'+$('<div/>').text(folder).html()+'</div>' : '')
        + '</div>'
        + '<button type="button" class="button button-small rg-bb-add">Hinzufügen</button>'
        + '</div>';
      $r.append(html);
    });
  }

  function bbDoSearch(){
    var term = ($('#rg_bb_search').val() || '').trim();
    var nonce = $('#rg_bb_nonce').val();
    var userId = parseInt($('#rg_bb_user_id').val(),10) || 0;
    if (!nonce) return;

    $('#rg_bb_results').html('<div class="rg-bb-loading">Suche…</div>');
    $.post(ajaxurl, {
      action: 'rg_bb_doc_search',
      nonce: nonce,
      term: term,
      user_id: userId
    }).done(function(resp){
      if (resp && resp.success && resp.data && resp.data.documents){
        renderBbResults(resp.data.documents);
      } else {
        $('#rg_bb_results').html('<div class="rg-bb-empty">Keine Treffer.</div>');
      }
    }).fail(function(){
      $('#rg_bb_results').html('<div class="rg-bb-empty">Fehler bei der Suche.</div>');
    });
  }

  
// --- BuddyBoss folder search + add ---
var bbFolderTimer = null;

function renderBbFolderResults(folders){
  var $r = $('#rg_bb_folder_results');
  $r.empty();
  if (!folders || !folders.length){
    $r.append('<div class="rg-bb-empty">Keine Ordner-Treffer.</div>');
    return;
  }
  folders.forEach(function(folder){
    var id = parseInt(folder.id,10);
    if (!id) return;
    var path = folder.path ? folder.path : '';
    var privacy = folder.privacy ? folder.privacy : '';
    var title = folder.title || ('Ordner #' + id);

    var html = '<div class="rg-bb-row" data-id="'+id+'" data-title="'+$('<div/>').text(title).html()+'" data-path="'+$('<div/>').text(path).html()+'" data-privacy="'+$('<div/>').text(privacy).html()+'">'
      + '<div class="rg-bb-row__meta">'
      +   '<div class="rg-bb-row__title">'+$('<div/>').text(title).html()+'</div>'
      +   (path ? '<div class="rg-bb-row__folder">'+$('<div/>').text(path).html()+'</div>' : '')
      +   (privacy ? '<div class="rg-bb-row__folder">Privacy: '+$('<div/>').text(privacy).html()+'</div>' : '')
      + '</div>'
      + '<button type="button" class="button button-small rg-bb-folder-add">Ordner hinzufügen</button>'
      + '</div>';
    $r.append(html);
  });
}

function bbDoFolderSearch(){
  var term = ($('#rg_bb_folder_search').val() || '').trim();
  var nonce = $('#rg_bb_nonce').val();
  var userId = parseInt($('#rg_bb_user_id').val(),10) || 0;
  if (!nonce) return;

  $('#rg_bb_folder_results').html('<div class="rg-bb-loading">Suche…</div>');
  $.post(ajaxurl, {
    action: 'rg_bb_folder_search',
    nonce: nonce,
    term: term,
    user_id: userId
  }).done(function(resp){
    if (resp && resp.success && resp.data && resp.data.folders){
      renderBbFolderResults(resp.data.folders);
    } else {
      $('#rg_bb_folder_results').html('<div class="rg-bb-empty">Keine Ordner-Treffer.</div>');
    }
  }).fail(function(){
    $('#rg_bb_folder_results').html('<div class="rg-bb-empty">Fehler bei der Ordner-Suche.</div>');
  });
}

$(document).on('input', '#rg_bb_folder_search', function(){
  clearTimeout(bbFolderTimer);
  bbFolderTimer = setTimeout(bbDoFolderSearch, 250);
});

$(document).on('click', '.rg-bb-folder-add', function(e){
  e.preventDefault();
  var $row = $(this).closest('.rg-bb-row');
  var id = parseInt($row.data('id'),10);
  if (!id) return;
  if (hasId($('#rg_bb_folder_list'), id)) return;

  var title = $row.data('title') || ('Ordner #' + id);
  var path = $row.data('path') || '';
  var privacy = $row.data('privacy') || '';

  var $it = $('<div class="rg-doc rg-doc--bb-folder" data-id="'+id+'">      <span class="rg-doc__drag">↕</span>      <span class="rg-doc__name"></span>      '+(path ? '<span class="rg-doc__meta"></span>' : '')+'      '+(privacy ? '<span class="rg-doc__meta"></span>' : '')+'      <a href="#" class="button button-small rg-doc__remove">Entfernen</a>    </div>');

  $it.find('.rg-doc__name').text($('<div/>').html(title).text());
  if (path){
    $it.find('.rg-doc__meta').eq(0).text($('<div/>').html(path).text());
  }
  if (privacy){
    var idxMeta = path ? 1 : 0;
    $it.find('.rg-doc__meta').eq(idxMeta).text($('<div/>').html(privacy).text());
  }

  $('#rg_bb_folder_list').append($it);
  syncBbFolderIds();
});

$(document).on('input', '#rg_bb_search', function(){
    clearTimeout(bbTimer);
    bbTimer = setTimeout(bbDoSearch, 250);
  });

  $(document).on('click', '.rg-bb-add', function(e){
    e.preventDefault();
    var $row = $(this).closest('.rg-bb-row');
    var id = parseInt($row.data('id'),10);
    if (!id) return;
    if (hasId($('#rg_bb_doc_list'), id)) return;
    var title = $row.data('title') || ('Dokument #' + id);
    var folder = $row.data('folder') || '';
    var ext = $row.data('ext') || '';

    var $it = $('<div class="rg-doc rg-doc--bb" data-id="'+id+'">\
      <span class="rg-doc__drag">↕</span>\
      <span class="rg-doc__name"></span>\
      '+(folder ? '<span class="rg-doc__meta"></span>' : '')+'\
      '+(ext ? '<span class="rg-doc__meta">'+ext+'</span>' : '')+'\
      <a href="#" class="button button-small rg-doc__remove">Entfernen</a>\
    </div>');
    $it.find('.rg-doc__name').text($('<div/>').html(title).text());
    if (folder){
      $it.find('.rg-doc__meta').first().text($('<div/>').html(folder).text());
    }
    $('#rg_bb_doc_list').append($it);
    syncBbIds();
  });

  $('#rg_bb_clear').on('click', function(e){
    e.preventDefault();
    $('#rg_bb_doc_ids').val('');
    $('#rg_bb_doc_list').empty();
  });
});


// UX: show/hide Media vs CPT blocks in robo_robot metabox depending on Ausgabe-Modus (visual only)
(function($){
  function rg_applyDocModeVisibility(){
    var $box = $('.rg-docs-admin');
    if (!$box.length) return;

    var mode = ($('input[name="rg_doc_mode"]:checked', $box).val() || 'both').toString();

    var $mediaDesc = $box.find('> p.description').first();
    var $mediaList = $box.find('#rg_doc_list');
    var $mediaAdd  = $box.find('#rg_add_docs');
    var $mediaClr  = $box.find('#rg_clear_docs');

    var $bbBlock   = $box.find('#rg_bb_docs_block');
    var $bbDivider = $box.find('#rg_docs_divider_bb');
    var $cptBlock  = $box.find('#rg_cpt_docs_block');
    var $divider   = $box.find('#rg_docs_divider');

    var showBb    = (mode === 'bb' || mode === 'all');
    var showCpt   = (mode === 'cpt' || mode === 'both' || mode === 'all');
    var showMedia = (mode === 'media' || mode === 'both' || mode === 'all');

    // Media UI
    $mediaDesc.toggle(!!showMedia);
    $mediaList.toggle(!!showMedia);
    $mediaAdd.toggle(!!showMedia);
    $mediaClr.toggle(!!showMedia);

    // BuddyBoss UI
    if ($bbBlock.length) $bbBlock.toggle(!!showBb);
    if ($bbDivider.length) $bbDivider.toggle(!!(showBb && (showCpt || showMedia)));

    // CPT UI
    if ($cptBlock.length) $cptBlock.toggle(!!showCpt);

    // Divider only when CPT + Media are both visible
    if ($divider.length){
      $divider.toggle(!!(showMedia && showCpt));
    }
  }

  $(document).on('change', '.rg-docs-admin input[name="rg_doc_mode"]', rg_applyDocModeVisibility);
  $(document).ready(rg_applyDocModeVisibility);

  // Clear cache button
  $(document).on('click', '#rg_docs_clear_cache', function(e){
    e.preventDefault();
    var postId = parseInt($(this).data('post'),10) || 0;
    var nonce = $('#rg_docs_clear_cache_nonce').val() || '';
    if (!postId || !nonce) return;

    $.post(ajaxurl, { action:'rg_docs_clear_cache', post_id: postId, nonce: nonce }, function(res){
      if (res && res.success) {
        alert('Cache gelöscht.');
      } else {
        alert((res && res.data && res.data.message) ? res.data.message : 'Fehler beim Cache löschen.');
      }
    });
  });

})(jQuery);
