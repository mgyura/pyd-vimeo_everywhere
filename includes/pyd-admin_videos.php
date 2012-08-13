<?php
    /**
     * User: mgyura
     * Date: 8/11/12
     */

    /*-----------------------------------------------------------------------------------*/
    /* Create settings menu for Vimeo Videos */
    /*-----------------------------------------------------------------------------------*/

    function pyd_vimeo_admin_menu() {
        global $pyd_vimeo_user_data;

        add_submenu_page( $pyd_vimeo_user_data[ 'admin_menu' ], $pyd_vimeo_user_data[ 'title' ], $pyd_vimeo_user_data[ 'title' ], 'edit_posts', 'vimeovideos-display', 'pyd_vimeo_videos_admin_vids' );
    }

    add_action( 'admin_menu', 'pyd_vimeo_admin_menu' );


    /*-----------------------------------------------------------------------------------*/
    /* Create admin page for display selceted Vimeo Videos */
    /*-----------------------------------------------------------------------------------*/

    function pyd_vimeo_videos_admin_vids() {
        global $pyd_vimeo_user_data;

        if ( isset ($pyd_vimeo_user_data[ 'admin_albums' ] )) {

            //build some cache out of the data
            $pyd_vimeo_admin_albums_get_trans = get_transient( 'pyd_vimeo_admin_albums' );

            if ( !$pyd_vimeo_admin_albums_get_trans ) {

                //run through the raw data and place it into arrays with album names and then make it a level deep to make sure a name conflict doesnt happen.
                foreach ( $pyd_vimeo_user_data[ 'admin_albums' ] as $pyd_vimeo_album => $album ) {
                    $pyd_vimeo_album_info                                         = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $album . '/info.php' ) );
                    $pyd_vimeo_album                                              = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $album . '/videos.php' ) );
                    $pyd_vimeo_album_array[ ][ $pyd_vimeo_album_info[ 'title' ] ] = $pyd_vimeo_album;
                }

                set_transient( 'pyd_vimeo_admin_albums', $pyd_vimeo_album_array, 3600 );
            }

            $pyd_vimeo_admin_albums = get_transient( 'pyd_vimeo_admin_albums' );
            ?>

        <div class="wrap">
            <h2><?php echo $pyd_vimeo_user_data[ 'title' ]; ?></h2>
            <?php

            foreach ( $pyd_vimeo_admin_albums as $pyd_vimeo_album => $albums ) {

                foreach ( $albums as $album => $albumdata ) {
                    ?>

                    <div class="pyd_viemo_table">
                        <h3><? echo $album; ?></h3>
                        <table class="widefat">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>

                                <?php foreach ( $albumdata as $pyd_vimeo_vids ) {

                                $vimeo_date = date( "F d, Y", strtotime( $pyd_vimeo_vids[ 'upload_date' ] ) );
                                $vimeo_mins = gmdate( "i:s", $pyd_vimeo_vids[ 'duration' ] );
                                ?>

                            <tr>
                                <td class="pyd_vimeo_title">

                                    <a href="#TB_inline?height=<?php echo $pyd_vimeo_user_data[ 'admin_vid_height' ]; ?>&amp;width=<?php echo $pyd_vimeo_user_data[ 'admin_vid_width' ]; ?>&amp;inlineId=<?php echo 'pyd_admin_vimeo_' . $pyd_vimeo_vids[ 'id' ]; ?>" title="<?php echo $pyd_vimeo_vids[ 'title' ]; ?>" class="thickbox">
                                        <img src="<?php echo $pyd_vimeo_vids[ 'thumbnail_small' ]; ?>" alt="<?php echo $pyd_vimeo_vids[ 'title' ]; ?>" class="pyd_vimeo_icon" />
                                    </a>

                                    <p>
                                        <a href="#TB_inline?height=<?php echo $pyd_vimeo_user_data[ 'admin_vid_height' ]; ?>&amp;width=<?php echo $pyd_vimeo_user_data[ 'admin_vid_width' ]; ?>&amp;inlineId=<?php echo 'pyd_admin_vimeo_' . $pyd_vimeo_vids[ 'id' ]; ?>" title="<?php echo $pyd_vimeo_vids[ 'title' ]; ?>" class="thickbox">
                                            <?php echo $pyd_vimeo_vids[ 'title' ]; ?>
                                        </a>
                                        <br />
                                        Record date: <?php echo $vimeo_date; ?>
                                        <br />
                                        Duration: <?php echo $vimeo_mins; ?>
                                    </p>

                                </td>

                                <td class="pyd_vimeo_desc">
                                    <?php echo $pyd_vimeo_vids[ 'description' ]; ?>
                                </td>

                            </tr>

                            <div id="<?php echo 'pyd_admin_vimeo_' . $pyd_vimeo_vids[ 'id' ]; ?>" class="pyd_vimeo_video" style="display:none;">
                                <iframe src="http://player.vimeo.com/video/<?php echo $pyd_vimeo_vids[ 'id' ]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;wmode=transparent" width="<?php echo $pyd_vimeo_user_data[ 'admin_vid_width' ]; ?>" height="<?php echo $pyd_vimeo_user_data[ 'admin_vid_height' ]; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                            </div>

                                <?php } ?>

                            </tbody>
                        </table>

                    </div>
                    <?php
                }
            }
            ?>

        </div>
        <?php
        }

        else {
            echo '<div class="wrap"><p>Vimeo Albums need to first be selected before videos will display here.  You can do that <a href="/wp-admin/options-general.php?page=vimeovideos-settings&settings-updated=true" >here</a></p></div>';
        }

    }