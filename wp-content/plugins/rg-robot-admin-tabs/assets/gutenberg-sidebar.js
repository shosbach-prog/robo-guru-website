(function(wp) {
    var registerPlugin = wp.plugins.registerPlugin;
    var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
    var createElement = wp.element.createElement;
    var Button = wp.components.Button;
    var Fragment = wp.element.Fragment;

    var RobotDetailsSidebarPanel = function() {
        var scrollToMetaBox = function() {
            var metaBox = document.getElementById('rg_robot_details_tabs');
            if (metaBox) {
                metaBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Flash effect to highlight
                metaBox.style.transition = 'box-shadow 0.3s';
                metaBox.style.boxShadow = '0 0 0 4px #007cba';
                setTimeout(function() {
                    metaBox.style.boxShadow = 'none';
                }, 2000);
            } else {
                alert('Die Roboter-Details Meta-Box wurde nicht gefunden. Bitte scrollen Sie nach unten unter den Editor.');
            }
        };

        var scrollToGallery = function() {
            var galleryBox = document.getElementById('rg_robot_gallery');
            if (galleryBox) {
                galleryBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
                galleryBox.style.transition = 'box-shadow 0.3s';
                galleryBox.style.boxShadow = '0 0 0 4px #007cba';
                setTimeout(function() {
                    galleryBox.style.boxShadow = 'none';
                }, 2000);
            }
        };

        return createElement(
            PluginDocumentSettingPanel,
            {
                name: 'rg-robot-details-panel',
                title: 'Roboter-Details',
                icon: 'admin-generic'
            },
            createElement(
                Fragment,
                null,
                createElement(
                    'p',
                    { style: { marginBottom: '12px', color: '#666' } },
                    'Die technischen Details und die Galerie befinden sich unterhalb des Editors.'
                ),
                createElement(
                    Button,
                    {
                        variant: 'primary',
                        onClick: scrollToMetaBox,
                        style: { width: '100%', justifyContent: 'center', marginBottom: '8px' }
                    },
                    'Zu Roboter-Details springen'
                ),
                createElement(
                    Button,
                    {
                        variant: 'secondary',
                        onClick: scrollToGallery,
                        style: { width: '100%', justifyContent: 'center' }
                    },
                    'Zur Galerie springen'
                )
            )
        );
    };

    registerPlugin('rg-robot-details-sidebar', {
        render: RobotDetailsSidebarPanel,
        icon: 'admin-generic'
    });

})(window.wp);
