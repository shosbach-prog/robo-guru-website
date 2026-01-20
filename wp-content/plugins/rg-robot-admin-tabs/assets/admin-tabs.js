(function($){
  function initTabs(root){
    var $root = $(root);
    var $tabs = $root.find('.rg-tab');
    var $panes = $root.find('.rg-pane');

    $tabs.on('click', function(){
      var key = $(this).data('rg-tab');
      $tabs.removeClass('is-active');
      $(this).addClass('is-active');
      $panes.removeClass('is-active');
      $root.find('.rg-pane[data-rg-pane="'+key+'"]').addClass('is-active');
    });
  }

  $(function(){
    $('[data-rg-tabs]').each(function(){ initTabs(this); });
  });
})(jQuery);