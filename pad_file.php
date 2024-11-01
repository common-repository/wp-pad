<?php
/*
 Plugin Name: WP-Pad
 Plugin URI: http://www.superwebhunt.com/wp-pad/
 Description: WP-Pad is a simple plugin that let you build a software database site based on pad files submission. It automatically creates a page and adds the default categories and you can start submitting pads.
 Version: 1.4.2
 Author: The Chef
 Author URI: http://ezbitz.com
 */

require_once (ABSPATH . '/wp-config.php');
require_once (ABSPATH . '/wp-load.php');
require_once 'include/padclass.php';
require_once 'include/tengcore.php';
require_once (ABSPATH . "wp-admin/includes/taxonomy.php");
require_once 'pad_post.php';

global $wpdb;

function insert_pad_post($padfile)
{
	
	function mksize($bytes)
	{
		if ($bytes < 1000 * 1024)
			return number_format ( $bytes / 1024, 2 ) . " kB"; elseif ($bytes < 1000 * 1048576)
			return number_format ( $bytes / 1048576, 2 ) . " MB"; elseif ($bytes < 1000 * 1073741824)
			return number_format ( $bytes / 1073741824, 2 ) . " GB"; else
			return number_format ( $bytes / 1099511627776, 2 ) . " TB";
	}
	
	global $wpdb;
	
	$PADu = new padzilla ( $padfile );
	
	if (! $PADu->getError ())
	{
		$uniq = get_option ( 'pad_page_uniq' );
		if ($uniq == 'title')
		{
			$mylink = $wpdb->get_var ( "SELECT count(*) FROM  programs  WHERE prog_name = '" . $PADu->getProgramName () . "';" );
		} elseif ($uniq == 'both')
		{
			$mylink = $wpdb->get_var ( "SELECT count(*) FROM  programs  WHERE prog_name = '" . $PADu->getProgramName () . "' AND prog_vers = '" . $PADu->getProgramVersion () . "';" );
		} else
		{
			die ( "Dubious error" );
		}
		
		if ($mylink == 0)
		{
			echo "<b>" . $PADu->getProgramName () . "</b> successfully added";
			
			$tg = new tengcore ( tengcoremodes::$unmanaged );
			
			$wm_mypost = new wm_mypost ( );
			
			$info = new div ( );
			$prg_type = new strong ( );
			$prg_type->AddItem ( "Type: " );
			$info->AddItem ( $prg_type );
			$info->AddItem ( $PADu->getProgramType () );
			
			$prg_cost = new strong ( );
			$prg_cost->AddItem ( "Cost: " );
			$info->AddItem ( $prg_cost );
			$info->AddItem ( "$" . ($PADu->getProgramPrice () == "" ? 0 : $PADu->getProgramPrice ()) );
			
			$prg_size = new strong ( );
			$prg_size->AddItem ( "Size: " );
			$info->AddItem ( $prg_size );
			$info->AddItem ( mksize ( $PADu->getProgramSize () ) );
			
			$prg_date = new strong ( );
			$prg_date->AddItem ( "Release date: " );
			$info->AddItem ( $prg_date );
			$info->AddItem ( date ( "d M Y", $PADu->getReleaseDate () ) );
			
			$prg_platform = new strong ( );
			$prg_platform->AddItem ( "Platform: " );
			$info->AddItem ( $prg_platform );
			$info->AddItem ( $PADu->getPlatform () );
			
			$prg_download = new strong ( );
			$prg_download->AddItem ( "Download :" );
			$prg_download_link = new a_href ( );
			$prg_download_link->setHref ( $PADu->getDownloadURL () );
			$prg_download_link->AddAtribute ( "target", "_blank" );
			$prg_download_link->AddItem ( $PADu->getProgramName () );
			$info->AddItem ( "\n<strong>Download: </strong><a href=\"" . $PADu->getDownloadURL () . "\">" . $PADu->getProgramName () . " " . $PADu->getProgramType () . "</a><br/>" );
			
			$affiliate_id = get_option ( 'pad_regnow_id' );
			if ($affiliate_id != "" && $affiliate_id != NULL)
			{
				$product_id = $PADu->getRegNowProductID ();
				if ($product_id != "" && $product_id != NULL)
				{
					$regnowUrl = sprintf ( "http://www.regnow.com/softsell/nph-softsell.cgi?item=%s&affiliate=%s&ss_short_order=true", $product_id, $affiliate_id );
					$info->AddItem ( "Get <a href=\"$regnowUrl\"><strong>FULL VERSION of " . $PADu->getProgramName () . "</strong></a><br/>" );
				}
			}
			
			$p_center = new p ( );
			$p_center->AddAtribute ( "style", "text-align: center;" );
			
			$a_img = new a_href ( );
			$p_center->AddItem ( $a_img );
			if (get_option ( 'pad_page_box' ))
			{
				$a_img->AddAtribute ( "rel", get_option ( 'pad_page_box_string' ) );
			}
			$a_img->setHref ( $PADu->getScreenshot () );
			
			$img = new img ( $PADu->getScreenshot (), $PADu->getProgramName () );
			$a_img->AddItem ( $img );
			$img->AddAtribute ( "style", "border: 0;" );
			$img->AddAtribute ( "width", "400px" );
			$info->AddItem ( $p_center );
			$info->AddItem ( new br ( ) );
			$info->AddItem ( "<div style=\"text-align: justify\">" );
			$info->AddItem ( htmlentities ( addslashes ( $PADu->getProgramDescription () ) ) );
			$info->AddItem ( "</div>" );
			
			$wm_mypost->post_title = $PADu->getProgramName () . " " . $PADu->getProgramVersion ();
			$wm_mypost->post_content = $info->Render ();
			$wm_mypost->post_status = 'publish';
			$wm_mypost->post_author = get_option ( 'pad_user_post' );
			$wm_mypost->tags_input = $PADu->getTags ();
			
			$cat = $PADu->getProgramCategory ();
			
			$cat_arr = preg_split ( '/::/im', $cat );
			
			$cat_id = array ( );
			
			foreach ( $cat_arr as $categ )
			{
				$tmp_id = get_category_by_slug ( strtolower ( preg_replace ( '/\W+/i', '-', $categ ) ) )->term_id;
				if ($tmp_id != NULL)
				{
					$cat_id [] = $tmp_id;
				}
			}
			
			$wm_mypost->post_category = $cat_id;
			
			$wm_mypost->post_type = 'post';
			if (get_option ( 'pad_allow_comments' ))
			{
				$wm_mypost->comment_status = 'open';
			} else
			{
				$wm_mypost->comment_status = 'closed';
			}
			
			$pid = wp_insert_post ( $wm_mypost );
			$wpdb->insert ( 'programs', array ('post_id' => $pid, 'prog_name' => $PADu->getProgramName (), 'prog_vers' => $PADu->getProgramVersion () ), array ('%s', '%s' ) );
		
		} else
		{
			echo "<h4 style='color: red;'>" . $PADu->getProgramName () . " vers " . $PADu->getProgramVersion () . " already inserted !</h4>";
		}
	} else
	{
		echo "invalid pad file";
	}
}

