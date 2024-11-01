<?php
require_once 'pad_post.php';
require_once 'include/tengcore.php';
global $wpdb;

if ($_POST ['pad_hidden'] == 'Y')
{
	//Form data sent
	$pad_user_post = $_POST ['pad_user_post'];
	update_option ( "pad_user_post", $pad_user_post );
	$pad_page_name = $_POST ['pad_page_name'];
	update_option ( "pad_page_name", $pad_page_name );

	if (get_option ( 'pad_page_created' ) == NULL || get_option ( 'pad_page_id' ) == 0)
	{
		$pad_page = new wm_mypost ( );
		$pad_page->post_title = get_option ( 'pad_page_name' );
		$pad_page->post_type = "page";
		$pad_page->post_status = "publish";
		$pad_page_id = wp_insert_post ( $pad_page );
		echo "<div class=\"updated\"><p><strong>Page " . get_option ( 'pad_page_id' ) . " created" . "</strong></p></div>";

		update_option ( 'pad_page_id', $pad_page_id );
		update_option ( 'pad_page_created', TRUE );
	} else
	{
		if ($pad_page_name != get_option ( 'pad_page_name' ))
		{
			$pad_page = new wm_mypost ( );
			$pad_page->ID = get_option ( 'pad_page_id' );
			$pad_page->post_type = "page";
			$pad_page->post_status = "publish";
			$pad_page->post_title = $pad_page_name;
			wp_update_post ( $pad_page );
		}
	}

	update_option ( 'pad_page_name', $pad_page_name );

	if ($_POST ['pad_page_fancy'])
	{
		update_option ( 'pad_page_fancy', TRUE );
	} else
	{
		update_option ( 'pad_page_fancy', FALSE );
	}

	if ($_POST ['pad_page_box'])
	{
		update_option ( 'pad_page_box', TRUE );
	} else
	{
		update_option ( 'pad_page_box', FALSE );
	}

	update_option ( 'pad_page_uniq', $_POST ['pad_page_uniq'] );

	if (isset ( $_POST ['pad_page_box_string'] ))
	{
		update_option ( 'pad_page_box_string', $_POST ['pad_page_box_string'] );
	}


	if (isset($_POST['pad_regnow_id']))
	{
		update_option('pad_regnow_id',$_POST['pad_regnow_id']);
	}

		update_option('pad_allow_comments',$_POST['allowcomm']);
	
	echo "<div class=\"updated\"><p><strong>Options saved.</strong></p></div>";
}


$pad_regnow_id=get_option('pad_regnow_id');

$pad_page_name = get_option ( 'pad_page_name' );

$tg = new tengcore ( tengcoremodes::$unmanaged );

$div = new div ( );
$div->setClass ( "wrap" );
$h2 = new h2 ( );
$h2->AddItem ( "Pad Settings" );
$div->AddItem ( $h2 );
$div->AddItem ( new hr ( ) );

if (get_option ( 'pad_page_created' ) == NULL)
{
	$h4 = new h4 ( );
	$h4->AddItem ( "There is no page yet ! Save options to create a new one" );
	$div->AddItem ( $h4 );
}
$form = new form ( );
$div->AddItem ( $form );
$form->AddAtribute ( "name", "pad_form" );
$form->AddAtribute ( "action", str_replace ( '%7E', '~', $_SERVER ['REQUEST_URI'] ) );
$form->AddAtribute ( "method", "post" );


$input_hidden = new input ( );
$form->AddItem ( $input_hidden );
$input_hidden->AddAtribute ( "type", "hidden" );
$input_hidden->AddAtribute ( "name", "pad_hidden" );
$input_hidden->AddAtribute ( "value", "Y" );

$h4 = new h4 ( );
$form->AddItem ( $h4 );
$h4->AddItem ( "Pad Settings" );
$p = new p ( );
$form->AddItem ( $p );

$p->AddItem ( "Page name: " );


$input_page_name = new input ( );
$p->AddItem ( $input_page_name );
$input_page_name->AddAtribute ( "type", "text" );
$input_page_name->AddAtribute ( "name", "pad_page_name" );
$input_page_name->AddAtribute ( "value", $pad_page_name );
$input_page_name->AddAtribute ( "size", "20" );

$p = new p ( );
$form->AddItem ( $p );
$p->AddItem ( "Make posts as user: " );
$select = new select ( );
$p->AddItem ( $select );
$select->AddAtribute ( "name", "pad_user_post" );

$users = $wpdb->get_results ( "SELECT ID, user_nicename FROM $wpdb->users " );

