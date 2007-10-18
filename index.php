<?php // vim: et
/*
   If you're reading this, it isn't because you've found a security hole.
   this is an open source website. read and learn!
*/

/* ------------------------------------------------------------------------- */

// Get the modification date of this PHP file
$timestamps = array(@getlastmod());

/*
   The date of prepend.inc represents the age of ALL
   included files. Please touch it if you modify any
   other include file (and the modification affects
   the display of the index page). The cost of stat'ing
   them all is prohibitive. Also note the file path,
   we aren't using the include path here.
*/
$timestamps[] = @filemtime("include/prepend.inc");

// Calendar, conference teasers & latest releaes box are the only "dynamic" features on this page
$timestamps[] = @filemtime("include/pregen-events.inc");
$timestamps[] = @filemtime("include/pregen-confs.inc");
$timestamps[] = @filemtime("include/pregen-news.inc");
$timestamps[] = @filemtime("include/version.inc");

// The latest of these modification dates is our real Last-Modified date
$timestamp = max($timestamps);

// Note that this is not a RFC 822 date (the tz is always GMT)
$tsstring = gmdate("D, d M Y H:i:s ", $timestamp) . "GMT";

// Check if the client has the same page cached
if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]) &&
    ($_SERVER["HTTP_IF_MODIFIED_SINCE"] == $tsstring)) {
    header("HTTP/1.1 304 Not Modified");
    exit();
}
// Inform the user agent what is our last modification date
else {
    header("Last-Modified: " . $tsstring);
}

$_SERVER['BASE_PAGE'] = 'index.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/prepend.inc';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/pregen-events.inc';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/pregen-confs.inc';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/pregen-news.inc';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/version.inc';

// This goes to the left sidebar of the front page
$SIDEBAR_DATA = '
<h3>What is PHP?</h3>
<p>
 <acronym title="recursive acronym for PHP: Hypertext Preprocessor">PHP</acronym>
 is a widely-used general-purpose scripting language that is
 especially suited for Web development and can be embedded into HTML.
 If you are new to PHP and want to get some idea
 of how it works, try the <a href="/tut.php">introductory tutorial</a>.
 After that, check out the online <a href="/docs.php">manual</a>,
 and the example archive sites and some of the other resources
 available in the <a href="/links.php">links section</a>.
</p>
<p>
 Ever wondered how popular PHP is? see the
 <a href="/usage.php">Netcraft Survey</a>.
</p>

<h3><a href="/thanks.php">Thanks To</a></h3>
<ul class="simple">
 <li><a href="http://www.easydns.com/?V=698570efeb62a6e2" title="DNS Hosting provided by easyDNS">easyDNS</a></li>
 <li><a href="http://www.directi.com/">Directi</a></li>
 <li><a href="http://promote.pair.com/direct.pl?php.net">pair Networks</a></li>
 <li><a href="http://www.ev1servers.net/">EV1Servers</a></li>
 <li><a href="http://www.servercentral.net/">Server Central</a></li>
 <li><a href="http://www.hostedsolutions.com/">Hosted Solutions</a></li>
 <li><a href="http://www.spry.com/">Spry VPS Hosting</a></li>
 <li><a href="http://ez.no/">eZ Systems</a> / <a href="http://www.hit.no/english">HiT</a></li>
 <li><a href="http://www.osuosl.org">OSU Open Source Lab</a></li>
 <li><a href="http://www.yahoo.com/">Yahoo! Inc.</a></li>
 <li><a href="http://www.binarysec.com/">BinarySEC</a></li>
 <li><a href="http://www.nexcess.net/">NEXCESS.NET</a></li>
</ul>
<h3>Related sites</h3>
<ul class="simple">
 <li><a href="http://www.apache.org/">Apache</a></li>
 <li><a href="http://www.mysql.com/">MySQL</a></li>
 <li><a href="http://www.postgresql.org/">PostgreSQL</a></li>
 <li><a href="http://www.zend.com/">Zend Technologies</a></li>
</ul>
<h3>Community</h3>
<ul class="simple">
 <li><a href="http://www.linuxfund.org/">LinuxFund.org</a></li>
 <li><a href="http://www.ostg.com/">OSTG</a></li>
</ul>

<h3>Syndication</h3>
<p>
 You can grab our news as an RSS feed via a daily dump in a file
 named <a href="/news.rss">news.rss</a>.
</p>';

$MIRROR_IMAGE = '';

