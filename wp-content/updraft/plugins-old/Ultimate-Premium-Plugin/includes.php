<?php 
/* load all files  */
include( SFSI_PLUS_DOCROOT.'/helpers/sfsi_wordpresshelper.php' );
include( SFSI_PLUS_DOCROOT.'/helpers/class.sfsiSystemInfo.php' );

include( SFSI_PLUS_DOCROOT.'/libs/iconsfactory/iconsOrder.php' );
include( SFSI_PLUS_DOCROOT.'/libs/controllers/sfsi_include_exclude_rules.php' );
include( SFSI_PLUS_DOCROOT.'/libs/controllers/sfsi_include_exclude_rules_onhover.php' );
include( SFSI_PLUS_DOCROOT.'/libs/sfsi_Init_JqueryCss.php' );

include( SFSI_PLUS_DOCROOT.'/libs/controllers/class.sfsiCumulativeCount.php' );
include( SFSI_PLUS_DOCROOT.'/libs/controllers/class.sfsiJobQueue.php' );
include( SFSI_PLUS_DOCROOT.'/libs/controllers/socialHelper/facebook.php' );
include( SFSI_PLUS_DOCROOT.'/libs/controllers/sfsi_socialhelper.php' );

include( SFSI_PLUS_DOCROOT.'/libs/sfsi_install_uninstall.php' );

$dirAdmin = SFSI_PLUS_DOCROOT.'/libs/controllers/admin/';

include( $dirAdmin.'sfsi_buttons_controller.php' );
include( $dirAdmin.'sfsi_iconsUpload_contoller.php' );
include( $dirAdmin.'sfsi_custom_social_sharing_data.php' );
include( $dirAdmin.'sfsi_social_prefetch.php' );

include( SFSI_PLUS_DOCROOT.'/libs/iconsfactory/iconsFactory.php' );
include( SFSI_PLUS_DOCROOT.'/libs/controllers/sfsi_css_settings.php' );
include( SFSI_PLUS_DOCROOT.'/libs/controllers/sfsi_notices.php' );
include( SFSI_PLUS_DOCROOT.'/libs/sfsi_newsletterSubscription.php' );
include( SFSI_PLUS_DOCROOT.'/libs/sfsi_urlShortner.php' );

include( SFSI_PLUS_LICENSING.'sfsi_licensing_setup.php' );
$dirIconPlacement = SFSI_PLUS_DOCROOT.'/libs/controllers/front/iconplacements/';

include( $dirIconPlacement.'sfsi_widget.php' );
include( $dirIconPlacement.'sfsi_floater_icons.php' );
include( $dirIconPlacement.'sfsiocns_OnPosts.php' );
include( $dirIconPlacement.'sfsi_shortcodes.php' );
include( $dirIconPlacement.'sfsi_frontpopUp.php' );
include( $dirIconPlacement.'sfsi_plus_subscribe_widget.php' );
include( $dirIconPlacement.'sfsi_gutenberg_block.php' );

include( SFSI_PLUS_DOCROOT.'/libs/controllers/sfsi_metatags.php' );