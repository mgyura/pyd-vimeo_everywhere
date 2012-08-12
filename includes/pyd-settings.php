<?php
    /**
     * User: mgyura
     * Date: 8/8/12
     */


    /*-----------------------------------------------------------------------------------*/
    /* Settings for Vimeo Albums Display */
    /*-----------------------------------------------------------------------------------*/

    function pyd_vimeo_videos_option_settings() {
        global $pyd_vimeo_username;

        ?>

    <div class="wrap">
        <h2>Vimeo Videos Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'pyd-vimeo-videos-group' ); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Your Vimeo Account Name</th>
                    <td>
                        <input type="text" name="pyd_vimeo_videos[username]" value="<?php echo $pyd_vimeo_username[ 'username' ]; ?>" /><br />
                        <small>Enter in the ID of the user account to use, an email address will NOT work.  Example: wpprof</small>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Admin Video Display Title</th>
                    <td>
                        <input type="text" name="pyd_vimeo_videos[title]" value="<?php echo $pyd_vimeo_username[ 'title' ]; ?>" /><br />
                        <small>What would you like the link on the dashboard menu to be?</small>
                    </td>
                </tr>
            </table>

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