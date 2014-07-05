<?php
/*********************************************************************************
 *       Filename: CardTypesRecord.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "CardTypesRecord.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("CardTypesRecord.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sCardTypesErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "CardTypes":
    CardTypes_action($sAction);
  break;
}Menu_show();
Footer_show();
CardTypes_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function CardTypes_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sCardTypesErr;
  
  $sParams = "";
  $sActionFileName = "CardTypesGrid.php";

  

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName); 

  
  // Create WHERE statement
  if($sAction == "update" || $sAction == "delete") 
  {
    $pPKcard_type_id = get_param("PK_card_type_id");
    if( !strlen($pPKcard_type_id)) return;
    $sWhere = "card_type_id=" . tosql($pPKcard_type_id, "Number");
  }

  // Load all form fields into variables
  
  $fldname = get_param("name");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldname))
      $sCardTypesErr .= "The value in field Name is required.<br>";
    

    if(strlen($sCardTypesErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "insert":
      
        $sSQL = "insert into card_types (" . 
          "name)" . 
          " values (" . 
          tosql($fldname, "Text") . ")";    
    break;
    case "update":
      
        $sSQL = "update card_types set " .
          "name=" . tosql($fldname, "Text");
        $sSQL .= " where " . $sWhere;
    break;
    case "delete":
      
        $sSQL = "delete from card_types where " . $sWhere;
    break;
  }
  // Execute SQL statement
  if(strlen($sCardTypesErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName);
  
}

function CardTypes_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sCardTypesErr;

  $sWhere = "";
  
  $bPK = true;
  $fldcard_type_id = "";
  $fldname = "";
  

  if($sCardTypesErr == "")
  {
    // Load primary key and form parameters
    $fldcard_type_id = get_param("card_type_id");
    $pcard_type_id = get_param("card_type_id");
    $tpl->set_var("CardTypesError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldcard_type_id = strip(get_param("card_type_id"));
    $fldname = strip(get_param("name"));
    $pcard_type_id = get_param("PK_card_type_id");
    $tpl->set_var("sCardTypesErr", $sCardTypesErr);
    $tpl->parse("CardTypesError", false);
  }

  
  if( !strlen($pcard_type_id)) $bPK = false;
  
  $sWhere .= "card_type_id=" . tosql($pcard_type_id, "Number");
  $tpl->set_var("PK_card_type_id", $pcard_type_id);

  $sSQL = "select * from card_types where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "CardTypes"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldcard_type_id = $db->f("card_type_id");
    if($sCardTypesErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldname = $db->f("name");
    }
    $tpl->set_var("CardTypesInsert", "");
    $tpl->parse("CardTypesEdit", false);
  }
  else
  {
    if($sCardTypesErr == "")
    {
      $fldcard_type_id = tohtml(get_param("card_type_id"));
    }
    $tpl->set_var("CardTypesEdit", "");
    $tpl->parse("CardTypesInsert", false);
  }
  $tpl->parse("CardTypesCancel", false);

  // Show form field
  
    $tpl->set_var("card_type_id", tohtml($fldcard_type_id));
    $tpl->set_var("name", tohtml($fldname));
  $tpl->parse("FormCardTypes", false);
  

}

?>