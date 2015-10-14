<?php

// Start YOURLS engine
require_once( dirname(__FILE__).'/includes/load-yourls.php' );

// Change this to match the URL of your public interface. Something like: http://your-own-domain-here.com/index.php
$page = YOURLS_SITE . '/index.php' ;

// Insert <head> markup and all CSS & JS files
yourls_html_head();

// Display title
echo "<h1>YOURLS - Your Own URL Shortener</h1>\n";

// Display left hand menu
yourls_html_menu() ;


?>

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


<h2>Bookmarklets</h2>

<p>Bookmark these links:</p>

<p>

<a href="javascript:(function()%7Bvar%20d=document,w=window,enc=encodeURIComponent,e=w.getSelection,k=d.getSelection,x=d.selection,s=(e?e():(k)?k():(x?x.createRange().text:0)),s2=((s.toString()=='')?s:enc(s)),f='<?php echo $page; ?>',l=d.location,p='?url='+enc(l.href)+'&title='+enc(d.title)+'&text='+s2,u=f+p;try%7Bthrow('ozhismygod');%7Dcatch(z)%7Ba=function()%7Bif(!w.open(u))l.href=u;%7D;if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else%20a();%7Dvoid(0);%7D)()" class="bookmarklet">Default</a>

<a href="javascript:(function()%7Bvar%20d=document,w=window,enc=encodeURIComponent,e=w.getSelection,k=d.getSelection,x=d.selection,s=(e?e():(k)?k():(x?x.createRange().text:0)),s2=((s.toString()=='')?s:enc(s)),f='<?php echo $page; ?>',l=d.location,k=prompt(%22Custom%20URL%22),k2=(k?'&keyword='+k:%22%22),p='?url='+enc(l.href)+'&title='+enc(d.title)+'&text='+s2+k2,u=f+p;if(k!=null)%7Btry%7Bthrow('ozhismygod');%7Dcatch(z)%7Ba=function()%7Bif(!w.open(u))l.href=u;%7D;if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else%20a();%7Dvoid(0)%7D%7D)()" class="bookmarklet">Custom</a>

<a href="javascript:(function()%7Bvar%20d=document,s=d.createElement('script');window.yourls_callback=function(r)%7Bif(r.short_url)%7Bprompt(r.message,r.short_url);%7Delse%7Balert('An%20error%20occured:%20'+r.message);%7D%7D;s.src='<?php echo $page; ?>?url='+encodeURIComponent(d.location.href)+'&jsonp=yourls';void(d.body.appendChild(s));%7D)();" class="bookmarklet">Popup</a>

<a href="javascript:(function()%7Bvar%20d=document,k=prompt('Custom%20URL'),s=d.createElement('script');if(k!=null){window.yourls_callback=function(r)%7Bif(r.short_url)%7Bprompt(r.message,r.short_url);%7Delse%7Balert('An%20error%20occured:%20'+r.message);%7D%7D;s.src='<?php echo $page; ?>?url='+encodeURIComponent(d.location.href)+'&keyword='+k+'&jsonp=yourls';void(d.body.appendChild(s));%7D%7D)();" class="bookmarklet">Custom Popup</a>

</p>

<h2>Please note</h2>

<p>Be aware that a public interface <strong>will</strong> attract spammers. You are strongly advised to install anti spam plugins and any appropriate counter measure to deal with this issue.</p>

<?php

// Display page footer
yourls_html_footer();