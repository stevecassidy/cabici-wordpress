<?php

class cabici_result_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            // Base ID of your widget
            'cabici_result_widget',

            // Widget name will appear in UI
            __('Cabici Result Widget', 'cabici_result_widget_domain'),

            // Widget description
            array( 'description' => __( 'Widget to display the results of the most recent race for a club', 'cabici_result_widget_domain' ), )
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) {

        $recent_race = most_recent_race($club);

        echo $args['before_widget'];
        echo $args['before_title'];
        echo 'Results of '.$recent_race['title'].' - '.$recent_race['location']['name'];
        echo $args['after_title'];


        $results = get_race_result($recent_race['id']);
        if ($results == []) {
            echo( '<p>No results available.</p>');
        } else {
            $last_grade = "X";
            echo('<table class="table cabicisummary">');
            foreach ($results as $result) {
                if ($result['grade'] != $last_grade) {
                    if ($last_grade != 'X') {
                        echo('</tr>');
                    }
                    echo('<tr><th>'.$result['grade'].'</th>');
                    $last_grade = $result['grade'];
                }
                echo('<td>'.$result['rider'].'</td>');
            }
            echo '</table>';
        }
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
} // Class cabici_result_widget ends here

// Register and load the widget
function wpb_load_raceresult_widget() {
	register_widget( 'cabici_result_widget' );
}
add_action( 'widgets_init', 'wpb_load_raceresult_widget' );



//----------- Shortcode cabici_last_result_brief --------------

add_shortcode( 'cabici_last_result_brief', 'cabici_last_result_brief_handler' );

function cabici_last_result_brief_handler( $atts, $content = null ) {

    ob_start();

    $recent_race = most_recent_race($club);

    $date = DateTime::createFromFormat('Y-m-d', $recent_race['date']);
    if ($date === false) {
      $racedate = "";
    } else {
      $racedate =  $date->format('M jS');
    }

    echo '<h3>'.$racedate.' | '.$recent_race['location']['shortname'].'</h3>';

    $results = get_race_result($recent_race['id']);
    if ($results == []) {
        echo( '<p>No results available.</p>');
    } else {
        $last_grade = "X";
        echo('<table class="table cabicisummary">');
        foreach ($results as $result) {
            if ($result['grade'] != $last_grade) {
                if ($lastgrace != 'X') {
                    echo('</tr>');
                }
                if ($result['place'] == '1') {
                  echo('<tr><th>' . $result['grade'] . '</th>');
                  echo('<td>' . $result['rider'] . '</td>');
                  $last_grade = $result['grade'];
                }
            }

        }
        echo '</table>';
    }

    $content = ob_get_clean();
    return $content;
}
