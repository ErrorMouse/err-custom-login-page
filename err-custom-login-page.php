<?php
/**
 * Plugin Name:       Custom Login Page
 * Plugin URI:        https://err-mouse.id.vn
 * Description:       Customize the logo, links, colors and background on the WordPress login page.
 * Version:           1.26.5
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Err
 * Author URI:        https://profiles.wordpress.org/nmtnguyen56/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       err-custom-login-page
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// REWRITTEN: Use a unique and longer prefix for all constants.
define( 'ERRCLP_SETTINGS_SLUG', 'err-custom-login-settings' );
define( 'ERRCLP_OPTION_NAME', 'err-custom-login-options' );
define( 'ERRCLP_VERSION', '1.26.5' );

/**
 * Helper function to convert HEX to RGBA.
 * REWRITTEN: Renamed with the new prefix.
 */
function errclp_hex_to_rgba( $hex, $alpha = 1 ) {
	if ( empty( $hex ) ) {
		return '';
	}
	$hex = str_replace( '#', '', $hex );
	if ( strlen( $hex ) == 3 ) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} elseif ( strlen( $hex ) == 6 ) {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	} else {
		return ''; // Invalid hex.
	}
	$alpha = floatval( $alpha );
	$alpha = max( 0, min( 1, $alpha ) );
	return "rgba({$r}, {$g}, {$b}, {$alpha})";
}


// 1. ADD SETTINGS PAGE TO ADMIN MENU
// REWRITTEN: Renamed function with the new prefix.
add_action( 'admin_menu', 'errclp_add_admin_menu' );
function errclp_add_admin_menu() {
	add_options_page(
		__( 'Custom Login Page', 'err-custom-login-page' ),
		__( 'Custom Login Page', 'err-custom-login-page' ),
		'manage_options',
		ERRCLP_SETTINGS_SLUG,
		'errclp_settings_page_html'
	);
}

