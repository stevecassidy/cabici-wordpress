<?php
class CabiciSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Cabici Races',
            'manage_options',
            'cabici-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'cabici_options' );
        ?>
        <div class="wrap">
            <h1>Cabici Races Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'cabici_option_group' );
                do_settings_sections( 'cabici-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'cabici_option_group', // Option group
            'cabici_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Club Settings', // club
            array( $this, 'print_section_info' ), // Callback
            'cabici-setting-admin' // Page
        );

        add_settings_field(
            'cabici_club',
            'Select Your Club',
            array( $this, 'club_callback' ),
            'cabici-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['club'] ) )
            $new_input['club'] = sanitize_text_field( $input['club'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Here you can select the name of the club that will be used to display race listings and results on this site.';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function club_callback()
    {
        $clubs = get_club_list();
        echo('<select id="club" name="cabici_options[club]">');
        foreach($clubs as $club) {
            if ($club['slug'] == $this->options['club']) {
                echo('<option selected value="'.$club['slug'].'">'.$club['name'].'</option>');
            } else {
                echo('<option value="'.$club['slug'].'">'.$club['name'].'</option>');
            }
        }
        echo('</select>');

        /*
        printf(
            '<input type="text" id="club" name="cabici_options[club]" value="%s" />',
            isset( $this->options['club'] ) ? esc_attr( $this->options['club']) : ''
        );
        */
    }
}

if( is_admin() )
    $my_settings_page = new CabiciSettingsPage();
