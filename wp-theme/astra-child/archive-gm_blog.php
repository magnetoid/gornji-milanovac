<?php
/**
 * Archive Template for GM Blog - Gornji Milanovac Portal
 *
 * This template is used for the editorial blog archive (gm_blog CPT)
 *
 * @package GM_Portal
 */

defined('ABSPATH') || exit;

get_header();
?>

<!-- Archive Header -->
<div class="gm-archive-header">
    <div class="gm-container">
        <h1 class="gm-archive-title">Blog</h1>
        <p class="gm-archive-desc">
            Autorski tekstovi, komentari i analize o životu u Gornjem Milanovcu.
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
                                    $terms = get_the_terms(get_the_ID(), 'gm_blog_category');
                                    if ($terms && !is_wp_error($terms)) :
                                    ?>
                                        <span class="gm-card-category">
                                            <?php echo esc_html($terms[0]->name); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="gm-card-body">
                                    <h2 class="gm-card-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <p class="gm-card-excerpt">
                                        <?php echo gm_truncate(get_the_excerpt(), 150); ?>
                                    </p>
                                    <div class="gm-card-meta">
                                        <span class="gm-card-date">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <?php echo gm_time_ago_serbian(get_post_time('U')); ?>
                                        </span>
                                        <span class="gm-card-author">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <?php the_author(); ?>
                                        </span>
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
                    <div class="gm-no-posts" style="text-align: center; padding: 4rem 2rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="64" height="64" style="margin: 0 auto 1rem; color: var(--gm-light-gray);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <h2>Uskoro novi članci</h2>
                        <p style="color: var(--gm-gray); margin-bottom: 2rem;">
                            Naš tim priprema zanimljive članke i analize.
                            Pratite nas za najnovije sadržaje.
                        </p>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="gm-btn gm-btn-primary">
                            Nazad na početnu
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="gm-sidebar">
                <!-- Blog Categories -->
                <div class="gm-widget">
                    <h3 class="gm-widget-title">Blog kategorije</h3>
                    <ul class="gm-quick-links">
                        <?php
                        $blog_cats = get_terms(array(
                            'taxonomy'   => 'gm_blog_category',
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'hide_empty' => true,
                        ));

                        if ($blog_cats && !is_wp_error($blog_cats)) :
                            foreach ($blog_cats as $cat) :
                        ?>
                            <li>
                                <a href="<?php echo get_term_link($cat); ?>">
                                    <?php echo esc_html($cat->name); ?>
                                    <span style="color: var(--gm-gray); margin-left: auto;">(<?php echo $cat->count; ?>)</span>
                                </a>
                            </li>
                        <?php
                            endforeach;
                        else :
                        ?>
                            <li><a href="#">Kolumne</a></li>
                            <li><a href="#">Reportaže</a></li>
                            <li><a href="#">Intervjui</a></li>
                            <li><a href="#">Životne priče</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Authors -->
                <div class="gm-widget">
                    <h3 class="gm-widget-title">Autori</h3>
                    <?php
                    $authors = get_users(array(
                        'has_published_posts' => array('gm_blog'),
                        'orderby'             => 'post_count',
                        'order'               => 'DESC',
                        'number'              => 5,
                    ));

                    foreach ($authors as $author) :
                    ?>
                        <div class="gm-card-small" style="margin-bottom: 0.5rem;">
                            <div class="gm-card-small-image" style="border-radius: 50%;">
                                <?php echo get_avatar($author->ID, 80); ?>
                            </div>
                            <div class="gm-card-small-content">
                                <h4 class="gm-card-small-title" style="margin-bottom: 0;">
                                    <a href="<?php echo get_author_posts_url($author->ID); ?>">
                                        <?php echo esc_html($author->display_name); ?>
                                    </a>
                                </h4>
                                <span class="gm-card-small-date">
                                    <?php echo count_user_posts($author->ID, 'gm_blog'); ?> članaka
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Newsletter -->
                <div class="gm-widget" style="background: linear-gradient(135deg, var(--gm-primary), var(--gm-primary-dark)); color: white;">
                    <h3 class="gm-widget-title" style="color: white; border-bottom-color: var(--gm-secondary);">Newsletter</h3>
                    <p style="color: rgba(255,255,255,0.9); margin-bottom: 1rem;">
                        Prijavite se za naš nedeljni newsletter i primajte najbolje članke direktno u inbox.
                    </p>
                    <form class="gm-newsletter-form" style="flex-direction: column;" action="<?php echo esc_url(home_url('/wp-json/gm/v1/lead')); ?>" method="post">
                        <?php wp_nonce_field('gm_newsletter', 'gm_newsletter_nonce'); ?>
                        <input type="hidden" name="tip_upita" value="newsletter">
                        <input type="email" name="email" class="gm-newsletter-input" placeholder="Vaša email adresa" required style="margin-bottom: 0.5rem;">
                        <button type="submit" class="gm-newsletter-btn" style="width: 100%;">Prijavi se</button>
                    </form>
                </div>

                <?php if (is_active_sidebar('sidebar-news')) : ?>
                    <?php dynamic_sidebar('sidebar-news'); ?>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php get_footer(); ?>
