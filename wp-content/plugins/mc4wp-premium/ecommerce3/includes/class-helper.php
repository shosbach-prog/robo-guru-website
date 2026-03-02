<?php

use Automattic\WooCommerce\Utilities\OrderUtil;

class MC4WP_Ecommerce_Helper
{
    /**
    * @var WPDB
    */
    private $db;

    /**
     * Whether WooCommerce HPOS is enabeld
     */
    private $hpos_enabled;

    /**
    * MC4WP_Ecommerce_Helper constructor.
    */
    public function __construct()
    {
        $this->db = $GLOBALS['wpdb'];
        $this->hpos_enabled = class_exists(OrderUtil::class) && OrderUtil::custom_orders_table_usage_is_enabled();
    }

    public function get_order_ids()
    {
        $query = $this->get_order_query('DISTINCT(p.id)', false);
        return $this->db->get_col($query);
    }

    public function get_tracked_order_ids()
    {
        $query = $this->get_order_query('DISTINCT(p.id)', true);
        return $this->db->get_col($query);
    }

    public function get_product_ids()
    {
        $query = $this->get_product_query('DISTINCT(p.id)');
        return $this->db->get_col($query);
    }

    public function get_tracked_product_ids()
    {
        $query = $this->get_product_query('DISTINCT(p.id)', true);
        return $this->db->get_col($query);
    }

    /**
    * @param string $select
    * @param bool $tracked_only
    *
    * @return string
    */
    private function get_product_query($select = 'p.*', $tracked_only = false)
    {
        $query = sprintf("SELECT %s FROM {$this->db->posts} p ", $select);
        if ($tracked_only) {
            $query .= sprintf(" RIGHT JOIN {$this->db->postmeta} pm ON p.id = pm.post_id AND pm.meta_key = '%s' ", MC4WP_Ecommerce::META_KEY);
        }
        $query .= "WHERE p.post_type = 'product'
			AND p.post_status IN('publish', 'draft', 'private', 'trash') ";

        // order by descending product ID so we start with newest first
        if (strpos($select, 'COUNT') === false) {
            $query .= " ORDER BY p.id DESC";
        }

        return $query;
    }

    /**
    * @param string $select
    * @param bool $tracked_only
    *
    * @return string
    */
    private function get_order_query($select = 'p.*', $tracked_only = false)
    {
        if ($this->hpos_enabled) {
            return $this->get_order_query_hpos($select, $tracked_only);
        }

        // TODO: Improve performance by not really checking for valid email address here.
        $query = "SELECT %s FROM {$this->db->posts} p LEFT JOIN {$this->db->postmeta} pm ON p.id = pm.post_id
			WHERE p.post_type = 'shop_order'
			AND p.post_status IN( %s )";

        // add IN clause for order statuses
        $order_statuses = mc4wp_ecommerce_get_order_statuses();
        $query = sprintf($query, $select . ' ', "'" . join("', '", $this->db->_escape($order_statuses)) . "'");

        if ($tracked_only) {
            $query .= sprintf(" AND pm.meta_key = '%s'", MC4WP_Ecommerce::META_KEY);
        } else {
            $query .= " AND pm.meta_key IN ('_billing_email', 'billing_email', '_customer_user') AND pm.meta_value != '' AND pm.meta_value != '0'";
        }

        // order by descending order ID so we start with newest orders first
        if (strpos($select, 'COUNT') === false) {
            $query .= " ORDER BY p.id DESC";
        }

        return $query;
    }

    private function get_order_query_hpos($select = 'p.*', $tracked_only = false)
    {
        global $wpdb;
        $order_statuses = mc4wp_ecommerce_get_order_statuses();
        $placeholders = join(', ', array_fill(0, count($order_statuses), '%s'));
        $query = "
    		SELECT {$select}
    		FROM {$wpdb->prefix}wc_orders p
    		LEFT JOIN {$wpdb->prefix}wc_orders_meta om ON om.order_id = p.id
    		WHERE p.status IN ({$placeholders})
    		AND (p.billing_email IS NOT NULL OR p.customer_id IS NOT NULL)";
        $params = $order_statuses;

        if ($tracked_only) {
            $query .= " AND om.meta_key = %s";
            $params[] = MC4WP_Ecommerce::META_KEY;
        }

        // order by descending order ID so we start with newest orders first
        if (strpos($select, 'COUNT') == false) {
            $query .= ' ORDER BY p.id DESC';
        }

        return $wpdb->prepare($query, $params);
    }

