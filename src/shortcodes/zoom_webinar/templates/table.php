<?php
/*
print '<pre>';
var_dump( $webinarResponse );
print '</pre>';
*/
?>

<h1>Upcoming Webinars</h1>

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
          <a class="button" href="<?php echo esc_url( WPZOOM_Settings::getRegistrationPageLink() ); ?>">Register</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
