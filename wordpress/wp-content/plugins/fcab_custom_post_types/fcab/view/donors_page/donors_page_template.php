<?php

namespace fcab;

use fcab\model\FCABDonor;
use WP_Query;

$q_args = [
    'post_type' => FCABDonor::POST_TYPE,
    'post_status' => 'publish',
    'posts_per_page' => 24,
];
// Get donors
$loop = new WP_Query($q_args);

get_header();
?>
    <style type="text/css">
        <?php require_once 'donor_page.css'; ?>
    </style>
    <div id="donation-container" class="content-area">
        <div>
            <h1 class="donors-heading">Make a donation</h1>
            <h3>Donate today and help us in our goal to facilitate development in Bangladesh. Every contribution
                counts.</h3>
        </div>
        <div id="paypal-container">
            <div id="paypal-button">
                <p>Click the button to be taken to PayPal to make a donation to FCAB. Alternatively, scan the QR code
                    with your smartphone to donate with the PayPal app.</p>
                <?php require_once 'paypal_button.html'; ?>
            </div>
            <div id="paypal-qr-code">
                <img src="<?php echo get_site_url(); ?>/wp-content/uploads/paypal_qr_code.png"
                     alt="Scan with your smartphone app to easily make a generous contribution" width="300"/>
            </div>

        </div>
    </div>

    <div id="donors-container">
        <h2 class="donors-heading">Our donors</h2>
        <div class="donor-box">
            <?php
            if ($loop->have_posts()):
                while ($loop->have_posts()):
                    $loop->the_post();
                    ?>
                    <a href="<?php the_permalink() ?>">
                        <div class="donor-card">
                            <div class="donor-image"
                                 style="background-image: url('<?php echo get_the_post_thumbnail_url() ?>');"></div>
                            <div class="donor-description">
                                <h3><?php the_title(); ?></h3>
                                <p><?php the_content(); ?></p>
                            </div>
                        </div>
                    </a>
                <?php
                endwhile;
            endif;
            // Clear query
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <?php
get_footer();

