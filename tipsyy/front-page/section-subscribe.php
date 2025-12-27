<div class="front_page_section front_page_section_subscribe<?php
	$tipsy_scheme = tipsy_get_theme_option( 'front_page_subscribe_scheme' );
	if ( ! empty( $tipsy_scheme ) && ! tipsy_is_inherit( $tipsy_scheme ) ) {
		echo ' scheme_' . esc_attr( $tipsy_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( tipsy_get_theme_option( 'front_page_subscribe_paddings' ) );
	if ( tipsy_get_theme_option( 'front_page_subscribe_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$tipsy_css      = '';
		$tipsy_bg_image = tipsy_get_theme_option( 'front_page_subscribe_bg_image' );
		if ( ! empty( $tipsy_bg_image ) ) {
			$tipsy_css .= 'background-image: url(' . esc_url( tipsy_get_attachment_url( $tipsy_bg_image ) ) . ');';
		}
		if ( ! empty( $tipsy_css ) ) {
			echo ' style="' . esc_attr( $tipsy_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$tipsy_anchor_icon = tipsy_get_theme_option( 'front_page_subscribe_anchor_icon' );
	$tipsy_anchor_text = tipsy_get_theme_option( 'front_page_subscribe_anchor_text' );
if ( ( ! empty( $tipsy_anchor_icon ) || ! empty( $tipsy_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_subscribe"'
									. ( ! empty( $tipsy_anchor_icon ) ? ' icon="' . esc_attr( $tipsy_anchor_icon ) . '"' : '' )
									. ( ! empty( $tipsy_anchor_text ) ? ' title="' . esc_attr( $tipsy_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_subscribe_inner
	<?php
	if ( tipsy_get_theme_option( 'front_page_subscribe_fullheight' ) ) {
		echo ' tipsy-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$tipsy_css      = '';
			$tipsy_bg_mask  = tipsy_get_theme_option( 'front_page_subscribe_bg_mask' );
			$tipsy_bg_color_type = tipsy_get_theme_option( 'front_page_subscribe_bg_color_type' );
			if ( 'custom' == $tipsy_bg_color_type ) {
				$tipsy_bg_color = tipsy_get_theme_option( 'front_page_subscribe_bg_color' );
			} elseif ( 'scheme_bg_color' == $tipsy_bg_color_type ) {
				$tipsy_bg_color = tipsy_get_scheme_color( 'bg_color', $tipsy_scheme );
			} else {
				$tipsy_bg_color = '';
			}
			if ( ! empty( $tipsy_bg_color ) && $tipsy_bg_mask > 0 ) {
				$tipsy_css .= 'background-color: ' . esc_attr(
					1 == $tipsy_bg_mask ? $tipsy_bg_color : tipsy_hex2rgba( $tipsy_bg_color, $tipsy_bg_mask )
				) . ';';
			}
			if ( ! empty( $tipsy_css ) ) {
				echo ' style="' . esc_attr( $tipsy_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_subscribe_content_wrap content_wrap">
			<?php
			// Caption
			$tipsy_caption = tipsy_get_theme_option( 'front_page_subscribe_caption' );
			if ( ! empty( $tipsy_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_subscribe_caption front_page_block_<?php echo ! empty( $tipsy_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $tipsy_caption, 'tipsy_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$tipsy_description = tipsy_get_theme_option( 'front_page_subscribe_description' );
			if ( ! empty( $tipsy_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_subscribe_description front_page_block_<?php echo ! empty( $tipsy_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $tipsy_description ), 'tipsy_kses_content' ); ?></div>
				<?php
			}

			// Content
			$tipsy_sc = tipsy_get_theme_option( 'front_page_subscribe_shortcode' );
			if ( ! empty( $tipsy_sc ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_output front_page_section_subscribe_output front_page_block_<?php echo ! empty( $tipsy_sc ) ? 'filled' : 'empty'; ?>">
				<?php
					tipsy_show_layout( do_shortcode( $tipsy_sc ) );
				?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
