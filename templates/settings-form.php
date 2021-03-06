<?php
/**
* Settings page template.
*
* @package ZOOMPRESS
*/
defined( 'ABSPATH' ) || exit;

$jwtKeys = get_option( 'wp_zoom_keys' );
$key = ZOOMPRESS_Settings::getTokenKey();
$secret = ZOOMPRESS_Settings::getTokenSecret();
$pages = ZOOMPRESS_Settings::getPages();
$registration_page = ZOOMPRESS_Settings::getRegistrationPage();
$view_link =  add_query_arg( array( 'page_id' => '#' ), home_url( '/' ) );
?>

<div class="wrap">
    <h1>ZoomPress Settings</h1>
    <p>ZoomPress uses the recommended JWT keys (not oAuth). Enter your JWT keys below.</p>

    <form id="wp-zoom-settings" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ;?>">
        <?php wp_nonce_field( ZOOMPRESS_Settings::NONCE_ACTION_KEY ); ?>
        <input name='action' type="hidden" value="<?php echo ZOOMPRESS_Settings::NONCE_ACTION_KEY; ?>">

        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="field-zoom-key">Token Key</label></th>
                <td>
                    <input id="field-zoom-key" class="regular-text" name="field_zoom_key" type="text" value="<?php echo esc_attr( $key ); ?>" />
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="field-zoom-secret">Token Secret</label></th>
                <td>
					<input id="field-zoom-secret" class="regular-text" name="field_zoom_secret" type="password" value="<?php echo esc_attr( $secret ); ?>" />
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="zoompress-registration-page">Webinar Registration Page</label></th>
                <td>
                    <select name="field_registration_page" id="zoompress-registration-page">
                    <?php foreach ( $pages as $page_id => $page_title ) : ?>
                        <option value="<?php echo $page_id; ?>" <?php selected( $registration_page, $page_id ); ?>><?php echo esc_html( $page_title ); ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <p class="submit" style="display: inline-block">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"><span class="spinner"></span>
		</p>
    </form>

</div>
