<?php

const PLUGIN_ID = 'fcab_custom_post_types';
const SETTINGS_ID = 'fcab_cpt_settings';
const SETTING_ID = 'fcab_cpt_donor_intervals';


function register_fcab_cpt_settings()
{
    register_setting(
        SETTINGS_ID,
        SETTING_ID,
        'validate_fcab_cpt_settings'
    );

    add_settings_section(
        'donations',
        'Donations',
        'fcab_cpt_donations_settings',
        PLUGIN_ID
    );

    add_settings_field(
        SETTING_ID,
        'Donations Intervals',
        'render_fcab_donations_interval',
        PLUGIN_ID,
        'donations'
    );
}

function fcab_custom_post_types_options()
{
    add_options_page(
        'FCAB Custom Post Types Options',
        'FCAB Custom Post Types',
        'manage_options',
        PLUGIN_ID,
        'render_fcab_cpt_options_page');
}

function render_fcab_cpt_options_page()
{
    ?>
    <div>
        <h1>FCAB Custom Post Type Options</h1>
        <form action="options.php" method="GET">
            <?php
            settings_fields(SETTINGS_ID);
            do_settings_sections(PLUGIN_ID);
            ?>
            <input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Save'); ?>"/>
        </form>
    </div>
    <?php
}

function render_fcab_donations_interval()
{
    ?>
    <div id="donor-interval-header">
        <button type="button" id="add-interval-button" value="+" class="add-button" onclick="addNewDonorInterval();">
            +
        </button>
        <label for="add-interval-button">Add interval</label>
    </div>
    <div id="donor-interval-container" class="donor-interval-container"></div>
    <script>
        // Initialize donor interval settings
        window.onload = function () {
            let container = document.getElementById('donor-interval-container');
            if (container.children.length === 0) {
                addNewDonorInterval();
            }
        };
    </script>
    <?php
}

function validate_fcab_cpt_settings($input)
{
    echo 'validating';
    var_dump($input);
    return $input;
}

function fcab_enqueue_admin_scripts($hook)
{
    if ($hook === "settings_page_fcab_custom_post_types") {
        wp_enqueue_script('fcab_settings_script', plugin_dir_url(__FILE__) . 'settings.js', [], '1.0');
        wp_enqueue_style('fcab_settings_styles', plugin_dir_url(__FILE__) . 'settings.css', [], '1.0');
    }
}

//add_action('admin_menu', 'fcab_custom_post_types_options');
//add_action('admin_init', 'register_fcab_cpt_settings');
add_action('admin_enqueue_scripts', 'fcab_enqueue_admin_scripts');
