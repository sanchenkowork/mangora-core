<?php
/**
 * Archive template for manga listing
 */
get_header();
?>

<main id="primary" class="site-main mangora-archive">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Manga Library', 'mangora'); ?></h1>
    </header>

    <div class="mangora-filters">
        <form id="mangora-filter-form">
            <div class="filter-group">
                <label for="filter-genre"><?php esc_html_e('Genre', 'mangora'); ?></label>
                <select id="filter-genre" name="genre[]" multiple>
                    <?php
                    $genres = get_terms(['taxonomy' => 'genre', 'hide_empty' => false]);
                    if (!is_wp_error($genres) && !empty($genres)) :
                        foreach ($genres as $genre) :
                    ?>
                        <option value="<?php echo esc_attr($genre->term_id); ?>">
                            <?php echo esc_html($genre->name); ?>
                        </option>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-status"><?php esc_html_e('Status', 'mangora'); ?></label>
                <select id="filter-status" name="status">
                    <option value=""><?php esc_html_e('All', 'mangora'); ?></option>
                    <?php
                    $statuses = get_terms(['taxonomy' => 'manga_status', 'hide_empty' => false]);
                    if (!is_wp_error($statuses) && !empty($statuses)) :
                        foreach ($statuses as $status) :
                    ?>
                        <option value="<?php echo esc_attr($status->slug); ?>">
                            <?php echo esc_html($status->name); ?>
                        </option>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-year"><?php esc_html_e('Year', 'mangora'); ?></label>
                <select id="filter-year" name="year">
                    <option value=""><?php esc_html_e('All', 'mangora'); ?></option>
                    <?php
                    $current_year = date('Y');
                    for ($y = $current_year; $y >= 1990; $y--) :
                    ?>
                        <option value="<?php echo esc_attr($y); ?>"><?php echo esc_html($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="button"><?php esc_html_e('Filter', 'mangora'); ?></button>
            <button type="reset" class="button reset"><?php esc_html_e('Reset', 'mangora'); ?></button>
        </form>
    </div>

    <div id="mangora-results" class="mangora-grid">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                Mangora\Template_Helper::render_card(get_the_ID());
            endwhile;
        else :
            ?>
            <p class="no-results"><?php esc_html_e('No manga found.', 'mangora'); ?></p>
            <?php
        endif;
        ?>
    </div>

    <div id="mangora-pagination" class="mangora-pagination">
        <?php the_posts_pagination(); ?>
    </div>
</main>

<?php
get_sidebar();
get_footer();
