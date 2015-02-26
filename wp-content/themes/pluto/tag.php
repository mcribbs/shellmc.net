<?php
/**
 * The template for displaying Tag pages
 *
 * Used to display archive-type pages for posts in a tag.
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
  <?php if ( have_posts() ) : ?>

    <header class="archive-header">
      <h3 class="archive-title"><?php printf( __( 'Tag Archives: %s', 'pluto' ), single_tag_title( '', false ) ); ?></h3>

      <?php
        // Show an optional term description.
        $term_description = term_description();
        if ( ! empty( $term_description ) ) :
          printf( '<div class="taxonomy-description">%s</div>', $term_description );
        endif;
      ?>
    </header><!-- .archive-header -->


    <div class="content side-padded-content">
      <div class="index-isotope v1" data-layout-mode="<?php echo (os_get_use_fixed_height_index_posts() == true) ? 'fitRows' : 'masonry'; ?>">
        <?php
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
        endwhile; ?>
      </div>
      <?php if(os_get_next_posts_link($wp_query)): ?>
        <div class="isotope-next-params" data-params="<?php echo os_get_next_posts_link($wp_query); ?>" data-layout-type="v1"></div>
        <?php if(os_get_current_navigation_type() == 'infinite_button'): ?>
        <div class="load-more-posts-button-w">
          <a href="#"><i class="os-icon-plus"></i> <span><?php _e('Load More Posts', 'pluto'); ?></span></a>
        </div>
        <?php endif; ?>
      <?php endif; ?>
      <div class="pagination-w hide-for-isotope">
        <?php if(function_exists('wp_pagenavi') && os_get_current_navigation_type() != 'default'): ?>
          <?php wp_pagenavi(); ?>
        <?php else: ?>
          <?php posts_nav_link(); ?>
        <?php endif; ?>
      </div>
    </div>
    <?php
    else :
      // If no content, include the "No posts found" template.
      get_template_part( 'content', 'none' );
    endif;
    ?>
    <?php os_footer(); ?>
  </div>
</div>
<?php
get_footer();
