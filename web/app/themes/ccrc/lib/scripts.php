<?php
/**
 * Scripts and stylesheets
 *
 * Enqueue stylesheets in the following order:
 * 1. /theme/assets/css/main.css
 *
 * Enqueue scripts in the following order:
 * 1. jquery-1.11.1.min.js via Google CDN
 * 2. /theme/assets/js/vendor/modernizr.min.js
 * 3. /theme/assets/js/scripts.js (in footer)
 *
 * Google Analytics is loaded after enqueued scripts if:
 * - An ID has been defined in config.php
 * - You're not logged in as an administrator
 */
function roots_scripts()
{
    /**
     * The build task in Grunt renames production assets with a hash
     * Read the asset names from assets-manifest.json
     */
    $get_assets = file_get_contents(get_template_directory() . '/dist/mix-manifest.json');
    $assets = json_decode($get_assets, true);
    $assets = array(
        'css' => '/dist' . $assets['/css/main.min.css'],
        'js' => '/dist' . $assets['/js/main.min.js'],
        //'modernizr' => '/assets/vendor/modernizr/modernizr.js',
        'jquery' => '//ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js',
        'g-fonts' => 'https://fonts.googleapis.com/css?family=Roboto:400,500,700'
    );

    wp_enqueue_style('roots_css', get_template_directory_uri() . $assets['css'], false, null);
    wp_enqueue_style('roots_css', $assets['g-fonts'], false, null);

    /**
     * jQuery is loaded using the same method from HTML5 Boilerplate:
     * Grab Google CDN's latest jQuery with a protocol relative URL; fallback to local if offline
     * It's kept in the header instead of footer to avoid conflicts with plugins.
     */
    if (!is_admin() && current_theme_supports('jquery-cdn')) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', $assets['jquery'], array(), '3.4.0', false);
        add_filter('script_loader_src', 'roots_jquery_local_fallback', 10, 2);

        // load jq migrate
        wp_deregister_script( 'jquery-migrate' );
        wp_enqueue_script( 'jquery-migrate', '//code.jquery.com/jquery-migrate-3.0.1.min.js', array( 'jquery' ), '3.0.1', false  );
    }

    if (is_single() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    //wp_enqueue_script('modernizr', get_template_directory_uri() . $assets['modernizr'], array(), null, false);
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-autocomplete');
    wp_enqueue_script('roots_js', get_template_directory_uri() . $assets['js'], array('jquery'), null, true);
    wp_localize_script('roots_js', 'ajax_url', admin_url('admin-ajax.php'));
}

add_action('wp_enqueue_scripts', 'roots_scripts', 10);

// http://wordpress.stackexchange.com/a/12450
function roots_jquery_local_fallback($src, $handle = null)
{
    static $add_jquery_fallback = false;

    if ($add_jquery_fallback) {
        echo '<script>window.jQuery || document.write(\'<script src="' . get_template_directory_uri() . '/assets/vendor/jquery/dist/jquery.min.js?1.11.1"><\/script>\')</script>' . "\n";
        $add_jquery_fallback = false;
    }

    if ($handle === 'jquery') {
        $add_jquery_fallback = true;
    }

    return $src;
}

add_action('wp_head', 'roots_jquery_local_fallback');
