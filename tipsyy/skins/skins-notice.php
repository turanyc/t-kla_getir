<?php
/**
 * The template to display Admin notices
 *
 * @package TIPSY
 * @since TIPSY 1.0.64
 */

$tipsy_skins_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_skins' );
$tipsy_skins_args = get_query_var( 'tipsy_skins_notice_args' );
?>
<div class="tipsy_admin_notice tipsy_skins_notice notice notice-info is-dismissible" data-notice="skins">
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
		<?php esc_html_e( 'New skins are available', 'tipsy' ); ?>
	</h3>
	<?php

	// Description
	$tipsy_total      = $tipsy_skins_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$tipsy_skins_msg  = $tipsy_total > 0
							// Translators: Add new skins number
							? '<strong>' . sprintf( _n( '%d new version', '%d new versions', $tipsy_total, 'tipsy' ), $tipsy_total ) . '</strong>'
							: '';
	$tipsy_total      = $tipsy_skins_args['free'];
	$tipsy_skins_msg .= $tipsy_total > 0
							? ( ! empty( $tipsy_skins_msg ) ? ' ' . esc_html__( 'and', 'tipsy' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d free skin', '%d free skins', $tipsy_total, 'tipsy' ), $tipsy_total ) . '</strong>'
							: '';
	$tipsy_total      = $tipsy_skins_args['pay'];
	$tipsy_skins_msg .= $tipsy_skins_args['pay'] > 0
							? ( ! empty( $tipsy_skins_msg ) ? ' ' . esc_html__( 'and', 'tipsy' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d paid skin', '%d paid skins', $tipsy_total, 'tipsy' ), $tipsy_total ) . '</strong>'
							: '';
	?>
	<div class="tipsy_notice_text">
		<p>
			<?php
			// Translators: Add new skins info
			echo wp_kses_data( sprintf( __( "We are pleased to announce that %s are available for your theme", 'tipsy' ), $tipsy_skins_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="tipsy_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $tipsy_skins_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			// Translators: Add theme name
			esc_html_e( 'Go to Skins manager', 'tipsy' );
			?>
		</a>
	</div>
</div>
