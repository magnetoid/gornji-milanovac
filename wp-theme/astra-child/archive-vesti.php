<?php
/**
 * Archive Template for News (Vesti) - Gornji Milanovac Portal
 *
 * This template is used for the news category archive
 *
 * @package GM_Portal
 */

defined('ABSPATH') || exit;

get_header();
?>

<!-- Archive Header -->
<div class="gm-archive-header">
    <div class="gm-container">
        <h1 class="gm-archive-title">Vesti iz Gornjeg Milanovca</h1>
        <p class="gm-archive-desc">
            Najnovije vesti i dešavanja iz grada i okoline.
            Pratite aktuelnosti, sport, kulturu i više.
        </p>
    </div>
</div>

<section class="gm-section">
    <div class="gm-container">
        <div class="gm-grid gm-grid-main-sidebar">
            <!-- Main Content -->
            <div class="gm-main-content">
                <?php if (have_posts()) : ?>
                    <div class="gm-grid gm-grid-2">
                        <?php while (have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('gm-card'); ?>>
                                <div class="gm-card-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('gm-card'); ?>
                                        <?php else : ?>
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/placeholder-card.jpg" alt="">
                                        <?php endif; ?>
                                    </a>
                                    <?php
                                    $categories = get_the_category();
                                    if ($categories) :
                                    ?>
                                        <span class="gm-card-category" style="background-color: <?php echo gm_get_category_color($categories[0]->slug); ?>">
                                            <?php echo esc_html($categories[0]->name); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="gm-card-body">
                                    <h2 class="gm-card-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <p class="gm-card-excerpt">
                                        <?php echo gm_truncate(get_the_excerpt(), 120); ?>
                                    </p>
                                    <div class="gm-card-meta">
                                        <span class="gm-card-date">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <?php echo gm_time_ago_serbian(get_post_time('U')); ?>
                                        </span>
                                        <?php
                                        $source = gm_get_post_source(get_the_ID());
                                        if ($source) :
                                        ?>
                                            <span class="gm-card-source"><?php echo esc_html($source); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="gm-pagination">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => '&laquo; Prethodna',
                            'next_text' => 'Sledeća &raquo;',
                            'type'      => 'plain',
                        ));
                        ?>
                    </div>

                <?php else : ?>
                    <div class="gm-no-posts">
                        <h2>Nema vesti</h2>
                        <p>Trenutno nema objavljenih vesti u ovoj kategoriji.</p>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="gm-btn gm-btn-primary">
                            Nazad na početnu
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="gm-sidebar">
                <!-- Categories -->
                <div class="gm-widget">
                    <h3 class="gm-widget-title">Kategorije</h3>
                    <ul class="gm-quick-links">
                        <?php
                        $categories = get_categories(array(
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'number'     => 10,
                            'hide_empty' => true,
                        ));
                        foreach ($categories as $cat) :
                        ?>
                            <li>
                                <a href="<?php echo get_category_link($cat->term_id); ?>">
                                    <?php echo esc_html($cat->name); ?>
                                    <span style="color: var(--gm-gray); margin-left: auto;">(<?php echo $cat->count; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Popular Posts -->
                <div class="gm-widget">
                    <h3 class="gm-widget-title">Popularne vesti</h3>
                    <?php
                    $popular_posts = get_posts(array(
                        'post_type'      => 'post',
                        'posts_per_page' => 5,
                        'post_status'    => 'publish',
                        'orderby'        => 'comment_count',
                        'order'          => 'DESC',
                    ));

                    foreach ($popular_posts as $post) : setup_postdata($post);
                    ?>
                        <div class="gm-card-small">
                            <div class="gm-card-small-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('gm-thumbnail'); ?>
                                    <?php else : ?>
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/placeholder-thumb.jpg" alt="">
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="gm-card-small-content">
                                <h4 class="gm-card-small-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <span class="gm-card-small-date">
                                    <?php echo gm_time_ago_serbian(get_post_time('U')); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; wp_reset_postdata(); ?>
                </div>

                <?php if (is_active_sidebar('sidebar-news')) : ?>
                    <?php dynamic_sidebar('sidebar-news'); ?>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php get_footer(); ?>
