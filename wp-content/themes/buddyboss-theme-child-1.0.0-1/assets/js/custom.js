/* Robo-Guru Frontend helpers (minimal) */
(function(){
  document.addEventListener('click', function(e){
    const t = e.target;

    // FAQ: keep it tidy (only one open) + smart scroll
    const details = t && t.closest ? t.closest('details.rg-faq__item') : null;
    if (details && t.closest('summary')) {
      const wrap = details.closest('.rg-faq__list');
      if (wrap) {
        // Close other open FAQ items
        wrap.querySelectorAll('details.rg-faq__item[open]').forEach(d => {
          if (d !== details) d.removeAttribute('open');
        });
      }

      // Smart scroll: smoothly scroll opened FAQ into view after a small delay
      if (!details.hasAttribute('open')) {
        setTimeout(function(){
          const rect = details.getBoundingClientRect();
          const isAboveViewport = rect.top < 80;
          const isBelowViewport = rect.bottom > window.innerHeight - 40;

          if (isAboveViewport || isBelowViewport) {
            details.scrollIntoView({
              behavior: 'smooth',
              block: 'center'
            });
          }
        }, 50);
      }
    }
  }, {passive:true});
})();
