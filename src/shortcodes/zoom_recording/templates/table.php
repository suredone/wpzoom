<div class="zoom-shortcode zoom-recording-shortcode">
    <h1>Webinar Recordings</h1>
    
    <table>
        <thead>
            <tr>
                <th>Meeting</th>
                <th>Audio</th>
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
                if( $meeting->recording_count >= 1 ) :
                    foreach( $meeting->recording_files as $recording ) :
                        if ( ! wpzoom_is_recording_playable( $recording ) ) {
                            continue;
                        }
                        $mimeType = sprintf( 'audio/%s', strtolower( $recording['file_type'] ) );
                        ?>
                        <audio controls>
                            <source src="<?php echo esc_url( $recording['download_url'] ); ?>" type="<?php echo esc_attr( $mimeType ); ?>">
                        </audio>
                    <?php endforeach; endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
