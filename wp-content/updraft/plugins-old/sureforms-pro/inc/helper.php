<?php
/**
 * Sureforms Submit Class file.
 *
 * @package sureforms-pro.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc;

use SRFM\Inc\Helper as SRFM_Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Helper Helper Class.
 *
 * @since 0.0.1
 */
class Helper {
	use Get_Instance;

	/**
	 * Sureforms SVGs.
	 *
	 * @var mixed srfm_svgs
	 */
	private static $srfm_pro_svgs = null;

	/**
	 * Get an SVG Icon
	 *
	 * @since 0.0.1
	 * @param string $icon the icon name.
	 * @param string $class if the baseline class should be added.
	 * @param string $html Custom attributes inside svg wrapper.
	 * @param string $wrapper_tag SVG icon wrapper html tag type. Default is span.
	 * @return string
	 */
	public static function fetch_pro_svg( $icon = '', $class = '', $html = '', $wrapper_tag = 'span' ) {

		$output = sprintf(
			'<%1$s class="%2$s" %3$s>',
			esc_attr( $wrapper_tag ),
			! empty( $class ) ? esc_attr( "srfm-icon {$class}" ) : 'srfm-icon',
			$html
		);

		if ( ! self::$srfm_pro_svgs ) {
			ob_start();

			include_once SRFM_PRO_DIR . 'assets/svg/svgs.json'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingNonPHPFile.IncludingNonPHPFile -- Required to get svg.json.
			// phpcs:ignore /** @phpstan-ignore-next-line */
			self::$srfm_pro_svgs = json_decode( ob_get_clean(), true );
			self::$srfm_pro_svgs = apply_filters( 'srfm_pro_svg_icons', self::$srfm_pro_svgs );
		}

			$output .= self::$srfm_pro_svgs[ $icon ] ?? '';
			$output .= "</{$wrapper_tag}>";

			return $output;
	}

