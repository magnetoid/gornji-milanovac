<?php
/**
 * Header Template - Gornji Milanovac Portal
 *
 * @package GM_Portal
 */

defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php gm_before_header(); ?>

<!-- Top Bar -->
<div class="gm-topbar">
    <div class="gm-container">
        <div class="gm-topbar-left">
            <!-- Datum -->
            <div class="gm-topbar-date">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span><?php echo gm_format_date_serbian(); ?></span>
            </div>

            <!-- Vreme -->
            <div class="gm-topbar-weather">
                <a href="https://www.yr.no/en/forecast/daily-table/2-789128/Serbia/Central%20Serbia/Moravica%20District/Gornji%20Milanovac" target="_blank" rel="noopener">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                    </svg>
                    <span>Vreme u GM</span>
                </a>
            </div>
        </div>

        <div class="gm-topbar-right">
            <!-- Društvene mreže -->
            <div class="gm-topbar-social">
                <a href="https://www.facebook.com/gornjimilanovac" target="_blank" rel="noopener" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                    </svg>
                </a>
                <a href="https://www.instagram.com/gornjimilanovac" target="_blank" rel="noopener" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                    </svg>
                </a>
                <a href="https://twitter.com/gornjimilanovac" target="_blank" rel="noopener" aria-label="Twitter/X">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <a href="https://www.youtube.com/@gornjimilanovac" target="_blank" rel="noopener" aria-label="YouTube">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Breaking News Ticker -->
<?php
$breaking_news = gm_get_breaking_news(5);
if ($breaking_news) :
?>
<div class="gm-breaking-news">
    <div class="gm-container">
        <span class="gm-breaking-label">Najnovije</span>
        <div class="gm-breaking-ticker">
            <div class="gm-breaking-ticker-inner">
                <?php foreach ($breaking_news as $news) : ?>
                    <div class="gm-breaking-item">
                        <a href="<?php echo get_permalink($news->ID); ?>">
                            <?php echo esc_html($news->post_title); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
                <!-- Duplicate for seamless loop -->
                <?php foreach ($breaking_news as $news) : ?>
                    <div class="gm-breaking-item">
                        <a href="<?php echo get_permalink($news->ID); ?>">
                            <?php echo esc_html($news->post_title); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Main Header -->
<header class="gm-header" role="banner">
    <div class="gm-container">
        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="gm-logo" rel="home">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 100 100" fill="#1B5E20">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#1B5E20" stroke-width="4"/>
                    <path d="M30 65 L50 25 L70 65 L50 50 Z" fill="#1B5E20"/>
                    <circle cx="50" cy="45" r="8" fill="#F57F17"/>
                </svg>
            <?php endif; ?>
            <div class="gm-logo-text">
                Gornji Milanovac
                <span>Digitalni portal</span>
            </div>
        </a>

        <!-- Navigation -->
        <nav class="gm-nav" role="navigation" aria-label="<?php esc_attr_e('Glavni meni', 'gm-portal'); ?>">
            <!-- Mobile menu toggle -->
            <button class="gm-nav-toggle" aria-label="<?php esc_attr_e('Otvori meni', 'gm-portal'); ?>" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <?php
            if (has_nav_menu('gm-primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'gm-primary',
                    'menu_class'     => 'gm-nav-menu',
                    'container'      => false,
                    'walker'         => new GM_Nav_Walker(),
                    'fallback_cb'    => false,
                ));
            } else {
                // Default menu if no menu is assigned
                ?>
                <ul class="gm-nav-menu">
                    <li class="gm-nav-item">
                        <a href="<?php echo esc_url(home_url('/vesti/')); ?>" class="gm-nav-link">Vesti</a>
                    </li>
                    <li class="gm-nav-item">
                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="gm-nav-link">Blog</a>
                    </li>
                    <li class="gm-nav-item">
                        <a href="<?php echo esc_url(home_url('/oglasi/')); ?>" class="gm-nav-link">Oglasi</a>
                    </li>
                    <li class="gm-nav-item">
                        <a href="<?php echo esc_url(home_url('/poslovni-imenik/')); ?>" class="gm-nav-link">Poslovni Imenik</a>
                    </li>
                    <li class="gm-nav-item">
                        <a href="<?php echo esc_url(home_url('/o-gradu/')); ?>" class="gm-nav-link">O Gradu</a>
                    </li>
                    <li class="gm-nav-item">
                        <a href="<?php echo esc_url(home_url('/kontakt/')); ?>" class="gm-nav-link">Kontakt</a>
                    </li>
                </ul>
                <?php
            }
            ?>
        </nav>
    </div>
</header>

<?php gm_after_header(); ?>

<main id="main" class="site-main" role="main">
