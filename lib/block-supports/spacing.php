<?php
/**
 * Spacing block support flag.
 *
 * For backwards compatibility with core, this remains separate to the
 * dimensions.php block support despite both belonging under a single panel in
 * the editor.
 *
 * @package gutenberg
 */

/**
 * Registers the style block attribute for block types that support it.
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function gutenberg_register_spacing_support( $block_type ) {
	$has_spacing_support = block_has_support( $block_type, array( 'spacing' ), false );

	// Setup attributes and styles within that if needed.
	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( $has_spacing_support && ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}
}

/**
 * Add CSS classes for block spacing to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @param WP_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 *
 * @return array Block spacing CSS classes and inline styles.
 */
function gutenberg_apply_spacing_support( $block_type, $block_attributes ) {
	if ( gutenberg_should_skip_block_supports_serialization( $block_type, 'spacing' ) ) {
		return array();
	}

	$attributes          = array();
	$has_padding_support = block_has_support( $block_type, array( 'spacing', 'padding' ), false );
	$has_margin_support  = block_has_support( $block_type, array( 'spacing', 'margin' ), false );
	$block_styles        = isset( $block_attributes['style'] ) ? $block_attributes['style'] : null;

	if ( ! $block_styles ) {
		return $attributes;
	}

	$skip_padding                    = gutenberg_should_skip_block_supports_serialization( $block_type, 'spacing', 'padding' );
	$skip_margin                     = gutenberg_should_skip_block_supports_serialization( $block_type, 'spacing', 'margin' );
	$spacing_block_styles            = array();
	$spacing_block_styles['padding'] = $has_padding_support && ! $skip_padding ? _wp_array_get( $block_styles, array( 'spacing', 'padding' ), null ) : null;
	$spacing_block_styles['margin']  = $has_margin_support && ! $skip_margin ? _wp_array_get( $block_styles, array( 'spacing', 'margin' ), null ) : null;
	$styles                          = gutenberg_style_engine_generate(
		array( 'spacing' => $spacing_block_styles ),
		array( 'css_vars' => true )
	);

	if ( ! empty( $styles['css'] ) ) {
		$attributes['style'] = $styles['css'];
	}

	return $attributes;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'spacing',
	array(
		'register_attribute' => 'gutenberg_register_spacing_support',
		'apply'              => 'gutenberg_apply_spacing_support',
	)
);
