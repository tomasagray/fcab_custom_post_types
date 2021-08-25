<?php

namespace fcab\view\customfields;

use DateTime;
use fcab\model\FCABDonor;
use JsonException;

class FCABDonationFields
{
    public const POST_TYPE = 'fcab-donation-fields';
    public const NONCE = 'fcab-customfields-wpnonce';
    public const PREFIX = 'fcab_donation_';
    public const DONATION_FIELD = 'donation_amount';
    public const DONATION_ID_PREFIX = 'donation-checkbox-';

    private const LOG = "./donation.log";

    private array $post_types = ['page', 'post', FCABDonor::POST_TYPE];
    private array $custom_fields = [
        self::DONATION_FIELD => [
            'name' => self::DONATION_FIELD,
            'title' => 'Donation Amount (USD)',
            'description' => '',
            'type' => 'number',
            'scope' => ['page', FCABDonor::POST_TYPE],
            'capability' => 'edit_pages'
        ],
    ];

    public static function getTotalDonations(array $donations)
    {
        $total_donations = 0;
        foreach ($donations as $donation) {
            $total_donations += $donation['amount'];
        }
        return $total_donations;
    }

    public function __construct()
    {
        add_action('admin_menu', [$this, 'createDonationFields']);
        add_action('save_post', [$this, 'saveCustomFields'], 1, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
    }

    public function createDonationFields(): void
    {
        if (function_exists('add_meta_box')) {
            foreach ($this->post_types as $post_type) {
                add_meta_box(
                    self::POST_TYPE,
                    'Donations',
                    [$this, 'displayCustomFields'],
                    $post_type,
                    'normal', 'high'
                );
            }
        }
    }

    public function displayCustomFields(): void
    {
        global $post; ?>
        <div class="fcab-donation-fields-wrapper">
            <?php
            wp_nonce_field(self::POST_TYPE, self::NONCE, false, true);

            foreach ($this->custom_fields as $field):
                if ($this->isInScope($field, $post) && current_user_can($field['capability'], $post->ID)): ?>
                    <div class="form-field form-required fcab-custom-field-wrapper">
                        <?php switch ($field['name']):
                            case self::DONATION_FIELD:
                                $donations = $this->getDonorDonations($post->ID);
                                // print donations table
                                if ($donations !== null) {
                                    $this->printDonationsTable($donations);
                                }
                                $donation_id = self::PREFIX . self::DONATION_FIELD;
                                ?>
                                <div class="add-donation-wrapper">
                                    <label for="<?php echo $donation_id; ?>">Enter new donation ($USD):</label>
                                    <input type="number"
                                           name="<?php echo $donation_id; ?>"
                                           id="<?php echo $donation_id; ?>"
                                           min="0" max="10000000" value="0"/>
                                </div>
                                <?php break;
                        endswitch; ?>
                    </div>
                <?php
                endif;
            endforeach;
            ?>
        </div>
        <?php
    }

    public function enqueueStyles($hook): void
    {
        $style_url = plugin_dir_url(__FILE__) . 'donation-styles.css';
        wp_enqueue_style('fcab_custom_fields_styles', $style_url, [], 1.0);
    }

    private function printDonationsTable(array $donations): void
    { ?>
        <table class="donations-list">
            <thead>
            <tr>
                <td>Date</td>
                <td>Amount</td>
            </tr>
            </thead>
            <?php
            foreach ($donations as $donation): ?>
                <tr class="donation-entry">
                    <td class="donation-date">
                        <?php echo gmdate("m/d/Y", $donation['timestamp']); ?>
                    </td>
                    <td class="donation-amount">
                        <div>
                            <?php
                            $amount = number_format($donation['amount']);
                            echo '$' . $amount;
                            ?>
                            <input type="checkbox" class="donation-delete-button"
                                   name="<?php echo $donation['donation_id']; ?>"
                                   id="<?php echo $donation['donation_id']; ?>" checked/>
                            <label for="<?php echo $donation['donation_id']; ?>"></label>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="donations-total">
                <td>
                    <span><strong>Total Donations:</strong></span>
                </td>
                <td>
                    <span>
                        <?php
                        $total_donations = number_format(self::getTotalDonations($donations));
                        echo '$' . $total_donations;
                        ?>
                    </span>
                </td>
            </tr>
        </table>
        <script>
            /**
             * Handle checkbox state change
             */
            (function ($) {
                console.log("Entered script");
                $('.donation-delete-button').change(function () {
                    if (!this.checked) {
                        $(this).parents(".donation-entry")
                            .children("td")
                            .addClass("removed-donation");
                    } else {
                        $(this).parents(".donation-entry")
                            .children("td")
                            .removeClass("removed-donation");
                    }
                });
            })(jQuery);
        </script>
        <?php
    }

    public function saveCustomFields($post_id, $post): void
    {
        if (!isset($_POST[self::NONCE]) || !wp_verify_nonce($_POST[self::NONCE], self::POST_TYPE)) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        if (!in_array($post->post_type, $this->post_types, true)) {
            return;
        }

        try {
            foreach ($this->custom_fields as $field) {
                $field_name = self::PREFIX . $field['name'];
                if ($this->isValidUpdateRequest($field['capability'], $post_id, $field_name)) {
                    $donations = $this->getDonorDonations($post_id);
                    $updated_donations = $this->updateDonorDonations($donations);


                    $amount = (int)$_POST[$field_name];
                    if ($amount !== 0) {
                        $donation = $this->getDonationObject($amount);
                        $updated_donations[] = $donation;
                    }
                    $metadata = json_encode($updated_donations, JSON_THROW_ON_ERROR);
                    update_post_meta($post_id, $field_name, $metadata);
                }
            }
        } catch (JsonException $e) {
            return;
        }
    }

    /**
     * @param $capability
     * @param $post_id
     * @param string $field_name
     * @return bool
     */
    private function isValidUpdateRequest($capability, $post_id, string $field_name): bool
    {
        return
            current_user_can($capability, $post_id);
//            && isset($_POST[$field_name])
//            && trim($_POST[$field_name]);
    }

    private function getDonorDonations($post_id)
    {
        $donation_meta_id = self::PREFIX . self::DONATION_FIELD;
        $metadata = get_post_meta($post_id, $donation_meta_id, true);
        $donations = $this->decodeJson($metadata);
        if ($donations === null) {
            $donations = [];
        }
        if (!is_array($donations)) {
            $donations = [$this->getDonationObject($donations)];
        }
        return $donations;
    }

    private function getDonationObject(int $amount): array
    {
        $date = new DateTime();
        return [
            'donation_id' => uniqid(self::DONATION_ID_PREFIX, true),
            'amount' => $amount,
            'timestamp' => $date->getTimestamp(),
        ];
    }

    private function updateDonorDonations(array $donations): array
    {
        // get donations enabled in POST request
        $active_donations = array_filter($_POST, static function ($key) {
            return preg_match("/^" . self::DONATION_ID_PREFIX . "/", $key);
        }, ARRAY_FILTER_USE_KEY);

        // update donations table with only active donations
        return array_filter($donations, static function ($donation, $idx) use ($active_donations) {
            $donation_id = str_replace('.', '_', $donation['donation_id']);
            return array_key_exists($donation_id, $active_donations);
        }, ARRAY_FILTER_USE_BOTH);
    }

    private function isInScope($field, $post): bool
    {
        foreach ($field['scope'] as $scope) {
            if ($post->post_type === $scope) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $data
     * @return mixed|null
     */
    private function decodeJson(string $data)
    {
        try {
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return null;
        }
    }

    private function writeLog(string $data): void
    {
        if (self::LOG !== null) {
            file_put_contents(self::LOG, $data . "\n", FILE_APPEND);
        } else {
            echo $data;
        }
    }
}

