<?php


$jwtKeys = get_option( 'wp_zoom_keys' );
$key = '';
$secret = '';
if( $jwtKeys || !empty( $jwtKeys ) || is_array( $jwtKeys )) {
  if( isset( $jwtKeys['key'] )) {
    $key= $jwtKeys['key'];
  }
  if( isset( $jwtKeys['secret'] )) {
    $secret = $jwtKeys['secret'];
  }
}

?>

<div class="wrap">

  <h1>Zoom API Key Settings</h1>
  <p>WP Zoom uses the recommended JWT keys (not oAuth). Enter your JWT keys below.</p>

    <form action="admin-post.php" method="post">

      <input name='action' type="hidden" value='zoom_settings_process'>
      <?php wp_nonce_field(); ?>

      <table class="form-table" role="presentation">

        <tr>
          <th scope="row"><label for="default_category">Token Key</label></th>
          <td>
            <input style="min-width:220px;" id="field-zoom-key" name="field-zoom-key" type="text" value="<?php print $key; ?>" />
          </td>
        </tr>

        <tr>
          <th scope="row"><label for="default_category">Token Secret</label></th>
          <td>
            <input style="min-width:220px;" id="field-zoom-secret" name="field-zoom-secret" type="password" value="<?php print $secret; ?>" />
          </td>
        </tr>

      </table>

      <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>

    </form>

</div>
