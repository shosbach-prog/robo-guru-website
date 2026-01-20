<?php
/**
 * Form Field Base Class.
 *
 * This file defines the base class for form fields in the SureForms package.
 *
 * @package sureforms-pro
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc\Fields;

use SRFM\Inc\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Field Base Class
 *
 * Defines the base class for form fields.
 *
 * @since 0.0.1
 */
class Base {
	/**
	 * Stores the attributes of the block.
	 *
	 * @var array<mixed> $attributes Block attributes.
	 * @since 1.3.0
	 */
	protected $attributes;

	/**
	 * Stores the block slug.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $block_slug;

	/**
	 * Indicates whether the field is required.
	 * This boolean variable stores the value of the 'required' attribute for the field.
	 *
	 * @var bool $required Value of the required attribute. Set to true if the field is required, otherwise false.
	 * @since 0.0.1
	 */
	protected $required;

	/**
	 * Represents the identifier of the block.
	 *
	 * @var string $block_id Unique identifier representing the block.
	 * @since 0.0.1
	 */
	protected $block_id;

	/**
	 * Represents a label used for an input field.
	 * The value of this variable specifies the text displayed as the label for the corresponding input field when rendered.
	 *
	 * @var string $label Label used for the input field.
	 * @since 0.0.1
	 */
	protected $label;

	/**
	 * Represents the width of a field.
	 *
	 * @var int|string $field_width Width for the field.
	 * @since 0.0.1
	 */
	protected $field_width;

	/**
	 * A string that provides help text or instructions.
	 *
	 * @var string $help
	 * @since 0.0.1
	 */
	protected $help;

	/**
	 * The minimum value for a field
	 * For the Date_Picker block it is a string and for rest of the blocks it is a number.
	 *
	 * @var int $min Minimum attribute.
	 * @since 0.0.1
	 */
	protected $min;

	/**
	 * The maximum value for a field
	 * For the Date_Picker block it is a string and for rest of the blocks it is a number.
	 *
	 * @var int $max Maximum attribute.
	 * @since 0.0.1
	 */
	protected $max;

	/**
	 * Validation error message for the fields.
	 *
	 * @var string $error_msg Input field validation error message.
	 * @since 0.0.1
	 */
	protected $error_msg;

	/**
	 * Represents the class name attribute.
	 *
	 * @var string $classname The value of the class name attribute.
	 * @since 0.0.1
	 */
	protected $classname;

	/**
	 * Dynamically sets the CSS class for block width based on the field width.
	 *
	 * @var string $block_width The CSS class for block width, dynamically generated from $this->field_width.
	 * @since 0.0.1
	 */
	protected $block_width;

	/**
	 * Indicates whether the attribute should be set to true or false.
	 *
	 * @var string $data_require_attr Value of the data-required attribute.
	 * @since 0.0.1
	 */
	protected $data_require_attr;

	/**
	 * Stores the default fallback value of the label for an input field if nothing is specified.
	 *
	 * @var string $input_label_fallback Default fallback value for the input label.
	 * @since 0.0.1
	 */
	protected $input_label_fallback;

	/**
	 * Stores the ID of the form.
	 *
	 * @var int $form_id Form ID.
	 * @since 0.0.1
	 */
	protected $form_id;

	/**
	 * Stores the slug.
	 *
	 * @var string $slug slug value.
	 * @since 0.0.1
	 */
	protected $slug;

	/**
	 * Stores the conditional class.
	 *
	 * @var string $conditional_class class name.
	 * @since 0.0.1
	 */
	protected $conditional_class;

	/**
	 * Stores the input label.
	 *
	 * @var string $input_label input label.
	 * @since 0.0.1
	 */
	protected $input_label;

	/**
	 * Stores the placeholder text.
	 *
	 * @var string $placeholder HTML field placeholder.
	 * @since 0.0.1
	 */
	protected $placeholder;

	/**
	 * Creates the placeholder attribute.
	 *
	 * @var string $placeholder_attr HTML field placeholder attribute.
	 * @since 0.0.1
	 */
	protected $placeholder_attr;

