<div class="item-isotope">
  <article id="post-<?php the_ID(); ?>" <?php post_class('pluto-post-box'); ?>>
    <div class="post-body">
      <?php osetin_top_social_share_index(); ?>
      <?php osetin_get_media_content(); ?>

      <?php if(os_is_post_element_active('title') || os_is_post_element_active('category') || os_is_post_element_active('excerpt')){ ?>
        <div class="post-content-body">
          <?php if(os_is_post_element_active('title')): ?>
            <h4 class="post-title entry-title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
          <?php endif; ?>
          <?php if(os_is_post_element_active('excerpt')): ?>
            <div class="post-content entry-summary"><?php echo os_excerpt(get_field('index_excerpt_length', 'option'), os_is_post_element_active('read_more')); ?></div>
          <?php endif; ?>
        </div>
      <?php } ?>
    </div>
    <?php if(os_is_post_element_active('date') || os_is_post_element_active('author') || os_is_post_element_active('like')): ?>
      <div class="post-meta entry-meta">



        <?php if(os_is_post_element_active('category')): ?>
          <?php echo get_the_category_list(); ?>
        <?php endif; ?>


        <?php if(os_is_post_element_active('date')): ?>
          <div class="meta-date">
            <i class="fa os-icon-clock-o"></i>
            <time class="entry-date updated" datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date('M j'); ?></time>
          </div>
        <?php endif; ?>

        <?php if(os_is_post_element_active('like') && function_exists('zilla_likes')): ?>
          <div class="meta-like">
            <?php if(!isset($facebook_likes)) $facebook_likes = false; ?>
            <?php if($facebook_likes){ ?>
              <?php os_facebook_like(); ?>
            <?php }else{ ?>
              <?php zilla_likes(); ?>
            <?php } ?>
          </div>
        <?php endif; ?>



      </div>
    <?php endif; ?>
  </article>
</div>