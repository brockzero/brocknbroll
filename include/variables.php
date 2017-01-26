<?php
//social media buttons
$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
$facebookButton = '<div id="social">
<div id="facebook" class="fb-like" data-href="'.$pageURL.'" data-send="false" data-layout="button_count" data-show-faces="true"></div>
<a id="twitter" href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
</div>';

// <!-- twitter script -->
$twitterButton = '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

//SITE NAME
if ($_SERVER['SERVER_ADDR'] == "127.0.0.1") {
	define("SITE_NAME", "awfulcontent.net");
	// Remote
} else {
	define("SITE_NAME", "awfulcontent.com");
}
//SITE MENU
$menu = '<ul id="menu">
      <a href="/comic"><li>Comic</li></a>
      <a href="/comic/archives"><li>Comic Archives</li></a>
      <a href="/words"><li>Words</li></a>
      <a href="/contact"><li>Contact</li></a>
    </ul>';
	
define("SITE_MENU", $menu);

///jscripts/tiny_mce/tiny_mce.js
$improvedForms = '<script type="text/javascript" src="/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
        // General options
		mode : "exact",
        elements : "article",
        theme : "advanced",
		element_format : "html",
		schema: "html5",
        plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,spellchecker",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,spellchecker",
		spellchecker_languages : "+English=en",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Example content CSS (should be your site CSS)
        content_css : "/layout/content_tinymce.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});

function toggleEditor(id) {
if (!tinyMCE.get(id))
tinyMCE.execCommand(\'mceAddControl\', false, id);
else
tinyMCE.execCommand(\'mceRemoveControl\', false, id);
}
</script>';

$achievementButton = '<script type="text/javascript">
function addAchievement() {
	var currentTime = new Date();
	var mon = currentTime.getMonth();
	var month = null;
	switch(mon)
	{
	case 0:
	month = "Jan";
	break;
	case 1:
	month = "Feb";
	break;
	case 2:
	month = "Mar";
	break;
	case 3:
	month = "Apr";
	break;
	case 4:
	month = "May";
	break;
	case 5:
	month = "Jun";
	break;
	case 6:
	month = "Jul";
	break;
	case 7:
	month = "Aug";
	break;
	case 8:
	month = "Sep";
	break;
	case 9:
	month = "Oct";
	break;
	case 10:
	month = "Nov";
	break;
	case 11:
	month = "Dec";
	break;
	default:
	}
	
	var day = currentTime.getDate()
	var year = currentTime.getFullYear()
	var hours = currentTime.getHours()
	var minutes = currentTime.getMinutes()
	if (minutes < 10){
		minutes = "0" + minutes
		}
	if(hours > 11){
		var am_pm = "pm";
	} else {
		var am_pm = "am";
	}
	if(hours > 12){
		hours = hours - 12;
	}
	if(hours == 0) {
		hours = 12;
	}
	var time = month + " " + day + ", " + year + " " + hours + ":" + minutes + am_pm;
	
	var newtext = \'<div class="achieveTxtHolder"><div class="achieveTxt"><div class="achieveUnlockTime">Unlocked: \'+ time +\'</div><h5>Achievement Name</h5><h6>Achievement description.</h6></div></div>\';

	document.updateForm.article.value += newtext;
}
</script>';
/*
Performance Tester: time to generate a page
###########################################
//beginning of page
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

//end of page
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = ($endtime - $starttime);
echo "This page was created in ".$totaltime." seconds";
*/

?>