	/**
	 * Stores the field name.
	 *
	 * @var string $field_name HTML field name.
	 * @since 0.0.1
	 */
	protected $field_name;

	/**
	 * Unique slug for the slider and password field, it combines the slug, block ID, and input label.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $unique_slug;

	/**
	 * Stores the help text markup.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $help_markup;

	/**
	 * Stores the HTML label markup.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $label_markup;

	/**
	 * Stores the error message markup.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $error_msg_markup;

	/**
	 * Stores attribute for aria-describedby.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $aria_described_by;

	/**
	 * Default value for the field.
	 *
	 * @var string
	 * @since 1.3.0
	 */
	protected $default;

	/**
	 * Whether or not block is render in editing mode.
	 * If it is true, then block is currently rendered in edit entry.
	 *
	 * @var bool
	 * @since 1.3.0
	 */
	protected $is_editing = false;

	/**
	 * Currently rendered entry ID.
	 *
	 * @var int
	 * @since 1.3.0
	 */
	protected $entry_id = 0;

	/**
	 * Configuration array for the field.
	 *
	 * This associative array contains configuration settings for the field,
	 * which can be used for various JavaScript functionalities such as
	 * calculation, validation, and conditional logic (in future).
	 * The array data will be stored in the block field's "data-field-config"
	 * attribute as a JSON string.
	 *
	 * @var string $field_config Configuration settings for the field.
	 * @since 1.5.0
	 */
	protected $field_config;

	/**
	 * Render default markup.
	 *
	 * @since 0.0.1
	 * @return string
	 */
	public function markup() {
		return '';
	}

	/**
	 * Return the common block classes.
	 *
	 * @param array<mixed> $extra_classes Extra classes to be added to the field.
	 * @since 1.8.0
	 * @return string
	 */
	public function get_field_classes( $extra_classes = [] ) {
		$common_classes = [
			'srfm-block',
			'srfm-block-single',
			"srfm-{$this->slug}-block",
			$this->block_width,
			$this->classname,
			"srfm-slug-{$this->block_slug}",
			$this->conditional_class,
		];

		if ( ! empty( $extra_classes ) && is_array( $extra_classes ) ) {
			$common_classes = array_merge( $common_classes, $extra_classes );
		}

		return Helper::join_strings( $common_classes );
	}