    /**
    * @param string $email_address
    *
    * @return float
    * @see wc_get_customer_total_spent
    */
    public function get_total_spent_for_email($email_address)
    {
        // use WooCommmerce method when this is a registered customer
        // note that this uses the WooCommerce registered order statuses for "reports" (vs the ones from mc4wp_ecommerce_get_order_statuses())
        if (function_exists('wc_get_customer_total_spent')) {
            $user = get_user_by('email', $email_address);
            if ($user instanceof WP_User && in_array('customer', $user->roles)) {
                return floatval(wc_get_customer_total_spent($user->ID));
            }
        }

        if ($this->hpos_enabled) {
            return $this->get_total_spent_for_email_hpos($email_address);
        }

        $order_statuses = mc4wp_ecommerce_get_order_statuses();
        $in = join("', '", $this->db->_escape($order_statuses));

        $query = "SELECT SUM(meta2.meta_value)
		FROM {$this->db->posts} as posts
		LEFT JOIN {$this->db->postmeta} AS meta ON posts.ID = meta.post_id AND ( meta.meta_key = '_billing_email' OR meta.meta_key = 'billing_email' )
		LEFT JOIN {$this->db->postmeta} AS meta2 ON posts.ID = meta2.post_id AND meta2.meta_key = '_order_total'
		WHERE   meta.meta_value     = %s
		AND     posts.post_type     = 'shop_order'
		AND     posts.post_status   IN( '{$in}' )
		";

        $query = $this->db->prepare($query, $email_address);

        $result = $this->db->get_var($query);
        return floatval($result);
    }

    /**
     * Get the total amount of money spent by a given email address
     * Note that this does not take currencies into amount
     *
     * @param string $email_address
     * @return float
     */
    public function get_total_spent_for_email_hpos($email_address)
    {
        global $wpdb;
        $order_statuses = mc4wp_ecommerce_get_order_statuses();
        $placeholders = join(', ', array_fill(0, count($order_statuses), '%s'));
        $query = "SELECT SUM(o.total_amount)
    		FROM {$wpdb->prefix}wc_orders o
    		WHERE o.status IN ({$placeholders})
    		AND o.billing_email = %s
    	";
        $params = $order_statuses;
        $params[] = $email_address;

        $query = $wpdb->prepare($query, $params);
        return (float) $wpdb->get_var($query);
    }

    /**
    * @param string $email_address
    *
    * @return int
    * @see wc_get_customer_order_count
    */
    public function get_order_count_for_email($email_address)
    {

        // use WooCommmerce method when this is a registered customer
        // note that this uses the WooCommerce registered order statuses for "reports" (vs the ones from mc4wp_ecommerce_get_order_statuses())
        if (function_exists('wc_get_customer_order_count')) {
            $user = get_user_by('email', $email_address);
            if ($user instanceof WP_User && in_array('customer', $user->roles)) {
                return intval(wc_get_customer_order_count($user->ID));
            }
        }

        if ($this->hpos_enabled) {
            return $this->get_order_count_for_email_hpos($email_address);
        }

        $order_statuses = mc4wp_ecommerce_get_order_statuses();
        $in = join("', '", $this->db->_escape($order_statuses));

        $query = "SELECT COUNT(DISTINCT(posts.id))
		FROM {$this->db->posts} as posts
		LEFT JOIN {$this->db->postmeta} AS meta ON posts.ID = meta.post_id
		WHERE meta.meta_key = '_billing_email' AND meta.meta_value     = %s
		AND posts.post_type     = 'shop_order'
		AND posts.post_status   IN( '{$in}' )
		";

        $query = $this->db->prepare($query, $email_address);
        $result = $this->db->get_var($query);
        return intval($result);
    }

    /**
     * Get the number of orders for a guest customer with the given email
     *
     * @param string $email_address
     * @return int
     * @since 4.10
     */
    public function get_order_count_for_email_hpos($email_address)
    {
        global $wpdb;
        $order_statuses = mc4wp_ecommerce_get_order_statuses();
        $placeholders = join(', ', array_fill(0, count($order_statuses), '%s'));
        $query = "SELECT COUNT(o.id)
    		FROM {$wpdb->prefix}wc_orders o
    		WHERE o.status IN ({$placeholders})
    		AND o.billing_email = %s
    	";
        $params = $order_statuses;
        $params[] = $email_address;

        $query = $wpdb->prepare($query, $params);
        return (int) $wpdb->get_var($query);
    }
}
