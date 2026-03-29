<?php
/**
 * Front Page Template - Gornji Milanovac Portal
 *
 * @package GM_Portal
 */

defined('ABSPATH') || exit;

get_header();

// Get hero posts (breaking news)
$hero_posts = get_posts(array(
    'post_type'      => 'post',
    'posts_per_page' => 5,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => array(
        array(
            'key'     => '_thumbnail_id',
            'compare' => 'EXISTS'
        )
    )
));

$hero_ids = wp_list_pluck($hero_posts, 'ID');
?>

<!-- Hero Slider -->
<?php if ($hero_posts) : ?>
<section class="gm-hero">
    <div class="gm-hero-slider" id="gm-hero-slider">
        <?php foreach ($hero_posts as $index => $post) : setup_postdata($post); ?>
            <div class="gm-hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <?php if (has_post_thumbnail($post->ID)) : ?>
                    <?php echo get_the_post_thumbnail($post->ID, 'gm-hero', array('loading' => $index === 0 ? 'eager' : 'lazy')); ?>
                <?php else : ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/placeholder-hero.jpg" alt="">
                <?php endif; ?>
                <div class="gm-hero-overlay"></div>
                <div class="gm-hero-content gm-container">
                    <?php
                    $categories = get_the_category($post->ID);
                    if ($categories) :
                    ?>
                        <span class="gm-hero-category"><?php echo esc_html($categories[0]->name); ?></span>
                    <?php endif; ?>
                    <h2 class="gm-hero-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <div class="gm-hero-meta">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <?php echo gm_time_ago_serbian(get_post_time('U', false, $post->ID)); ?>
                        </span>
                        <?php
                        $source = gm_get_post_source($post->ID);
                        if ($source) :
                        ?>
                            <span>Izvor: <?php echo esc_html($source); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; wp_reset_postdata(); ?>
    </div>

    <!-- Slider Navigation -->
    <div class="gm-hero-nav">
        <button class="gm-hero-nav-btn gm-hero-prev" aria-label="Prethodna vest">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button class="gm-hero-nav-btn gm-hero-next" aria-label="Sledeća vest">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <!-- Slider Dots -->
    <div class="gm-hero-dots">
        <?php for ($i = 0; $i < count($hero_posts); $i++) : ?>
            <button class="gm-hero-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>" aria-label="Vest <?php echo $i + 1; ?>"></button>
        <?php endfor; ?>
    </div>
</section>
<?php endif; ?>

<!-- Main Content: Latest News + Sidebar -->
<section class="gm-section">
    <div class="gm-container">
        <div class="gm-grid gm-grid-main-sidebar">
            <!-- Latest News -->
            <div class="gm-main-content">
                <h2 class="gm-section-title">Najnovije vesti</h2>

                <div class="gm-grid gm-grid-2">
                    <?php
                    $latest_news = get_posts(array(
                        'post_type'      => 'post',
                        'posts_per_page' => 6,
                        'post_status'    => 'publish',
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                        'post__not_in'   => $hero_ids,
                    ));

                    foreach ($latest_news as $post) : setup_postdata($post);
                    ?>
                        <article class="gm-card">
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
                                <h3 class="gm-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
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
                    <?php endforeach; wp_reset_postdata(); ?>
                </div>

                <div class="text-center mt-8">
                    <a href="<?php echo esc_url(home_url('/vesti/')); ?>" class="gm-btn gm-btn-primary">
                        Sve vesti
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="gm-sidebar">
                <!-- Weather Widget -->
                <div class="gm-widget gm-widget-weather">
                    <h3 class="gm-widget-title">Vreme</h3>
                    <?php echo do_shortcode('[gm_weather]'); ?>
                </div>

                <!-- Quick Links -->
                <div class="gm-widget">
                    <h3 class="gm-widget-title">Brzi linkovi</h3>
                    <ul class="gm-quick-links">
                        <li>
                            <a href="<?php echo esc_url(home_url('/vazni-telefoni/')); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                Važni telefoni
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/zdravlje/')); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Zdravlje
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/poslovi-gornji-milanovac/')); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Poslovi
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/istorija-gornjeg-milanovca/')); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Istorija grada
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/oglasi/')); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Oglasi
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Popular News -->
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

<!-- Featured Categories -->
<section class="gm-featured-cats">
    <div class="gm-container">
        <h2 class="gm-section-title">Kategorije</h2>

        <div class="gm-cat-tabs">
            <button class="gm-cat-tab active" data-category="sport">Sport</button>
            <button class="gm-cat-tab" data-category="kultura">Kultura</button>
            <button class="gm-cat-tab" data-category="ekonomija">Ekonomija</button>
            <button class="gm-cat-tab" data-category="drustvo">Društvo</button>
        </div>

        <div class="gm-cat-content">
            <?php
            // Get posts from first category (Sport)
            $cat_posts = get_posts(array(
                'post_type'      => 'post',
                'posts_per_page' => 4,
                'post_status'    => 'publish',
                'category_name'  => 'sport',
            ));
            ?>

            <div class="gm-grid gm-grid-4" id="gm-cat-posts">
                <?php foreach ($cat_posts as $post) : setup_postdata($post); ?>
                    <article class="gm-card">
                        <div class="gm-card-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('gm-card'); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/placeholder-card.jpg" alt="">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="gm-card-body">
                            <h3 class="gm-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="gm-card-meta">
                                <span class="gm-card-date">
                                    <?php echo gm_time_ago_serbian(get_post_time('U')); ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
</section>

