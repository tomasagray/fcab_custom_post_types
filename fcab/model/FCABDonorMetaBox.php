<?php

namespace fcab\model;

use const fcab\DOMAIN;

class FCABDonorMetaBox
{

    public static function add(): void
    {
        add_meta_box(
            'fcab_cpt_meta_boxes',
            __('Donor Information', DOMAIN),
            [self::class, 'html'],
            'fcab_cpt_donor',
            'side',
            'high'
        );
    }

    public static function save(int $post_id): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        $fields = ['' . FCABDonor::DONATION_FIELD_NAME . ''];
        foreach ($fields as $field) {
            if (array_key_exists($field, $_POST)) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    public static function html($post): void
    {
        // State verification
        wp_nonce_field(plugin_basename(__FILE__), 'fcab_cpt_meta_nonce');
        $total_donations = get_post_meta($post->ID, FCABDonor::DONATION_FIELD_NAME, true);
        ?>
        <!-- FCAB Donor custom data fields -->
        <div>
            <label for="<?php echo FCABDonor::DONATION_FIELD_NAME ?>">Total Donations (US$)</label>
            <input type="number" min="1" step="1" name="<?php echo FCABDonor::DONATION_FIELD_NAME ?>"
                   id="<?php echo FCABDonor::DONATION_FIELD_NAME ?>" value="<?php echo $total_donations ?>"/>
        </div>
        <?php
    }
}

add_action('add_meta_boxes', [FCABDonorMetaBox::class, 'add']);
add_action('save_post', [FCABDonorMetaBox::class, 'save']);
