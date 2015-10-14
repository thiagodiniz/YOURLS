<?php

// Start YOURLS engine
require_once( dirname(__FILE__).'/includes/load-yourls.php' );

// Change this to match the URL of your public interface. Something like: http://your-own-domain-here.com/index.php
// $page = YOURLS_SITE . '/index.php' ;

// Insert <head> markup and all CSS & JS files
//yourls_html_head();

?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="/js/jquery-1.9.1.min.js?v=1.7.1" type="text/javascript"></script>
    <script src="/js/common.js?v=1.7.1" type="text/javascript"></script>
    <script src="/js/jquery.notifybar.js?v=1.7.1" type="text/javascript"></script>

    <link rel="stylesheet" href="http://www.produto.io/css/main.css" type="text/css" media="screen">

    <link rel="stylesheet" href="/css/style.css?v=1.7.1" type="text/css" media="screen">
    <link rel="stylesheet" href="/css/layout.css?v=1.7.1" type="text/css" media="screen">
    <link rel="stylesheet" href="/css/tablesorter.css?v=1.7.1" type="text/css" media="screen">
    <script src="/js/jquery.tablesorter.min.js?v=1.7.1" type="text/javascript"></script>
  </head>
  <body class="index desktop">
    <header class="site-header">
    <div class="wrapper">
      <h1 class="logo">
        <a class="site-title" href="/">Produto.tips</a>
      </h1>

      <nav class="site-nav">
        <a href="#" class="menu-icon">
          <svg viewBox="0 0 18 15">
            <path fill="#424242" d="M18,1.484c0,0.82-0.665,1.484-1.484,1.484H1.484C0.665,2.969,0,2.304,0,1.484l0,0C0,0.665,0.665,0,1.484,0 h15.031C17.335,0,18,0.665,18,1.484L18,1.484z"></path>
            <path fill="#424242" d="M18,7.516C18,8.335,17.335,9,16.516,9H1.484C0.665,9,0,8.335,0,7.516l0,0c0-0.82,0.665-1.484,1.484-1.484 h15.031C17.335,6.031,18,6.696,18,7.516L18,7.516z"></path>
            <path fill="#424242" d="M18,13.516C18,14.335,17.335,15,16.516,15H1.484C0.665,15,0,14.335,0,13.516l0,0 c0-0.82,0.665-1.484,1.484-1.484h15.031C17.335,12.031,18,12.696,18,13.516L18,13.516z"></path>
          </svg>
        </a>

        <div class="trigger">
  				<a class="page-link" href="http://produto.io">Sobre</a>
          <a class="page-link" href="http://produto.io/artigos/">Artigos</a>
          <a class="page-link" href="http://produto.io/signup/">Faça parte</a>
        </div>
      </nav>
    </div>
  </header>

  <div id="wrap">
    <h2>Links</h2>
<?php
  $table_url = YOURLS_DB_TABLE_URL;
  $where = '';

  yourls_table_head();
  yourls_table_tbody_start();

  // Main Query
  $where = yourls_apply_filter( 'admin_list_where', $where );
  $url_results = $ydb->get_results( "SELECT * FROM `$table_url` WHERE 1=1 $where ORDER BY `timestamp` DESC;" );
  $found_rows = false;

  if( $url_results ) {
          $found_rows = true;
          foreach( $url_results as $url_result ) {
                  $keyword = yourls_sanitize_string( $url_result->keyword );
                  $timestamp = strtotime( $url_result->timestamp );
                  $url = stripslashes( $url_result->url );
                  $ip = $url_result->ip;
                  $title = $url_result->title ? $url_result->title : '';
                  $clicks = $url_result->clicks;

                  echo yourls_table_add_row( $keyword, $url, $title, $ip, $clicks, $timestamp );
           }
  }

  $display = $found_rows ? 'display:none' : '';
  echo '<tr id="nourl_found" style="'.$display.'"><td colspan="6">' . yourls__('No URL') . '</td></tr>';

  yourls_table_tbody_end();
  yourls_table_end();
?>



<?php

// Display page footer
yourls_html_footer();