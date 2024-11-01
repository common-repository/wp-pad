<?php
//////////////////////////////////////////////////////////////////////////////
// Includes
//////////////////////////////////////////////////////////////////////////////


include_once ("xmlfile.php");

//////////////////////////////////////////////////////////////////////////////
// Classes
//////////////////////////////////////////////////////////////////////////////


// PADSpec (derives from XMLFile)
// Represents the PAD specification based on a XML PAD spec file
class PADSpec extends XMLFile
{
	//////////////////////////////////////////////////////////////////////////////
	// Public Properties
	//////////////////////////////////////////////////////////////////////////////
	

	// Reference to the /PAD_Spec/Fields node
	var $FieldsNode;
	
	//////////////////////////////////////////////////////////////////////////////
	// Construction
	//////////////////////////////////////////////////////////////////////////////
	

	// Constructor
	// IN: $URL - the URL or local path of the PAD spec file (optional)
	function PADSpec($URL = "")
	{
		// Inherited
		parent::XMLFile ( $URL );
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// Public Methods
	//////////////////////////////////////////////////////////////////////////////
	

	// Load
	// RETURNS: true  - Success (LastError is ERR_NO_ERROR)
	//          false - Failure (see LastError)
	function Load()
	{
		$ret = parent::Load ();
		
		$this->FieldsNode = & $this->XML->FindNodeByPath ( "PAD_Spec/Fields" );
		
		return $ret;
	}
	
	// Find spec node for field with Path
	// IN:      $Path - path of the PAD field, i.e. XML_DIZ_INFO/Company_Info/Company_Name
	// RETURNS: reference to the Field node in the spec, NULL if not found
	function &FindFieldNode($Path)
	{
		// Walk over all fields in the spec and compare Path
		foreach ( $this->FieldsNode->ChildNodes as $FieldNode )
			if ($FieldNode->GetValue ( "Path" ) == $Path)
				return $FieldNode;
			
		// Not found
		// To make PHP5 happy, we will not return NULL, but a variable which
		// has a value of NULL.
		$NULL = NULL;
		return $NULL;
	}
}

?>