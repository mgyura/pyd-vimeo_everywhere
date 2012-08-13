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
            <?php delete_transient( 'pyd_vimeo_admin_albums' ); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Your Vimeo Account Name</th>
                    <td>
                        <input type="text" name="pyd_vimeo_videos[username]" value="<?php echo $pyd_vimeo_user_data[ 'username' ]; ?>" /><br />
                        <small>Enter in the ID of the user account to use, an email address will NOT work.  Example: wpprof</small>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Dashboard Video Display Title</th>
                    <td>
                        <input type="text" name="pyd_vimeo_videos[title]" value="<?php echo $pyd_vimeo_user_data[ 'title' ]; ?>" /><br />
                        <small>What would you like the link on the dashboard menu to be?</small>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Video Dashboard Menu Location</th>
                    <td>
                        <select name="pyd_vimeo_videos[admin_menu]">
                            <option value="index.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'index.php' ) {
                                echo 'selected="selected"';
                            } ?>> Dashboard </option>
                            <option value="edit.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'edit.php' ) {
                                echo 'selected="selected"';
                            } ?>> Posts </option>
                            <option value="upload.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'upload.php' ) {
                                echo 'selected="selected"';
                            } ?>> Media </option>
                            <option value="link-manager.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'link-manager.php' ) {
                                echo 'selected="selected"';
                            } ?>> Links </option>
                            <option value="edit.php?post_type=page" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'edit.php?post_type=page' ) {
                                echo 'selected="selected"';
                            } ?>> Pages </option>
                            <option value="edit-comments.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'edit-comments.php' ) {
                                echo 'selected="selected"';
                            } ?>> Comments </option>
                            <option value="themes.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'themes.php' ) {
                                echo 'selected="selected"';
                            } ?>> Appearance </option>
                            <option value="plugins.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'plugins.php' ) {
                                echo 'selected="selected"';
                            } ?>> Plugins </option>
                            <option value="users.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'users.php' ) {
                                echo 'selected="selected"';
                            } ?>> Users </option>
                            <option value="tools.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'tools.php' ) {
                                echo 'selected="selected"';
                            } ?>> Tools </option>
                            <option value="options-general.php" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'options-general.php' ) {
                                echo 'selected="selected"';
                            } ?>> Settings </option>
                            <?php


                            //Run through and print out any custom post types'
                            $pyd_CPT_args = array(
                                'public'   => true,
                                '_builtin' => false
                            );

                            $post_types = get_post_types( $pyd_CPT_args );

                            if ( $post_types ) {
                                foreach ( $post_types as $post_type ) {
                                    ?>
                                    <option value="edit.php?post_type=<?php echo $post_type; ?>" <?php if ( $pyd_vimeo_user_data[ 'admin_menu' ] == 'edit.php?post_type=' . $post_type ) {
                                        echo 'selected="selected"';
                                    } ?>> CPT: <?php echo $post_type; ?> </option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
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

            <?php
            if ( $pyd_vimeo_user_data[ 'username' ] != null ) {
                ?>
                <hr />

                <h2>Select the albums to show in <?php echo $pyd_vimeo_user_data[ 'title' ]; ?></h2>
                <?php

                $pyd_vimeo_album_ids = unserialize( file_get_contents( 'http://vimeo.com/api/v2/' . $pyd_vimeo_user_data[ 'username' ] . '/albums.php' ) );

                echo '<div class="pyd_vimeo_checkboxes">';
                foreach ( $pyd_vimeo_album_ids as $albumid => $albumvalue ) {
                    if ( $albumvalue ) {
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
                    else {
                        echo 'No public Vimeo albums found on your account.';
                    }
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
        add_submenu_page( 'options-general.php', 'Vimeo Everywhere', 'Vimeo Everywhere', 'edit_posts', 'vimeovideos-settings', 'pyd_vimeo_videos_option_settings' );
    }

    add_action( 'admin_menu', 'pyd_vimeo_album_settings_menu' );