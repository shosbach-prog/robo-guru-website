<?php
/**
 * Upload Markup Class file.
 *
 * @package sureforms-pro.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc\Fields;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Helper as Pro_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Upload Markup Class.
 *
 * @since 0.0.1
 */
class Upload_Markup extends Base {
	/**
	 * Maximum file size allowed for upload.
	 *
	 * @var int
	 * @since 0.0.1
	 */
	protected $file_size;

	/**
	 * Accepted attribute for the file input, formatted for HTML.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $accepted_attr;

	/**
	 * Whether multiple files can be uploaded.
	 *
	 * @var bool
	 * @since 0.0.1
	 */
	protected $multiple;

	/**
	 * Maximum number of files that can be uploaded.
	 *
	 * @var int
	 * @since 0.0.1
	 */
	protected $max_files;

	/**
	 * Initialize the properties based on block attributes.
	 *
	 * @param array<mixed> $attributes Block attributes.
	 * @since 0.0.1
	 */
	public function __construct( $attributes ) {
		$this->set_properties( $attributes );
		$this->set_error_msg( 'srfm_upload_block_required_text', $attributes );
		$this->set_input_label( __( 'Upload', 'sureforms-pro' ) );
		$this->slug      = 'upload';
		$this->file_size = $attributes['fileSizeLimit'] ?? '';

		$this->accepted_attr = isset( $attributes['allowedFormats'] ) && is_array( $attributes['allowedFormats'] ) ? implode(
			', ',
			array_map(
				static function( $item ) {
						// Remove spaces and dots from 'value' directly.
					$item['value'] = str_replace( [ ' ', '.' ], '', $item['value'] );
					return '.' . $item['value'];
				},
				$attributes['allowedFormats']
			)
		) : '';

		$this->multiple  = $attributes['multiple'] ?? false;
		$this->max_files = $attributes['maxFiles'] ?? 2;
		$this->set_markup_properties( '', true );
		$this->set_aria_described_by();
	}

	/**
	 * Render HTML markup of a block.
	 * This function renders the markup of the file upload block.
	 *
	 *  @return string|bool
	 */
	public function markup() {
		$pro_svg = Pro_Helper::fetch_pro_svg( 'upload', 'srfm-' . esc_attr( $this->slug ) . '-icon', 'aria-hidden="true"' );
		ob_start();
		?>
			<div data-block-id="<?php echo esc_attr( $this->block_id ); ?>" class="srfm-block-single srfm-block srfm-<?php echo esc_attr( $this->slug ); ?>-block srf-<?php echo esc_attr( $this->slug ); ?>-<?php echo esc_attr( $this->block_id ); ?>-block<?php echo esc_attr( $this->block_width ); ?><?php echo esc_attr( $this->classname ); ?> <?php echo esc_attr( $this->conditional_class ); ?>">
				<?php echo wp_kses_post( $this->label_markup ); ?>
				<?php echo wp_kses_post( $this->help_markup ); ?>
				<div class="srfm-block-wrap">
					<?php echo $pro_svg; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ignored to render svg ?>
					<div class="srfm-<?php echo esc_attr( $this->slug ); ?>-wrap">
						<input class="srfm-<?php echo esc_attr( $this->slug ); ?>-size" value="<?php echo esc_attr( Helper::get_string_value( $this->file_size ) ); ?>" type="hidden" />
						<label class="srfm-classic-<?php echo esc_attr( $this->slug ); ?>-label">
							<?php esc_html_e( 'Click to upload or drag and drop', 'sureforms-pro' ); ?>
						</label>
						<input id="srfm-<?php echo esc_attr( $this->slug ); ?>-<?php echo esc_attr( $this->block_id ); ?>" class="srfm-input-<?php echo esc_attr( $this->slug ); ?>" name="srfm-<?php echo esc_attr( $this->slug ); ?>-<?php echo esc_attr( $this->block_id ); ?><?php echo esc_attr( $this->field_name ); ?>[]" type="file" <?php echo $this->multiple ? esc_attr( 'multiple=' . $this->max_files ) : ''; ?> <?php echo ! empty( $this->aria_described_by ) ? "aria-describedby='" . esc_attr( trim( $this->aria_described_by ) ) . "'" : ''; ?> data-required="<?php echo esc_attr( $this->data_require_attr ); ?>" aria-required="<?php echo esc_attr( $this->data_require_attr ); ?>" accept="<?php echo esc_attr( $this->accepted_attr ); ?>">
					</div>
				</div>
				<div class="srfm-<?php echo esc_attr( $this->slug ); ?>-data"></div>
				<div class="srfm-error-wrap"><?php echo wp_kses_post( $this->error_msg_markup ); ?></div>
			</div>
		<?php
		$markup = ob_get_clean();

		return apply_filters(
			'srfm_block_field_markup',
			$markup,
			[
				'slug'       => $this->slug,
				'is_editing' => $this->is_editing,
				'field_name' => $this->field_name,
				'attributes' => $this->attributes,
			]
		);
	}
}
