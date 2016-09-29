
<?php

add_shortcode( 'cabici_race_schedule', 'cabici_race_schedule_handler' );

function cabici_race_schedule_handler( $atts, $content = null ) {

    $options = get_option('cabici_options');
    $club = $options['club'];

    if (!$club) {
        return '<p>No Club configured.</p>';
    }

    $recent_race = most_recent_race($club);

    $races = get_club_races();

    ob_start();
    ?>
    <div class="cabici-container">
        <p>Race Listings from <a target=new href="http://cabici.net/"</a>cabici.net</a>.</p>
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
                <a target=new href="http://cabici.net/races/<?= $options['club']?>/<?= $race['id'] ?>"><?= $race['title'] ?></a>
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

?>
