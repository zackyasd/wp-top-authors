<?php
/**
 * Top Author Widgets
 *
 * @package WordPress
 * @subpackage Framework
 * @since Framework 1.0.0
 */
 
// Widgets
add_action( 'widgets_init', 'top_author_widget' );

// Register our widget
function top_author_widget() {
    register_widget( 'Top_Author' );
}

// Define top author widget
class Top_Author extends WP_Widget {

    // Setting up the widget
    function Top_Author() {
        $widget_ops  = array( 'classname' => 'top_author', 'description' => __('Display top agents.', 'framework') );
        $control_ops = array( 'id_base' => 'top_author' );

        $this->WP_Widget( 'top_author', __('Top Agents', 'framework'), $widget_ops, $control_ops );
    }

    function widget( $args, $instance ) {
        
        extract( $args );

        $top_author_title = apply_filters( 'widget_title', empty( $instance['top_author_title'] ) ? __( 'Top Author', 'framework' ) : $instance['top_author_title'], $instance, $this->id_base );
        $top_author_limit = !empty( $instance['top_author_limit'] ) ? absint( $instance['top_author_limit'] ) : 5;

?>
        <?php echo $before_widget; ?>
        <?php echo $before_title . $top_author_title . $after_title; ?>
<?php
    global $wpdb;
    
    $top_authors = $wpdb->get_results("
        SELECT u.ID, count(post_author) as posts FROM {$wpdb->posts} as p
        LEFT JOIN {$wpdb->users} as u ON p.post_author = u.ID
        WHERE p.post_status = 'publish'
        AND p.post_type = 'property'
        AND p.post_date > '" . date('Y-m-d H:i:s', strtotime('-' . 7 . ' days')) . "'
        GROUP by p.post_author
        ORDER by posts DESC
        LIMIT 0,$warrior_top_author_limit
    ");
    
    if( !empty( $top_authors ) ) {
        echo '<ul>';
        foreach( $top_authors as $key => $author ) :
                echo '<li>';
                    echo get_avatar( $author->ID, '50' );
                    echo '<a href="' . get_author_posts_url( $author->ID ) . '">' . get_the_author_meta( 'display_name' , $author->ID ) . '</a>
                    (' . $author->posts . ')';
                echo '</li>';
        endforeach;
        echo '</ul>';
    }  

?>
        <?php echo $after_widget; ?>
<?php
    }
    
    // Set update function
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['top_author_title']     = strip_tags( $new_instance['top_author_title'] );
        $instance['top_author_limit']     = (int) $new_instance['top_author_limit'];

        return $instance;
    }
    
    // Set form widget
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array('top_author_title' => __('Top Author', 'framework'), 'top_author_limit' => '5') );
    ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'top_author_title' ); ?>"><?php _e('Widget Title:', 'framework'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'top_author_title' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'top_author_title' ); ?>" value="<?php echo $instance['top_author_title']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'top_author_limit' ); ?>"><?php _e('Number of agents to show:', 'framework'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'top_author_limit' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'top_author_limit' ); ?>" value="<?php echo $instance['top_author_limit']; ?>" />
        </p>
    <?php
    }
}
?>