$cat_raw = array ("Audio & Multimedia::Audio Encoders/Decoders", "Audio &Multimedia::Audio File Players", "Audio & Multimedia::Audio FileRecorders", "Audio & Multimedia::CD Burners", "Audio & Multimedia::CDPlayers", "Audio & Multimedia::Multimedia Creation Tools", 
				"Audio &Multimedia::Music Composers", "Audio & Multimedia::Other", "Audio &Multimedia::Presentation Tools", "Audio & Multimedia::Rippers &Converters", "Audio & Multimedia::Speech", "Audio & Multimedia::VideoTools", "Business::Accounting & Finance", 
				"Business::Calculators &Converters", "Business::Databases & Tools", "Business::Helpdesk &Remote PC", "Business::Inventory & Barcoding", "Business::InvestmentTools", "Business::Math & Scientific Tools", "Business::Office Suites& Tools", "Business::Other", 
				"Business::PIMS &Calendars", "Business::Project Management", "Business::Vertical MarketApps", "Communications::Chat & InstantMessaging", "Communications::Dial Up & ConnectionTools", "Communications::E-Mail Clients", "Communications::E-Mail ListManagement", 
				"Communications::Fax Tools", "Communications::NewsgroupClients", "Communications::Other Comms Tools", "Communications::OtherE-Mail Tools", "Communications::PagerTools", "Communications::Telephony", "Communications::Web/VideoCams", "Desktop::Clocks & Alarms", 
				"Desktop::Cursors &Fonts", "Desktop::Icons", "Desktop::Other", "Desktop::Screen Savers:Art", "Desktop::Screen Savers: Cartoons", "Desktop::Screen Savers:Nature", "Desktop::Screen Savers: Other", "Desktop::Screen Savers:People", "Desktop::Screen Savers: Science", 
				"Desktop::Screen Savers:Seasonal", "Desktop::Screen Savers: Vehicles", "Desktop::Themes &Wallpaper", "Development::Active X", "Development::Basic, VB, VBDotNet", "Development::C / C++ / C#", "Development::Compilers &Interpreters", "Development::Components &Libraries", 
				"Development::Debugging", "Development::Delphi", "Development::Help Tools", "Development::Install &Setup", "Development::Management &Distribution", "Development::Other", "Development::SourceEditors", "Education::Computer", "Education::Dictionaries", "Education::Geography", 
				"Education::Kids", "Education::Languages", "Education::Mathematics", "Education::Other", "Education::ReferenceTools", "Education::Science", "Education::Teaching & TrainingTools", "Games & Entertainment::Action", "Games &Entertainment::Adventure & Roleplay", 
				"Games &Entertainment::Arcade", "Games & Entertainment::Board", "Games &Entertainment::Card", "Games & Entertainment::Casino &Gambling", "Games & Entertainment::Kids", "Games &Entertainment::Online Gaming", "Games & Entertainment::Other", 
				"Games& Entertainment::Puzzle & Word Games", "Games &Entertainment::Simulation", "Games & Entertainment::Sports", "Games &Entertainment::Strategy & War Games", "Games & Entertainment::Tools& Editors", "Graphic Apps::Animation Tools", "GraphicApps::CAD", 
				"Graphic Apps::Converters & Optimizers", "GraphicApps::Editors", "Graphic Apps::Font Tools", "Graphic Apps::Gallery &Cataloging Tools", "Graphic Apps::Icon Tools", "GraphicApps::Other", "Graphic Apps::Screen Capture", "GraphicApps::Viewers", 
				"Home & Hobby::Astrology/Biorhythms/Mystic", "Home &Hobby::Astronomy", "Home & Hobby::Cataloging", "Home & Hobby::Food &Drink", "Home & Hobby::Genealogy", "Home & Hobby::Health &Nutrition", "Home & Hobby::Other", "Home & Hobby::PersonalFinance", "Home & Hobby::Personal Interest", 
				"Home &Hobby::Recreation", "Home & Hobby::Religion", "Network & Internet::AdBlockers", "Network & Internet::Browser Tools", "Network &Internet::Browsers", "Network & Internet::Download Managers", "Network& Internet::File Sharing/Peer to Peer", "Network & Internet::FTPClients", 
				"Network & Internet::Network Monitoring", "Network &Internet::Other", "Network & Internet::Remote Computing", "Network &Internet::Search/Lookup Tools", "Network & Internet::Terminal &Telnet Clients", "Network & Internet::Timers & Time Synch", 
				"Network &Internet::Trace & Ping Tools", "Security & Privacy::AccessControl", "Security & Privacy::Anti-Spam & Anti-Spy Tools", "Security& Privacy::Anti-Virus Tools", "Security & Privacy::CovertSurveillance", "Security & Privacy::Encryption Tools", "Security &Privacy::Other", 
				"Security & Privacy::PasswordManagers", "Servers::Firewall & Proxy Servers", "Servers::FTPServers", "Servers::Mail Servers", "Servers::NewsServers", "Servers::Other Server Applications", "Servers::TelnetServers", "Servers::Web Servers", "System Utilities::AutomationTools", 
				"System Utilities::Backup & Restore", "SystemUtilities::Benchmarking", "System Utilities::Clipboard Tools", "SystemUtilities::File & Disk Management", "System Utilities::FileCompression", "System Utilities::Launchers & Task Managers", "SystemUtilities::Other", 
				"System Utilities::Printer", "SystemUtilities::Registry Tools", "System Utilities::Shell Tools", "SystemUtilities::System Maintenance", "System Utilities::Text/DocumentEditors", "Web Development::ASP & PHP", "Web Development::E-Commerce", "Web Development::Flash Tools", 
				"Web Development::HTML Tools", "Web Development::Java & JavaScript", "Web Development::Log Analysers", "Web Development::Other", "Web Development::Site Administration", "Web Development::Wizards &Components", "Web Development::XML/CSS Tools" );

