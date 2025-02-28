<?php

//////////////////////////////////////////////////////////////////////////////
// Includes
//////////////////////////////////////////////////////////////////////////////

// Includes
include_once("xmlfile.php");


//////////////////////////////////////////////////////////////////////////////
// Classes
//////////////////////////////////////////////////////////////////////////////

// PADFile class (derives from XMLFile)
// Represents a PAD file
class PADFile extends XMLFile
{
  //////////////////////////////////////////////////////////////////////////////
  // Construction
  //////////////////////////////////////////////////////////////////////////////

  // Constructor
  // IN: $URL - the URL or local path of the PAD file (optional)
  function PADFile($URL = "")
  {
    // Inherited
    parent::XMLFile($URL);
  }


  //////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////

  // Returns the descriptions from the PAD file that fits best to the
  // following arguments
  // IN: $Length    - the desired length in characters (optional, default is 2000)
  // IN: $Language  - the identifier of the desired language (optional, default is "English")
  // RETURNS: true  - Success (LastError is ERR_NO_ERROR)
  //          false - Failure (see LastError)
  function GetBestDescription($Length = 2000, $Language = "English")
  {
    $Descr = "";

    // Try $Language
    if ( $Length >= 2000 )
      $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/" . $Language . "/Char_Desc_2000");
    if ( $Descr != "" )
      return $Descr;
    if ( $Length >= 450 )
      $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/" . $Language . "/Char_Desc_450");
    if ( $Descr != "" )
      return $Descr;
    if ( $Length >= 250 )
      $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/" . $Language . "/Char_Desc_250");
    if ( $Descr != "" )
      return $Descr;
    if ( $Length >= 80 )
      $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/" . $Language . "/Char_Desc_80");
    if ( $Descr != "" )
      return $Descr;
    if ( $Length >= 45 )
      $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/" . $Language . "/Char_Desc_45");
    if ( $Descr != "" )
      return $Descr;

    // Try English, if nothing found yet
    if ( $Language != "English" )
    {
      if ( $Length >= 2000 )
        $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/English/Char_Desc_2000");
      if ( $Descr != "" )
        return $Descr;
      if ( $Length >= 450 )
        $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/English/Char_Desc_450");
      if ( $Descr != "" )
        return $Descr;
      if ( $Length >= 250 )
        $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/English/Char_Desc_250");
      if ( $Descr != "" )
        return $Descr;
      if ( $Length >= 80 )
        $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/English/Char_Desc_80");
      if ( $Descr != "" )
        return $Descr;
      if ( $Length >= 45 )
        $Descr = $this->XML->GetValue("XML_DIZ_INFO/Program_Descriptions/English/Char_Desc_45");
      if ( $Descr != "" )
        return $Descr;
    }

    // Nothing found
    return "";
  }
}

?>