foreach ( $users as $user )
{
	$opt = new option ( );
	$select->AddItem ( $opt );
	$opt->AddAtribute ( "value", $user->ID );
	if (get_option ( 'pad_user_post' ) == $user->ID)
	{
		$opt->AddAtribute ( "selected", "selected" );
	}
	$opt->AddItem ( $user->user_nicename );
}

$p = new p ( );
$form->AddItem ( $p );
$p->AddItem ( "Fancy input form(css) " );
$input_pad_page_fancy = new input ( );
$p->AddItem($input_pad_page_fancy);
$input_pad_page_fancy->AddAtribute ( "type", "checkbox" );
$input_pad_page_fancy->AddAtribute ( "name", "pad_page_fancy" );
if (get_option ( 'pad_page_fancy' ))
{
	$input_pad_page_fancy->AddAtribute ( "checked", "checked" );
}
$span = new span ( );
$p->AddItem ( $span );
$span->setClass ( "description" );
$span->AddItem ( " Skin the input form using defaults" );

$p = new p ( );
$form->AddItem ( $p );
$p->AddItem ( "Use lightbox/thickbox/shadowbox etc " );
$input_pad_page_box = new input ( );
$p->AddItem ( $input_pad_page_box );
$input_pad_page_box->AddAtribute ( "name", "pad_page_box" );
$input_pad_page_box->AddAtribute("type","checkbox");
if (get_option ( 'pad_page_box' ))
{
	$input_pad_page_box->AddAtribute ( "checked", "checked" );
}

$p = new p ( );
$form->AddItem ( $p );
$input_pad_page_box_string = new input ( );
$p->AddItem ( $input_pad_page_box_string );
$input_pad_page_box_string->AddAtribute ( "name", "pad_page_box_string" );
$input_pad_page_box_string->AddAtribute ( "size", "30" );
$input_pad_page_box_string->AddAtribute ( "value", get_option ( 'pad_page_box_string' ) );
$span = new span ( );
$p->AddItem ( $span );
$span->setClass ( "description" );
$span->AddItem ( "Most boxes use an rel attribute in order to activate. This is the string that rel attribute contains. Eg: rel=\"lightbox\" rel=\"shadowbox\"" );


$p = new p ( );
$form->AddItem ( $p );
$p->AddItem ( "Verify if a pad was already added by: " );
$p->AddItem ( new br ( ) );
$input_pad_page_uniq_title = new input ( );
$p->AddItem ( $input_pad_page_uniq_title );
$input_pad_page_uniq_title->AddAtribute ( "type", "radio" );
$input_pad_page_uniq_title->AddAtribute ( "name", "pad_page_uniq" );
$input_pad_page_uniq_title->AddAtribute ( "value", "title" );
$p->AddItem ( "Program name" );
$p->AddItem ( new br ( ) );
$input_pad_page_uniq_both = new input ( );
$p->AddItem ( $input_pad_page_uniq_both );
$input_pad_page_uniq_both->AddAtribute ( "type", "radio" );
$input_pad_page_uniq_both->AddAtribute ( "name", "pad_page_uniq" );
$input_pad_page_uniq_both->AddAtribute ( "value", "both" );

if (get_option ( 'pad_page_uniq' ) == 'title')
{
	$input_pad_page_uniq_title->AddAtribute ( "checked", "checked" );
}elseif (get_option ( 'pad_page_uniq' ) == 'both')
{
	$input_pad_page_uniq_both->AddAtribute("checked", "checked" );
}

$p->AddItem ( "Program name and version" );
$p->AddItem ( new br ( ) );


$p=new p();
$form->AddItem($p);
$p->AddItem("RegNow affiliate id:");
$input_pad_regnow_id=new input();
$p->AddItem($input_pad_regnow_id);
$input_pad_regnow_id->AddAtribute("type","text");
$input_pad_regnow_id->AddAtribute("name","pad_regnow_id");
$input_pad_regnow_id->AddAtribute('value',$pad_regnow_id);





$p=new p();
$form->AddItem($p);
$p->AddItem("Allow comments: ");
$input_pad_comments=new input();
$p->AddItem($input_pad_comments);
$input_pad_comments->AddAtribute("type","checkbox");
if (get_option('pad_allow_comments'))
{
	$input_pad_comments->AddAtribute('checked','checked');
}
$input_pad_comments->AddAtribute("name","allowcomm");


$form->AddItem ( new hr ( ) );

$p = new p ( );
$form->AddItem ( $p );
$p->setClass ( "submit" );
$input_Submit = new input ( );
$p->AddItem ( $input_Submit );
$input_Submit->AddAtribute ( "type", "submit" );
$input_Submit->AddAtribute ( "name", "Submit" );
$input_Submit->AddAtribute ( "value", "Update Options" );
echo $div->Render ();
?>