$caturi = array ( );

$wpdb->query ( 'CREATE TABLE IF NOT EXISTS `programs` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`prog_name` VARCHAR( 1024 ) NOT NULL ,
`prog_vers` VARCHAR( 32 ) NOT NULL ,
`post_id` INT NOT NULL
) ENGINE = innodb;' );

foreach ( $cat_raw as $cat_raw_item )
{
	$maincat = preg_split ( "/::/", strtolower ( preg_replace ( '/[ &]+/', '-', $cat_raw_item ) ) );
	if (! isset ( $caturi [$maincat [0]] ))
	{
		$caturi [$maincat [0]] = array ( );
		$caturi [$maincat [0]] [] = $maincat [1];
		$main_cat_id = wp_create_category ( $maincat [0], 0 );
		wp_create_category ( $maincat [1], $main_cat_id );
	} else
	{
		wp_create_category ( $maincat [1], $main_cat_id );
		$caturi [$maincat [0]] [] = $maincat [1];
	}
}

function clear_pads($postid)
{
	global $wpdb;
	$wpdb->query ( 'DELETE FROM programs WHERE post_id = ' . $postid );
	
	$pad_page_id = get_option ( 'pad_page_id' );
	if ($pad_page_id == $postid)
	{
		update_option ( 'pad_page_id', 0 );
		update_option ( 'pad_page_name', "" );
		update_option ( 'pad_page_created', FALSE );
	}
}

