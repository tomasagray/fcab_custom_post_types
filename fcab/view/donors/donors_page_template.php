<?php

namespace fcab;

use fcab\model\FCABDonor;
use WP_Query;
use const fcab\model\DONATIONS_PAGE_TITLE;


function partition_donors(array $_donors): array
{
    $donors = $_donors;
    $intervals = [500, 1000, 2000, 5000, 10000, PHP_INT_MAX];
    $donor_table = [];

    while (($donor = array_pop($donors)) !== null) {
        $donations = get_post_meta($donor->ID, FCABDonor::DONATION_FIELD_NAME, true);
        foreach ($intervals as $interval) {
            if ($donations <= $interval) {
                $donor_table[$interval][] = $donor;
                break;
            }
        }
    }
    return $donor_table;
}

/**
 * @param array $donors
 */
function print_donors(array $donors)
{
    echo '<div class="donor-box">';
    foreach ($donors as $donor):
        ?>
        <a href="<?php echo get_post_permalink($donor->ID); ?>">
            <div class="donor-card">
                <?php
                $thumb_url = get_the_post_thumbnail_url($donor);
                if ($thumb_url !== false):
                    ?>
                    <div class="donor-image" style="background-image: url('<?php echo $thumb_url; ?>');"></div>
                <?php
                endif;
                ?>
                <div class="donor-summary">
                    <p class="donor-title"><?php echo $donor->post_title; ?></p>
                    <?php
                    $content = $donor->post_content;
                    if ($content !== false && $content !== ''):
                        ?>
                        <p class="donor-description"><?php echo $content; ?></p>
                    <?php endif; ?>
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
$donations_page = get_page_by_title(DONATIONS_PAGE_TITLE);

echo $donations_page->post_content;
?>
    <div id="donors-container">
        <?php
        $donors = $loop->get_posts();
        $donor_groups = partition_donors($donors);
        ksort($donor_groups);
        $intervals = array_keys($donor_groups);
        // Top-level donors
        $top_interval = array_pop($intervals);
        $top_floor = $intervals[count($intervals) - 1];
        if (count($donor_groups[$top_interval]) > 0) {
            echo '<h3 class="donors-heading">$' . $top_floor . ' - above</h3>';
            print_donors($donor_groups[$top_interval]);
        }

        for ($i = count($intervals) - 1; $i > 0; $i--) {
            $top = $intervals[$i];
            $bottom = $intervals[$i - 1];
            if (count($donor_groups[$top]) > 0) {
                echo '<h3 class="donors-heading">$' . $bottom . ' - $' . $top . '</h3>';
                print_donors($donor_groups[$top]);
            }
        }

        $bottom_group = $intervals[0];
        if (count($donor_groups[$bottom_group]) > 0) {
            echo '<h3 class="donors-heading">Up to $' . $bottom_group . '</h3>';
            print_donors($donor_groups[$bottom_group]);
        }
        ?>
    </div>
    <?php
get_footer();
