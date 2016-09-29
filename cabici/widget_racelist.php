<?php
// Creating the widget
class cabici_racelist_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            // Base ID of your widget
            'cabici_racelist_widget',

            // Widget name will appear in UI
            __('Cabici Race List Widget', 'cabici_racelist_widget_domain'),

            // Widget description
            array( 'description' => __( 'Widget to display a short list of upcoming races for all clubs', 'cabici_racelist_widget_domain' ), )
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) {
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        echo $args['before_title'] . "Upcoming Races" . $args['after_title'];

        $races = get_all_races(5);
        echo('<table class="table">');
        foreach ($races as $race){
            echo('<tr><th>'.$race['date'].' '.$race['club']['name'].'</th></tr>');
            echo('<tr><td><a target=new href="http://cabici.net/races/'.$race['club']['slug'].'/'.$race['id'].'">'.$race['location']['name'].'</a></td>');
            echo('</tr>');
        }
        echo('</table>');
        echo('<p>Race listings from <a href="http://cabici.net">cabici.net</a></p>');

        echo $args['after_widget'];
    }

} // Class cabici_racelist_widget ends here

// Register and load the widget
function wpb_load_racelist_widget() {
	register_widget( 'cabici_racelist_widget' );
}
add_action( 'widgets_init', 'wpb_load_racelist_widget' );