function pad_admin()
{
	include ('pad_import_admin.php');
}

function pad_admin_actions()
{
	add_options_page ( "WP-Pad", "wp-pad", 1, "wp-pad", "pad_admin" );
}

function pad_build_page($content)
{
	if (get_option ( 'pad_page_fancy' ))
	{
		$pad_form_style = "background:transparent url(" . plugins_url ( 'images/form1_01.gif', __FILE__ ) . ") no-repeat scroll left top; border:0 none; color:#818181; float:left; font-size:19px; height:55px; margin:0 0 0 5px; padding:15px 0 0 10px; width:375px;";
		$pad_button_style = "background:transparent url(" . plugins_url ( 'images/form1_02.gif', __FILE__ ) . ") no-repeat scroll left top; border:0 none; float:left; height:55px; margin:0 0 0 0; padding:0 0 0 -10px; width:150px;";
		$pad_button_value = "";
	} else
	{
		$pad_button_value = "Submit pad";
	}
	$pgeid = get_option ( 'pad_page_id' );
	if ($pgeid != 0)
	{
		if (is_page ( $pgeid ))
		{
			if (isset ( $_POST ["padfile"] ))
			{
				insert_pad_post ( $_POST ["padfile"] );
			}
			echo "Use this form to submit or update your pad files.<br /><form method=\"post\"><input style=\"";
			echo $pad_form_style;
			echo "\" type=\"text\" name=\"padfile\"	size=\"64\" onfocus=\"if (value == 'Enter pad url') {value =''}\"	onblur=\"if (value == '') {value = 'Enter pad url'}\"	value=\"Enter pad url\"> <input style=\"";
			echo $pad_button_style;
			echo "\" type=\"submit\" value=\"" . $pad_button_value . "\"></form>";
			echo '<p style="clear: both;"><br/><br/><br/><img style="border: 0;" src="' . plugins_url ( 'images/swh.gif', __FILE__ ) . '"> Powered by <a href="http://www.superwebhunt.com/">Super Web Hunt</a> &amp; <a href="http://www.superwebhunt.com/wp-pad/">WP-Pad</a></p>';
		}
	}
	echo $content;
}

function init_plugin_pad()
{
	update_option ( 'pad_page_box', TRUE );
	update_option ( 'pad_page_fancy', TRUE );
	update_option ( "pad_user_post", 1 );
	update_option ( "pad_page_name", "Submit pad" );
	update_option ( 'pad_page_uniq', "both" );
	update_option ( 'pad_allow_comments', FALSE );
}

function deactivate_plugin_pad()
{
	delete_option ( 'pad_page_box' );
	delete_option ( 'pad_page_fancy' );
	delete_option ( 'pad_user_post' );
	delete_option ( 'pad_page_name' );
	delete_option ( 'pad_page_uniq' );
	delete_option ( 'pad_allow_comments' );
}

register_activation_hook ( __FILE__, 'init_plugin_pad' );
register_deactivation_hook ( __FILE__, 'deactivate_plugin_pad' );
add_filter ( 'the_content', 'pad_build_page' );
add_action ( 'admin_menu', 'pad_admin_actions' );
add_action ( 'delete_post', 'clear_pads' );

?>
