<?php
/** @var MC4WP_Ecommerce_Object_Count $order_count */
?>
<h3>
    <?php _e('Orders', 'mc4wp-ecommerce'); ?>

    <span class="mc4wp-status-label" id="orders-count">
        <span class="tracked">-</span>/<span class="all">-</span>
    </span>
</h3>

<p>
    <?php _e('Adding your orders to Mailchimp will allow you to see purchases made by your subscribers.', 'mc4wp-ecommerce'); ?>
</p>

<div data-wizard="orders"></div>
<noscript><?php esc_html_e('Please enable JavaScript to use this feature.', 'mc4wp-ecommerce'); ?></noscript>

