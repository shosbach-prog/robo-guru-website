/* Robo-Guru Frontend helpers (minimal) */
(function(){
  document.addEventListener('click', function(e){
    const t = e.target;

    // FAQ: keep it tidy (only one open)
    const details = t && t.closest ? t.closest('details.rg-faq__item') : null;
    if (details && t.closest('summary')) {
      const wrap = details.closest('.rg-faq__list');
      if (wrap) {
        wrap.querySelectorAll('details.rg-faq__item[open]').forEach(d => {
          if (d !== details) d.removeAttribute('open');
        });
      }
    }
  }, {passive:true});
})();
