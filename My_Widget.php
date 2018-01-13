<?php
class My_Widget extends WP_Widget {
    
    /**
     * __construct
     *
     */
    public function __construct() {
        parent::__construct(
            'foo_widget',
            __('ウィジェットのタイトル', 'text_domain'),
            [ 'description' => __('サンプルのウィジェット「Foo Widget」です。', 'text_domain' ), ]
        );
    }

    /**
     * widget
     *
     * @param mixed $args
     * @param mixed $instance
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( !empty( $instance['title']) ) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        echo __( '世界のみなさん、こんにちは', 'text_domain' );
        echo $args['after_widget'];
    }

    /**
     * form
     *
     * @param mixed $instance
     */
    public function form( $instance ) {
        $title = !empty( $instance['title'] ) ? $instance['title'] : __( '新しいタイトル', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('タイトル:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    /**
     * update
     *
     * @param mixed $new_instance
     * @param mixed $old_instance
     */
    public function update( $new_instance, $old_instance ) {
        $instasce = [];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }
}