	/**
	 * Sets the properties of class based on block attributes.
	 * This function will set variables from attributes.
	 *
	 * @param array<mixed> $attributes Block attributes.
	 * @since 0.0.1
	 * @return void
	 */
	protected function set_properties( $attributes ) {
		$default_classes = isset( $attributes['className'] ) ? ' ' . $attributes['className'] : '';
		$filter_classes  = apply_filters( 'srfm_field_classes', $default_classes, [ 'attributes' => $attributes ] );
		$field_config    = apply_filters( 'srfm_field_config', [], [ 'attributes' => $attributes ] );

		$this->field_config      = $field_config ? htmlspecialchars( Helper::get_string_value( wp_json_encode( $field_config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ), ENT_QUOTES, 'UTF-8' ) : '';
		$this->attributes        = $attributes;
		$this->block_slug        = $attributes['slug'] ?? '';
		$this->required          = $attributes['required'] ?? false;
		$this->block_id          = $attributes['block_id'] ?? '';
		$this->label             = $attributes['label'] ?? '';
		$this->field_width       = $attributes['fieldWidth'] ?? '';
		$this->help              = $attributes['help'] ?? '';
		$this->min               = $attributes['min'] ?? '';
		$this->max               = $attributes['max'] ?? '';
		$this->classname         = $filter_classes;
		$this->form_id           = $attributes['formId'] ?? '';
		$this->conditional_class = apply_filters( 'srfm_conditional_logic_classes', $this->form_id, $this->block_id );
		$this->placeholder       = $attributes['placeholder'] ?? '';
		$this->placeholder_attr  = $this->placeholder ? ' placeholder="' . esc_attr( $this->placeholder ) . '" ' : '';

		$this->block_width       = $this->field_width ? ' srfm-block-width-' . str_replace( '.', '-', $this->field_width ) : '';
		$this->data_require_attr = $this->required ? 'true' : 'false';

		$this->default    = $attributes['defaultValue'] ?? '';
		$this->is_editing = isset( $attributes['isEditing'] ) ? true : false;
		$this->entry_id   = $attributes['entryID'] ?? 0;
	}

	/**
	 * Sets the label of input field if available, otherwise default provided value is set.
	 * Invokes the set_field_name() function to set the field_name property.
	 *
	 * @param string $value The default fallback text.
	 * @since 0.0.1
	 * @return void
	 */
	protected function set_input_label( $value ) {
		$this->input_label_fallback = $this->label ? $this->label : $value;
		$this->input_label          = '-lbl-' . Helper::encrypt( $this->input_label_fallback );
		$this->set_field_name( $this->input_label );
	}

	/**
	 * Sets the error message for the block.
	 *
	 * @param string       $key meta key name.
	 * @param array<mixed> $attributes Block attributes, expected to contain 'errorMsg' key.
	 * @since 0.0.1
	 * @return void
	 */
	protected function set_error_msg( $key, $attributes ) {
		$this->error_msg = isset( $attributes['errorMsg'] ) && $attributes['errorMsg'] ? $attributes['errorMsg'] : Helper::get_default_dynamic_block_option( $key );
	}

	/**
	 * Sets the field name property.
	 *
	 * @param string $string Contains $input_label value or the $unique_slug based on the block.
	 * @since 0.0.1
	 * @return void
	 */
	protected function set_field_name( $string ) {
		$this->field_name = $string . '-' . $this->block_slug;
	}

	/**
	 * Sets the unique slug.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	protected function set_unique_slug() {
		$this->unique_slug = 'srfm-' . $this->slug . '-' . $this->block_id . $this->input_label;
	}

	/**
	 * Sets the markup properties used in rendering the HTML markup of blocks.
	 * The parameters are for generating the label and error message markup of blocks.
	 *
	 * @param string $input_label Optional. Additional label to be appended to the block ID.
	 * @param bool   $override Optional. Override for error markup. Default is false.
	 * @since 0.0.1
	 * @return void
	 */
	protected function set_markup_properties( $input_label = '', $override = false ) {
		$this->help_markup      = Helper::generate_common_form_markup( $this->form_id, 'help', '', '', $this->block_id, false, $this->help );
		$this->label_markup     = Helper::generate_common_form_markup( $this->form_id, 'label', $this->label, $this->slug, $this->block_id . $input_label, boolval( $this->required ) );
		$this->error_msg_markup = Helper::generate_common_form_markup( $this->form_id, 'error', '', '', $this->block_id, boolval( $this->required ), '', $this->error_msg, false, '', $override );
	}

	/**
	 * Setter for the aria-describedby attribute.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	protected function set_aria_described_by() {
		$this->aria_described_by .= ' srfm-error-' . $this->block_id;
		$this->aria_described_by .= ! empty( $this->help ) ? ' srfm-description-' . $this->block_id : '';
	}

	/**
	 * This function creates placeholder markup from label
	 * works when user selects option 'Use labels as placeholder'
	 *
	 * @param string $input_label label of block where functionality is required.
	 * @since 1.8.0
	 * @return void
	 */
	protected function set_label_as_placeholder( $input_label = '' ) {
		$this->placeholder_attr = '';
		$placeholder            = Helper::generate_common_form_markup( $this->form_id, 'placeholder', $input_label, $this->slug, $this->block_id . $input_label, boolval( $this->required ) );
		if ( ! empty( $placeholder ) ) {
			$this->label_markup     = '';
			$this->placeholder_attr = ' placeholder="' . esc_attr( $placeholder ) . '" ';
		} elseif ( '' !== $this->placeholder ) {
			$this->placeholder_attr = ' placeholder="' . esc_attr( $this->placeholder ) . '" ';
		}
	}
}
