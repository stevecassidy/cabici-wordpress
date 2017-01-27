<?php
/*
 * last result shortcode inserts a table containing the results
 * of the most recent race
 */
add_shortcode( 'cabici_pointscore', 'cabici_pointscore_handler' );

function cabici_pointscore_handler( $atts, $content = null ) {

    $options = get_option('cabici_options');
    $club = $options['club'];
    $a = shortcode_atts( array(
        'id' => 0,
    ), $atts );

    if (!$club) {
        return '<p>No Club configured.</p>';
    }
    if ($a['id'] == 0) {
        return '<p>No pointscore identifier specified [cabici_pointscore id=NNNNN]</p>';
    }

    $pointscore = get_pointscore($a['id']);

    ob_start();
    ?>
    <div class="cabici-container">
        <h3><?= $pointscore['name'] ?></h3>
        <table class='racetable'>
            <tr><th>Rider</th><th>Club</th><th>Points</th><th>Events</th></tr>
        <?php
        foreach ($pointscore['results'] as $result) {
            ?>
            <tr>
                <td><?= $result['rider'] ?></td>
                <td><?= $result['club'] ?></td>
                <td><?= $result['points'] ?></td>
                <td><?= $result['eventcount'] ?></td>
            </tr>
            <?php
        }
        ?>
            </tbody></table>
            <p>Race Results from <a target=new href="http://cabici.net/"</a>cabici.net</a>.</p>
    </div>

    <?php

    $table = ob_get_clean();
    return $table;
}
?>
