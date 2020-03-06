<?php

/*
print '<pre>';
var_dump( $recordingResponse );
print '</pre>';
*/

?>

<h1>Webinar Recordings</h1>

<table>
  <thead>
    <tr>
      <th>Title</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $recordings as $recording ) : ?>
      <tr>
        <td>
          <?php print $recording['topic']; ?>
        </td>
        <td>
          <button>Download</button>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