// 2. REGISTER SETTINGS (SETTINGS API)
// REWRITTEN: Renamed function and all callbacks with the new prefix.
add_action( 'admin_init', 'errclp_settings_init' );
function errclp_settings_init() {
	register_setting( ERRCLP_SETTINGS_SLUG, ERRCLP_OPTION_NAME, 'errclp_sanitize_options' );

	// Logo Section
	add_settings_section(
		'errclp_logo_section',
		__( 'Login Page Logo Settings', 'err-custom-login-page' ),
		null,
		ERRCLP_SETTINGS_SLUG
	);
	add_settings_field( 'logo_image_url', __( 'Select Logo Image', 'err-custom-login-page' ), 'errclp_field_logo_image_html', ERRCLP_SETTINGS_SLUG, 'errclp_logo_section' );
	add_settings_field( 'logo_target_url', __( 'Logo Link URL', 'err-custom-login-page' ), 'errclp_field_logo_target_url_html', ERRCLP_SETTINGS_SLUG, 'errclp_logo_section' );
	add_settings_field( 'logo_height', __( 'Logo Height (px)', 'err-custom-login-page' ), 'errclp_field_logo_height_html', ERRCLP_SETTINGS_SLUG, 'errclp_logo_section' );

	// Appearance Section
	add_settings_section(
		'errclp_appearance_section',
		__( 'Login Page Appearance Settings', 'err-custom-login-page' ),
		null,
		ERRCLP_SETTINGS_SLUG
	);
	add_settings_field( 'body_bg_image_url', __( 'Body Background Image', 'err-custom-login-page' ), 'errclp_field_body_bg_image_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'body_bg_color', __( 'Body Background Color (Fallback)', 'err-custom-login-page' ), 'errclp_field_body_bg_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_bg_color', __( 'Form Background Color', 'err-custom-login-page' ), 'errclp_field_form_bg_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_bg_color_opacity', __( 'Form Background Opacity', 'err-custom-login-page' ), 'errclp_field_form_bg_color_opacity_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_backdrop_blur', __( 'Enable Form Backdrop Blur', 'err-custom-login-page' ), 'errclp_field_form_backdrop_blur_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_border_width', __( 'Form Border Width (px)', 'err-custom-login-page' ), 'errclp_field_form_border_width_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_border_color', __( 'Form Border Color', 'err-custom-login-page' ), 'errclp_field_form_border_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_border_color_opacity', __( 'Form Border Opacity', 'err-custom-login-page' ), 'errclp_field_form_border_color_opacity_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_border_radius', __( 'Form Border Radius (px)', 'err-custom-login-page' ), 'errclp_field_form_border_radius_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_box_shadow_color', __( 'Form Box Shadow Color', 'err-custom-login-page' ), 'errclp_field_form_box_shadow_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_text_color', __( 'Form Label Text Color', 'err-custom-login-page' ), 'errclp_field_form_text_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_input_text_color', __( 'Form Input Text Color', 'err-custom-login-page' ), 'errclp_field_form_input_text_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_link_color', __( 'Form Link Color', 'err-custom-login-page' ), 'errclp_field_form_link_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'form_link_hover_color', __( 'Form Link Color (Hover)', 'err-custom-login-page' ), 'errclp_field_form_link_hover_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'button_bg_color', __( 'Login Button Background Color', 'err-custom-login-page' ), 'errclp_field_button_bg_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'button_text_color', __( 'Login Button Text Color', 'err-custom-login-page' ), 'errclp_field_button_text_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'button_bg_hover_color', __( 'Login Button Background Color (Hover)', 'err-custom-login-page' ), 'errclp_field_button_bg_hover_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'button_text_hover_color', __( 'Login Button Text Color (Hover)', 'err-custom-login-page' ), 'errclp_field_button_text_hover_color_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'hide_privacy_policy_link', null, 'errclp_field_hide_privacy_policy_link_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'hide_language_switcher', null, 'errclp_field_hide_language_switcher_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
	add_settings_field( 'hide_back_to_blog_link', null, 'errclp_field_hide_back_to_blog_link_html', ERRCLP_SETTINGS_SLUG, 'errclp_appearance_section' );
}

// ---- SETTINGS FIELD CALLBACKS ----
// REWRITTEN: All function names and IDs/classes/names in HTML are updated with the new prefix.

function errclp_field_logo_image_html() {
	$options   = get_option( ERRCLP_OPTION_NAME );
	$image_url = isset( $options['logo_image_url'] ) ? $options['logo_image_url'] : '';
	$image_id  = isset( $options['logo_image_id'] ) ? $options['logo_image_id'] : 0;
	?>
	<div class="errclp-image-uploader">
		<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[logo_image_url]" id="errclp_logo_image_url" value="<?php echo esc_url( $image_url ); ?>" class="regular-text">
		<input type="hidden" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[logo_image_id]" id="errclp_logo_image_id" value="<?php echo esc_attr( $image_id ); ?>">
		<button type="button" class="button errclp_upload_image_button"><?php esc_html_e( 'Select image', 'err-custom-login-page' ); ?></button>
		<div class="errclp_image_preview" style="margin-top:10px; max-width:300px;">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" style="max-width:100%; height:auto; border:1px solid #ddd;" />
				<br>
				<button type="button" class="button button-small errclp_remove_image_button" style="margin-top:5px;"><?php esc_html_e( 'Remove image', 'err-custom-login-page' ); ?></button>
			<?php endif; ?>
		</div>
	</div>
	<p class="description">
		<?php esc_html_e( 'Select an image from the Media Library. For best results, use a PNG with a transparent background.', 'err-custom-login-page' ); ?>
	</p>
	<?php
}

function errclp_field_logo_target_url_html() {
	$options    = get_option( ERRCLP_OPTION_NAME );
	$target_url = isset( $options['logo_target_url'] ) && ! empty( $options['logo_target_url'] ) ? $options['logo_target_url'] : home_url();
	?>
	<input type="url" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[logo_target_url]" value="<?php echo esc_url( $target_url ); ?>" class="regular-text">
	<p class="description">
	<?php
		printf(
			wp_kses(
				/* translators: %s: The site's home URL wrapped in <code> tags. */
				__( 'Enter the URL the logo will point to. Defaults to the homepage: %s', 'err-custom-login-page' ),
				array( 'code' => array() )
			),
			'<code>' . esc_url( home_url() ) . '</code>'
		);
	?>
	</p>
	<?php
}

function errclp_field_logo_height_html() {
	$options    = get_option( ERRCLP_OPTION_NAME );
	$max_height = isset( $options['logo_height'] ) ? $options['logo_height'] : '';
	?>
	<input type="number" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[logo_height]" value="<?php echo esc_attr( $max_height ); ?>" class="regular-text" min="0" step="1">
	<p class="description"> <?php esc_html_e( 'Leave blank for no height restriction.', 'err-custom-login-page' ); ?> </p>
	<?php
}

function errclp_field_body_bg_image_html() {
	$options   = get_option( ERRCLP_OPTION_NAME );
	$image_url = isset( $options['body_bg_image_url'] ) ? $options['body_bg_image_url'] : '';
	?>
	<div class="errclp-image-uploader">
		<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[body_bg_image_url]" id="errclp_body_bg_image_url" value="<?php echo esc_url( $image_url ); ?>" class="regular-text">
		<button type="button" class="button errclp_upload_image_button"><?php esc_html_e( 'Select image', 'err-custom-login-page' ); ?></button>
		<div class="errclp_image_preview" style="margin-top:10px; max-width:300px;">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" style="max-width:100%; height:auto; border:1px solid #ddd;" />
				<br>
				<button type="button" class="button button-small errclp_remove_image_button" style="margin-top:5px;"><?php esc_html_e( 'Remove image', 'err-custom-login-page' ); ?></button>
			<?php endif; ?>
		</div>
	</div>
	<p class="description">
		<?php esc_html_e( 'Select a background image for the entire login page.', 'err-custom-login-page' ); ?>
	</p>
	<?php
}

function errclp_field_body_bg_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['body_bg_color'] ) ? $options['body_bg_color'] : '#f0f0f1';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[body_bg_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#f0f0f1" />
	<p class="description"><?php esc_html_e( 'This color is a fallback if no background image is set.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_form_bg_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['form_bg_color'] ) ? $options['form_bg_color'] : '#ffffff';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_bg_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#ffffff" />
	<p class="description"><?php esc_html_e( 'For backdrop blur to be visible, set opacity below.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_form_bg_color_opacity_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$opacity = ( isset( $options['form_bg_color_opacity'] ) && '' !== $options['form_bg_color_opacity'] ) ? floatval( $options['form_bg_color_opacity'] ) : 1;
	?>
	<input type="range" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_bg_color_opacity]" id="errclp_form_bg_opacity_slider" value="<?php echo esc_attr( $opacity ); ?>" min="0" max="1" step="0.01" style="width: 200px; vertical-align: middle;">
	<span id="errclp_form_bg_opacity_value" style="margin-left: 10px; font-weight: bold; vertical-align: middle;"><?php echo esc_html( $opacity ); ?></span>
	<p class="description"><?php esc_html_e( 'Drag to adjust opacity (0.0 is transparent, 1.0 is opaque).', 'err-custom-login-page' ); ?></p>
	<?php
	// CHANGED: Inline script is now moved to the main admin enqueue function.
}

function errclp_field_form_backdrop_blur_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$checked = isset( $options['form_backdrop_blur'] ) && '1' === $options['form_backdrop_blur'];
	?>
	<label for="errclp_form_backdrop_blur">
		<input type="checkbox" id="errclp_form_backdrop_blur" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_backdrop_blur]" value="1" <?php checked( $checked, true ); ?> />
		<?php esc_html_e( 'Enable a backdrop blur effect for the login form.', 'err-custom-login-page' ); ?>
	</label>
	<p class="description"><?php esc_html_e( 'For this to be noticeable, "Form Background Opacity" must be less than 1.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_form_border_width_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$width   = isset( $options['form_border_width'] ) ? $options['form_border_width'] : '';
	?>
	<input type="number" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_border_width]" value="<?php echo esc_attr( $width ); ?>" class="regular-text" min="0" step="1">
	<p class="description">
	<?php
		printf(
			wp_kses(
				__( 'Enter <code>0</code> for no border. Leave blank for WordPress default.', 'err-custom-login-page' ),
				array( 'code' => array() )
			)
		);
	?>
	</p>
	<?php
}

