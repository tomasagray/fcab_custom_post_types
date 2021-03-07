<?php

namespace fcab;


/**
 * Plugin Name: FCAB Post Types
 * Description: Custom post types for the FCAB WordPress website
 * Version: 0.9a
 * Author: Tomás Gray
 **/


require_once 'fcab/model/FCABDonor.php';
require_once 'fcab/model/FCABDonorMetaBox.php';
require_once 'fcab/view/Page.php';
require_once 'fcab/view/Controller.php';
require_once 'fcab/view/TemplateLoader.php';
require_once 'fcab/view/donors/DonorsPage.php';
require_once 'fcab/view/donors/DonorsPageController.php';
require_once 'fcab/view/donors/DonorsPageTemplateLoader.php';

const DOMAIN = 'fcab_cpt';
const DONATION_FIELD_NAME = 'fcab_cpt_donor_total_donations';
// Options
const FCAB_CPT_OPTIONS = 'fcab_cpt_options';
const FCAB_CPT_DONATION_PAGE_ID = 'fcab_cpt_donation_page_id';

require_once 'init-controllers.php';
require_once 'fcab/functions.php';


/**
 * Create Donations page
 */
function create_donations_page()
{

    // Read default content
    $content = file_get_contents("fcab/view/donors/default_content.php", 'rb')
                    or die("Could not load default FCAB content!");
    $qr_img = plugin_dir_url(__FILE__) . "/fcab/view/donors/paypal_qr_code.png";
    $img_content = str_replace("%QR_CODE%", $qr_img, $content);

    // Check if page already exists
    $page = get_page_by_title('Donations');
    if ($page !== null) {
        return;
    }

    $donations_page = [
        'post_title' => wp_strip_all_tags('Donations'),
        'post_content' => $img_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_type' => 'page',
    ];
    $page_id = wp_insert_post($donations_page);
    update_option(FCAB_CPT_DONATION_PAGE_ID, $page_id);
}
register_activation_hook(__FILE__, 'fcab\create_donations_page');

/**
 * Create plugin settings
 */
function create_settings()
{
    register_setting(FCAB_CPT_OPTIONS, FCAB_CPT_DONATION_PAGE_ID);
}
add_action('admin_init', 'fcab\create_settings');

/**
 * Add plugin style sheets
 */
function add_donor_stylesheet()
{
    wp_enqueue_style('donor_stylesheet', plugins_url('fcab/view/donors/donor_page.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'fcab\add_donor_stylesheet');
