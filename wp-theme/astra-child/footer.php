<?php
/**
 * Footer Template - Gornji Milanovac Portal
 *
 * @package GM_Portal
 */

defined('ABSPATH') || exit;
?>

</main><!-- #main -->

<?php gm_before_footer(); ?>

<footer class="gm-footer" role="contentinfo">
    <div class="gm-container">
        <div class="gm-footer-main">
            <!-- Kolona 1: O nama -->
            <div class="gm-footer-col gm-footer-about">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <?php dynamic_sidebar('footer-1'); ?>
                <?php else : ?>
                    <h4>O portalu</h4>
                    <p>
                        Gornji Milanovac - Digitalni portal je nezavisni informativni sajt
                        posvećen gradu Gornjem Milanovcu i okolini. Donosimo vam najnovije
                        vesti, događaje, oglase i sve važne informacije iz našeg grada.
                    </p>
                    <div class="gm-footer-social">
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
                <?php endif; ?>
            </div>

            <!-- Kolona 2: Brzi linkovi -->
            <div class="gm-footer-col">
                <?php if (is_active_sidebar('footer-2')) : ?>
                    <?php dynamic_sidebar('footer-2'); ?>
                <?php else : ?>
                    <h4>Brzi linkovi</h4>
                    <ul class="gm-footer-links">
                        <li><a href="<?php echo esc_url(home_url('/vesti/')); ?>">Vesti</a></li>
                        <li><a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a></li>
                        <li><a href="<?php echo esc_url(home_url('/oglasi/')); ?>">Oglasi</a></li>
                        <li><a href="<?php echo esc_url(home_url('/poslovni-imenik/')); ?>">Poslovni imenik</a></li>
                        <li><a href="<?php echo esc_url(home_url('/o-gradu/')); ?>">O gradu</a></li>
                        <li><a href="<?php echo esc_url(home_url('/vazni-telefoni/')); ?>">Važni telefoni</a></li>
                        <li><a href="<?php echo esc_url(home_url('/kontakt/')); ?>">Kontakt</a></li>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Kolona 3: Newsletter -->
            <div class="gm-footer-col">
                <?php if (is_active_sidebar('footer-3')) : ?>
                    <?php dynamic_sidebar('footer-3'); ?>
                <?php else : ?>
                    <h4>Prijavite se na newsletter</h4>
                    <p style="color: rgba(255,255,255,0.7); margin-bottom: 1rem;">
                        Budite u toku sa najnovijim vestima iz Gornjeg Milanovca.
                        Prijavite se na naš newsletter.
                    </p>
                    <form class="gm-newsletter-form" action="<?php echo esc_url(home_url('/wp-json/gm/v1/lead')); ?>" method="post" id="gm-newsletter-form">
                        <?php wp_nonce_field('gm_newsletter', 'gm_newsletter_nonce'); ?>
                        <input type="hidden" name="tip_upita" value="newsletter">
                        <input type="email" name="email" class="gm-newsletter-input" placeholder="Vaša email adresa" required>
                        <button type="submit" class="gm-newsletter-btn">Prijavi se</button>
                    </form>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.75rem; margin-top: 0.5rem;">
                        Vaši podaci su bezbedni. Možete se odjaviti u bilo kom trenutku.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer bottom -->
        <div class="gm-footer-bottom">
            <p>
                &copy; <?php echo date('Y'); ?> Gornji Milanovac - Digitalni Portal.
                Sva prava zadržana.
                |
                <a href="<?php echo esc_url(home_url('/politika-privatnosti/')); ?>" style="color: rgba(255,255,255,0.7);">Politika privatnosti</a>
                |
                <a href="<?php echo esc_url(home_url('/uslovi-koriscenja/')); ?>" style="color: rgba(255,255,255,0.7);">Uslovi korišćenja</a>
            </p>
        </div>
    </div>
</footer>

<?php gm_after_footer(); ?>

<?php wp_footer(); ?>

</body>
</html>
