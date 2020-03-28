<div class="zoom-shortcode zoom-recording-shortcode">

  <h1>Webinar Recordings</h1>

  <table>
    <thead>
      <tr>
        <th>Meeting</th>
        <th>Files</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach( $meetings as $meeting ) :
        $dateTime = new DateTime( $meeting->start );
        $dateTime->setTimezone( wpzoom_timezone() ); // Had to set forcefully
        $startTime = $dateTime->format( 'Y-m-d' ) . ' at ' . $dateTime->format( 'g:iA' );
        ?>
        <tr>
          <td>
            <h2><?php print $meeting->title; ?></h2>
            <div class="meeting-start"><?php print $startTime; ?></div>
          </td>
          <td>
            <?php
              if( $meeting->recording_count >= 1 ):
                foreach( $meeting->recording_files as $recording ) :
            ?>
              <div class="download-link">
                <a href="<?php print $recording['download_url']; ?>" target="_blank"><?php print $recording['download_url']; ?></a>
              </div>
            <?php endforeach; endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>
