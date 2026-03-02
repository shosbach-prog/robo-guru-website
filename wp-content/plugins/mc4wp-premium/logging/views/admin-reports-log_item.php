<?php
defined('ABSPATH') or exit;

/** @var object $item */
/** @var object $list */

?>

<style>
    .mc4wp-log-item table {
        border-collapse: collapse;
        border-spacing: 0;
        background: white;
        width: 100%;
        table-layout: fixed;
    }

    .mc4wp-log-item th,
    .mc4wp-log-item td {
        border: 1px solid #ddd;
        padding: 12px;
    }

    .mc4wp-log-item th {
        width: 160px;
        font-size: 14px;
        text-align: left;
    }

    .mc4wp-log-item pre{
        background: white;
        padding: 20px;
        border: 1px solid #ddd;
    }
</style>

<div class="mc4wp-log-item">
    <h2 style="margin-top: 0;"><span><?php esc_html_e('View log item', 'mailchimp-for-wp'); ?></span></h2>

    <table>
        <tr>
            <th><?php esc_html_e('Email Address', 'mailchimp-for-wp'); ?></th>
            <td><?php echo esc_html($item->email_address); ?></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Audience', 'mailchimp-for-wp'); ?></th>
            <td><?php
                echo $list ? sprintf('<a href="https://admin.mailchimp.com/audience/contacts/?id=%s">%s</a>', $list->web_id, esc_html($list->name)) : 'Unknown list';
            ?>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e('Merge Fields', 'mailchimp-for-wp'); ?></th>
            <td><?php
            foreach ((array) $item->merge_fields as $tag => $value) {
                if (in_array($tag, [ 'INTERESTS', 'GROUPINGS', 'OPTIN_IP' ], true)) {
                    continue;
                }

                // address fields and other array style fields
                $value = is_array($value) ? join(', ', $value) : $value;
                echo '<strong>', esc_html($tag), '</strong>: ', esc_html($value), '<br>';
            }

            if (empty($item->merge_fields)) {
                echo '&mdash;';
            }
            ?>
            </td>
        </tr>
        <tr>
            <th><?php _e('Interests', 'mailchimp-for-wp'); ?></th>
            <td><?php
                $categories = [];

            foreach ((array) $item->interests as $interest_id => $interested) {
                // only show interests which were marked as "true"
                if (! $interested) {
                    continue;
                }

                $category = $this->get_interest_category_by_interest_id($list->id, $interest_id);
                if (!isset($categories[$category->title])) {
                    $categories[$category->title] = [];
                }
                $categories[$category->title][] = $category->interests[$interest_id];
            }

            foreach ($categories as $category_name => $interests) {
                echo '<strong>', esc_html($category_name), '</strong>: ', esc_html(join(', ', $interests)), '<br>';
            }

            if (empty($item->interests)) {
                echo '&mdash;';
            }
            ?>
            </td>
        </tr>
        <?php if ($item->status) { ?>
        <tr>
            <th><?php esc_html_e('Status', 'mailchimp-for-wp'); ?></th>
            <td><?php echo esc_html($item->status); ?></td>
        </tr>
        <?php } // end if status?>
        <?php if ($item->ip_signup) { ?>
        <tr>
            <th><?php esc_html_e('IP Address', 'mailchimp-for-wp'); ?></th>
            <td><?php echo esc_html($item->ip_signup); ?></td>
        </tr>
        <?php } // end if ip_signup ?>
        <?php if ($item->language) { ?>
        <tr>
            <th><?php esc_html_e('Language', 'mailchimp-for-wp'); ?></th>
            <td><?php echo esc_html($item->language); ?></td>
        </tr>
        <?php } // end if language ?>
        <?php if ($item->vip) { ?>
        <tr>
            <th><?php esc_html_e('VIP', 'mailchimp-for-wp'); ?></th>
            <td><?php echo esc_html($item->vip); ?></td>
        </tr>
        <?php } // end if vip?>
        <tr>
            <th><?php esc_html_e('Source', 'mailchimp-for-wp'); ?></th>
            <td><?php echo make_clickable(strip_tags($item->url)); ?></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Method', 'mailchimp-for-wp'); ?></th>
            <td><?php echo esc_html($item->type); // TODO: Use pretty name here.?></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Datetime', 'mailchimp-for-wp'); ?></th>
            <td><?php echo mc4wp_logging_gmt_date_format($item->datetime); ?></td>
        </tr>
    </table>

    <p>
        <a href="<?php echo admin_url('admin.php?page=mailchimp-for-wp-reports&tab=log'); ?>">&leftarrow; <?php esc_html_e('Back to log overview', 'mailchimp-for-wp'); ?></a>
    </p>

    <?php
    if (WP_DEBUG) {
        echo '<h4>', __('Raw', 'mailchimp-for-wp'), '</h4>';
        echo '<pre>';
        echo version_compare(PHP_VERSION, '5.4', '>=') ? json_encode($item->to_json(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) : json_encode($item->to_json());
        echo '</pre>';
    }
    ?>
</div>
