<?php
/**
 * Template Name: Robo Finder (ohne H1)
 */

get_header(); ?>

<main id="primary" class="site-main robo-finder-page">

  <?php
  // WICHTIG: Kein the_title(), kein entry-header!
  while ( have_posts() ) :
    the_post();
    the_content();
  endwhile;
  ?>

</main>

<?php get_footer();
