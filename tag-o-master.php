<?php
/**
Plugin Name: TAG-O-MASTER
Plugin URI:  http://www.bilalunal.net/tag-o-master
Description: a wordpress plugin to list tags by number of ratings of posts
Version:     1.0
Author:      Bilal Ünal
Author URI:  http://www.bilalunal.net
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 **/


class tagOmaster {
    /**
     * Action hooks
     */
    public function run() {
        // Enqueue plugin styles and scripts
        add_action('plugins_loaded', array($this,'enqueue_tom_scripts'));
        add_action('plugins_loaded', array($this,'enqueue_tom_styles'));
        // Setup filter hook to place button under the each post
        add_filter( 'the_content', array( $this, 'like_button' ) );
    }

    /**
     * Register plugin styles and scripts
     */
    public function register_tom_scripts() {
        wp_register_script( 'tom-script', plugins_url( 'js/tom-script.js', __FILE__ ), array('jquery'), null, true );
        wp_register_style( 'tom-style', plugins_url( 'css/tagomaster_main.css' ) );
    }
    /**
     * Enqueues plugin-specific scripts.
     */
    public function enqueue_tom_scripts() {
        wp_enqueue_script( 'tom-script' );
        wp_localize_script( 'tom-script', 'tom_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );
    }
    /**
     * Enqueues plugin-specific styles.
     */
    public function enqueue_tom_styles() {
        wp_enqueue_style( 'tom-style' );
    }

    /**
     * Adding like button to end of post
     * @param $content
     * @return string
     */
    public function like_button($content) {
        if (get_post_type() == post && is_singular()) {
            $html = '<a href="#" class="tom-likebtn" data-id="' . get_the_ID() . '">Like</a>';
            $content .= $html;
        }
        return $content;
    }
}

$plugin = new tagOmaster();
$plugin->run();



