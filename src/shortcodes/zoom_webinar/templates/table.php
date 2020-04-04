<?php
/*
print '<pre>';
var_dump( $webinarResponse );
print '</pre>';
*/
?>
<table>
  <thead>
    <tr>
      <th>Title</th>
      <th>Start</th>
      <th>Duration</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $webinars as $webinar ) : ?>
      <tr>
        <td>
          <?php print $webinar->title; ?>
        </td>
        <td>
          <?php print $webinar->start; ?>
        </td>
        <td>
          <?php print $webinar->duration; ?> Minutes
        </td>
        <td>
          <a class="button" href="<?php echo esc_url( add_query_arg( array( 'wid' => $webinar->id ), WPZOOM_Settings::getRegistrationPageLink() ) ); ?>">Register</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
