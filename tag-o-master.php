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
        // Register-Enqueue plugin styles and scripts
        add_action('init', array($this,'register_script'));
        add_action('wp_enqueue_scripts', array($this,'enqueue_style'));
        // Setup filter hook to place button under the each post
        add_filter('the_content', array($this, 'like_button' ));
        // Add ajax custom hook
        add_action('wp_ajax_nopriv_like', array($this,'like'));
        add_action('wp_ajax_like', array($this,'like'));
    }
    /**
     * register jquery and style on initialization
     */
    public function register_script() {
        wp_register_script('tom-script', plugins_url('js/tom-script.js', __FILE__), array('jquery'), '2.5.1' );
        wp_register_style('tom-style', plugins_url('css/tagomaster_main.css', __FILE__), false, '1.0.0', 'all');
        wp_localize_script('tom-script', 'tom_ajax',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('i-just-need-a-coffee')
            )
        );
    }
    /**
     * use the registered jquery and style above
     */
    public function enqueue_style(){
        wp_enqueue_script('tom-script');
        wp_enqueue_style('tom-style');
    }
    /**
     * Adding like button to end of post
     * @param $content
     * @return string
     */
    public function like_button($content) {
        if (get_post_type() == post && is_singular()) {
            $html = '<p class="post-like"><a data-event="like" data-post_id="'.get_the_ID().'" href="#">';
            $html .= 'Like</a><span class="count">'.$this->likesCount(get_the_ID()).'</span></p>';
            $content .= $html;
        }
        return $content;
    }
    /**
     * AJAX response function
     */
    public function like () {
        check_ajax_referer('i-just-need-a-coffee', 'nonce');
        $post_id = $_POST['post_id'];
        $event = $_POST['event'];
        if ($event == "like") {
            $this->likePost($post_id);
        }else{
            $this->dislikePost($post_id);
        }
        die();
    }
    /**
     * @param $post_id
     * helper function to like post
     */
    public function likePost($post_id) {
        $likes_count = $this->likesCount($post_id);
        if ($likes_count) {
            update_post_meta($post_id, '_likes_count', ++$likes_count);
            echo $likes_count;
        }else{
            add_post_meta($post_id, '_likes_count', 1);
            echo 1;
        }
    }
    /**
     * @param $post_id
     * helper function to dislike post
     */
    public function dislikePost($post_id) {
        $likes_count = $this->likesCount($post_id);
        if ($likes_count) {
            update_post_meta($post_id, '_likes_count', --$likes_count);
            echo $likes_count;
        }else{
            add_post_meta($post_id, '_likes_count', -1);
            echo -1;
        }
    }
    public function likesCount($post_id) {
        return get_post_meta($post_id,'_likes_count',true);
    }

    public function hasAlreadyLiked($post_id) {

    }

}

$plugin = new tagOmaster();
$plugin->run();