	/**
	 * Get an SVG Icon
	 *
	 * @since 0.0.1
	 * @param string $icon the icon name.
	 * @return string
	 */
	public static function get_pro_icon( $icon = '' ) {
		if ( ! self::$srfm_pro_svgs ) {
			$file_path = SRFM_PRO_DIR . 'assets/svg/svgs.json';

			if ( file_exists( $file_path ) ) {
				$file_contents = file_get_contents( $file_path ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

				if ( ! empty( $file_contents ) ) {
					self::$srfm_pro_svgs = json_decode( $file_contents, true );
					self::$srfm_pro_svgs = apply_filters( 'srfm_pro_svg_icons', self::$srfm_pro_svgs );
				}
			}
		}

		return self::$srfm_pro_svgs[ $icon ] ?? '';
	}

	/**
	 * Print sanitized SVG Icon.
	 *
	 * @since 1.4.1
	 * @param string $icon the icon name.
	 * @return void
	 */
	public static function print_pro_icon( $icon = '' ) {
		$allowed_svg_attrs = [
			'svg'      => [
				'width'   => true,
				'height'  => true,
				'fill'    => true,
				'viewbox' => true,
				'xmlns'   => true,
			],
			'g'        => [
				'clip-path' => true,
			],
			'defs'     => [],
			'clipPath' => [
				'id' => true,
			],
			'path'     => [
				'd'               => true,
				'opacity'         => true,
				'class'           => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
			],
			'rect'     => [
				'width'  => true,
				'height' => true,
				'fill'   => true,
			],
		];

		echo wp_kses( self::get_pro_icon( $icon ), $allowed_svg_attrs );
	}

	/**
	 * Registers script translations for a specific handle.
	 *
	 * This function sets the script translations for a given script handle, allowing
	 * localization of JavaScript strings using the specified text domain and path.
	 *
	 * @param string $handle The script handle to apply translations to.
	 * @param string $domain Optional. The text domain for translations. Default is 'sureforms'.
	 * @param string $path   Optional. The path to the translation files. Default is the 'languages' folder in the SureForms directory.
	 *
	 * @since 1.0.5
	 * @return void
	 */
	public static function register_script_translations( $handle, $domain = 'sureforms-pro', $path = SRFM_PRO_DIR . 'languages' ) {
		wp_set_script_translations( $handle, $domain, $path );
	}

	/**
	 * Return the string with hsl color for the given hex color.
	 *
	 * @param string    $hex_color The hex color.
	 * @param int|float $alpha The alpha value.
	 *
	 * @since 1.6.3
	 * @return string The hsl color string.
	 */
	public static function get_hsl_notation_from_hex( $hex_color, $alpha = 1 ) {
		return 'hsl( from ' . $hex_color . ' h s l / ' . $alpha . ')';
	}

	/**
	 * Return the classes based on normal and hover state to add to the submit button.
	 *
	 * @param string $background_type The background type of the button's normal state.
	 * @param string $hover_type The backround type of the button's hover state. Default 'normal'.
	 *
	 * @since 1.6.3
	 * @return string The classes to add to the button.
	 */
	public static function get_button_background_classes( $background_type, $hover_type ) {
		$background_type_class = $background_type ? 'srfm-btn-bg-' . $background_type : '';
		$hover_type_class      = $hover_type ? 'srfm-btn-bg-hover-' . $hover_type : '';

		return SRFM_Helper::join_strings( [ $background_type_class, $hover_type_class ] );
	}

	/**
	 * Add CSS variables to the form.
	 *
	 * @param array $css_variables The CSS variables to add.
	 * @since 1.6.3
	 * @return void
	 */
	public static function add_css_variables( $css_variables ) {
		if ( empty( $css_variables ) || ! is_array( $css_variables ) ) {
			return;
		}
		foreach ( $css_variables as $key => $value ) {
			if ( ! empty( $value ) ) {
				echo esc_html( SRFM_Helper::get_string_value( $key ) ) . ': ' . esc_html( SRFM_Helper::get_string_value( $value ) ) . ';';
			}
		}
	}

	/**
	 * Get the WordPress file types.
	 *
	 * @param bool $should_return_file_type Whether to return an associative array. Default is false.
	 * Example: For True ['jpg' => 'image/jpeg', 'png' => 'image/png'].
	 * If false, it returns an array of strings like ['image/jpeg', 'image/png'].
	 *
	 * @since 1.8.0
	 * @return array<string,mixed> An associative array representing the file types.
	 */
	public static function get_wp_file_types( $should_return_file_type = false ) {

		/**
		 * These file types are excluded from the allowed file types.
		 * As these are not allowed in the wp_handle_upload function.
		 */
		$excluded_file_types = [
			'js'  => 'application/javascript', // Exclude JavaScript files.
			'css' => 'text/css', // Exclude CSS files.
			'rar' => 'application/rar', // Exclude RAR files.
		];

		/**
		 * Extra MIME types to support in addition to WP defaults.
		 * Key = file extension, Value = array of additional mime types.
		 */
		$extra_mimes = [
			'zip' => [ 'application/x-zip-compressed' ],
		];

		$formats = [];
		$mimes   = get_allowed_mime_types();
		$maxsize = wp_max_upload_size() / 1048576;

		// Merge extra MIME types dynamically.
		foreach ( $extra_mimes as $ext => $aliases ) {
			if ( isset( $mimes[ $ext ] ) ) {
				// Append aliases if not already present.
				$current = explode( '|', $mimes[ $ext ] );
				foreach ( $aliases as $alias ) {
					if ( ! in_array( $alias, $current, true ) ) {
						$current[] = $alias;
					}
				}
				$mimes[ $ext ] = implode( '|', $current );
			} else {
				// Add new extension with all aliases.
				$mimes[ $ext ] = implode( '|', $aliases );
			}
		}

		if ( ! empty( $mimes ) ) {
			foreach ( $mimes as $type => $mime ) {
				$multiple = explode( '|', $type );
				foreach ( $multiple as $file_type ) {

					// Skip excluded file types.
					if ( isset( $excluded_file_types[ $file_type ] ) ) {
						continue;
					}

					if ( $should_return_file_type ) {
						$formats[ $file_type ] = $mime;
					} else {
						$formats[] = $mime;
					}
				}
			}
		}

		return [
			'formats' => $formats,
			'maxsize' => $maxsize,
		];
	}

	/**
	 * Normalize WordPress file types into extension => [ mime1, mime2, ... ] format.
	 *
	 * @since 1.12.1
	 * @return array<string, array<string>>
	 */
	public static function get_normalized_file_types() {
		$file_types = self::get_wp_file_types( true );
		$normalized = [];

		if ( isset( $file_types['formats'] ) && is_array( $file_types['formats'] ) ) {
			foreach ( $file_types['formats'] as $ext => $mimes ) {
				if ( is_string( $mimes ) ) {
					$mime_list                        = array_map( 'trim', explode( '|', $mimes ) );
					$normalized[ strtolower( $ext ) ] = $mime_list;
				} elseif ( is_array( $mimes ) ) {
					$normalized[ strtolower( $ext ) ] = array_map( 'trim', $mimes );
				}
			}
		}

		return $normalized;
	}

	/**
	 * Delete uploaded file from a specific subdirectory.
	 *
	 * @param string $file_url The file URL to delete.
	 * @param string $subdir The subdirectory to delete the file from.
	 *
	 * @since 1.8.0
	 * @return bool
	 */
	public static function delete_upload_file_from_subdir( $file_url, $subdir = 'sureforms/' ) {
		// Decode the file URL.
		$file_url = urldecode( $file_url );

		// Check if the file URL is empty.
		if ( empty( $file_url ) || ! is_string( $file_url ) ) {
			return false;
		}

		// Normalize and sanitize the subdirectory.
		$subdir = trailingslashit( sanitize_text_field( $subdir ) );

		// Get the base upload directory.
		$upload_dir       = wp_upload_dir();
		$base_upload_path = trailingslashit( $upload_dir['basedir'] ) . $subdir;

		$filename       = basename( $file_url );
		$file_extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

		// Validate the file extension.
		if ( empty( $file_extension ) || ! is_string( $file_extension ) ) {
			return false;
		}

		$allowed_file_types = self::get_normalized_file_types();

		if ( ! array_key_exists( $file_extension, $allowed_file_types ) ) {
			return false;
		}

		$file_path      = $base_upload_path . $filename;
		$real_file_path = realpath( $file_path );
		$real_base_path = realpath( $base_upload_path );

		// Security check: ensure file is inside the target subdir.
		if ( ! $real_file_path || ! $real_base_path || strpos( $real_file_path, $real_base_path ) !== 0 ) {
			return false;
		}

		// Delete if file exists.
		if ( file_exists( $real_file_path ) ) {
			return unlink( $real_file_path );
		}

		return false;
	}

	/**
	 * Check if a string is JSON.
	 *
	 * @param string $string String to check.
	 * @return bool True if the string is JSON, false otherwise.
	 * @since 1.13.0
	 */
	public static function is_json( $string ) {
		if ( ! is_string( $string ) || empty( trim( $string ) ) ) {
			return false;
		}

		$string = trim( $string );
		// Check if the string starts with { or [ and ends with } or ].
		if ( ( '{' === $string[0] && '}' === $string[ strlen( $string ) - 1 ] ) ||
			( '[' === $string[0] && ']' === $string[ strlen( $string ) - 1 ] ) ) {
			json_decode( $string );
			return json_last_error() === JSON_ERROR_NONE;
		}

		return false;
	}

	/**
	 * Check conditional logic for trigger
	 *
	 * @param array<string, mixed> $data The data containing conditional logic settings.
	 * @param array<string, mixed> $submission_data The submission data to check against the rules.
	 *
	 * @since 1.10.0
	 * @return bool True if conditions are met, false otherwise.
	 */
	public static function check_trigger_conditions( $data, $submission_data = [] ) {
		$trigger = true;

		if ( is_array( $data ) && ! isset( $data['conditionalLogic'] ) || ! is_array( $data['conditionalLogic'] ) ) {
			return $trigger; // If conditional logic is not set, return true.
		}

		$conditional_logic_status = isset( $data['conditionalLogic']['status'] ) && is_bool( $data['conditionalLogic']['status'] )
			? $data['conditionalLogic']['status']
			: false;

		$logic = isset( $data['conditionalLogic']['logic'] ) && is_string( $data['conditionalLogic']['logic'] )
			? $data['conditionalLogic']['logic']
			: '_AND_';

		$rules = isset( $data['conditionalLogic']['rules'] ) && is_array( $data['conditionalLogic']['rules'] )
			? $data['conditionalLogic']['rules']
			: [];

		if ( $conditional_logic_status
		&& ! empty( $logic ) &&
			count( $rules ) > 0
		) {
			return self::process_logic( $data['conditionalLogic'], $submission_data );
		}
		return $trigger;
	}

	/**
	 * Process conditional logic.
	 *
	 * @param array<string, mixed> $rules logical conditions.
	 * @param array<string, mixed> $submission_data The submission data to check against the rules.
	 *
	 * @since 1.10.0
	 * @return bool True if conditions are met, false otherwise.
	 */
	public static function process_logic( $rules, $submission_data = [] ) {
		// By default trigger is true.
		$trigger = true;

		// If rules are not set, return true.
		if ( ! is_array( $rules ) || ! isset( $rules['logic'] ) || ! isset( $rules['rules'] ) || ! is_array( $rules['rules'] ) ) {
			return $trigger; // If rules are not set, return true.
		}

		// Check conditions in the rules. if conditions are not empty then process the conditions.
		switch ( $rules['logic'] ) {
			case '_AND_':
				foreach ( $rules['rules'] as $rule ) {
					if ( ! self::process_condition( $rule, $submission_data ) ) {
						$trigger = false;
						break;
					}
				}
				break;
			case '_OR_':
				foreach ( $rules['rules'] as $rule ) {
					$result = self::process_condition( $rule, $submission_data );
					if ( true === $result ) {
						$trigger = true;
						break;
					}
					if ( false === $result ) {
						$trigger = false;
					}
				}
				break;
		}

		return $trigger;
	}

	/**
	 * Get the editor default styles.
	 * This is the default styles for the editors (tinymce, html editor ).
	 *
	 * @since 1.12.1
	 * @return string
	 */
	public static function editor_default_styles() {
		return 'body {
						font-family: \'figtree\', sans-serif;
						font-size: 14px;
						color: #111827;
						line-height: 1.2;
					}
					h1, h2, h3, h4, h5, h6 {
						margin: 10px 0;
						color: #111827;
					}
					h1 { font-size: 32px; }
					h2 { font-size: 28px; }
					h3 { font-size: 24px; }
					h4 { font-size: 20px; }
					h5 { font-size: 16px; }
					h6 { font-size: 14px; }
					p {
						margin: 10px 0;
					}
					ul, ol {
						padding-left: 24px;
						margin-bottom: 16px;
					}
					ul li, ol li {
						margin-bottom: 8px;
					}
					pre {
						white-space: pre-wrap;
						word-wrap: break-word;
						overflow-wrap: break-word;
					}
					code {
						font-family: \'figtree\', sans-serif;
						font-size: 14px;
						color: #111827;
						background-color: #f3f4f6;
					}
					img.alignleft {
						float: left;
						margin: 0 15px 15px 0;
					}
					img.alignright {
						float: right;
						margin: 0 0 15px 15px;
					}
					img.aligncenter {
						display: block;
						margin-left: auto;
						margin-right: auto;
					}';
	}

