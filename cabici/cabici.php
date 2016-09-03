<?php
/*
Plugin Name: Cabici Race Schedule
Plugin URI: http://cabici.net/
Description: Add a race schedule for your club with Cabici
Version: 0
Author: Owen Cassidy
Author URI: http://owencassidy.me/
*/

include 'cabici_options.php';

function get_club_races() {

    $options = get_option('cabici_options');
    $club = $options['club'];
    $key = 'cabici_races_' . $club;

    if ( false === ( $races = get_transient( $key ) ) ) {

        $url = 'http://test.cabici.net/api/races?club='.$club.'&scheduled=true';
        $json = wp_remote_retrieve_body( wp_remote_get( $url ) );
        $races = json_decode( $json, true );

        set_transient($key, $races, HOUR_IN_SECONDS);
    }

    return $races;
}

function build_table() {

    $options = get_option('cabici_options');
    $club = $options['club'];

    if (!$club) {
        return '<p>No Club configured.</p>';
    }

    $races = get_club_races();

    ob_start();
    ?>
    <div class="cabici-container">
        <table class='table'>
            <thead>
                <tr>
                    <th>Date/Start Time</th>
                    <th>Race</th>
                    <th>Location</th>
                    <th>Officials</th>
                </tr>
            </thead>
            <tbody>
    <?php
    foreach ($races as $race) {
        $url_arr = explode( '/', $race['url'] );
        end( $url_arr );
        $race_number = prev( $url_arr );
        ?>
        <tr>
            <td><?= $race['date'] ?><br>
               <?= $race['starttime'] ?></td>
            <td>
                <a target=new href="http://test.cabici.net/races/xyzzy/<?= $race['id'] ?>"><?= $race['title'] ?></a>
            </td>
            <td><?= $race['location']['name'] ?></td>
            <td>
                <?php if ($race['officials']) { ?>
                <ul>
                    <li><strong>Commissaire:</strong> <?= $race['officials']['Commissaire'][0]['name'] ?></li>
                    <li><strong>Duty Officer:</strong>  <?= $race['officials']['Duty Officer'][0]['name'] ?></li>
                    <li><strong>Duty Helpers:</strong>
                        <?php
                            foreach ($race['officials']['Duty Helper'] as $dh) {
                                echo($dh['name']. ' ');
                            }
                        ?>
                    </li>
                </ul>
                <?php } ?>
            </td>
            <!--
		<td><a onclick="insertResults('<?= $race['id'] ?>', '.race-container')">View results</a></td>
	    -->
        </tr>
        <?php
    }
    ?>
            </tbody>
        </table>
        <div class="race-container"></div>
    </div>
    <script type="text/javascript" src="/wp-content/plugins/cabici/races.js"></script>
    <!-- also rely on jquery -->
    <?php

    $table = ob_get_clean();

    return $table;
}

function cabici_handler( $atts, $content = null ) {
    $table = build_table();

    return $table;
}

add_shortcode( 'cabici', 'cabici_handler' );

// Custom Post Type

add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'cabici_races',
    array(
      'labels' => array(
        'name' => __( 'Races' ),
        'singular_name' => __( 'Race' )
      ),
      'public' => true,
      'has_archive' => true,
    )
  );
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

        $races = get_club_races();
        $race = $races[0];
        ?>
        <ul class='raceinfo'>
            <li class='date'><?= $race['date'] ?></li>
            <li class='starttime'><?= $race['starttime'] ?></li>
            <li class='title'><a target=new href="http://test.cabici.net/races/xyzzy/<?= $race['id'] ?>"><?= $race['title'] ?></a></li>
            <li class='location'><?= $race['location']['name'] ?></li>
        </ul>
        <?php

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
function wpb_load_widget() {
	register_widget( 'cabici_nextrace_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );


?>