<!-- Listings Teaser -->
<section class="gm-listings-section">
    <div class="gm-container">
        <h2 class="gm-section-title">Oglasi</h2>

        <?php
        $listings = gm_get_featured_listings(3);

        if ($listings) :
        ?>
            <div class="gm-grid gm-grid-3">
                <?php foreach ($listings as $listing) : setup_postdata($listing); ?>
                    <article class="gm-listing-card">
                        <div class="gm-listing-image">
                            <a href="<?php echo get_permalink($listing->ID); ?>">
                                <?php if (has_post_thumbnail($listing->ID)) : ?>
                                    <?php echo get_the_post_thumbnail($listing->ID, 'gm-card'); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/placeholder-listing.jpg" alt="">
                                <?php endif; ?>
                            </a>
                            <span class="gm-listing-badge">Istaknuto</span>
                        </div>
                        <div class="gm-listing-body">
                            <?php
                            $terms = get_the_terms($listing->ID, 'rtcl_category');
                            if ($terms && !is_wp_error($terms)) :
                            ?>
                                <span class="gm-listing-category"><?php echo esc_html($terms[0]->name); ?></span>
                            <?php endif; ?>
                            <h3 class="gm-listing-title">
                                <a href="<?php echo get_permalink($listing->ID); ?>"><?php echo get_the_title($listing->ID); ?></a>
                            </h3>
                            <?php
                            $price = get_post_meta($listing->ID, '_price', true);
                            if ($price) :
                            ?>
                                <div class="gm-listing-price"><?php echo number_format($price, 0, ',', '.'); ?> RSD</div>
                            <?php endif; ?>
                            <div class="gm-listing-meta">
                                <span><?php echo gm_time_ago_serbian(get_post_time('U', false, $listing->ID)); ?></span>
                                <span>Gornji Milanovac</span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <div class="gm-grid gm-grid-3">
                <!-- Placeholder listings when no real ones exist -->
                <article class="gm-listing-card">
                    <div class="gm-listing-image">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/placeholder-listing.jpg" alt="">
                    </div>
                    <div class="gm-listing-body">
                        <span class="gm-listing-category">Nekretnine</span>
                        <h3 class="gm-listing-title">
                            <a href="<?php echo esc_url(home_url('/oglasi/')); ?>">Postavite prvi oglas</a>
                        </h3>
                        <p style="color: var(--gm-gray); font-size: var(--gm-text-sm);">
                            Budite među prvima koji objavljuju oglase na našem portalu.
                        </p>
                    </div>
                </article>
                <article class="gm-listing-card">
                    <div class="gm-listing-image">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/placeholder-listing.jpg" alt="">
                    </div>
                    <div class="gm-listing-body">
                        <span class="gm-listing-category">Zaposlenje</span>
                        <h3 class="gm-listing-title">
                            <a href="<?php echo esc_url(home_url('/listing-form/')); ?>">Oglašavanje je besplatno</a>
                        </h3>
                        <p style="color: var(--gm-gray); font-size: var(--gm-text-sm);">
                            Registrujte se i postavite oglas za posao ili uslugu.
                        </p>
                    </div>
                </article>
                <article class="gm-listing-card">
                    <div class="gm-listing-image">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/placeholder-listing.jpg" alt="">
                    </div>
                    <div class="gm-listing-body">
                        <span class="gm-listing-category">Usluge</span>
                        <h3 class="gm-listing-title">
                            <a href="<?php echo esc_url(home_url('/poslovni-imenik/')); ?>">Poslovni imenik</a>
                        </h3>
                        <p style="color: var(--gm-gray); font-size: var(--gm-text-sm);">
                            Pronađite lokalne firme i usluge u Gornjem Milanovcu.
                        </p>
                    </div>
                </article>
            </div>
        <?php endif; ?>

        <div class="text-center mt-8">
            <a href="<?php echo esc_url(home_url('/oglasi/')); ?>" class="gm-btn gm-btn-primary">
                Svi oglasi
            </a>
            <a href="<?php echo esc_url(home_url('/listing-form/')); ?>" class="gm-btn gm-btn-outline" style="margin-left: 1rem;">
                Postavi oglas
            </a>
        </div>
    </div>
</section>

<!-- About the City -->
<section class="gm-about-city">
    <div class="gm-container">
        <div class="gm-about-content">
            <h2 class="gm-about-title">O Gornjem Milanovcu</h2>
            <p class="gm-about-text">
                Gornji Milanovac je grad u centralnoj Srbiji, smešten u dolini Despotovice,
                okružen predivnim planinama Rudnik i Suvobor. Poznat je po bogatoj istoriji,
                Takovskom ustanku i spomen-kompleksu na Takovu gde je podignut Drugi srpski ustanak.
            </p>
            <p class="gm-about-text">
                Danas je Gornji Milanovac moderan grad sa razvijenom industrijom,
                posebno metalskom i automobilskom. Grad je poznat i po proizvođačima
                auto-delova koji izvoze u ceo svet.
            </p>
            <a href="<?php echo esc_url(home_url('/o-gradu/')); ?>" class="gm-btn gm-btn-white gm-btn-lg">
                Saznajte više o gradu
            </a>

            <div class="gm-about-stats">
                <div class="gm-stat">
                    <span class="gm-stat-number">44.000</span>
                    <span class="gm-stat-label">Stanovnika</span>
                </div>
                <div class="gm-stat">
                    <span class="gm-stat-number">836</span>
                    <span class="gm-stat-label">km²</span>
                </div>
                <div class="gm-stat">
                    <span class="gm-stat-number">1853</span>
                    <span class="gm-stat-label">Godina osnivanja</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
