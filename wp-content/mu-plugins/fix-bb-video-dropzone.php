<?php
/**
 * Fix BuddyBoss Video Dropzone empty previewTemplate
 */
add_action('wp_footer', function() {
    if (!bp_is_active('video')) return;
    ?>
    <script>
    (function(){
        var origDropzone = window.Dropzone;
        if (!origDropzone) return;
        var origInit = origDropzone.prototype.init;
        origDropzone.prototype.init = function() {
            if (!this.options.previewTemplate || this.options.previewTemplate.trim() === '') {
                var tpl = document.getElementsByClassName('uploader-post-video-template');
                if (tpl.length && tpl[0].innerHTML.trim()) {
                    this.options.previewTemplate = tpl[0].innerHTML.trim();
                }
            }
            return origInit.apply(this, arguments);
        };
    })();
    </script>
    <?php
}, 5);