function errclp_field_form_border_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['form_border_color'] ) ? $options['form_border_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_border_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#c3c4c7">
	<p class="description"><?php esc_html_e( 'Only effective if Border Width is set.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_form_border_color_opacity_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$opacity = ( isset( $options['form_border_color_opacity'] ) && '' !== $options['form_border_color_opacity'] ) ? floatval( $options['form_border_color_opacity'] ) : 1;
	?>
	<input type="range" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_border_color_opacity]" id="errclp_form_border_color_opacity_slider" value="<?php echo esc_attr( $opacity ); ?>" min="0" max="1" step="0.01" style="width: 200px; vertical-align: middle;">
	<span id="errclp_form_border_color_opacity_value" style="margin-left: 10px; font-weight: bold; vertical-align: middle;"><?php echo esc_html( $opacity ); ?></span>
	<p class="description"><?php esc_html_e( 'Border color opacity (0.0 - 1.0).', 'err-custom-login-page' ); ?></p>
	<?php
	// CHANGED: Inline script is now moved to the main admin enqueue function.
}

function errclp_field_form_border_radius_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$radius  = isset( $options['form_border_radius'] ) ? $options['form_border_radius'] : '';
	?>
	<input type="number" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_border_radius]" value="<?php echo esc_attr( $radius ); ?>" class="regular-text" min="0" step="1">
	<p class="description"> <?php esc_html_e( 'Leave blank for default.', 'err-custom-login-page' ); ?> </p>
	<?php
}

function errclp_field_form_box_shadow_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['form_box_shadow_color'] ) ? $options['form_box_shadow_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_box_shadow_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="">
	<p class="description"> <?php esc_html_e( 'Leave blank for no shadow.', 'err-custom-login-page' ); ?> </p>
	<?php
}

