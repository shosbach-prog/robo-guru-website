/* Robo-Guru Frontend helpers (minimal) */
(function(){
  document.addEventListener('click', function(e){
    const t = e.target;

    // FAQ (Theme: details.rg-faq__item)
    const details = t && t.closest ? t.closest('details.rg-faq__item') : null;
    if (details && t.closest('summary')) {
      const wrap = details.closest('.rg-faq__list');
      if (wrap) {
        wrap.querySelectorAll('details.rg-faq__item[open]').forEach(d => {
          if (d !== details) d.removeAttribute('open');
        });
      }
      if (!details.hasAttribute('open')) {
        setTimeout(function(){
          const rect = details.getBoundingClientRect();
          if (rect.top < 80 || rect.bottom > window.innerHeight - 40) {
            details.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        }, 50);
      }
    }

    // FAQ (Plugin: .rg-faq-item with .rg-faq-q)
    const faqQ = t && t.closest ? t.closest('.rg-faq-q') : null;
    if (faqQ) {
      const item = faqQ.closest('.rg-faq-item');
      if (!item) return;
      const wrap = item.closest('.rg-faq-wrap');
      const wasOpen = item.classList.contains('open');

      // Close other items
      if (wrap) {
        wrap.querySelectorAll('.rg-faq-item.open').forEach(i => {
          if (i !== item) i.classList.remove('open');
        });
      }

      // Toggle current
      item.classList.toggle('open', !wasOpen);

      // Smart scroll
      if (!wasOpen) {
        setTimeout(function(){
          const rect = item.getBoundingClientRect();
          if (rect.top < 80 || rect.bottom > window.innerHeight - 40) {
            item.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        }, 50);
      }
    }
  }, {passive:true});
})();
