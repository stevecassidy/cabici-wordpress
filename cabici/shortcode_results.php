<?php
/*
 * last result shortcode inserts a table containing the results
 * of the most recent race
 */
add_shortcode( 'cabici_last_result', 'cabici_last_result_handler' );

function cabici_last_result_handler( $atts, $content = null ) {

    $options = get_option('cabici_options');
    $club = $options['club'];

    if (!$club) {
        return '<p>No Club configured.</p>';
    }

    $recent_race = most_recent_race($club);

    $results = get_race_result($recent_race['id']);
    $lastgrade = "X";

    // count the number of riders in each grade
    $counter = 0;
    $totalriders = 0;
    foreach ($results as $result) {
        if ($result['grade'] != $lastgrade) {
            if ($lastgrade != 'X') {
                $gradecount[$lastgrade] = $counter;
                $counter = 0;
            }
        }
        $counter += 1;
        $totalriders += 1;
        $lastgrade = $result['grade'];
    }
    $gradecount[$lastgrade] = $counter;
    $lastgrade = "X";

    ob_start();
    ?>
    <div class="cabici-container">
        <h3><?= $recent_race['date'] ?>: <?= $recent_race['title'] ?> - <?= $recent_race['location']['name'] ?></h3>
            <?php
            echo('<p>'.$totalriders.' Riders</p>');
            foreach ($results as $result) {
                if ($result['grade'] != $lastgrade) {
                    if ($lastgrace != 'X') {
                        echo ('</tbody></table>');
                    }
                    echo('<h4>'.$result['grade'].' Grade ('.$gradecount[$result['grade']].' riders)</h4>');
                    echo('<table class="racetable">
                        <thead><tr><th>Place</th><th>Rider</th><th>Club</th></tr></thead>
                        <tbody>');
                    $lastgrade = $result['grade'];
                }
                if ($result['place'] != '') {
                    ?>
                    <tr>
                        <td><?= $result['place'] ?></td>
                        <td><?= $result['rider'] ?></td>
                        <td><?= $result['club'] ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody></table>
            <p>Race Results from <a target=new href="https://cabici.net/"</a>cabici.net</a>.</p>
    </div>

    <?php

    $table = ob_get_clean();
    return $table;
}
?>
