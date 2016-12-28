<?php


// Generate output describing the next race, for use in the widget and shortcode
function generate_next_race() {

    $races = get_club_races();

    if ($races==[]) {
        echo('<p>No next race information available.</p>');
    } else {
        $race = $races[0];

        $date = DateTime::createFromFormat('Y-m-d', $race['date']);
        $racedate =  $date->format('D M dS');

        echo('<ul class="nextrace">');
        echo('<li class="date">'.$racedate.'</li>');
        echo('<li class="starttime">'.$race['starttime'].'</li>');
        echo('<li class="location">'.$race['location']['name'].'</li>');
        echo('<li class="title">'.$race['title'].'</li>');
        echo('</ul>');
    }
}

// Creating the widget
class cabici_nextrace_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            // Base ID of your widget
            'cabici_nextrace_widget',

            // Widget name will appear in UI
            __('Cabici Next Race Widget', 'cabici_nextrace_widget_domain'),

            // Widget description
            array( 'description' => __( 'Widget to display the next race for a club', 'cabici_nextrace_widget_domain' ), )
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) {
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        echo $args['before_title'] . "Next Race" . $args['after_title'];

        generate_next_race();

        echo $args['after_widget'];
    }

    // Widget Backend
    public function form( $instance ) {
        return "<p></p>";
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['club'] = ( ! empty( $new_instance['club'] ) ) ? strip_tags( $new_instance['club'] ) : '';
        return $instance;
    }
} // Class cabici_nextrace_widget ends here


// Register and load the widget
function wpb_load_nextrace_widget() {
	register_widget( 'cabici_nextrace_widget' );
}
add_action( 'widgets_init', 'wpb_load_nextrace_widget' );


//----------- Shortcode cabici_next_race --------------

add_shortcode( 'cabici_next_race', 'cabici_next_race_handler' );

function cabici_next_race_handler( $atts, $content = null ) {

    ob_start();
    generate_next_race();
    $content = ob_get_clean();
    return $content;
}
