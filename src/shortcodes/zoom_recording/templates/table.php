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
    <?php foreach( $meetings as $meeting ) : ?>
      <tr>
        <td>
          <?php print $meeting->title; ?>
        </td>
        <td>
          <?php
            if( $meeting->recording_count >= 1 ):
              foreach( $meeting->recording_files as $recording ) :
          ?>
            <button>Download <?php print $recording['download_url']; ?></button>

          <?php endforeach; endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