	/**
	 * Process conditional logic.
	 *
	 * @param array<string, mixed> $rule logical condition.
	 * @param array<string, mixed> $submission_data The submission data to check against the rules.
	 *
	 * @since 1.10.0
	 * @return string|bool Returns 'field_not_found' if field is not found,
	 */
	public static function process_condition( $rule, $submission_data = [] ) {
		if ( ! is_array( $rule ) || ! isset( $rule['field'] ) ) {
			return 'field_not_found';
		}

		// Check if field has ":" then split and get the field and value. this is in case of form:field present. in that time we need to get only field.
		$rule['field'] = is_string( $rule['field'] ) && strpos( $rule['field'], ':' ) ? explode( ':', $rule['field'] )[1] : $rule['field'];

		// From the field label remove {, }.
		$rule['field'] = str_replace( [ '{', '}' ], '', SRFM_Helper::get_string_value( $rule['field'] ) );

		if ( ! isset( $submission_data[ $rule['field'] ] ) ) {
			return 'field_not_found';
		}

		$value = SRFM_Helper::get_string_value( $rule['value'] );
		$field = SRFM_Helper::get_string_value( $submission_data[ $rule['field'] ] );

		switch ( $rule['operator'] ) {
			case '_EQUAL_':
				$trigger = (
					$value === $field
				);
				break;

			case '_NOT_EQUAL_':
				$trigger = (
					$value !== $field
				);
				break;

			case '_GREATER_':
				$trigger = (
					$value < $field
				);
				break;

			case '_GREATER_OR_EQUAL_':
				$trigger = (
					$value <= $field
				);
				break;

			case '_LESSER_':
				$trigger = (
					$value > $field
				);
				break;

			case '_LESSER_OR_EQUAL_':
				$trigger = (
					$value >= $field
				);
				break;

			case '_STARTS_WITH_':
				$trigger = 0 === substr_compare(
					$field,
					$value,
					0,
					strlen( $value )
				);
				break;

			case '_ENDS_WITH_':
				$trigger = (
					0 === substr_compare(
						$field,
						$value,
						-strlen( $value )
					)
				);
				break;

			case '_CONTAINS_':
				$trigger = false !== strpos(
					$field,
					$value
				);
				break;
			case '_NOT_CONTAINS_':
				$trigger = false === strpos(
					$field,
					$value
				);
				break;

			case '_REGEX_':
				$trigger = 1 === preg_match(
					$value,
					$field
				);
				break;
			default:
				$trigger = true;
				break;
		}

		return $trigger;
	}

