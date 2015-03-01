<?php
/**
 * The template for displaying Author archive pages
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @since Pluto 1.0
 */

get_header(); ?>

<div class="main-content-w">
  <?php os_the_primary_sidebar(); ?>
  <div class="main-content-i">
    <header class="archive-header">
      <?php echo do_shortcode('[userpro template=view header_only=true max_width=100% user=author]'); ?>
    </header><!-- .archive-header -->

    <div class="content side-padded-content">

    <?php
      $layout_mode = (os_get_use_fixed_height_index_posts() == true) ? 'fitRows' : 'masonry';
      echo '<div class="index-isotope v1" data-layout-mode="'.$layout_mode.'">';
      $os_current_box_counter = 1; $os_ad_block_counter = 0;
      // Start the Loop.
      while ( have_posts() ) : the_post();

        /*
         * Include the post format-specific template for the content. If you want to
         * use this in a child theme, then include a file called called content-___.php
         * (where ___ is the post format) and that will be used instead.
         */
        get_template_part( 'content', get_post_format() );
        os_ad_between_posts();
      endwhile;
      // Previous/next page navigation.

      echo '</div>';

    // Previous/next post navigation.
    if(os_get_next_posts_link($wp_query)): ?>
      <div class="isotope-next-params" data-params="<?php echo os_get_next_posts_link($wp_query); ?>" data-layout-type="v1"></div>
    <?php endif; ?>
    <div class="pagination-w hide-for-isotope">
      <?php if(function_exists('wp_pagenavi')): ?>
        <?php wp_pagenavi(); ?>
      <?php else: ?>
        <?php posts_nav_link(); ?>
      <?php endif; ?>
    </div>
  </div>
    <?php os_footer(); ?>
  </div>
</div>
<?php
get_footer();
