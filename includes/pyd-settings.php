<?php
    /**
     * User: mgyura
     * Date: 8/8/12
     */


    /*-----------------------------------------------------------------------------------*/
    /* Settings for Vimeo Albums Display */
    /*-----------------------------------------------------------------------------------*/

    function pyd_vimeo_videos_option_settings() {
        global $pyd_vimeo_user_data;
        ?>

    <div class="wrap">
        <h2>Vimeo Videos Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'pyd-vimeo-videos-group' ); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Your Vimeo Account Name</th>
                    <td>
                        <input type="text" name="pyd_vimeo_videos[username]" value="<?php echo $pyd_vimeo_user_data[ 'username' ]; ?>" /><br />
                        <small>Enter in the ID of the user account to use, an email address will NOT work.  Example: wpprof</small>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Admin Video Display Title</th>
                    <td>
                        <input type="text" name="pyd_vimeo_videos[title]" value="<?php echo $pyd_vimeo_user_data[ 'title' ]; ?>" /><br />
                        <small>What would you like the link on the dashboard menu to be?</small>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"></th>
                    <td>
                        <input type="hidden" size="6" name="pyd_vimeo_videos[admin_vid_width]" value="<?php echo $pyd_vimeo_user_data[ 'admin_vid_width' ]; ?>" />
                        <input type="hidden" size="6" name="pyd_vimeo_videos[admin_vid_height]" value="<?php echo $pyd_vimeo_user_data[ 'admin_vid_height' ]; ?>" />

                    </td>
                </tr>
            </table>
            <hr />

            <h2>Select the albums to show in <?php echo $pyd_vimeo_user_data[ 'title' ]; ?></h2>

            <?php
            if ( $pyd_vimeo_user_data[ 'username' ] != null ) {

                $pyd_vimeo_album_ids = unserialize( file_get_contents( 'http://vimeo.com/api/v2/' . $pyd_vimeo_user_data[ 'username' ] . '/albums.php' ) );

                echo '<div class="pyd_vimeo_checkboxes">';
                foreach ( $pyd_vimeo_album_ids as $albumid => $albumvalue ) {
                    ?>
                    <div class="pyd_vimeo_checkbox">
                        <input type="checkbox" name="pyd_vimeo_videos[admin_albums][<?php echo $albumvalue[ 'id' ] ?>]" id="<?php echo $albumvalue[ 'id' ] ?>" value="<?php echo $albumvalue[ 'id' ] ?>" <?php if ( isset( $pyd_vimeo_user_data[ 'admin_albums' ][ $albumvalue[ 'id' ] ] ) ) {
                            echo 'checked="checked"';
                        } ?> />
                        <label for="<?php echo $albumvalue[ 'id' ] ?>">
                            <?php echo $albumvalue[ 'title' ] ?>
                        </label>

                    </div>
                    <?php
                }
                echo '<div class="pydClear"></div></div>';
            }
            ?>

            <p class="submit">
                <input type="submit" class="button-primary" value="Save" />
            </p>

        </form>

    </div>

    <?php
    }


    /*-----------------------------------------------------------------------------------*/
    /* Create settings menu for Vimeo Videos */
    /*-----------------------------------------------------------------------------------*/

    function pyd_vimeo_album_settings_menu() {
        add_submenu_page( 'options-general.php', 'Vimeo Videos', 'Vimeo Videos', 'edit_posts', 'vimeovideos-settings', 'pyd_vimeo_videos_option_settings' );
    }

    add_action( 'admin_menu', 'pyd_vimeo_album_settings_menu' );