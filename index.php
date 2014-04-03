<?php
/**
 * Plugin Name: Feeed Me
 * Plugin URI: http://www.newelementdesigns.com
 * Description: Twitter, Facebook and Instagram feed data.
 * Version: 1.0
 * Author: New Element Designs
 * Author URI: http://www.newelementdesigns.com
 * License: MIT
 */
 
include('FeeedmeSocial.php');

define('FMOPTIONS_ID', 'feeedme-options');
define('FMPLUGINOPTIONS_NICK', 'Feeed Me Settings');

$FeeedmeSocial = new FeeedmeSocial();

function feeedme_twitter(){
   global $FeeedmeSocial;
   return $FeeedmeSocial->twitterFeed(); 
    
}


function feeedme_facebook(){
    global $FeeedmeSocial;
    return $FeeedmeSocial->facebookFeed();
    
}


function feeedme_instagram(){
    global $FeeedmeSocial;
    return $FeeedmeSocial->instagramFeed();
    
}



function feeedme_truncate($text, $chars = 137) {
    
    $text = $text." ";
    $text = substr($text,0,$chars);
    $text = substr($text,0,strrpos($text,' '));
    $text = $text."...";
    
    return $text;

}



function feeedme_parseTwitterText($text) {

    $returnText = $text;
    $hashPattern = '/\#([A-Za-z0-9\_]+)/i';
    $mentionPattern = '/\@([A-Za-z0-9\_]+)/i';
    $urlPattern = '/(http[s]?\:\/\/[^\s]+)/i';
    $robotsFollow = false;

    // SCAN FOR LINKS FIRST!!! Otherwise it will replace the hashes and mentions
    $returnText = preg_replace($urlPattern, '<a href="$1" ' . (($robotsFollow)? '':'rel="nofollow"') . ' target="_blank">$1</a>', $returnText);
    $returnText = preg_replace($hashPattern, '<a href="https://twitter.com/#!/search?q=%23$1" ' . (($robotsFollow)? '':'rel="nofollow"') . ' target="_blank">#$1</a>', $returnText);
    $returnText = preg_replace($mentionPattern, '<a href="https://twitter.com/$1" ' . (($robotsFollow)? '':'rel="nofollow"') . ' target="_blank">@$1</a>', $returnText);
    
    return $returnText;
}




function feeedme_parseInstagramText($text) {

    $returnText = $text;
    $hashPattern = '/\#([A-Za-z0-9\_]+)/i';
    $mentionPattern = '/\@([A-Za-z0-9\_]+)/i';
    $urlPattern = '/(http[s]?\:\/\/[^\s]+)/i';
    $robotsFollow = false;

    // SCAN FOR LINKS FIRST!!! Otherwise it will replace the hashes and mentions
    $returnText = preg_replace($urlPattern, '<a href="$1" ' . (($robotsFollow)? '':'rel="nofollow"') . ' target="_blank">$1</a>', $returnText);
    //$returnText = preg_replace($hashPattern, '<a href="http://instagram.com/#!/search?q=%23$1" ' . (($robotsFollow)? '':'rel="nofollow"') . ' target="_blank">#$1</a>', $returnText);
    $returnText = preg_replace($mentionPattern, '<a href="http://instagram.com/$1" ' . (($robotsFollow)? '':'rel="nofollow"') . ' target="_blank">@$1</a>', $returnText);
    
    return $returnText;
}



function feeedme_prettyDate($date){
    
    $time = strtotime($date);
    $now = time();
    $ago = $now - $time;
    if($ago < 60){
        $when = round($ago);
        $s = ($when == 1)?"second":"seconds";
        return "$when $s ago";
    }elseif($ago < 3600){
        $when = round($ago / 60);
        $m = ($when == 1)?"minute":"minutes";
        return "$when $m ago";
    }elseif($ago >= 3600 && $ago < 86400){
        $when = round($ago / 60 / 60);
        $h = ($when == 1)?"hour":"hours";
        return "$when $h ago";
    }elseif($ago >= 86400 && $ago < 2629743.83){
        $when = round($ago / 60 / 60 / 24);
        $d = ($when == 1)?"day":"days";
        return "$when $d ago";
    }elseif($ago >= 2629743.83 && $ago < 31556926){
        $when = round($ago / 60 / 60 / 24 / 30.4375);
        $m = ($when == 1)?"month":"months";
        return "$when $m ago";
    }else{
        $when = round($ago / 60 / 60 / 24 / 365);
        $y = ($when == 1)?"year":"years";
        return "$when $y ago";
    }

}


// Includes the options page form
function feeedme_options_page(){
    
    if (!current_user_can('manage_options')) {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    
    $plugin_id = FMOPTIONS_ID;
    
    include(ABSPATH.'wp-content/plugins/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'options.php');
    
}



function feeedme_register_settings(){
		
    register_setting(FMOPTIONS_ID.'_options', 'feeedme_twitter_username');
    register_setting(FMOPTIONS_ID.'_options', 'feeedme_facebook_id');
    register_setting(FMOPTIONS_ID.'_options', 'feeedme_instagram_id');
		
    register_setting(FMOPTIONS_ID.'_options', 'feeedme_twitter_limit');
    register_setting(FMOPTIONS_ID.'_options', 'feeedme_facebook_limit');
    register_setting(FMOPTIONS_ID.'_options', 'feeedme_instagram_limit');
		
}



function feeedme_menu(){
    add_options_page(FMPLUGINOPTIONS_NICK.' Settings', FMPLUGINOPTIONS_NICK, 'manage_options', FMOPTIONS_ID.'_options', 'feeedme_options_page');
}
 
 

if ( is_admin() ){
    add_action('admin_init', 'feeedme_register_settings' );	
	add_action('admin_menu', 'feeedme_menu');
}

?>