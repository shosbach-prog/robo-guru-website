
<?php
/**
 * Plugin Template: Archive Robo Robot
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<main id="primary" class="site-main" style="max-width:1200px;margin:0 auto;padding:24px 16px;">
  <header style="margin-bottom:18px;">
    <h1 style="margin:0 0 10px 0;font-weight:900;letter-spacing:-0.02em;">Roboter</h1>
    <p style="margin:0;opacity:.8;">Vergleiche, filtere und finde den passenden Reinigungs- oder Serviceroboter für deinen Einsatz.</p>
  </header>

  <div class="rf-archive-content">
    <?php echo do_shortcode('[robo_robot_grid]'); ?>
  </div>
</main>
<?php get_footer(); ?>
