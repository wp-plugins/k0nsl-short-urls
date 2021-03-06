<?php
/*
Plugin Name: k0nsl Short URLs
Plugin URI: http://k0nsl.org/blog/k0nsl-short-urls-plugin/
Description: Automatically shortens the blog post URL via knsl.net
Version: 0.3a
Author: k0nsl
Author URI: http://k0nsl.org/blog/
*/

define('DEFAULT_API_URL', 'http://knsl.net/api.php?url=%s');
define( 'k0nsl_plugin_path', plugin_dir_path(__FILE__) );

/* returns a result from url */
if ( ! function_exists( 'curl_get_url' ) ){
  function curl_get_url($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
 }
}

if ( ! function_exists( 'get_k0nsl_url' ) ){ /* what's the odds of that? */
 function get_k0nsl_url($url,$format='txt') {
   $connectURL = 'http://knsl.net/api.php?url='.$url;
   return curl_get_url($connectURL);
 }
}

if ( ! function_exists( 'k0nsl_show_url' ) ){
 function k0nsl_show_url($showurl) { /* use with echo statement */
  $url_create = get_k0nsl_url(get_permalink( $id ));

  $kshort .= '<a href="'.$url_create.'" target="_blank">'.$url_create.'</a>';
  return $kshort;
 }
}

if ( ! function_exists( 'k0nsl_shortcode_handler' ) ){
 function k0nsl_shortcode_handler( $atts, $text = null, $code = "" ) {	
	extract( shortcode_atts( array( 'u' => null ), $atts ) );
	
	$url = get_k0nsl_url( $u );
	
	if( !$text )
		return $url;
	
	return '<a href="' .$url. '">' .$text. '</a>';
 }
}
add_shortcode('knsl-url', 'k0nsl_shortcode_handler');

class k0nsl_Short_URL
{
    const META_FIELD_NAME='Shorter link';	
	
    /**
     * List of short URL website API URLs (only knsl.net for now)
     */
    function api_urls()
    {
        return array(
            array(
                'name' => '',
                'url'  => '',
                ),
            array(
                'name' => 'knsl.net',
                'url'  => 'http://knsl.net/api.php?url=%s',
                ),
            );
    }

    /**
     * Create short URL based on post URL
     */
    function create($post_id)
    {
        if (!$apiURL = get_option('k0nslShortUrlApiUrl')) {
            $apiURL = DEFAULT_API_URL;
        }

        // For some reason the post_name changes to /{id}-autosave/ when a post is autosaved
        $post = get_post($post_id);
        $pos = strpos($post->post_name, 'autosave');
        if ($pos !== false) {
            return false;
        }
        $pos = strpos($post->post_name, 'revision');
        if ($pos !== false) {
            return false;
        }

        $apiURL = str_replace('%s', urlencode(get_permalink($post_id)), $apiURL);

        $result = false;

        if (ini_get('allow_url_fopen')) {
            if ($handle = @fopen($apiURL, 'r')) {
                $result = fread($handle, 4096);
                fclose($handle);
            }
        } elseif (function_exists('curl_init')) {
            $ch = curl_init($apiURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $result = @curl_exec($ch);
            curl_close($ch);
        }

        if ($result !== false) {
            delete_post_meta($post_id, 'knslShortURL');
            $res = add_post_meta($post_id, 'knslShortURL', $result, true);
            return true;
        }
    }

    /**
     * Option list (default settings)
     */
    function options()
    {
        return array(
           'ApiUrl'         => DEFAULT_API_URL,
           'Display'        => 'Y',
           'TwitterLink'    => 'Y',
           );
    }

    /**
     * Plugin settings
     *
     */
    function settings()
    {
        $apiUrls = $this->api_urls();
        $options = $this->options();
        $opt = array();

        if (!empty($_POST)) {
            foreach ($options AS $key => $val)
            {
                if (!isset($_POST[$key])) {
                    continue;
                }
                update_option('knslShortURL' . $key, $_POST[$key]);
            }
        }
        foreach ($options AS $key => $val)
        {
            $opt[$key] = get_option('knslShortURL' . $key);
        }
        include k0nsl_plugin_path . 'template/settings.tpl.php';
    }

    /**
     *
     */
    function admin_menu()
    {
        add_options_page('k0nsl Short URL', 'Short URLs', 10, 'k0nsl_shorturl-settings', array(&$this, 'settings'));
    }

    /**
     * Display the short URL
     */
    function display($content)
    {

        global $post;

        if ($post->ID <= 0) {
            return $content;
        }

        $options = $this->options();

        foreach ($options AS $key => $val)
        {
            $opt[$key] = get_option('knslShortURL' . $key);
        }

        $shortUrl = get_post_meta($post->ID, 'knslShortURL', true);

        if (empty($shortUrl)) {
            return $content;
        }

        $shortUrlEncoded = urlencode($shortUrl);

        ob_start();
        include k0nsl_plugin_path . 'template/public.tpl.php';
        $content .= ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function pre_get_shortlink($false, $id, $context=null, $allow_slugs=null) /* Thanks to Rob Allen */
    {
        // get the post id
        global $wp_query;
        if ($id == 0) {
            $post_id = $wp_query->get_queried_object_id();
        } else {
            $post = get_post($id);
            $post_id = $post->ID;
        }

        $short_link = get_post_meta($post_id, self::META_FIELD_NAME, true);
        if('' == $short_link) {
            $short_link = $post_id;
        }

        $url = get_k0nsl_url(get_permalink( $id ));
        if (!empty($url)) {
            $short_link = $url;
        } else {
            $short_link = home_url($short_link);
        }
        return $short_link;
    }

}

$knsl = new k0nsl_Short_URL;

if (is_admin()) {
    add_action('edit_post', array(&$knsl, 'create'));
    add_action('save_post', array(&$knsl, 'create'));
    add_action('publish_post', array(&$knsl, 'create'));
    add_action('admin_menu', array(&$knsl, 'admin_menu'));
    add_filter('pre_get_shortlink',  array(&$knsl, 'pre_get_shortlink'), 10, 4);
} else {
    add_filter('the_content', array(&$knsl, 'display'));
}