	/**
	 * Extracts the field type from the dynamic field key (or field slug) that starts with "srfm".
	 *
	 * This function is used to extract the field type prefix (e.g., "srfm-email" from "srfm-email-xxxx-lbl-email")
	 * from a dynamic field key. It checks if the key contains the "-lbl-" delimiter and if the prefix starts with "srfm".
	 * If so, it returns the prefix in the format "srfm-<type>", otherwise returns an empty string.
	 *
	 * @param string $field_key Dynamic field key.
	 * @since 1.12.2
	 * @return string Extracted field type (e.g., "srfm-email"), or empty string if not found.
	 */
	public static function get_field_type_from_key_with_srfm( $field_key ) {

		if ( false === strpos( $field_key, '-lbl-' ) ) {
			return '';
		}

		$parts = explode( '-', $field_key );

		// Check if the key starts with "srfm" and has a valid type part.
		// Example: "srfm-email-xxxx-lbl-email" => returns "srfm-email".
		if ( isset( $parts[0], $parts[1] ) && 'srfm' === $parts[0] && is_string( $parts[1] ) ) {
			return trim( $parts[0] . '-' . $parts[1] );
		}

		return '';
	}

	/**
	 * Sanitize conditional logic array.
	 *
	 * @param array $logic The conditionalLogic array.
	 * @return array Sanitized conditionalLogic array.
	 * @since 2.2.0
	 */
	public static function sanitize_conditional_logic( $logic ) {
		if ( ! is_array( $logic ) ) {
			return [
				'status' => false,
				'logic'  => '_OR_',
				'rules'  => [],
			];
		}

		$rules = [];

		if ( isset( $logic['rules'] ) && is_array( $logic['rules'] ) ) {
			foreach ( $logic['rules'] as $rule ) {
				if ( ! is_array( $rule ) ) {
					continue;
				}
				$rules[] = [
					'field'    => isset( $rule['field'] ) ? sanitize_text_field( $rule['field'] ) : '',
					'operator' => isset( $rule['operator'] ) ? sanitize_text_field( $rule['operator'] ) : '_EQUAL_',
					'value'    => isset( $rule['value'] ) ? sanitize_text_field( $rule['value'] ) : '',
				];
			}
		}

		return [
			'status' => isset( $logic['status'] ) ? (bool) $logic['status'] : false,
			'logic'  => isset( $logic['logic'] ) ? sanitize_text_field( $logic['logic'] ) : '_OR_',
			'rules'  => $rules,
		];
	}
}
