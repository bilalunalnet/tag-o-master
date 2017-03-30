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
        global $wpdb;
        // PAGINATION

        $tag_count_sql = "SELECT COUNT(*)
                          FROM $wpdb->terms K, $wpdb->term_taxonomy Y, $wpdb->term_relationships P, $wpdb->postmeta Q
                          WHERE K.term_id = Y.term_id
                            AND Y.taxonomy = 'post_tag'
                            AND P.term_taxonomy_id = K.term_id
                            AND Q.post_id = P.object_id
                            AND Q.meta_key = '_likes_count'";
        $tag_count = $wpdb->get_var($tag_count_sql);
        $tags_per_page = 10;
        $page_count = ceil($tag_count / $tags_per_page);
        $page = isset($_GET['pages']) ? (int) $_GET['pages'] : 1;
        if($page < 1) $page = 1;
        if($page > $page_count) $page = $page_count;
        $limit = ($page - 1) * $tags_per_page;
        $current_url = add_query_arg( NULL, NULL );

        // PAGE LINKS
        for($s = 1; $s <= $page_count; $s++) {
            if($page == $s) {
                echo $s . ' ';
            } else {
                echo '<a href="'.$current_url.'&pages=' . $s . '">' . $s . '</a> ';
            }
        }
        ?>

        <table class="widefat">
            <thead>
            <tr>
                <th>Tag ID</th>
                <th>Tag Name</th>
                <th>Posts the tag is associated with</th>
                <th>Likes count</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Tag ID</th>
                <th>Tag Name</th>
                <th>Posts the tag is associated with</th>
                <th>Likes count</th>
            </tr>
            </tfoot>
            <tbody>
            <?php
            // LISTING TAGS
            $sql = "SELECT
                      K.term_id as tag_id,
                      K.name, Y.count, SUM(Q.meta_value) as like_count
                    FROM $wpdb->terms K, $wpdb->term_taxonomy Y, $wpdb->term_relationships P, $wpdb->postmeta Q
                    WHERE K.term_id = Y.term_id
                          AND Y.taxonomy = 'post_tag'
                          AND P.term_taxonomy_id = K.term_id
                          AND Q.post_id = P.object_id
                          AND Q.meta_key = '_likes_count'
                    GROUP BY K.term_id,K.name,Y.count
                    ORDER BY like_count DESC
                    LIMIT $limit, $tags_per_page
                    ";
            $tags = $wpdb->get_results($sql, OBJECT);
            foreach ($tags as $tag) {
                echo "<tr>";
                echo "<td>$tag->tag_id</td>";
                echo "<td><a href='".get_tag_link($tag->tag_id)."'>$tag->name</a></td>";
                echo "<td>$tag->count</td>";
                echo "<td>$tag->like_count</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
        <?php
        // PAGE LINKS
        for($s = 1; $s <= $page_count; $s++) {
        if($page == $s) {
        echo $s . ' ';
        } else {
        echo '<a href="'.$current_url.'&pages=' . $s . '">' . $s . '</a> ';
        }
        }
        ?>
    </div>
<?php
}