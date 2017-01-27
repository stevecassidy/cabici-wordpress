<?php

add_shortcode( 'cabici_past_results', 'cabici_past_results_handler' );

function cabici_past_results_handler( $atts, $content = null ) {

    $options = get_option('cabici_options');
    $club = $options['club'];
    $a = shortcode_atts( array(
        'count' => 15,
    ), $atts );

    if (!$club) {
        return '<p>No Club configured.</p>';
    }

    $recent_race = most_recent_race($club);

    $races = get_club_races_with_results($a['count']);

    ob_start();
    ?>
    <div class="cabici-container">
        <table class='racetable'>
            <thead>
                <tr>
                    <th>Date/Start Time</th>
                    <th>Location/Race</th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan=3>
                        <p>Race Listings from <a target=new href="http://cabici.net/"</a>cabici.net</a>.</p>
                    </th>
                </tr>
            </tfoot>
            <tbody>
    <?php
    foreach ($races as $race) {
        $url_arr = explode( '/', $race['url'] );
        end( $url_arr );
        $race_number = prev( $url_arr );
        ?>
        <tr>
            <td>
                <strong><?php
                $date = DateTime::createFromFormat('Y-m-d', $race['date']);
                echo $date->format('D M jS');
                ?></strong>
            </td>
            <td>
                <?= $race['location']['name'] ?><br>
                <a target=new href="http://cabici.net/races/<?= $options['club']?>/<?= $race['id'] ?>"><?= $race['title'] ?></a>
                <?php
                if ($race['status'] == "c") {
                    echo('CANCELLED');
                }?>
            </td>
            <td><a id="restoggle<?= $race['id'] ?>" onclick="insertResults('<?= $race['id'] ?>', this)">View Results</a></td>
        </tr>
        <?php
    }
    ?>
            </tbody>
        </table>
    </div>
    <?php

    $table = ob_get_clean();

    return $table;
}

?>
