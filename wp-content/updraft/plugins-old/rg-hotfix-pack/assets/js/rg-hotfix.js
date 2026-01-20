/* Robo-Guru Hotfix Pack
 *
 * Goal:
 * 1) Guard against a specific, common mobile-only crash:
 *    Uncaught (in promise) TypeError: Cannot read properties of null (reading 'setAttribute')
 *    This typically comes from a minified bundle that tries to setAttribute on a missing element.
 *
 * 2) Trigger a small layout recalculation for embeds after load (helps with lazy/consent wrappers).
 */

(function () {
  'use strict';

  function str(x){ return (typeof x === 'string') ? x : ''; }

  function matchesNullSetAttributeError(payload) {
    try {
      var msg = '';
      var stack = '';

      if (payload && payload.reason) {
        msg = str(payload.reason.message);
        stack = str(payload.reason.stack);
      } else if (payload && payload.error) {
        msg = str(payload.error.message);
        stack = str(payload.error.stack);
      } else if (payload && payload.message) {
        msg = str(payload.message);
      }

      var hay = (msg + '\n' + stack).toLowerCase();

      if (hay.indexOf('setattribute') === -1) return false;
      if (hay.indexOf('cannot read properties of null') === -1 && hay.indexOf("can't read properties of null") === -1) return false;

      // Often the bundle name looks like ade9920...js in the console.
      // We don't require it, but if present, it's a strong match.
      return true;
    } catch (e) {
      return false;
    }
  }

  // Swallow *only* the known crash to avoid breaking other error reporting.
  window.addEventListener('unhandledrejection', function (event) {
    if (matchesNullSetAttributeError(event)) {
      try { event.preventDefault(); } catch (e) {}
    }
  });

  window.addEventListener('error', function (event) {
    if (matchesNullSetAttributeError(event)) {
      try { event.preventDefault(); } catch (e) {}
      return false;
    }
  });

  function normalizeEmbeds(root){
    root = root || document;

    // Ensure Gutenberg embed wrappers have aspect ratio.
    var wrappers = root.querySelectorAll('.wp-block-embed__wrapper');
    wrappers.forEach(function(w){
      if (!w.style.aspectRatio) {
        w.style.aspectRatio = '16 / 9';
      }
      if (!w.style.width) {
        w.style.width = '100%';
      }
    });

    // Force iframes/videos to fill their wrapper.
    var ifr = root.querySelectorAll('.wp-block-embed__wrapper iframe, .wp-block-embed iframe');
    ifr.forEach(function(f){
      f.setAttribute('loading','lazy');
      f.style.width = '100%';
      f.style.height = '100%';
      f.style.display = 'block';
    });

    var vids = root.querySelectorAll('video');
    vids.forEach(function(v){
      // Mobile inline playback helper.
      v.setAttribute('playsinline','');
      v.setAttribute('webkit-playsinline','');
      // If it should autoplay, it must be muted.
      if (v.hasAttribute('autoplay') && !v.muted) v.muted = true;
    });

    // Make common consent overlays clickable.
    var overlays = root.querySelectorAll('.consent-overlay, .video-consent, .rg-video-consent');
    overlays.forEach(function(o){
      o.style.pointerEvents = 'auto';
      o.style.zIndex = '50';
    });
  }

  function kickLayout(){
    try { window.dispatchEvent(new Event('resize')); } catch(e) {}
    try { window.dispatchEvent(new Event('scroll')); } catch(e) {}
  }

  document.addEventListener('DOMContentLoaded', function(){
    normalizeEmbeds(document);
    // Some tools replace DOM after a tick.
    setTimeout(function(){ normalizeEmbeds(document); kickLayout(); }, 300);
    setTimeout(function(){ normalizeEmbeds(document); kickLayout(); }, 1200);
  });
})();
