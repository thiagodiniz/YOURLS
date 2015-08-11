<?php
/*
 * YOURLS API
 *
 * Note about translation : this file should NOT be translation ready
 * API messages and returns are supposed to be programmatically tested, so default English is expected
 *
 */

define( 'YOURLS_API', true );
require_once( dirname( __FILE__ ) . '/includes/load-yourls.php' );
// token=e0xywzLDHsQPUwhC6lJQIlwP
//  $_REQUEST['token']

$slack_arguments = explode(" ",  $_REQUEST['text'] );
$slack_user_name = $_REQUEST['user_name'];
$channel_name = $_REQUEST['channel_name'];
$is_not_channel = ($channel_name == 'privatechannel') || ($channel_name == 'directmessage');


$_REQUEST['url'] = $slack_arguments[0];

if (array_key_exists(1, $slack_arguments)) {
    $_REQUEST['keyword'] = $slack_arguments[1];
}

if ( $_REQUEST['token'] != 'e0xywzLDHsQPUwhC6lJQIlwP'){

   $format = ( isset($_REQUEST['format']) ? $_REQUEST['format'] : 'xml' );
   $callback = ( isset($_REQUEST['callback']) ? $_REQUEST['callback'] : '' );
   yourls_api_output( $format, array(
      'simple' => false,
      'message' => 'Slack token wrong',
      'errorCode' => 403,
      'callback' => $callback,
   ) );

   die();
}

error_log("channel:" . $_REQUEST['channel_name'] . " user_name:" . $_REQUEST['user_name'] ." channel_id:" . $_REQUEST['channel_id']);


// https://hooks.slack.com/services/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i
$action = ( isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : null );

yourls_do_action( 'api', $action );

// Define standard API actions
$api_actions = array(
        'shorturl'  => 'yourls_api_action_shorturl',
        'stats'     => 'yourls_api_action_stats',
        'db-stats'  => 'yourls_api_action_db_stats',
        'url-stats' => 'yourls_api_action_url_stats',
        'expand'    => 'yourls_api_action_expand',
        'version'   => 'yourls_api_action_version',
);
$api_actions = yourls_apply_filter( 'api_actions', $api_actions );

// Register API actions
foreach( (array) $api_actions as $_action => $_callback ) {
        yourls_add_filter( 'api_action_' . $_action, $_callback, 99 );
}

// Try requested API method. Properly registered actions should return an array.
$return = yourls_apply_filter( 'api_action_' . $action, false );
if ( false === $return ) {
        $return = array(
                'errorCode' => 400,
                'message'   => 'Unknown or missing "action" parameter',
                'simple'    => 'Unknown or missing "action" parameter',
        );
}




if($is_not_channel){
  $message_with_link = 'Sorry, I am still figuring out how to share links on '. $channel_name . ', but here is your shortlink:' . $shorturl;

   /*yourls_api_output( "simple", array(
      'simple' =>  $message_with_link
   ) );*/

  $channel_name = $_REQUEST['channel_id'];
} else {
  $channel_name = "#".$channel_name;
}

$shorturl  = "<". $return['simple'].">";
$slack_user_name = 'produto.tips por ' . $slack_user_name ;

$return = array(
  'channel' => $channel_name,
  'username' => ,
  'text' => $shorturl,
  'unfurl_links' => true,
  'icon_emoji' => ':link:'
);

$json = json_encode($return);
$headers = array('Content-Type' => 'application/json');
$webhook = "https://hooks.slack.com/services/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";

$response = yourls_http_post($webhook, $headers, $json);

error_log("slack_response: " . $response->status_code . " url:" . $shorturl );

die();