function errclp_field_form_text_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['form_text_color'] ) ? $options['form_text_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_text_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#3c434a" />
	<p class="description"><?php esc_html_e( 'Color for labels like "Username or Email Address".', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_form_input_text_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['form_input_text_color'] ) ? $options['form_input_text_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_input_text_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#3c434a">
	<p class="description"><?php esc_html_e( 'Text color inside input fields.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_form_link_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['form_link_color'] ) ? $options['form_link_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_link_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#2271b1" />
	<p class="description"><?php esc_html_e( 'Leave blank to use default color.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_form_link_hover_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['form_link_hover_color'] ) ? $options['form_link_hover_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[form_link_hover_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#1d679e" />
	<p class="description"><?php esc_html_e( 'Leave blank to use default color.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_button_bg_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['button_bg_color'] ) ? $options['button_bg_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[button_bg_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#2271b1" />
	<p class="description"><?php esc_html_e( 'Leave blank to use default color.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_button_text_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['button_text_color'] ) ? $options['button_text_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[button_text_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#ffffff" />
	<p class="description"><?php esc_html_e( 'Leave blank to use default color.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_button_bg_hover_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['button_bg_hover_color'] ) ? $options['button_bg_hover_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[button_bg_hover_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#1d679e" />
	<p class="description"><?php esc_html_e( 'Leave blank to use default color.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_button_text_hover_color_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$color   = isset( $options['button_text_hover_color'] ) ? $options['button_text_hover_color'] : '';
	?>
	<input type="text" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[button_text_hover_color]" value="<?php echo esc_attr( $color ); ?>" class="errclp-color-picker" data-default-color="#ffffff" />
	<p class="description"><?php esc_html_e( 'Leave blank to use default color.', 'err-custom-login-page' ); ?></p>
	<?php
}

function errclp_field_hide_privacy_policy_link_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$checked = isset( $options['hide_privacy_policy_link'] ) && '1' === $options['hide_privacy_policy_link'];
	?>
	<label for="errclp_hide_privacy_policy_link">
		<input type="checkbox" id="errclp_hide_privacy_policy_link" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[hide_privacy_policy_link]" value="1" <?php checked( $checked, true ); ?> />
		<?php esc_html_e( 'Hide the privacy policy link.', 'err-custom-login-page' ); ?>
	</label>
	<?php
}

function errclp_field_hide_language_switcher_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$checked = isset( $options['hide_language_switcher'] ) && '1' === $options['hide_language_switcher'];
	?>
	<label for="errclp_hide_language_switcher">
		<input type="checkbox" id="errclp_hide_language_switcher" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[hide_language_switcher]" value="1" <?php checked( $checked, true ); ?> />
		<?php esc_html_e( 'Hide the language switcher.', 'err-custom-login-page' ); ?>
	</label>
	<?php
}

function errclp_field_hide_back_to_blog_link_html() {
	$options = get_option( ERRCLP_OPTION_NAME );
	$checked = isset( $options['hide_back_to_blog_link'] ) && '1' === $options['hide_back_to_blog_link'];
	?>
	<label for="errclp_hide_back_to_blog_link">
		<input type="checkbox" id="errclp_hide_back_to_blog_link" name="<?php echo esc_attr( ERRCLP_OPTION_NAME ); ?>[hide_back_to_blog_link]" value="1" <?php checked( $checked, true ); ?> />
		<?php esc_html_e( 'Hide the "Back to [Site Name]" link.', 'err-custom-login-page' ); ?>
	</label>
	<?php
}

/**
 * Sanitize all options from the settings page.
 * REWRITTEN: Renamed function with the new prefix.
 */
function errclp_sanitize_options( $input ) {
	$sanitized_input = array();
	$defaults        = errclp_get_default_options();

	// Sanitize URLs
	$url_fields = array( 'logo_image_url', 'body_bg_image_url', 'logo_target_url' );
	foreach ( $url_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$sanitized_input[ $field ] = esc_url_raw( trim( $input[ $field ] ) );
		}
	}

	// Sanitize integers
	$int_fields = array( 'logo_image_id', 'logo_height', 'form_border_width', 'form_border_radius' );
	foreach ( $int_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$sanitized_input[ $field ] = '' === $input[ $field ] ? '' : absint( $input[ $field ] );
		}
	}

	// Sanitize colors
	$color_fields = array(
		'body_bg_color', 'form_bg_color', 'form_text_color', 'form_input_text_color', 'form_link_color',
		'button_bg_color', 'button_text_color', 'form_link_hover_color', 'button_bg_hover_color',
		'button_text_hover_color', 'form_border_color', 'form_box_shadow_color',
	);
	foreach ( $color_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$sanitized_input[ $field ] = sanitize_hex_color( $input[ $field ] );
		}
	}

	// Sanitize opacity values (float between 0 and 1)
	$opacity_fields = array( 'form_bg_color_opacity', 'form_border_color_opacity' );
	foreach ( $opacity_fields as $key ) {
		if ( isset( $input[ $key ] ) && '' !== $input[ $key ] && is_numeric( $input[ $key ] ) ) {
			$opacity_val               = floatval( $input[ $key ] );
			$sanitized_input[ $key ] = (string) max( 0, min( 1, $opacity_val ) );
		} else {
			$sanitized_input[ $key ] = $defaults[ $key ];
		}
	}

	// Sanitize checkboxes
	$checkbox_fields = array(
		'hide_language_switcher', 'hide_privacy_policy_link',
		'hide_back_to_blog_link', 'form_backdrop_blur',
	);
	foreach ( $checkbox_fields as $field ) {
		$sanitized_input[ $field ] = ( isset( $input[ $field ] ) && '1' === $input[ $field ] ) ? '1' : '0';
	}

	return $sanitized_input;
}

/**
 * HTML for the settings page.
 * REWRITTEN: Renamed function and updated IDs with the new prefix.
 */
function errclp_settings_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div id="errclp-settings-page">
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?><span class="author">by <a href="https://www.linkedin.com/in/nmtnguyen56" target="_blank" rel="noopener noreferrer">Err</a></span><span class="donate"><?php errclp_donate_link_html(); ?></span></h1>
			
			<div id="errclp-ajax-popup" class="errclp-notice" style="display:none;">
				<p></p>
			</div>

			<form action="options.php" method="post" id="errclp-settings-form">
				<?php
				submit_button( __( 'Save Changes', 'err-custom-login-page' ) );
				settings_fields( ERRCLP_SETTINGS_SLUG );
				do_settings_sections( ERRCLP_SETTINGS_SLUG );
				submit_button( __( 'Save Changes', 'err-custom-login-page' ) );
				?>
			</form>

			<div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
				<h2><?php esc_html_e( 'Reset Settings', 'err-custom-login-page' ); ?></h2>
				<p><?php esc_html_e( 'If you want to return all settings to their original default values, click the button below.', 'err-custom-login-page' ); ?></p>
				<p>
					<button type="button" id="errclp-reset-settings-button" class="button button-large" style="background-color: #d63638; color: white; border-color: #b02a2c;">
						<?php esc_html_e( 'Reset All Options to Default', 'err-custom-login-page' ); ?>
					</button>
				</p>
				<p class="description"><?php esc_html_e( 'Warning: This action is irreversible.', 'err-custom-login-page' ); ?></p>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Enqueue admin scripts and styles.
 * REWRITTEN: Renamed function, updated handles, and added inline scripts/styles here.
 */
add_action( 'admin_enqueue_scripts', 'errclp_enqueue_admin_scripts' );
function errclp_enqueue_admin_scripts( $hook_suffix ) {

	$is_settings_page = ( 'settings_page_' . ERRCLP_SETTINGS_SLUG === $hook_suffix );
	$is_plugins_page  = ( 'plugins.php' === $hook_suffix );

	if ( ! $is_settings_page && ! $is_plugins_page ) {
		return;
	}

	// Styles and scripts for the settings page.
	if ( $is_settings_page ) {
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style(
			'errclp-admin-styles',
			plugins_url( 'assets/css/err-custom-login-page.css', __FILE__ ),
			array( 'wp-color-picker' ),
			ERRCLP_VERSION
		);

		wp_enqueue_script(
			'errclp-admin-script',
			plugins_url( 'assets/js/err-custom-login-page.js', __FILE__ ),
			array( 'jquery', 'wp-color-picker', 'wp-mediaelement' ),
			ERRCLP_VERSION,
			true
		);

		wp_localize_script(
			'errclp-admin-script',
			'errclp_ajax_params',
			array(
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'nonce'              => wp_create_nonce( 'errclp_save_settings_nonce' ),
				'reset_nonce'        => wp_create_nonce( 'errclp_reset_settings_nonce' ),
				'saving_text'        => __( 'Saving...', 'err-custom-login-page' ),
				'success_text'       => __( 'Settings saved successfully!', 'err-custom-login-page' ),
				'remove_image_text'  => __( 'Remove image', 'err-custom-login-page' ),
				'error_text'         => __( 'Error! Could not save settings.', 'err-custom-login-page' ),
				'reset_confirm_text' => __( 'Are you sure you want to reset all settings? This cannot be undone.', 'err-custom-login-page' ),
				'resetting_text'     => __( 'Resetting...', 'err-custom-login-page' ),
			)
		);

		// NEW: Inline script for opacity sliders, moved from HTML callbacks.
		$inline_script = "
            jQuery(document).ready(function($) {
                $('#errclp_form_bg_opacity_slider').on('input change', function() {
                    $('#errclp_form_bg_opacity_value').text($(this).val());
                });
                $('#errclp_form_border_color_opacity_slider').on('input change', function() {
                    $('#errclp_form_border_color_opacity_value').text($(this).val());
                });
            });
        ";
		wp_add_inline_script( 'errclp-admin-script', $inline_script );
	}

	// Styles for the donate link on the plugins page.
	if ( $is_plugins_page ) {
		$donate_css = "
            .err-donate-link {
                font-weight: bold;
                background: linear-gradient(90deg, #0066ff, #00a1ff, rgb(255, 0, 179), #0066ff);
                background-size: 200% auto;
                color: #fff;
                -webkit-background-clip: text;
                -moz-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: errGradientText 2s linear infinite;
            }
            @keyframes errGradientText {
                to { background-position: -200% center; }
            }";
		wp_add_inline_style( 'wp-admin', $donate_css );
	}
}

/**
 * All the custom CSS for the login page.
 */
function errclp_get_custom_login_page_css() {
	$options       = get_option( ERRCLP_OPTION_NAME, errclp_get_default_options() );
	$css_output    = '';
	$form_selector = '.login form#loginform, .login #registerform, .login #lostpasswordform';

	// Logo Styles
	$logo_url      = ! empty( $options['logo_image_url'] ) ? esc_url( $options['logo_image_url'] ) : '';
	$logo_id       = ! empty( $options['logo_image_id'] ) ? absint( $options['logo_image_id'] ) : 0;
	$logo_height   = ( isset( $options['logo_height'] ) && '' !== $options['logo_height'] ) ? absint( $options['logo_height'] ) : null;

    $css_output .= "#login { width: unset; max-width: 100%; padding: 10vh 15px 0;}\n";
    $css_output .= "#login .message, #login .notice, #login .success { width: 600px; max-width: 100%; margin: 0 auto 20px; text-align: center; }\n";
    $css_output .= "#loginform, #lostpasswordform { width: calc(100% - 50px); max-width: 320px; margin: 0 auto; padding: 40px 25px;}\n";
    $css_output .= "#loginform input[type=text], #login input[type=password] { background-color: #ffffffcc; border: 1px solid #e7e7e7; }\n";
    $css_output .= "#login #nav, #login #backtoblog { text-align: center; }\n";
    $css_output .= "#login a { text-decoration: none; }\n";
    $css_output .= "#login a:focus { box-shadow: none; }\n";
    $css_output .= "#login .g-recaptcha { max-width: 100%; }\n";

	if ( $logo_url ) {
		$css_output .= '#login h1 a, .login h1 a {';
		$css_output .= "max-width: 100%;";
		$css_output .= "margin-bottom: 30px;";
		$css_output .= "background-image: url('{$logo_url}');";
		$css_output .= 'background-size: contain; background-repeat: no-repeat; background-position: center;';
		if ( null !== $logo_height && $logo_height > 0 ) {
			$css_output .= "height: {$logo_height}px;";
			// Calculate width to maintain aspect ratio if possible
			if ( $logo_id > 0 ) {
				$image_meta = wp_get_attachment_image_src( $logo_id, 'full' );
				if ( $image_meta && $image_meta[1] > 0 && $image_meta[2] > 0 ) {
					$ratio            = $image_meta[1] / $image_meta[2];
					$calculated_width = round( $logo_height * $ratio );
					$css_output      .= "width: {$calculated_width}px;";
				}
			}
		}
		$css_output .= "}\n";
	}

	// Body Background
	$body_bg_image_url = ! empty( $options['body_bg_image_url'] ) ? esc_url( $options['body_bg_image_url'] ) : '';
	if ( $body_bg_image_url ) {
		$css_output .= "body.login { background-image: url('{$body_bg_image_url}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed; }\n";
	} elseif ( ! empty( $options['body_bg_color'] ) ) {
		$css_output .= 'body.login { background-color: ' . esc_attr( $options['body_bg_color'] ) . "; }\n";
	}

	// Form Styles
	$form_css = '';
	if ( ! empty( $options['form_bg_color'] ) ) {
		$opacity     = ( isset( $options['form_bg_color_opacity'] ) && '' !== $options['form_bg_color_opacity'] ) ? floatval( $options['form_bg_color_opacity'] ) : 1.0;
		$final_color = ( $opacity < 1.0 ) ? errclp_hex_to_rgba( $options['form_bg_color'], $opacity ) : $options['form_bg_color'];
		$form_css   .= 'background-color: ' . esc_attr( $final_color ) . ' !important;';
	}
	$border_width = ( isset( $options['form_border_width'] ) && '' !== $options['form_border_width'] ) ? absint( $options['form_border_width'] ) : null;
	if ( null !== $border_width && 0 === $border_width ) {
		$form_css .= 'border: none !important;';
	} elseif ( null !== $border_width && $border_width > 0 && ! empty( $options['form_border_color'] ) ) {
		$border_opacity = ( isset( $options['form_border_color_opacity'] ) && '' !== $options['form_border_color_opacity'] ) ? floatval( $options['form_border_color_opacity'] ) : 1.0;
		$final_border_color = ( $border_opacity < 1.0 ) ? errclp_hex_to_rgba( $options['form_border_color'], $border_opacity ) : $options['form_border_color'];
		$form_css .= " border: {$border_width}px solid " . esc_attr( $final_border_color ) . ' !important;';
	}
	$border_radius = ( isset( $options['form_border_radius'] ) && '' !== $options['form_border_radius'] ) ? absint( $options['form_border_radius'] ) : null;
	if ( null !== $border_radius ) {
		$form_css .= "border-radius: {$border_radius}px !important;";
	}
	if ( ! empty( $options['form_box_shadow_color'] ) ) {
		$form_css .= 'box-shadow: 0 0 30px ' . esc_attr( errclp_hex_to_rgba( $options['form_box_shadow_color'], 0.2 ) ) . ' !important;';
	}
	$is_blur_enabled = isset( $options['form_backdrop_blur'] ) && '1' === $options['form_backdrop_blur'];
	if ( $is_blur_enabled && isset( $options['form_bg_color_opacity'] ) && floatval( $options['form_bg_color_opacity'] ) < 1.0 ) {
		$form_css .= 'backdrop-filter: blur(10px) !important; -webkit-backdrop-filter: blur(10px) !important;';
	}
	if ( ! empty( $form_css ) ) {
		$css_output .= "{$form_selector} { {$form_css} }\n";
	}

	// Text and Link Colors
	if ( ! empty( $options['form_text_color'] ) ) {
		$css_output .= '.login label { color: ' . esc_attr( $options['form_text_color'] ) . " !important; }\n";
	}
	if ( ! empty( $options['form_input_text_color'] ) ) {
		$css_output .= ".login form .input, .login input[type='text'] { color: " . esc_attr( $options['form_input_text_color'] ) . " !important; }\n";
	}
	if ( ! empty( $options['form_link_color'] ) ) {
		$css_output .= '.login #nav a, .login #backtoblog a, .login .privacy-policy-page-link a { color: ' . esc_attr( $options['form_link_color'] ) . " !important; }\n";
	}
	if ( ! empty( $options['form_link_hover_color'] ) ) {
		$css_output .= '.login #nav a:hover, .login #backtoblog a:hover, .login .privacy-policy-page-link a:hover { color: ' . esc_attr( $options['form_link_hover_color'] ) . " !important; }\n";
	}

	// Button Colors
	if ( ! empty( $options['button_bg_color'] ) ) {
		$css_output .= '.wp-core-ui .button-primary { background: ' . esc_attr( $options['button_bg_color'] ) . ' !important; border-color: ' . esc_attr( $options['button_bg_color'] ) . " !important; box-shadow: none !important; text-shadow: none !important; }\n";
		$css_output .= '#login input:focus { border-color: ' . esc_attr( $options['button_bg_color'] ) . '; box-shadow: 0 0 0 1px ' . esc_attr( $options['button_bg_color'] ) . "; }\n";
	}
	if ( ! empty( $options['button_text_color'] ) ) {
		$css_output .= '.wp-core-ui .button-primary { color: ' . esc_attr( $options['button_text_color'] ) . " !important; }\n";
	}
	if ( ! empty( $options['button_bg_hover_color'] ) ) {
		$css_output .= '.wp-core-ui .button-primary:hover, .wp-core-ui .button-primary:focus { background: ' . esc_attr( $options['button_bg_hover_color'] ) . ' !important; border-color: ' . esc_attr( $options['button_bg_hover_color'] ) . " !important; }\n";
	}
	if ( ! empty( $options['button_text_hover_color'] ) ) {
		$css_output .= '.wp-core-ui .button-primary:hover, .wp-core-ui .button-primary:focus { color: ' . esc_attr( $options['button_text_hover_color'] ) . " !important; }\n";
	}

	// Hide elements
	if ( isset( $options['hide_privacy_policy_link'] ) && '1' === $options['hide_privacy_policy_link'] ) {
		$css_output .= ".login .privacy-policy-page-link { display: none !important; }\n";
	}
	if ( isset( $options['hide_language_switcher'] ) && '1' === $options['hide_language_switcher'] ) {
		$css_output .= ".language-switcher { display: none !important; }\n";
	}
	if ( isset( $options['hide_back_to_blog_link'] ) && '1' === $options['hide_back_to_blog_link'] ) {
		$css_output .= "#login #backtoblog { display: none !important; }\n";
	}

	return $css_output;
}

/**
 * Enqueue styles for the login page.
 * NEW: This function now correctly enqueues inline styles.
 */
add_action( 'login_enqueue_scripts', 'errclp_enqueue_login_styles' );
function errclp_enqueue_login_styles() {
    $custom_css = errclp_get_custom_login_page_css();
    if ( ! empty( $custom_css ) ) {
        // We add the style to a dummy handle since we only have inline styles.
        wp_register_style( 'errclp-login-styles', false, array(), ERRCLP_VERSION );
        wp_enqueue_style( 'errclp-login-styles' );
        wp_add_inline_style( 'errclp-login-styles', $custom_css );
    }
}

/**
 * Customize the login page logo URL.
 * REWRITTEN: Renamed function with the new prefix.
 */
add_filter( 'login_headerurl', 'errclp_custom_login_url' );
function errclp_custom_login_url( $login_url ) {
	$options = get_option( ERRCLP_OPTION_NAME );
	return ! empty( $options['logo_target_url'] ) ? esc_url( $options['logo_target_url'] ) : home_url();
}

/**
 * Customize the login page logo title.
 * REWRITTEN: Renamed function with the new prefix.
 */
add_filter( 'login_headertext', 'errclp_custom_login_title' );
function errclp_custom_login_title( $login_title ) {
	return get_bloginfo( 'name', 'display' );
}

/**
 * Add "Settings" link to the plugin list.
 * REWRITTEN: Renamed function and updated constants.
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'errclp_add_settings_link' );
function errclp_add_settings_link( $links ) {
	$settings_page_url = esc_url( admin_url( 'options-general.php?page=' . ERRCLP_SETTINGS_SLUG ) );
	$settings_link     = '<a href="' . $settings_page_url . '">' . esc_html__( 'Settings', 'err-custom-login-page' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

/**
 * AJAX handler for saving settings.
 * REWRITTEN: Renamed function, updated nonces and option names. Removed phpcs:ignore.
 */
add_action( 'wp_ajax_errclp_save_settings', 'errclp_ajax_save_settings_callback' );
function errclp_ajax_save_settings_callback() {
    if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'errclp_save_settings_nonce' ) ) {
        wp_send_json_error( array( 'message' => __( 'Security error.', 'err-custom-login-page' ) ), 403 );
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => __( 'Permission denied.', 'err-custom-login-page' ) ), 403 );
    }

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $posted_options    = isset( $_POST[ ERRCLP_OPTION_NAME ] ) ? wp_unslash( (array) $_POST[ ERRCLP_OPTION_NAME ] ) : array();
    $sanitized_options = errclp_sanitize_options( $posted_options );

    update_option( ERRCLP_OPTION_NAME, $sanitized_options );
    wp_send_json_success( array( 'message' => __( 'Settings saved successfully!', 'err-custom-login-page' ) ) );
}

/**
 * AJAX handler for resetting settings.
 * REWRITTEN: Renamed function, updated nonces and option names.
 */
add_action( 'wp_ajax_errclp_reset_settings', 'errclp_ajax_reset_settings_callback' );
function errclp_ajax_reset_settings_callback() {
	if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'errclp_reset_settings_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Security error.', 'err-custom-login-page' ) ), 403 );
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'err-custom-login-page' ) ), 403 );
	}

	update_option( ERRCLP_OPTION_NAME, errclp_get_default_options() );
	wp_send_json_success( array( 'message' => __( 'Settings have been reset.', 'err-custom-login-page' ) ) );
}

