<?php

defined('ABSPATH') or exit;

/**
 * For compatibility with User Extra Fields fields of the type "radio" and "checkboxes"
 *
 * @link https://codecanyon.net/item/user-extra-fields/12949844
 */
add_filter('mc4wp_user_sync_get_user_field', function ($value, $meta_key, $user) {
    global $wpuef_option_model, $wpuef_user_model;

    if (! class_exists(WPUEF_Option::class) || ! class_exists(WPUEF_User::class)) {
        return $value;
    }

    if (
        substr($meta_key, 0, 10) !== 'wpuef_cid_'
        || false === $wpuef_option_model instanceof WPUEF_Option
        || false === $wpuef_user_model instanceof WPUEF_User
    ) {
        return $value;
    }

    $cid = substr($meta_key, 10);
    $extra_fields = $wpuef_option_model->get_option('json_fields_string');
    if (empty($extra_fields)) {
        return $value;
    }

    $extra_field = null;
    foreach ($extra_fields->fields as $field) {
        if ($field->cid === $cid) {
            $extra_field = $field;
            break;
        }
    }

    if ($extra_field === null) {
        return $value;
    }

    if (false === in_array($extra_field->field_type, ['dropdown', 'radio', 'checkboxes'], true)) {
        return $value;
    }

    $value = $wpuef_user_model->get_field($cid, $user->ID);
    $value_labels = [];
    foreach ($extra_field->field_options->options as $index => $extra_option) {
        if ($extra_field->field_type === 'checkboxes' && isset($value[$index])) {
            $value_labels[] = $extra_option->label;
        }

        if ($extra_field->field_type === 'radio' && $value == $index) {
            $value_labels[] = $extra_option->label;
            break;
        }

        if ($extra_field->field_type === 'dropdown' && $value !== '' && $value == $index) {
            $value_labels[] = $extra_option->label;
            break;
        }
    }

    return join(', ', $value_labels);
}, 10, 3);