// Try to find a sponsor image in case this is an official mirror
if (is_official_mirror()) {

    // Iterate through possible mirror provider logo types in priority order
    $types = array("gif", "jpg", "png");
    while (list(,$ext) = each($types)) {

        // Check if file exists for this type
        if (file_exists("backend/mirror." . $ext)) {

            // Add text to rigth sidebar
            $MIRROR_IMAGE = "<div align=\"center\"><h3>This mirror sponsored by:</h3>\n";

            // Create image HTML code
            $img = make_image(
                'mirror.' . $ext,
                htmlspecialchars(mirror_provider()),
                FALSE,
                FALSE,
                'backend',
                0
            );

            // Add size information depending on mirror type
            if (is_primary_site() || is_backup_primary()) {
                $img = resize_image($img, 125, 125);
            } else {
                $img = resize_image($img, 120, 60);
            }

            // End mirror specific part
            $MIRROR_IMAGE .= '<a href="' . mirror_provider_url() . '">' .
                             $img . "</a></div><br /><hr />\n";

            // We have found an image
            break;
        }
    }
}

/* {{{ Generate latest release info */
/* NOTE: You are editing the wrong file, you should be in include/version.inc
 *  For RC: See the $PHP_x_RC variable
 *  For STABLE: See the $PHP_x_VERSION/_DATE/_MD5 variables
 */
$PHP_5_STABLE = $PHP_4_STABLE = array();
$PHP_5_RC     = "5.2.5RC1";
$PHP_4_RC     = "";
$rel          = $rc           = "";

list($PHP_5_STABLE, ) = each($RELEASES[5]);
list($PHP_4_STABLE, ) = each($RELEASES[4]);

$rel = <<< EOT
  <div id="releaseBox">
   <h4>Stable Releases</h4>
   <ol id="releases">
    <li class="php5"><a href="/downloads.php#v5">Current PHP 5 Stable: <span class="release">$PHP_5_STABLE</span></a></li>
    <li class="php5"><a href="/downloads.php#v4">Historical PHP 4 Stable: <span class="release">$PHP_4_STABLE</span></a></li>
   </ol>
  </div>\n
EOT;

/* Do we have any release candidates to brag about? */
if (count($RELEASES[5]>1)) {
    list($PHP_5_RC, ) = each($RELEASES[5]);

    if (!empty($PHP_5_RC)) {
        $rc .= "    <li class=\"php5\"><a href=\"http://qa.php.net/\">Current PHP 5 RC: <span class=\"release\">$PHP_5_RC</span></a></li>\n";
    }
}
if (count($RELEASES[4]>1)) {
    list($PHP_4_RC, ) = each($RELEASES[4]);

    if (!empty($PHP_4_RC)) {
        $rc .= "    <li class=\"php4\"><a href=\"http://qa.php.net/\">Current PHP 4 RC: <span class=\"release\">$PHP_4_RC</span></a></li>\n";
    }
}

if (!empty($rc)) {
	$rel .= <<< EOT
  <div id="candidateBox">
   <h4><a href="http://qa.php.net/rc.php">Release Candidates</a></h4>
   <ol id="candidates">
$rc
   </ol>
  </div>\n
EOT;
}
/* }}} */

// Prepend mirror image & latest releases to sidebar text
$RSIDEBAR_DATA = $MIRROR_IMAGE . $rel . $RSIDEBAR_DATA;

// Write out common header
site_header("Hypertext Preprocessor",
    array(
        'onload' => 'boldEvents();',
        'headtags' => '<link rel="alternate" type="application/rss+xml" title="PHP: Hypertext Preprocessor" href="' . $MYSITE . 'news.rss" />',
        'link' => array(
            array(
                "rel"   => "search",
                "type"  => "application/opensearchdescription+xml",
                "href"  => $MYSITE . "phpnetimprovedsearch.src",
                "title" => "Add PHP.net search"
            ),
        ),
    )
);

if (is_array($CONF_TEASER) && count($CONF_TEASER)) {
    $categories = array("conference" => "Upcoming conferences", "cfp" => "Calling for papers");
    echo '  <div id="confTeaser">' . "\n";
    echo "   <table>\n";
    foreach($CONF_TEASER as $k => $a) {
        if (is_array($a) && count($a)) {
            echo "    <tr>\n     <td valign='top' style='white-space: nowrap'>".$categories[$k].":</td>\n";
            echo "     <td valign='top'>\n";
            echo '      <ul class="' .$k. '">' . "\n";
            $count = 0;
            $a = preg_replace("'([A-Za-z0-9])([\s\:\-\,]*?)call for(.*?)$'i", "$1", $a);
            foreach($a as $url => $title) {
                if ($count++ >= 4) {
                    break;
                }
                echo '       <li><a href="' . $url. '">' . $title. '</a></li>' . "\n";
            }
            echo "      </ul>\n     </td>\n    </tr>\n";
        } // if set
    }
    echo "   </table>\n  </div>\n\n<br />\n";
}

// DO NOT REMOVE THIS COMMENT (the RSS parser is dependant on it)
?>

<?php
/* Where the h*ll did all the news go?
 * See archives/2007.xml
 */
print_news($NEWS_ENTRIES["frontpage"]);
?>

<p class="center"><a href="/archive/index.php">News Archive</a></p>

<?php
site_footer(
    array("rss" => "/news.rss") // Add a link to the feed at the bottom
);