/**
 * Get default options.
 * REWRITTEN: Renamed function with the new prefix.
 */
function errclp_get_default_options() {
	return array(
		'logo_image_url'          => '',
		'logo_image_id'           => 0,
		'logo_target_url'         => '',
		'logo_height'             => '',
		'body_bg_image_url'       => '',
		'body_bg_color'           => '#f0f0f1',
		'form_bg_color'           => '#ffffff',
		'form_bg_color_opacity'   => '1',
		'form_backdrop_blur'      => '0',
		'form_border_width'       => '',
		'form_border_color'       => '',
		'form_border_color_opacity' => '1',
		'form_border_radius'      => '',
		'form_box_shadow_color'   => '',
		'form_text_color'         => '',
		'form_input_text_color'   => '',
		'form_link_color'         => '',
		'form_link_hover_color'   => '',
		'button_bg_color'         => '',
		'button_text_color'       => '',
		'button_bg_hover_color'   => '',
		'button_text_hover_color' => '',
		'hide_privacy_policy_link' => '0',
		'hide_language_switcher'  => '0',
		'hide_back_to_blog_link'  => '0',
	);
}

/* Donate */
function errclp_donate_link_html() {
	$donate_url = 'https://err-mouse.id.vn/donate';
	printf(
		'<a href="%1$s" target="_blank" rel="noopener noreferrer" class="err-donate-link" aria-label="%2$s"><span>%3$s 🚀</span></a>',
		esc_url( $donate_url ),
		esc_attr__( 'Donate to support this plugin', 'err-custom-login-page' ),
		esc_html__( 'Donate', 'err-custom-login-page' )
	);
}

add_filter( 'plugin_row_meta', 'errclp_plugin_row_meta', 10, 2 );
function errclp_plugin_row_meta( $links, $file ) {
	if ( plugin_basename( __FILE__ ) === $file ) {
		ob_start();
		errclp_donate_link_html();
		$links['donate'] = ob_get_clean();
	}
	return $links;
}