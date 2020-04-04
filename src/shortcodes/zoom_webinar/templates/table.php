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
          <?php wpzoom_webinar_register_button( $webinar ); ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
