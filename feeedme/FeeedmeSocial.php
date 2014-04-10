<?php
class FeeedmeSocial{



/* Getting a JSON Twitter Feed
   ==========================================================================

   1. Sign in as a developer at https://dev.twitter.com/

   2. Click "Create a new application" at https://dev.twitter.com/apps

   3. Under Application Details, find the OAuth settings and the access token
*/

/* Configuring a JSON Twitter Feed
   ==========================================================================

   1. Find the desired twitter username

   2. Set the maximum number of tweets to retrieve

   3. Set the seconds to wait between caching the response
*/

/* Enjoying a JSON Twitter Feed
   ==========================================================================

   Visit this URL and make sure everything is working

   Use JSONP by adding ?callback=YOUR_FUNCTION to this URL

   Tweet love or hate @jon_neal

   Permission errors? http://stackoverflow.com/questions/4917811/file-put-contents-permission-denied
*/

/* Twitter creds */
private $twitterConsumerKey = '6G1oCsqlNSZGaIgNwSt8Q';
private $twitterConsumerSecret = 'UCmIjcSYip6b9QSTzd1WBwEOKw52wFspWLo8Moijc0U';
private $twitterAccessToken = '14474087-c0QCr6wnetwGuGIT74DbRdA0vqS2WFpRePCQxB20p';
private $twitterAccessTokenSecret = '2tSblvQaqkLSOl7lc8BAWEfbJbG2B59iR4neJCraEw';
public $twitterUsername;
public $twitterMaximum;




/* Getting a JSON Facebook Feed
   ==========================================================================

   1. Sign in as a developer at https://developers.facebook.com/

   2. Click "Create New App" at https://developers.facebook.com/apps

   3. Under Apps Settings, find the App ID and App Secret
*/

/* Configuring a JSON Facebook Feed
   ==========================================================================

   1. Find the desired feed ID at http://findmyfacebookid.com/

   2. Set the maximum number of stories to retrieve

   3. Set the seconds to wait between caching the response
*/

/* Enjoying a JSON Facebook Feed
   ==========================================================================

   Visit this URL and make sure everything is working

   Use JSONP by adding ?callback=YOUR_FUNCTION to this URL

   Tweet love or hate @jon_neal

   Permission errors? http://stackoverflow.com/questions/4917811/file-put-contents-permission-denied
*/

/* Facebook creds */
private $facebookAppID = '559671357451070';
private $facebookAppSecret = 'cf4f7f443876c23e4eca011c8e2b0542';
public $facebookFeed;
public $facebookMaximum;




/* Getting a JSON Instagram Feed
   ==========================================================================

*/

/* Instagram creds */
public $instagramUserID;
public $instagramMaximum;
private $instagramAccessToken = '353403.d5ac6a1.bec4fbdf0dd648cb899e74adee46354c';
private $instagramBaseURL = 'https://api.instagram.com/v1';



/* Cache time */
private $caching = 60;


public function __construct()
{

    $this->twitterUsername = get_option('feeedme_twitter_username');
    $this->twitterMaximum = get_option('feeedme_twitter_limit');
    
    $this->facebookFeed = get_option('feeedme_facebook_id');
    $this->facebookMaximum = get_option('feeedme_facebook_limit');
    
    $this->instagramUserID = get_option('feeedme_instagram_id');
    $this->instagramMaximum = get_option('feeedme_instagram_limit');

}


/*

    Twitter

*/



/**
 * fetchTwitterFeed function.
 * 
 * @access private
 * @return string
 */
private function fetchTwitterData(){

    $path = pathinfo(__FILE__);
    $filename = $path['dirname'].'/twitter.json';
    
    $filetime = file_exists($filename) ? filemtime($filename) : time() - $this->caching - 1;
    
    $maximum = $this->twitterMaximum;

    if (time() - $this->caching > $filetime) {
	
	    $utc_str = gmdate("M d Y H:i:s", time());
        $filetime = strtotime($utc_str);
	
    	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    	$base = 'GET&'.rawurlencode($url).'&'.rawurlencode("count={$maximum}&oauth_consumer_key={$this->twitterConsumerKey}&oauth_nonce={$filetime}&oauth_signature_method=HMAC-SHA1&oauth_timestamp={$filetime}&oauth_token={$this->twitterAccessToken}&oauth_version=1.0&screen_name={$this->twitterUsername}");
    	$key = rawurlencode($this->twitterConsumerSecret).'&'.rawurlencode($this->twitterAccessTokenSecret);
    	$signature = rawurlencode(base64_encode(hash_hmac('sha1', $base, $key, true)));
    	$oauth_header = "oauth_consumer_key=\"{$this->twitterConsumerKey}\", oauth_nonce=\"{$filetime}\", oauth_signature=\"{$signature}\", oauth_signature_method=\"HMAC-SHA1\", oauth_timestamp=\"{$filetime}\", oauth_token=\"{$this->twitterAccessToken}\", oauth_version=\"1.0\", ";
    
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Oauth {$oauth_header}", 'Expect:'));
    	curl_setopt($ch, CURLOPT_HEADER, false);
    	curl_setopt($ch, CURLOPT_URL, $url."?screen_name={$this->twitterUsername}&count={$maximum}");
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	$response = curl_exec($ch);
    	curl_close($ch);
    
        //$t = '{"errors":[{"message":"Timestamp out of bounds","code":135}]}';
        
        $json = json_decode($response, true);
        
    
        if( $json["errors"] ){
            $err_code = (int) $json["errors"][0]["code"];
        } else {
            $err_code = false;
        }
    
        if( $err_code && $err_code === 135  ){
    	    $response = file_get_contents($filename);
        } else {
            file_put_contents($filename, $response);
        }
        
    } else {
    
    	$response = file_get_contents($filename);
    }
    
    //return $_GET['callback'] ? $_GET['callback'].'('.$response.')' : $response;

    return $response;

}


/**
 * twitterFeed function.
 * 
 * @access public
 * @return json
 */
public function twitterFeed(){
    
    $result = $this->fetchTwitterData();
    
    $json = json_decode($result, true);
    
    return $json;
    
}








/*

    Facebook

*/


/**
 * fetchFacebookData function.
 * 
 * @access private
 * @return string
 */
private function fetchFacebookData(){
    
    $path = pathinfo(__FILE__);
    $filename = $path['dirname'].'/facebook.json';
    
    $filetime = file_exists($filename) ? filemtime($filename) : time() - $this->caching - 1;
    
    if (time() - $this->caching > $filetime) {
    	
    	$authentication = file_get_contents("https://graph.facebook.com/oauth/access_token?grant_type=client_credentials&client_id={$this->facebookAppID}&client_secret={$this->facebookAppSecret}");
    
    	$response = file_get_contents("https://graph.facebook.com/{$this->facebookFeed}/posts?{$authentication}&limit={$this->facebookMaximum}");
    
    	file_put_contents($filename, $response);
    
    } else {
    	
    	$response = file_get_contents($filename);
    
    }

    //$_GET['callback'] ? $_GET['callback'].'('.$response.')' : $response ;

    return $response ;
    
}


/**
 * facebookFeed function.
 * 
 * @access public
 * @return json
 */
public function facebookFeed(){
    
    $result = $this->fetchFacebookData();
    
    $json = json_decode($result, true);
    
    return $json;
    
}








/*

    INSTAGRAM

*/

/**
 * instagramData function.
 * 
 * @access private
 * @param mixed $url
 * @return string
 */
private function fetchInstagramData($url){
    
    $path = pathinfo(__FILE__);
    $filename = $path['dirname'].'/instagram.json';
    
    $filetime = file_exists($filename) ? filemtime($filename) : time() - $this->caching - 1;
    
    if (time() - $this->caching > $filetime) {
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);
        curl_close($ch); 
        
    	file_put_contents($filename, $response);
    
    } else {
    	
    	$response = file_get_contents($filename);
    
    }
    
    return $response;

}


/**
 * instagramFeed function.
 * 
 * @access public
 * @param int $limit (default: 4)
 * @return json
 */
public function instagramFeed(){
    
    $url = $this->instagramBaseURL."/users/{$this->instagramUserID}/media/recent/?access_token={$this->instagramAccessToken}&count={$this->instagramMaximum}";
    
    $result = $this->fetchInstagramData($url);
    
    $json = json_decode($result, true);
    
    return $json;
}

/*
    Example Instagram data
*/

/*
$json = $Social->instagramFeed();

foreach ($json->data as $post) {

    echo '<a class="instagram-unit" target="blank" href="'.$post->link.'">
        <img src="'.$post->images->thumbnail->url.'" alt="'.$post->caption->text.'" height="auto">
        <div class="instagram-desc">'.htmlentities($post->caption->text).' | '.htmlentities(date("F j, Y, g:i a", $post->caption->created_time)).'</div></a>';

}
*/  


}
?>