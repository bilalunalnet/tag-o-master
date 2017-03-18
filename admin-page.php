<?php
/**
 * Created by PhpStorm.
 * User: bilalunalnet
 * Date: 11.03.2017
 * Time: 17:01
 */

add_action( 'admin_menu', 'admin_menu' );

/**
 * creates Tag-o-Master entry under posts menu
 */
function admin_menu() {
    add_posts_page('Tag-o-Master settings page', 'Tag-o-Master', 'manage_options', 'tom_admin_page', 'admin_menu_content');
}

/**
 * tag-o-master admin page content
 */
function admin_menu_content() {
    if (!current_user_can( 'manage_options' )) {
        wp_die(__( 'You do not have sufficient permissions to access this page.'));
    }
    ?>
    <div class="wrap">
        <h2>Tag-o-Master Admin Page</h2>
        <?php
        $id_likes_count = array();
        $post_ids = get_posts(array(
            'fields'        => 'ids', // Only get post IDs
            'posts_per_page' => -1
        ));
        $i = 0;
        foreach ($post_ids as $id) {
            $likes_count = get_post_meta($id,'_likes_count',true);
            if ($likes_count) {
                $id_likes_count[$i] = array(
                    "id" => $id,
                    "likes_count" => $likes_count
                );
                $i++;
            }
        }
        $tags = array(); // TODO sort this array
        foreach ($id_likes_count as $id) {
            $post_tags = get_the_tags($id["id"]);
            if ($post_tags) {
                foreach($post_tags as $tag) {
                    if($tag->taxonomy = 'post_tag') {
                        if (!isset($tags[$tag->term_id])) {
                            $tags[$tag->term_id] = array(
                                "tag_id" => $tag->term_id,
                                "tag_name" => $tag->name,
                                "tag_likes_count" => $id["likes_count"]
                            );
                        }else{
                            $tags[$tag->term_id]["tag_likes_count"] += $id["likes_count"];
                        }
                    }
                }
            }
        }
        echo "<pre>";
        print_r($tags);
        echo "</pre>";
        ?>
    </div>
<?php
}