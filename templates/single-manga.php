<?php
/**
 * Single manga template with episode list
 */
get_header();

$manga_id = get_the_ID();
$rating = get_post_meta($manga_id, '_manga_rating', true);
$year = get_post_meta($manga_id, '_manga_year', true);
$status = get_post_meta($manga_id, '_manga_status', true);
$genres = get_the_terms($manga_id, 'genre');
?>

<main id="primary" class="site-main mangora-single">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('mangora-manga'); ?>>
            <div class="manga-header">
                <div class="manga-poster">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php else : ?>
                        <div class="no-poster"><?php esc_html_e('No Image', 'mangora'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="manga-info">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

                    <div class="meta-fields">
                        <?php if ($rating) : ?>
                            <span class="rating"><?php printf( esc_html__( 'Rating: %.1f/10', 'mangora' ), $rating ); ?></span>
                        <?php endif; ?>

                        <?php if ($year) : ?>
                            <span class="year"><?php printf( esc_html__( 'Year: %d', 'mangora' ), $year ); ?></span>
                        <?php endif; ?>

                        <?php if ($status) : ?>
                            <span class="status status-<?php echo esc_attr($status); ?>">
                                <?php echo esc_html(ucfirst($status)); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($genres && !is_wp_error($genres)) : ?>
                        <div class="genres">
                            <?php foreach ($genres as $genre) : ?>
                                <a href="<?php echo esc_url(get_term_link($genre)); ?>" class="genre-tag">
                                    <?php echo esc_html($genre->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="manga-description">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <div class="episodes-section">
                <h2><?php esc_html_e('Episodes', 'mangora'); ?></h2>
                <div id="mangora-episodes" class="episodes-list" data-manga-id="<?php echo esc_attr($manga_id); ?>">
                    <span class="loading"><?php esc_html_e('Loading episodes...', 'mangora'); ?></span>
                </div>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<!-- Video Player Modal -->
<div id="mangora-player-modal" class="mangora-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <button class="modal-close">&times;</button>
        <div id="mangora-player-container"></div>
    </div>
</div>

<?php
get_sidebar();
get_footer();
