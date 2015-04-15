<?php
	define('REMOTE_URL', 'http://www.mywebsite.com/');
	define('REMOTE_CLASS', '<div class="my_class"'); //begin parser by special class
	define('REMOTE_IMG_WIDTH', 800);
	define('REMOTE_IMG_HEIGHT', '600');

	try {
		if(REMOTE_URL){ print '<div class="banner"><a href='.REMOTE_URL.'>'.load_content_base_on_site().'</a></div>'; }
		else {throw new Exception('Not defined remote URL'); }
	} catch (Exception $e) { echo '<strong>Catch exception:</strong> '.$e->getMessage(); }


function load_content_base_on_site(){
	if(!defined('AUTH_KEY')){ return explode_remote_content(get_remote_content()); }
	else { return explode_remote_content(WP_get_remote_content());  }
}

function WP_get_remote_content(){
	$data = wp_remote_retrieve_body( wp_remote_get(REMOTE_URL) );
	if(!empty($data)){ return $data; } else { throw new Exception('No remote content');  }
}

function get_remote_content() {
	$data = url_get_contents();
	if (!empty( $data )) { return $data; } else { throw new Exception ( 'Error getting remote content' ); }
}
function explode_remote_content($data){
	if ( preg_match ( '/'.REMOTE_CLASS.'(.*?)<\/div>/s', $data, $matches ) ){
		if(is_array($matches)){ return get_image($matches[0]); } else { return get_image($matches); }
	}
	else { throw new Exception('No remote content was parser'); }
}

function get_image($var){ return strip_tags($var, '<img>'); }

function url_get_contents () {
	if(function_exists('file_get_contents')){
		$url_get_contents_data = file_get_contents(REMOTE_URL);
	}elseif(function_exists('fopen') && function_exists('stream_get_contents')){
		$handle = fopen (REMOTE_URL, "r");
		$url_get_contents_data = stream_get_contents($handle);
	}elseif (function_exists('curl_exec')){
		$conn = curl_init(REMOTE_URL);
		curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($conn, CURLOPT_FRESH_CONNECT,  true);
		curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
		$url_get_contents_data = (curl_exec($conn));
		curl_close($conn);
	}else{
		$url_get_contents_data = false;
	}
	return $url_get_contents_data;
}
?>
<style>
.banner img { width: <?=REMOTE_IMG_WIDTH; ?>px; height: <?=REMOTE_IMG_HEIGHT;?>;}
</style>