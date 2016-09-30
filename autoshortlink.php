<?php
/*
Plugin Name: AutoShortLink
Plugin URI: mailto:prasadkirpekar@outlook.com
Description: Forget the hassle of creating shorte.st shortlinks manually. This Plugin do that for you.
Version: 1.0
Author: Prasad Kirpekar
Author URI: https://facebook.com/prasadkirpekar96
License: GPL v2
Copyright: Prasad Kirpekar
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define( 'PLUGIN_PATH', plugins_url( __FILE__ ) );

//Detect and convert links from post/page before saving it.
function asl_translinks($content) {
	$string = $content;
	preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $string, $match);
	$out=$match[0];
	for($i=0;$i<count($out);$i++)
	{
		if(strpos($out[$i],'sh.st')==false){
		$string=str_replace($out[$i],asl_shst($out[$i]),$string);
		}	
	}

		return $string;
}

add_filter('content_save_pre','asl_translinks');

//Get shorte.st url using API and key
function asl_shst($surl){
	$apikey=get_option('AutoShortLink_api_key');
	$url = "https://api.shorte.st/v1/data/url";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array("public-api-token: ".$apikey));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS,"urlToShorten=$surl");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
	$result = curl_exec($ch);
	$statusCode = curl_getInfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($statusCode == 200){
		$json = json_decode($result, true);
		if($json["status"] == "ok"){
		    $shortUrl = $json["shortenedUrl"];
			return $shortUrl;	   
		}
		else{
			return $surl."Failed";
		}
	}
}

function asl_settings(){
    if(current_user_can('manage_options')){
	    $asl_key=get_option('asl_api_key');
	    if(isset($_POST['submitted'])&&check_admin_referer( 'asl_nonce_action', 'asl_nonce_field' )){
		if(isset($_POST['service_key'])){
		    $asl_key=sanitize_text_field($_POST['service_key']);
			//validation of key
			if(strlen($asl_key)==32){
			update_option('asl_api_key',$asl_key);
		echo "<div class='updated fade'><p>Settings Updated! Your New post urls will use this 			$asl_key key</p></div>";
			}
			else{
echo "<div class='error fade'><p>Invalid Key</p></div>";
}
		}
		
		
	    }
		$action_url = $_SERVER['REQUEST_URI'];
		include "admin/options.php";
}

}


function asl_add_settings(){

 
    add_option('asl_api_key',"");
	
}

function asl_admin_settings()
{
			add_options_page('AutoShortLink', 'AutoShortLink', 10, 'autoshortlink', 'asl_settings');
	
}

register_activation_hook(__FILE__, 'asl_add_settings');
add_action('admin_menu','asl_admin_settings');




 
?>
