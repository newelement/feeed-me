
<div class="wrap">
	
    <?php screen_icon(); ?>
    
	<form action="options.php" method="post" id="<?php echo $plugin_id; ?>_options_form" name="<?php echo $plugin_id; ?>_options_form">
    
	    <?php 
	        settings_fields($plugin_id.'_options'); 
    	    do_settings_sections( $plugin_id.'_options' );
    	    
    	    $twitter_limit = ( get_option('feeedme_twitter_limit') )? get_option('feeedme_twitter_limit') : '4' ;
    	    $facebook_limit = ( get_option('feeedme_facebook_limit') )? get_option('feeedme_facebook_limit') : '4' ;
    	    $instagram_limit = ( get_option('feeedme_instagram_limit') )? get_option('feeedme_instagram_limit') : '4' ;
    	    
	    ?>
    
        <h2>Feeed Me Settings</h2>
        
        <table class="form-table">
            
            <tr valign="top">
                <th scope="row">Twitter Username</th>
                <td>@<input type="text" name="feeedme_twitter_username" value="<?php echo get_option('feeedme_twitter_username'); ?>" /><br>
                Twitter username without @ symbol</td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Twitter Limit</th>
                <td><input type="text" name="feeedme_twitter_limit" value="<?php echo $twitter_limit; ?>" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Facebook ID</th>
                <td><input type="text" name="feeedme_facebook_id" value="<?php echo get_option('feeedme_facebook_id'); ?>" /><br>
                <a href="https://www.facebook.com/note.php?note_id=91532827198" target="_blank">Look here</a> or <a href="http://findmyfacebookid.com/" target="_blank">here</a> to find the Facebook ID</td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Facebook Limit</th>
                <td><input type="text" name="feeedme_facebook_limit" value="<?php echo $facebook_limit; ?>" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Instagram ID</th>
                <td><input type="text" name="feeedme_instagram_id" value="<?php echo get_option('feeedme_instagram_id'); ?>" /><br>
                <a href="http://jelled.com/instagram/lookup-user-id" target="_blank">Look here</a> or <a href="http://instagram-uid.herokuapp.com/" target="_blank">here</a> to find the Instagram ID
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Instagram Limit</th>
                <td><input type="text" name="feeedme_instagram_limit" value="<?php echo $instagram_limit; ?>" /></td>
            </tr>
             
        </table>
        
        <?php submit_button(); ?>
    
	</form>
    
</div>