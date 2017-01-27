<?php
/*
Plugin Name: Cabici Race Schedule
Plugin URI: http://cabici.net/
Description: Add a race schedule for your club with Cabici
Version: 1.0
Author: Steve Cassidy
Author URI: http://stevecassidy.net/
*/


include 'cabici_options.php';
include 'widget_nextrace.php';
include 'widget_result.php';
include 'widget_racelist.php';
include 'shortcode_results.php';
include 'shortcode_schedule.php';
include 'shortcode_pastresults.php';
include 'shortcode_pointscore.php';
include 'widget_pointscore.php';

/**
 * Proper way to enqueue scripts and styles
 */
function wpdocs_cabici_scripts() {
    wp_enqueue_style( 'style-name', plugin_dir_url(__FILE__) . 'cabici.css');
    wp_enqueue_script('races-js', plugin_dir_url(__FILE__) . 'races.js');
}
add_action( 'wp_enqueue_scripts', 'wpdocs_cabici_scripts' );


// global configuration
$cabici_config = array(
        'devel' => false,
        'url' => 'http://cabici.net/',
        'cache' => true
);

if ($cabici_config['devel']) {
    $cabici_config['url'] = 'http://localhost/wp-content/plugins/cabici/';
}

function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}

// -------------------------------------------
// data acquisition
// -------------------------------------------

function api_request($url) {

    global $cabici_config;

    $options = get_option('cabici_options');

    $cabici_url = $cabici_config['url'];

    $url = $cabici_url.$url;

    if ( !$cabici_config['cache'] || false === ( $data = get_transient( $url ) ) ) {

        $resp = wp_remote_get( $url );
        $json = wp_remote_retrieve_body( $resp );
        $data = json_decode( $json, true );

        set_transient($url, $data, HOUR_IN_SECONDS);
    }

    return $data;
}


function get_club_races() {

    global $cabici_config;

    $options = get_option('cabici_options');
    $club = $options['club'];

    $url = 'api/races?club='.$club.'&select=future';

    return api_request($url);
}


function get_club_races_with_results($count) {

    global $cabici_config;

    $options = get_option('cabici_options');
    $club = $options['club'];

    $url = 'api/races?club='.$club.'&select=results&count='.$count;

    return api_request($url);
}

function get_all_races($count) {

    global $cabici_config;

    $options = get_option('cabici_options');
    $club = $options['club'];

    $url = 'api/races?select=future&count='.$count;

    return api_request($url);
}

function get_club_info() {

    global $cabici_config;

    $options = get_option('cabici_options');
    $club = $options['club'];
    if ($cabici_config['devel']) {
        $url = 'api/clubs/'.$club;
    } else {
        $url = 'api/clubs/'.$club.'/';
    }
    return api_request($url);
}

function get_club_list() {

    global $cabici_config;

    $options = get_option('cabici_options');
    $club = $options['club'];
    if ($cabici_config['devel']) {
        $url = 'api/clubs';
    } else {
        $url = 'api/clubs/';
    }
    return api_request($url);
}


// get results for the given race, only riders placing in each grade
function get_race_result($raceid) {

    global $cabici_config;

    $options = get_option('cabici_options');
    if ($cabici_config['devel']) {
        $url = 'api/raceresults-placed';
    } else {
        $url = 'api/raceresults/?race='.$raceid;
    }

    $result = api_request($url);
    return $result;
}

// get the id of the most recent race for a club
function most_recent_race($clubslug) {

    global $cabici_config;

    $options = get_option('cabici_options');
    $club = $options['club'];

    $key = 'cabici_races_' . $club;
    if ($cabici_config['devel']) {
        $url = 'api/races-recent';
    } else {
        $url = 'api/races?club='.$club.'&select=results&count=1';
    }

    $races = api_request($url);
    return $races[0];
}



function get_pointscore($id) {

    $url = 'api/pointscores/'.$id;

    console_log($url);
    return api_request($url);
}


// Dashboard Widget

function cabici_add_dashboard_widgets() {

	wp_add_dashboard_widget(
                 'cabici_dashboard_widget',         // Widget slug.
                 'Cabici Race Management',         // Title.
                 'cabici_dashboard_widget_function' // Display function.
        );
}
add_action( 'wp_dashboard_setup', 'cabici_add_dashboard_widgets' );

/**
 * Otput the contents of our Dashboard Widget.
 */
function cabici_dashboard_widget_function() {

    global $cabici_config;
    $info = get_club_info();

	echo "<p>Cabici plugin configured for ".$info['name']."</p>";
    echo "<p>Go to <a href='".$cabici_config['url']."clubs/".$info['slug']."/dashboard/'>";
    echo $info['name']." Dashboard</a>";
    echo " on Cabici to update race schedule and add results.</p>";
}

?>
