(function(){
  function qs(sel, root){ return (root||document).querySelector(sel); }
  function qsa(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); }

  function findForumContent(){
    return qs('#bbpress-forums')
      || qs('.bbp-body')
      || qs('.bb-single-forum')
      || qs('.bb-rl-forum-content')
      || qs('article.type-forum')
      || null;
  }

  function findRightSidebarCandidate(){
    // BuddyBoss right sidebar variants (best effort)
    return qs('.bb-sidebar.bb-sidebar--right')
      || qs('.bb-right-panel')
      || qs('.bb-side-panel--right')
      || qs('aside.sidebar.sidebar-right')
      || qs('#secondary.sidebar')
      || null;
  }

  function wrapContentWithGrid(contentEl){
    if (!contentEl || contentEl.closest('.rgfde-wrap')) return contentEl.closest('.rgfde-wrap');

    // Choose a safe wrapper: closest element that contains the forum content but not the entire page
    var container = contentEl.closest('.bb-content, .bb-main-content, .bb-content-wrapper, .site-content, #content')
      || contentEl.parentElement;

    if (!container) return null;

    // If container already has two columns from theme, we still can insert our grid inside it around the content only.
    var wrap = document.createElement('div');
    wrap.className = 'rgfde-wrap';

    var left = document.createElement('div');
    left.className = 'rgfde-main';

    // Place wrap right before contentEl in its parent
    var parent = contentEl.parentNode;
    parent.insertBefore(wrap, contentEl);
    left.appendChild(contentEl);
    wrap.appendChild(left);

    return wrap;
  }

  document.addEventListener('DOMContentLoaded', function(){
    if (!window.RGFDE || !RGFDE.html) return;

    var content = findForumContent();
    if (!content) return;

    // Build sidebar node
    var tmp = document.createElement('div');
    tmp.innerHTML = RGFDE.html;
    var sidebar = tmp.firstElementChild;
    if (!sidebar) return;

    // Ensure card body wrapper exists (for styling)
    var card = sidebar.querySelector('.rgfde-card');
    if (card && !card.querySelector('.rgfde-card-body')){
      // move all except first img into body
      var body = document.createElement('div');
      body.className = 'rgfde-card-body';
      var nodes = Array.prototype.slice.call(card.childNodes);
      nodes.forEach(function(n){
        if (n.nodeType === 1 && n.tagName.toLowerCase() === 'img') return;
        body.appendChild(n);
      });
      // keep image if exists
      var img = card.querySelector('img');
      card.innerHTML = '';
      if (img) card.appendChild(img);
      card.appendChild(body);
    }

    // 1) If theme provides a right sidebar container, place there
    var right = findRightSidebarCandidate();
    if (right){
      right.appendChild(sidebar);
      return;
    }

    // 2) Otherwise create our own two-column wrap around the forum content
    var wrap = wrapContentWithGrid(content);
    if (!wrap) return;

    wrap.appendChild(sidebar);
  });
})();
