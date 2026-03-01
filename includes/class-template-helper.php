<?php
namespace Mangora;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Template_Helper {

    public static function render_card($manga_id) {
        $rating = get_post_meta($manga_id, '_manga_rating', true);
        $status = get_post_meta($manga_id, '_manga_status', true);
        ?>
        <article class="manga-card">
            <a href="<?php echo esc_url(get_permalink($manga_id)); ?>" class="card-link">
                <div class="card-poster">
                    <?php if (has_post_thumbnail($manga_id)) : ?>
                        <?php echo get_the_post_thumbnail($manga_id, 'medium'); ?>
                    <?php else : ?>
                        <div class="no-poster"><?php esc_html_e('No Image', 'mangora'); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($status) : ?>
                        <span class="card-status status-<?php echo esc_attr($status); ?>">
                            <?php echo esc_html(ucfirst($status)); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-info">
                    <h3 class="card-title"><?php echo esc_html(get_the_title($manga_id)); ?></h3>
                    <?php if ($rating) : ?>
                        <span class="card-rating"><?php echo esc_html($rating); ?> ★</span>
                    <?php endif; ?>
                </div>
            </a>
        </article>
        <?php
    }
}
