<?php
/**
 * Field Validation Class file.
 *
 * Handles all field validation for SureForms Pro.
 *
 * @package SureForms Pro
 * @since 1.12.2
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM\Inc\Helper;
use SRFM\Inc\Translatable;
use SRFM_Pro\Inc\Helper as Pro_Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Field Validation Class
 */
class Field_Validation {
	use Get_Instance;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'srfm_field_validation_data', [ $this, 'prepare_field_validation_data' ], 10, 1 );
		add_filter( 'srfm_block_config', [ $this, 'add_block_config' ], 10, 1 );
		add_filter( 'srfm_validate_form_data', [ $this, 'validate_field_data' ], 10, 1 );
		add_filter( 'srfm_block_config_name_with_id', [ $this, 'validate_field_data_for_pro' ], 10, 2 );
	}

	/**
	 * Normalize field names for validation.
	 *
	 * This function normalizes the field names used in validation to ensure consistency
	 * between the configuration and submitted data. Specifically, it handles the case
	 * where the date-picker block name differs between the configuration and the submitted data.
	 *
	 * @param string $name_with_id The original name with ID from the configuration.
	 * @param array  $block        The block data array.
	 * @return string The normalized name with ID for validation.
	 * @since 2.3.0
	 */
	public function validate_field_data_for_pro( $name_with_id, $block ) {
		$normalized_config_name = $name_with_id;
		if ( 'srfm/date-picker' === $block['blockName'] ) {
			$normalized_config_name = str_replace( 'srfm-date-picker', 'srfm-datepicker', $name_with_id );
		}
		return $normalized_config_name;
	}

	/**
	 * Prepare field validation data by merging uploaded files into form data.
	 *
	 * This function checks if there are any uploaded files in the $_FILES superglobal.
	 * If so, it merges the $_FILES array into the provided $form_data array.
	 * This ensures that file upload fields are included in the validation process.
	 *
	 * Example:
	 * - If $form_data = [ 'field1' => 'value1' ] and $_FILES = [ 'upload1' => [ ... ] ],
	 *   the result will be [ 'field1' => 'value1', 'upload1' => [ ... ] ].
	 *
	 * @param array $form_data The submitted form data.
	 * @return array The merged form data including uploaded files.
	 */
	public function prepare_field_validation_data( $form_data ) {
		// If there are uploaded files, merge them into the form data.
		// Example: $_FILES contains file fields, which are added to $form_data for validation.
		if ( ! empty( $_FILES ) ) {
			$form_data = array_merge( $form_data, $_FILES );
		}

		// Return the merged form data, now including any uploaded files.
		return $form_data;
	}

	/**
	 * Process and add configuration for form blocks.
	 *
	 * Processes block configuration, specifically handling upload field blocks.
	 * Validates and extracts relevant attributes from the block and returns a processed
	 * configuration array.
	 *
	 * @param array $block {
	 *     Block data array.
	 *     @type array $block {
	 *         The actual block data.
	 *         @type string $blockName The name of the block.
	 *         @type array  $attrs     Block attributes.
	 *     }
	 * }
	 * @return array|null Returns processed block config array or null if not processed
	 * @since 1.12.2
	 */
	public function add_block_config( $block ) {
		if ( ! isset( $block['block'] ) || ! is_array( $block['block'] ) ) {
			return null;
		}

		$block = $block['block'];

		if ( ! isset( $block['blockName'] ) || ! isset( $block['attrs'] ) ) {
			return null;
		}

		$block_name  = sanitize_text_field( $block['blockName'] );
		$block_attrs = $block['attrs'];

		$processed_value = [];

		// Example: If the block is an upload field, extract and validate its configuration.
		if ( 'srfm/upload' === $block_name ) {
			// Extract and validate upload field configuration attributes.
			$is_required     = isset( $block_attrs['required'] ) ? (bool) $block_attrs['required'] : false;
			$is_multiple     = isset( $block_attrs['multiple'] ) ? (bool) $block_attrs['multiple'] : false;
			$file_size_limit = isset( $block_attrs['fileSizeLimit'] ) ? absint( $block_attrs['fileSizeLimit'] ) : 10;
			$allowed_formats = isset( $block_attrs['allowedFormats'] ) && is_array( $block_attrs['allowedFormats'] )
				? $block_attrs['allowedFormats']
				: [];
			$max_files       = isset( $block_attrs['maxFiles'] ) ? absint( $block_attrs['maxFiles'] ) : 2;
			$slug            = isset( $block_attrs['slug'] ) ? sanitize_text_field( $block_attrs['slug'] ) : '';
			$block_id        = isset( $block_attrs['block_id'] ) ? sanitize_text_field( $block_attrs['block_id'] ) : '';

			// Example: $processed_value will contain all validated and sanitized attributes.
			$processed_value = [
				'blockName'      => $block_name,
				'required'       => $is_required,
				'multiple'       => $is_multiple,
				'fileSizeLimit'  => $file_size_limit,
				'allowedFormats' => $allowed_formats,
				'maxFiles'       => $max_files,
				'slug'           => $slug,
				'block_id'       => $block_id,
			];

			// Store isConditionalLogic flag to detect hidden fields during server-side validation.
			// This allows the validation logic to skip fields that are hidden by conditional logic.
			if ( array_key_exists( 'isConditionalLogic', $block_attrs ) ) {
				$processed_value['isConditionalLogic'] = (bool) $block_attrs['isConditionalLogic'];
			}
		}

		// Process date-picker field configuration.
		if ( 'srfm/date-picker' === $block_name ) {
			// Extract and validate date-picker field configuration attributes.
			$is_required        = isset( $block_attrs['required'] ) ? (bool) $block_attrs['required'] : false;
			$disable_past_dates = isset( $block_attrs['disablePastDates'] ) ? (bool) $block_attrs['disablePastDates'] : false;
			$date_format        = isset( $block_attrs['dateFormat'] ) ? sanitize_text_field( $block_attrs['dateFormat'] ) : 'mm/dd/yyyy';
			$slug               = isset( $block_attrs['slug'] ) ? sanitize_text_field( $block_attrs['slug'] ) : '';
			$block_id           = isset( $block_attrs['block_id'] ) ? sanitize_text_field( $block_attrs['block_id'] ) : '';

			// Store the date-picker configuration for server-side validation.
			$processed_value = [
				'blockName'        => $block_name,
				'required'         => $is_required,
				'disablePastDates' => $disable_past_dates,
				'dateFormat'       => $date_format,
				'slug'             => $slug,
				'block_id'         => $block_id,
			];

			// Store isConditionalLogic flag to detect hidden fields during server-side validation.
			if ( array_key_exists( 'isConditionalLogic', $block_attrs ) ) {
				$processed_value['isConditionalLogic'] = (bool) $block_attrs['isConditionalLogic'];
			}
		}

		// Example: If $processed_value is not empty, return it wrapped in 'processed_value' key.
		if ( ! empty( $processed_value ) ) {
			return [ 'processed_value' => $processed_value ];
		}

		return null;
	}

	/**
	 * Validate upload field data for SureForms.
	 *
	 * This function validates the uploaded file(s) for a given upload field, checking:
	 * - Required field presence (e.g., if a file is required but not uploaded, validation fails)
	 * - Allowed file types (e.g., only jpg, png, pdf, etc. are accepted; 'exe' would be rejected)
	 * - Maximum number of files (e.g., if maxFiles is 2, uploading 3 files will fail)
	 * - Maximum file size (e.g., if fileSizeLimit is 10MB, a 12MB file will fail)
	 *
	 * @param array<mixed> $field_data Field data.
	 * @return array|void
	 */
	public function validate_field_data( $field_data ) {
		// Validate input structure.
		// Example: $field_data must be an array and contain required keys.
		if (
			! is_array( $field_data ) ||
			! isset( $field_data['field_key'], $field_data['field_value'], $field_data['form_config'], $field_data['block_id'], $field_data['name_with_id'], $field_data['field_name'], $field_data['block_slug'] )
		) {
			return $field_data;
		}

		$field_value  = $field_data['field_value'];
		$block_id     = $field_data['block_id'];
		$form_config  = $field_data['form_config'];
		$name_with_id = $field_data['name_with_id'];
		$field_name   = $field_data['field_name'];
		$block_slug   = $field_data['block_slug'];

		// Process upload fields.
		// Example: Only proceed if $field_name is 'srfm-upload'.
		if ( 'srfm-upload' === $field_name ) {
			return $this->validate_upload_field( $field_data, $field_value, $block_id, $form_config, $name_with_id, $block_slug );
		}

		// Process date-picker fields.
		// Example: Only proceed if $field_name is 'srfm-datepicker'.
		if ( 'srfm-datepicker' === $field_name ) {
			return $this->validate_date_picker_field( $field_data, $field_value, $block_id, $form_config, $name_with_id, $block_slug );
		}

		return $field_data;
	}

	/**
	 * Validate upload field data.
	 *
	 * @param array<mixed> $field_data   Field data.
	 * @param mixed        $field_value  Field value.
	 * @param string       $block_id     Block ID.
	 * @param array<mixed> $form_config  Form configuration.
	 * @param string       $name_with_id Name with ID.
	 * @param string       $block_slug   Block slug.
	 * @return array
	 * @since 2.3.0
	 */
	private function validate_upload_field( $field_data, $field_value, $block_id, $form_config, $name_with_id, $block_slug ) {

		if ( ! isset( $form_config[ $block_id ] ) ) {
			return $field_data;
		}

		// Example: $form_config[$block_id] should contain config for this field.
		$get_form_config = $form_config[ $block_id ];

		// Validate that config is an array.
		if ( ! is_array( $get_form_config ) ) {
			return $field_data;
		}

		// Validate that field_value is an array (file upload data).
		if ( ! is_array( $field_value ) ) {
			return $field_data;
		}

		// Example: Config must have 'slug' and 'name_with_id' keys.
		if ( ! isset( $get_form_config['slug'] ) || ! isset( $get_form_config['name_with_id'] ) ) {
			return $field_data;
		}

		$return_data = [
			'validated' => false,
			'error'     => __( 'Field is not valid.', 'sureforms-pro' ),
		];

		// Verify the slug.
		if ( $get_form_config['slug'] !== $block_slug ) {
			return $return_data;
		}

		// "name_with_id" should be same as $name_with_id.
		// Example: If config's name_with_id is 'upload-123', $name_with_id must also be 'upload-123'.
		if ( $get_form_config['name_with_id'] !== $name_with_id ) {
			return $return_data;
		}

		$is_required     = ! empty( $get_form_config['required'] );
		$is_multiple     = ! empty( $get_form_config['multiple'] );
		$file_size_limit = isset( $get_form_config['fileSizeLimit'] ) ? absint( $get_form_config['fileSizeLimit'] ) : 10;
		$file_size_limit = $file_size_limit * 1024 * 1024; // Convert MB to bytes. Example: 10 => 10485760 bytes.
		$allowed_formats = isset( $get_form_config['allowedFormats'] ) && is_array( $get_form_config['allowedFormats'] ) && ! empty( $get_form_config['allowedFormats'] )
			? $get_form_config['allowedFormats']
			: [
				[
					'value' => 'jpg',
					'label' => 'jpg',
				],
				[
					'value' => 'jpeg',
					'label' => 'jpeg',
				],
				[
					'value' => 'gif',
					'label' => 'gif',
				],
				[
					'value' => 'png',
					'label' => 'png',
				],
				[
					'value' => 'pdf',
					'label' => 'pdf',
				],
			];

		$max_files = isset( $get_form_config['maxFiles'] ) ? absint( $get_form_config['maxFiles'] ) : 1;

		// Check for required field.
		// Example: If required and no file uploaded, return error.
		if ( $is_required && empty( $get_form_config['isConditionalLogic'] ) ) {
			if ( ! isset( $field_value['name'] ) || ! is_array( $field_value['name'] ) || empty( $field_value['name'][0] ) ) {
				return [
					'validated' => false,
					'error'     => Helper::get_common_err_msg()['required'],
				];
			}
		}

		// Validate file types.
		$allowed_file_types = Pro_Helper::get_normalized_file_types();

		// Extract allowed extensions from the form configuration.
		$allowed_extensions = [];
		foreach ( $allowed_formats as $format ) {
			if ( is_array( $format ) && isset( $format['value'] ) ) {
				$allowed_extensions[] = strtolower( $format['value'] );
			} elseif ( is_string( $format ) ) {
				$allowed_extensions[] = strtolower( $format );
			}
		}

		// Filter MIME types based on allowed extensions from form config.
		$allowed_mimes = [];
		foreach ( $allowed_extensions as $ext ) {
			if ( isset( $allowed_file_types[ $ext ] ) && is_array( $allowed_file_types[ $ext ] ) ) {
				$allowed_mimes = array_merge( $allowed_mimes, $allowed_file_types[ $ext ] );
			}
		}

		// Validate file types using MIME type checking.
		if ( ! empty( $field_value['type'] ) && is_array( $field_value['type'] ) ) {
			foreach ( $field_value['type'] as $file_type ) {
				// Skip if no file uploaded.
				if ( empty( $file_type ) ) {
					continue;
				}

				// Check if the file type is allowed by validating against MIME types.
				if ( ! is_string( $file_type ) || ! in_array( $file_type, $allowed_mimes, true ) ) {
					return [
						'validated' => false,
						'error'     => Translatable::dynamic_validation_messages()['srfm_file_type_not_allowed'],
					];
				}
			}
		}

		// Validate multiple files.
		// Example: If not multiple and more than one file uploaded, return error.
		if (
			isset( $field_value['name'] ) &&
			is_array( $field_value['name'] ) &&
			! $is_multiple &&
			count( $field_value['name'] ) > 1
		) {
			return [
				'validated' => false,
				'error'     => sprintf( Translatable::dynamic_validation_messages()['srfm_file_upload_limit'], 1 ),
			];
		}

		// Validate max files.
		// Example: If maxFiles is 2 and 3 files uploaded, return error.
		if (
			isset( $field_value['name'] ) &&
			is_array( $field_value['name'] ) &&
			$max_files < count( $field_value['name'] )
		) {
			return [
				'validated' => false,
				'error'     => sprintf( Translatable::dynamic_validation_messages()['srfm_file_upload_limit'], $max_files ),
			];
		}

		// Validate file sizes.
		// Example: If fileSizeLimit is 10MB and a file is 12MB, return error.
		if (
			! empty( $field_value['size'] ) &&
			is_array( $field_value['size'] )
		) {
			foreach ( $field_value['size'] as $file_size ) {
				if ( $file_size_limit < $file_size ) {
					$convert_to_mb = $file_size / 1024 / 1024;
					return [
						'validated' => false,
						'error'     => sprintf( Translatable::dynamic_validation_messages()['srfm_file_size_exceed'], $convert_to_mb ),
					];
				}
			}
		}

		return [
			'validated' => true,
			'error'     => '',
		];
	}

	/**
	 * Validate date-picker field data.
	 *
	 * This function validates date-picker fields, specifically checking if past dates
	 * are submitted when the "Disable Past Dates" setting is enabled.
	 *
	 * @param array<mixed> $field_data   Field data.
	 * @param mixed        $field_value  Field value (the submitted date string).
	 * @param string       $block_id     Block ID.
	 * @param array<mixed> $form_config  Form configuration.
	 * @param string       $name_with_id Name with ID.
	 * @param string       $block_slug   Block slug.
	 * @return array Validation result with 'validated' and 'error' keys.
	 * @since 2.3.0
	 */
	private function validate_date_picker_field( $field_data, $field_value, $block_id, $form_config, $name_with_id, $block_slug ) {
		// Validate that the block config exists.
		if ( ! isset( $form_config[ $block_id ] ) ) {
			return $field_data;
		}

		$get_form_config = $form_config[ $block_id ];

		// Validate that config is an array.
		if ( ! is_array( $get_form_config ) ) {
			return $field_data;
		}

		// Validate config structure.
		if ( ! isset( $get_form_config['slug'] ) || ! isset( $get_form_config['name_with_id'] ) ) {
			return $field_data;
		}

		$return_data = [
			'validated' => false,
			'error'     => __( 'Field is not valid.', 'sureforms-pro' ),
		];

		// Verify the slug matches.
		if ( $get_form_config['slug'] !== $block_slug ) {
			return $return_data;
		}

		// Verify name_with_id matches.
		if ( $get_form_config['name_with_id'] !== $name_with_id ) {
			return $return_data;
		}

		// Check if "Disable Past Dates" is enabled.
		$disable_past_dates = ! empty( $get_form_config['disablePastDates'] );

		// If disable past dates is not enabled, skip validation (field is valid).
		if ( ! $disable_past_dates ) {
			return [
				'validated' => true,
				'error'     => '',
			];
		}

		// If the field is hidden by conditional logic, skip validation.
		if ( ! empty( $get_form_config['isConditionalLogic'] ) ) {
			return [
				'validated' => true,
				'error'     => '',
			];
		}

		// If field value is empty, it's valid (required validation is handled separately).
		if ( empty( $field_value ) || ! is_string( $field_value ) ) {
			// If the field is required and empty, this will be caught by the required field validation.
			// Here we only validate the "past date" constraint.
			return [
				'validated' => true,
				'error'     => '',
			];
		}

		// Get the date format from configuration (defaults to mm/dd/yyyy for backward compatibility).
		$date_format = $get_form_config['dateFormat'] ?? 'mm/dd/yyyy';

		// Map the frontend date format to PHP DateTime format.
		$format_map = [
			'mm/dd/yyyy' => 'm/d/Y',
			'dd/mm/yyyy' => 'd/m/Y',
		];

		$php_date_format = isset( $format_map[ strtolower( $date_format ) ] )
			? $format_map[ strtolower( $date_format ) ]
			: 'm/d/Y';

		// Create DateTime object from the submitted value using the detected format.
		$date_obj = \DateTime::createFromFormat( $php_date_format, $field_value );

		// Validate that the date was parsed correctly and matches the expected format.
		// This also catches invalid dates like 31/02/2024.
		if ( false === $date_obj || $date_obj->format( $php_date_format ) !== $field_value ) {
			return [
				'validated' => false,
				'error'     => __( 'Invalid date format.', 'sureforms-pro' ),
			];
		}

		// Get the timestamp from the parsed date, normalized to midnight.
		$submitted_date_timestamp = $date_obj->setTime( 0, 0, 0 )->getTimestamp();

		// Get today's date in WordPress timezone, normalized to midnight.
		$today = current_datetime()->setTime( 0, 0, 0 )->getTimestamp();

		// Check if the submitted date is in the past.
		if ( $submitted_date_timestamp < $today ) {
			return [
				'validated' => false,
				'error'     => __( 'Past dates are not allowed. Please select today or a future date.', 'sureforms-pro' ),
			];
		}

		// Date is valid (today or future).
		return [
			'validated' => true,
			'error'     => '',
		];
	}
}
