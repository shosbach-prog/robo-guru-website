(function($){
  function collect(){
    return {
      action: 'rg_robot_profile_save_meta',
      nonce: (window.RGRobotProfile && RGRobotProfile.nonce) ? RGRobotProfile.nonce : '',
      post_id: $('#post_ID').val() || 0,
      rg_ideal_for: $('textarea[name="rg_ideal_for"]').val() || '',
      rg_not_ideal_for: $('textarea[name="rg_not_ideal_for"]').val() || '',
      rg_faq_raw: $('textarea[name="rg_faq_raw"]').val() || '',
      rg_video_url: $('input[name="rg_video_url"]').val() || '',
      rg_docking_options: $('input[name="rg_docking_options[]"]:checked').map(function(){return $(this).val();}).get()
    };
  }

  function toast(msg){
    const $n = $('<div/>').text(msg).css({
      position:'fixed', right:'16px', bottom:'16px', zIndex:999999,
      background:'#111', color:'#fff', padding:'10px 12px', borderRadius:'12px', fontWeight:900
    });
    $('body').append($n);
    setTimeout(()=>{$n.fadeOut(250, ()=>$n.remove());}, 1200);
  }

  function saveNow(){
    const payload = collect();
    if(!payload.post_id) return;
    $.post((window.RGRobotProfile && RGRobotProfile.ajax_url) ? RGRobotProfile.ajax_url : ajaxurl, payload)
      .done(function(res){
        if(res && res.success){ toast('RG Robot Profile: gespeichert ✓'); }
      });
  }

  // Save on common buttons (Gutenberg + Classic)
  $(document).on('click', '#publish, #save-post, #post-preview', saveNow);
  $(document).on('click', '.editor-post-publish-button, .editor-post-publish-panel__toggle, .editor-post-save-draft, .editor-post-publish-button__button', saveNow);

  // Also save when leaving fields
  $(document).on('change', 'textarea[name="rg_ideal_for"], textarea[name="rg_not_ideal_for"], textarea[name="rg_faq_raw"], input[name="rg_video_url"], input[name="rg_docking_options[]"]', function(){
    saveNow();
  });
})(jQuery);