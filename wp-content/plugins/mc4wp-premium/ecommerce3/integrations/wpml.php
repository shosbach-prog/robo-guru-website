<?php

/**
 * WPML compatibility for Mailchimp ecommerce product permalinks.
 *
 * When WooCommerce products are synced to Mailchimp via the ecommerce module,
 * the product URLs may not include the correct WPML language context, causing
 * translated products to link to the default language version.
 *
 * These functions hook into the ecommerce product data filters to ensure
 * product URLs are generated with the correct WPML language.
 *
 * @since 4.10.22
 * @see https://github.com/ibericode/mailchimp-for-wordpress/issues/775
 */

defined('ABSPATH') or exit;

// WPML: fix product permalinks in ecommerce data synced to Mailchimp
add_filter('mc4wp_ecommerce_product_data', 'mc4wp_wpml_ecommerce_product_permalink');
add_filter('mc4wp_ecommerce_product_variants_data', 'mc4wp_wpml_ecommerce_product_variants_permalink');

/**
 * Fixes the product permalink for WPML translated products.
 *
 * Hooks into the ecommerce product data filter to replace the product URL
 * with the correct WPML-translated permalink.
 *
 * @since 4.10.22
 *
 * @param array $data Product data array containing 'id' and 'url' keys.
 * @return array Modified product data with corrected URL.
 */
function mc4wp_wpml_ecommerce_product_permalink($data)
{
    $language = apply_filters('wpml_element_language_code', null, [
        'element_id'   => $data['id'],
        'element_type' => 'product',
    ]);
    $data['url'] = apply_filters('wpml_permalink', $data['url'], $language);
    return $data;
}

/**
 * Fixes the product variant permalinks for WPML translated products.
 *
 * Hooks into the ecommerce product variants data filter to replace each
 * variant URL with the correct WPML-translated permalink.
 *
 * @since 4.10.22
 *
 * @param array $variants Array of variant data arrays, each containing 'id' and 'url' keys.
 * @return array Modified variants data with corrected URLs.
 */
function mc4wp_wpml_ecommerce_product_variants_permalink($variants)
{
    foreach ($variants as $key => $variant) {
        $language = apply_filters('wpml_element_language_code', null, [
            'element_id'   => $variant['id'],
            'element_type' => 'product',
        ]);

        $variants[$key]['url'] = apply_filters('wpml_permalink', $variant['url'], $language);
    }
    return $variants;
}
