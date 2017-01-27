<?php

// Creating the widget
class cabici_pointscore_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            // Base ID of your widget
            'cabici_pointscore_widget',

            // Widget name will appear in UI
            __('Cabici Pointscore Widget', 'cabici_pointscore_widget_domain'),

            // Widget description
            array( 'description' => __( 'Widget to display top 5 riders in a pointscore', 'cabici_pointscore_widget_domain' ), )
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) {
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        echo $args['before_title'] . "Pointscore" . $args['after_title'];

        echo generate_pointscore($instance['id']);

        echo $args['after_widget'];
    }

    // Widget Backend
    public function form( $instance ) {
        $id = ! empty( $instance['id'] ) ? $instance['id'] : esc_html__( '0', 'text_domain' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Identifier:', 'text_domain' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" type="text" value="<?php echo esc_attr( $id ); ?>">
		</p>
		<?php
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['id'] = ( ! empty( $new_instance['id'] ) ) ? strip_tags( $new_instance['id'] ) : '';
        return $instance;
    }
} // Class cabici_pointscore_widget ends here


// Register and load the widget
function wpb_load_pointscore_widget() {
	register_widget( 'cabici_pointscore_widget' );
}
add_action( 'widgets_init', 'wpb_load_pointscore_widget' );

function generate_pointscore($id) {

        $pointscore = get_pointscore($id);
        if (!$pointscore) {
            return "<p>No pointscore data found.</p>";
        }

        ob_start();
        ?>
        <div class="cabici-container">
            <h3><?= $pointscore['name'] ?></h3>
            <table class='racetable'>
                <tr><th>Rider</th><th>Points</th></tr>
            <?php
            $results = array_slice($pointscore['results'], 0, 5);
            foreach ($results as $result) {
                ?>
                <tr>
                    <td><?= $result['rider'] ?></td>
                    <td><?= $result['points'] ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody></table>
        </div>

        <?php

        $table = ob_get_clean();
        console_log($table);
        return $table;
}
?>
