
<?php
/**
 * Plugin Template: Single Robo Robot
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<main id="primary" class="site-main rf-robot-single" style="max-width:1200px;margin:0 auto;padding:24px 16px;">
  <?php while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <header style="margin-bottom:18px;">
        <h1 style="margin:0 0 10px 0;font-weight:900;letter-spacing:-0.02em;"><?php the_title(); ?></h1>
        <?php if ( has_post_thumbnail() ) : ?>
          <div style="border-radius:18px;overflow:hidden;box-shadow:0 14px 34px rgba(0,0,0,.10);margin:0 0 16px 0;">
            <?php the_post_thumbnail('large', array('style' => 'width:100%;height:auto;display:block;')); ?>
          </div>
        <?php endif; ?>
      </header>

      <div class="rf-single-content">
        <?php
          // Robo Finder Pro injects the full layout via the_content filter (tech data + article + ideal/nicht ideal)
          the_content();
        ?>
      </div>
    </article>
  <?php endwhile; ?>
</main>
<?php get_footer(); ?>
