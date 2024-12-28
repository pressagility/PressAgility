<?php

// Embed the scripts we need for this plugin
function flying_pages_enqueue_scripts() {
    
    // Disable for logged admins
    //if(get_option('flying_pages_config_disable_on_login') && current_user_can( 'manage_options' )) return;
    if(flying_pages_config_disable_on_login && current_user_can( 'manage_options' )) return;
    

    // Abort if the response is AMP since custom JavaScript isn't allowed
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) return;
    
    //wpsp_patch start
    //wp_enqueue_script('flying-pages', plugin_dir_url(__FILE__) . 'flying-pages.min.js', array(), FLYING_PAGES_VERSION, true);
    wp_enqueue_script('flying-pages', '/wp-content/site'.WPSP_SITE_ID.'/mu-plugins/wpsp-critical-plugins/flying-pages/flying-pages.min.js', array(), FLYING_PAGES_VERSION, true);
    //wpsp_patch end
    
    wp_add_inline_script(
        'flying-pages',
'window.FPConfig= {
	delay: '.esc_html(flying_pages_config_delay).',
	ignoreKeywords: '.json_encode(flying_pages_config_ignore_keywords, true).',
	maxRPS: '.esc_html(flying_pages_config_max_rps).',
    hoverDelay: '.esc_html(flying_pages_config_hover_delay).'
};',
        "before"
    );
}
add_action('wp_enqueue_scripts', 'flying_pages_enqueue_scripts');

// Add defer attribute to Flying Pages script tag
function flying_pages_add_defer($tag, $handle) {
    if ('flying-pages' === $handle && false === strpos($tag, 'defer')) {
        $tag = preg_replace(':(?=></script>):', ' defer', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'flying_pages_add_defer', 10, 2);
