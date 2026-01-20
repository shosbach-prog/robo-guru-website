<?php
/**
 * Rating Markup Class file.
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
 * Rating Markup Class.
 *
 * @since 0.0.1
 */
class Rating_Markup extends Base {
	/**
	 * Flag indicating whether to show numbers in the rating.
	 *
	 * @var bool|string
	 * @since 0.0.1
	 */
	protected $show_numbers;

	/**
	 * Shape of the icon used in the rating.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $icon_shape;

	/**
	 * Maximum value allowed for the rating.
	 *
	 * @var int|string
	 * @since 0.0.1
	 */
	protected $max_value;

	/**
	 * Size of Icon used in rating.
	 *
	 * @var int|string
	 * @since 0.0.1
	 */
	protected $icon_size;

	/**
	 * Text for rating.
	 *
	 * @var array<mixed>
	 * @since 0.0.1
	 */
	protected $rating_text;

	/**
	 * Text for thumbs.
	 *
	 * @var array<mixed>
	 * @since 0.0.1
	 */
	protected $thumbs_text;

	/**
	 * Default rating value.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $default_rating;

	/**
	 * Initialize the properties based on block attributes.
	 *
	 * @param array<mixed> $attributes Block attributes.
	 * @since 0.0.1
	 */
	public function __construct( $attributes ) {
		$this->set_properties( $attributes );
		$this->set_error_msg( 'srfm_rating_block_required_text', $attributes );
		$this->set_input_label( __( 'Rating', 'sureforms-pro' ) );
		$this->help           = $attributes['help'] ?? '';
		$this->show_numbers   = $attributes['showNumbers'] ?? '';
		$this->icon_shape     = $attributes['iconShape'] ?? '';
		$this->max_value      = 5;
		$this->rating_text    = $attributes['ratingText'] ?? [];
		$this->thumbs_text    = $attributes['thumbsText'] ?? [];
		$this->default_rating = ! empty( $attributes['defaultRating'] ) ? $attributes['defaultRating'] : '';
		$this->slug           = 'rating';
		$this->set_markup_properties();
		$this->set_aria_described_by();
	}

	/**
	 * Render HTML markup of a block.
	 * This function renders the markup of the rating block.
	 *
	 * @since 0.0.1
	 * @return string|bool
	 */
	public function markup() {
		$input_id     = "srfm-rating-{$this->block_id}";
		$input_name   = "{$input_id}{$this->field_name}";
		$tooltip_text = $this->rating_text;
		$icon_labels  = [];
		switch ( $this->icon_shape ) {
			case 'star':
				$icon_labels = array_map(
					// translators: %d represents the number of stars.
					static fn( $i ) => sprintf( _n( '%d star', '%d stars', $i, 'sureforms-pro' ), $i ),
					range( 1, $this->max_value )
				);
				break;
			case 'smiley':
				$icon_labels = [
					__( 'Poor Smiley', 'sureforms-pro' ),
					__( 'Fair Smiley', 'sureforms-pro' ),
					__( 'Good Smiley', 'sureforms-pro' ),
					__( 'Very Good Smiley', 'sureforms-pro' ),
					__( 'Excellent Smiley', 'sureforms-pro' ),
				];
				break;
			case 'thumb':
				$icon_labels     = [
					__( 'Thumbs Up', 'sureforms-pro' ),
					__( 'Thumbs Down', 'sureforms-pro' ),
				];
				$this->max_value = 2;
				$tooltip_text    = $this->thumbs_text;
				break;
		}

		$wrapper_class = "srfm-block-single srfm-block srfm-rating-block srf-rating-{$this->block_id}-block{$this->block_width}{$this->classname} {$this->conditional_class}";

		ob_start(); ?>
		<div data-block-id="<?php echo esc_attr( $this->block_id ); ?>" class="<?php echo esc_attr( $wrapper_class ); ?>">
			<?php echo wp_kses_post( $this->label_markup ); ?>
			<?php echo wp_kses_post( $this->help_markup ); ?>
			<div class="srfm-block-wrap">
				<input
					id="<?php echo esc_attr( $input_id ); ?>"
					type="hidden"
					class="srfm-input-common srfm-input-rating"
					name="<?php echo esc_attr( $input_name ); ?>"
					data-required="<?php echo esc_attr( $this->data_require_attr ); ?>"
					value="<?php echo esc_attr( $this->default_rating ); ?>"
				/>
				<ul
					data-icon-labels="<?php echo esc_attr( Helper::get_string_value( wp_json_encode( $icon_labels ) ) ); ?>"
					data-icon-type="<?php echo esc_attr( $this->icon_shape ); ?>">
					<?php
					for ( $i = 1; $i <= $this->max_value; $i++ ) {
						?>
						<li>
							<?php
							if ( 'thumb' === $this->icon_shape ) {
								$icon_type = 1 === $i ? 'thumbUp' : 'thumbDown';
							} else {
								$icon_type = 'star' === $this->icon_shape ? 'star' : "smiley{$i}";
							}
							?>
							<span
								class="srfm-icon"
								role="radio"
								tabindex=<?php echo 1 === $i ? '0' : '-1'; ?>
								aria-label="<?php echo esc_attr( $icon_labels[ $i - 1 ] ?? '' ); ?>"
								aria-checked="<?php echo $this->default_rating === $i ? 'true' : 'false'; ?>"
								data-value="<?php echo esc_attr( strval( $i ) ); ?>"
								<?php echo 1 === $i ? 'aria-required="' . esc_attr( $this->data_require_attr ) . '"' : ''; ?>
								<?php echo 1 === $i ? 'aria-describedby="srfm-label-' . esc_attr( $this->block_id ) . ( ! empty( $this->aria_described_by ) ? ' ' . esc_attr( trim( $this->aria_described_by ) ) : '' ) . '"' : ''; ?>>
								<?php echo Pro_Helper::get_pro_icon( $icon_type ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ignored to render svg ?>
								<?php if ( ! empty( $tooltip_text[ $i - 1 ] ) ) { ?>
									<span class="srfm-tooltip"><?php echo esc_html( $tooltip_text[ $i - 1 ] ); ?></span>
								<?php } ?>
							</span>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="srfm-error-wrap">
				<?php echo wp_kses_post( $this->error_msg_markup ); ?>
			</div>
		</div>
		<?php
		$markup = ob_get_clean();

		return apply_filters(
			'srfm_block_field_markup',
			$markup,
			[
				'slug'       => $this->slug,
				'field_name' => $this->field_name,
				'is_editing' => $this->is_editing,
				'attributes' => $this->attributes,
			]
		);
	}
}
