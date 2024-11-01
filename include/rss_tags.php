<?php
require_once 'tag.php';

class rss_rss extends tag
{
	function __construct()
	{
		$this->Name = "rss";
		//		$this->AddAtribute ( "xmlns:content", "http://purl.org/rss/1.0/modules/content/" );
		//		$this->AddAtribute ( "xmlns:wfw", "http://wellformedweb.org/CommentAPI/" );
		//		$this->AddAtribute ( "xmlns:dc", "http://purl.org/dc/elements/1.1/" );
		//		$this->AddAtribute ( "xmlns:atom", "http://www.w3.org/2005/Atom" );
		$this->AddAtribute ( "version", "2.0" );
	}
	
	function Render()
	{
		return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . parent::Render ();
	}
}

class rss_channel extends tag
{
	function __construct()
	{
		$this->Name = "channel";
	}
}

class rss_item extends tag
{
	function __construct()
	{
		$this->Name = "item";
	}
}

class rss_title extends tag
{
	function __construct($title)
	{
		$this->Name = "title";
		$this->AddItem ( $title );
	}
}

class rss_link extends tag
{
	function __construct($url)
	{
		$this->Name = "link";
		$this->AddItem ( $url );
	}
}

class rss_description extends tag
{
	function __construct($desc)
	{
		$this->Name = "description";
		$this->AddItem ( $desc );
	}
}

class rss_pubDate extends tag
{
	function __construct($date)
	{
		$this->Name = "pubDate";
		$date = date ( "r", strtotime ( $date ) );
		$this->AddItem ( $date );
	}
}

class rss_generator extends tag
{
	function __construct($gen)
	{
		$this->Name = "generator";
		$this->AddItem ( $gen );
	}
}

class rss_language extends tag
{
	function __construct($lang)
	{
		$this->Name = "language";
		$this->AddItem ( $lang );
	}
}

class rss_author extends tag
{
	function __construct($auth)
	{
		$this->Name = "author";
		$this->AddItem ( $auth );
	}
}

class rss_item_builder extends rss_item
{
	function __construct($pbdate, $title, $desc, $link, $auth)
	{
		$this->Name = "item";
		parent::__construct ();
		$rss_pub = new rss_pubDate ( $pbdate );
		$rss_title = new rss_title ( $title );
		$rss_desc = new rss_description ( htmlentities ( $desc ) );
		$rss_link = new rss_link ( $link );
		$rss_auth = new rss_author ( $auth );
		
		$this->AddItem ( $rss_pub );
		$this->AddItem ( $rss_title );
		$this->AddItem ( $rss_desc );
		$this->AddItem ( $rss_link );
		$this->AddItem ( $rss_auth );
	}
}

class rss_rss_builder extends rss_rss
{
	private $mRSSItems;
	private $mChan;
	function __construct($title, $desc, $link, $gen, $lang)
	{
		//		$this->Name = "rss";
		$this->mRSSItems = array ( );
		
		parent::__construct ();
		
		$this->mChan = new rss_channel ( );
		
		$rss_title = new rss_title ( $title );
		$rss_desc = new rss_description ( $desc );
		$rss_link = new rss_link ( $link );
		$rss_gen = new rss_generator ( $gen );
		$rss_lang = new rss_language ( $lang );
		
		$this->mChan->AddItem ( $rss_desc );
		$this->mChan->AddItem ( $rss_gen );
		$this->mChan->AddItem ( $rss_lang );
		$this->mChan->AddItem ( $rss_link );
		$this->mChan->AddItem ( $rss_title );
		
		$this->AddItem ( $this->mChan );
	}
	/*
	public function Render()
	{
		
		foreach ( $this->mRSSItems as $item )
		{
			$this->AddItem ( $item );
		}
		parent::Render ();
	}
	*/
	public function AddRSSItem(rss_item_builder $item)
	{
		$this->mChan->AddItem ( $item );
		//		$this->mRSSItems [] = $item;
	}
}

?>