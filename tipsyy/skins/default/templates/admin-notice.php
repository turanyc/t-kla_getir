<?php
/**
 * The template to display Admin notices
 *
 * @package TIPSY
 * @since TIPSY 1.0.1
 */

$tipsy_theme_slug = get_option( 'template' );
$tipsy_theme_obj  = wp_get_theme( $tipsy_theme_slug );
?>
<div class="tipsy_admin_notice tipsy_welcome_notice notice notice-info is-dismissible" data-notice="admin">
	<?php
	// Theme image
	$tipsy_theme_img = tipsy_get_file_url( 'screenshot.jpg' );
	if ( '' != $tipsy_theme_img ) {
		?>
		<div class="tipsy_notice_image"><img src="<?php echo esc_url( $tipsy_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'tipsy' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="tipsy_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'tipsy' ),
				$tipsy_theme_obj->get( 'Name' ) . ( TIPSY_THEME_FREE ? ' ' . __( 'Free', 'tipsy' ) : '' ),
				$tipsy_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="tipsy_notice_text">
		<p class="tipsy_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $tipsy_theme_obj->description ) );
			?>
		</p>
		<p class="tipsy_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'tipsy' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="tipsy_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=tipsy_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'tipsy' );
			?>
		</a>
	</div>
</div>
