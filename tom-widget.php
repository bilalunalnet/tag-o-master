<?php
/**
 * Created by PhpStorm.
 * User: bilalunal
 * Date: 16.03.2017
 * Time: 16:06
 */

class tom_Widget extends WP_Widget {
    /**
     * tom_Widget constructor.
     * Set up the widget name and description.
     */
    public function __construct() {
        $widget_options = array(
            'classname' => 'tom_widget',
            'description' => 'A widget to list top 10 tags'
        );
        parent::__construct( 'tom_widget', 'Tag-o-Master', $widget_options );
    }
    /**
     * Outputs the content of the widget
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance) {
        global $wpdb;
        $title = apply_filters('widget_title',$instance['title']);
        $limit = $instance['limit'];
        $seperator = $instance['seperator'];
        $like_text = $instance['like_text'];
        echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
        $sql = "SELECT
                  K.term_id as tag_id,
                  K.name, SUM(Q.meta_value) as like_count
                FROM $wpdb->terms K, $wpdb->term_taxonomy Y, $wpdb->term_relationships P, $wpdb->postmeta Q
                WHERE K.term_id = Y.term_id
                  AND Y.taxonomy = 'post_tag'
                  AND P.term_taxonomy_id = K.term_id
                  AND Q.post_id = P.object_id
                  AND Q.meta_key = '_likes_count'
                GROUP BY K.term_id,K.name
                ORDER BY like_count DESC
                LIMIT 0, $limit
                    ";
        $tags = $wpdb->get_results($sql, OBJECT);
        echo "<ul>";
        foreach ($tags as $tag) {
            echo "<li><a href='".get_tag_link($tag->tag_id)."'>".$tag->name."</a> $seperator ".$tag->like_count." $like_text</li>";
        }
        echo "</ul>";

        echo $args['after_widget'];
    }

    /**
     * back-end widget form.
     * @param array $instance
     * @return string|void
     */
    public function form($instance) {
        echo $this->createInput($instance,'title');
        echo $this->createInput($instance,'limit');
        echo $this->createInput($instance,'like_text');
        echo $this->createInput($instance,'seperator');
    }
    /**
     * Updating widget replacing old instances with new
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['limit'] = strip_tags($new_instance['limit']);
        $instance['seperator'] = strip_tags($new_instance['seperator']);
        $instance['like_text'] = strip_tags($new_instance['like_text']);
        return $instance;
    }

    /**
     * Helper method to create form inputs for form method
     * @param $instance
     * @param $field
     * @return string
     */
    public function createInput($instance, $field) {
        $value = !empty( $instance[$field] ) ? $instance[$field] : '';
        $input = "<p><label for='".$this->get_field_id($field)."'>".$field." : </label>";
        $input .= "<input type='text' id='".$this->get_field_id($field)."'";
        $input .= "name='".$this->get_field_name($field)."'";
        $input .= "value='".esc_attr($value)."'></p>";
        return $input;
    }
}

// Register the widget.
function tom_register_widget() {
    register_widget('tom_Widget');
}

add_action('widgets_init', 'tom_register_widget');
