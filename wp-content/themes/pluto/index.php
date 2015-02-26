<?php
/**
 * The main template file.
 *
 * @package Pluto
 */
?>
<?php get_header(); ?>
<div class="main-content-w">

  <?php os_the_primary_sidebar(true); ?>

  <div class="main-content-i">


    <?php if(os_get_show_featured_posts_on_index() == true){ ?>
      <?php if(os_get_featured_posts_type_on_index() == 'compact'){ ?>
        <?php echo do_shortcode('[os_featured_slider]'); ?>
      <?php }else{ ?>
        <?php echo do_shortcode('[os_featured_carousel]'); ?>
      <?php } ?>
    <?php } ?>
    <div class="content side-padded-content">
      <?php if ( is_active_sidebar( 'sidebar-3' ) && (get_field('hide_top_ad_from_index', 'option') != true) ) : ?>
        <div class="top-sidebar-wrapper"><?php dynamic_sidebar( 'sidebar-3' ); ?></div>
      <?php endif; ?>
      <div id="primary-content" class="index-isotope v1" data-layout-mode="<?php echo (os_get_use_fixed_height_index_posts() == true) ? 'fitRows' : 'masonry'; ?>">
        <?php $os_current_box_counter = 1; $os_ad_block_counter = 0; ?>
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'content', get_post_format() ); ?>
          <?php os_ad_between_posts(); ?>
        <?php endwhile; endif; ?>
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


    <?php os_footer(); ?>
  </div>
</div>
<?php get_footer(); ?>