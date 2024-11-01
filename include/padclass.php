<?php

require_once 'padfile.php';

class padzilla
{
	private $PAD;
	private $Error = TRUE;
	private $Screenshot;
	private $ProgramName;
	private $ProgramVersion;
	private $ProgramDescription;
	private $CompanyName;
	private $CompanyURL;
	private $Platform;
	private $ProgramType;
	private $ProgramPrice = 0;
	private $ProgramCategory;
	private $ProgramSize;
	private $ReleaseDate;
	private $DownloadURL;
	private $Tags;
	private $RegNowVendorID;
	private $RegNowProductID;
	
	function __construct($file)
	{
		$PAD = new PADFile ( $file );
		if (! $PAD->Load ())
		{
			$this->Error = TRUE;
		} else
		{
			$this->Error = FALSE;
			$this->ProgramName = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/Program_Name" );
			$this->ProgramVersion = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/Program_Version" );
			$this->ProgramDescription = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Descriptions/English/Char_Desc_2000" );
			$this->Tags = array ( );
			$tgs = explode ( ",", $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Descriptions/English/Keywords" ) );
			foreach ( $tgs as $tag )
			{
				$this->Tags [] = trim ( $tag );
			}
			
			$this->CompanyName = $PAD->XML->GetValue ( "XML_DIZ_INFO/Company_Info/Company_Name" );
			$this->CompanyURL = $PAD->XML->GetValue ( "XML_DIZ_INFO/Company_Info/Company_WebSite_URL" );
			$this->Platform = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/Program_OS_Support" );
			$this->ProgramType = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/Program_Type" );
			$this->ProgramPrice = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/Program_Cost_Dollars" );
			$this->ProgramCategory = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/Program_Category_Class" );
			$this->ProgramSize = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/File_Info/File_Size_Bytes" );
			$month = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/Program_Release_Month" );
			$day = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/Program_Release_Day" );
			$year = $PAD->XML->GetValue ( "XML_DIZ_INFO/Program_Info/Program_Release_Year" );
			$this->ReleaseDate = mktime ( 0, 0, 0, $month, $day, $year );
			$this->DownloadURL = $PAD->XML->GetValue ( "XML_DIZ_INFO/Web_Info/Download_URLs/Primary_Download_URL" );
			$this->Screenshot = $PAD->XML->GetValue ( "XML_DIZ_INFO/Web_Info/Application_URLs/Application_Screenshot_URL" );
			$this->RegNowVendorID = $PAD->XML->GetValue ( "XML_DIZ_INFO/Affiliates/Affiliates_Regnow_Vendor_ID" );
			$this->RegNowProductID = $PAD->XML->GetValue ( "XML_DIZ_INFO/Affiliates/Affiliates_Regnow_Product_ID" );
		}
	}
	
	public function getScreenshot()
	{
		return $this->Screenshot;
	}
	
	public function getError()
	{
		return $this->Error;
	}
	
	public function getTags()
	{
		return $this->Tags;
	}
	
	public function getCompanyName()
	{
		return $this->CompanyName;
	}
	
	public function getCompanyURL()
	{
		return $this->CompanyURL;
	}
	
	public function getDownloadURL()
	{
		return $this->DownloadURL;
	}
	
	public function getPad()
	{
		return $this->pad;
	}
	
	public function getPlatform()
	{
		return $this->Platform;
	}
	
	public function getProgramCategory()
	{
		return $this->ProgramCategory;
	}
	
	public function getProgramDescription()
	{
		return $this->ProgramDescription;
	}
	
	public function getProgramName()
	{
		return $this->ProgramName;
	}
	
	public function getProgramPrice()
	{
		return $this->ProgramPrice;
	}
	
	public function getProgramSize()
	{
		return $this->ProgramSize;
	}
	
	public function getProgramType()
	{
		return $this->ProgramType;
	}
	
	public function getProgramVersion()
	{
		return $this->ProgramVersion;
	}
	
	public function getReleaseDate()
	{
		return $this->ReleaseDate;
	}
	public function getRegNowVendorID()
	{
		return $this->RegNowVendorID;
	}
	
	public function getRegNowProductID()
	{
		return $this->RegNowProductID;
	}

}

?>