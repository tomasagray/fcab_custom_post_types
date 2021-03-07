<?php

namespace fcab;

use fcab\model\FCABDonor;
use WP_Query;

/**
 * @param $donor1
 * @param $donor2
 * @return int
 */
function sort_donors($donor1, $donor2): int
{
    $d1 = get_post_meta($donor1->ID, DONATION_FIELD_NAME, true);
    $d2 = get_post_meta($donor2->ID, DONATION_FIELD_NAME, true);
    if ($d1 === $d2) {
        return 0;
    }
    return ($d1 < $d2) ? -1 : 1;
}

function partition_donors(array $donors): array
{
    $intervals = [500, 1000, 2000, 5000, 10000];
    $donor_table = [];
    foreach ($donors as $donor) {
        $donations = get_post_meta($donor->ID, DONATION_FIELD_NAME, true);
        $i = 0;
        foreach ($intervals as $inc) {
            if ($donations <= $inc) {
                $donor_table[$inc][$i] = $donor;
                $i++;
            }
        }
    }
    return $donor_table;
}

/**
 * @param $donors
 */
function print_donors(array $donors)
{
    echo '<div class="donor-box">';
    foreach ($donors as $donor):
        ?>
        <a href="<?php echo get_post_permalink($donor->ID); ?>">
            <div class="donor-card">
                <div class="donor-description">
                    <h4><?php echo $donor->post_title; ?></h4>
                    <p><?php echo $donor->post_content; ?></p>
                </div>
            </div>
        </a>
    <?php
    endforeach;
    echo '</div>';
}

$q_args = [
    'post_type' => FCABDonor::POST_TYPE,
    'post_status' => 'publish',
    'posts_per_page' => -1,
];
// Get donors
$loop = new WP_Query($q_args);

get_header();

// Print donor page
$page_id = get_option('fcab_cpt_donation_page_id');
$donations_page = get_post($page_id);
echo $donations_page->post_content;
?>
    <div id="donors-container">
            <?php
            $donors = $loop->get_posts();
            $donor_groups = partition_donors($donors);
            $intervals = array_keys($donor_groups);
            // Top-level donors
            $top_interval = array_pop($intervals);
            echo '<h3 class="donors-heading">$' . $top_interval . ' - above</h3>';
            print_donors($donor_groups[$top_interval]);

            for ($i = count($intervals) - 1; $i > 0; $i--) {
                $top = $intervals[$i];
                $bottom = $intervals[$i - 1];
                echo '<h3 class="donors-heading">$' . $bottom . ' - $' . $top . '</h3>';
                print_donors($donor_groups[$top]);
            }

            $bottom_group = $intervals[0];
            echo '<h3 class="donors-heading">Up to ' . $bottom_group . '</h3>';
            print_donors($donor_groups[$bottom_group]);
            ?>
    </div>
    <?php
get_footer();
