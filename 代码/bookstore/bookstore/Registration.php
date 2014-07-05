<?php
/*********************************************************************************
 *       Filename: Registration.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "Registration.php";




$tpl = new Template($app_path);
$tpl->load_file("Registration.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sRegErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "Reg":
    Reg_action($sAction);
  break;
}Menu_show();
Footer_show();
Reg_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Reg_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sRegErr;
  
  $sParams = "";
  $sActionFileName = "Default.php";

  

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName); 

  

  // Load all form fields into variables
  
  $fldmember_login = get_param("member_login");
  $fldmember_password = get_param("member_password");
  $fldmember_password2 = get_param("member_password2");
  $fldfirst_name = get_param("first_name");
  $fldlast_name = get_param("last_name");
  $fldemail = get_param("email");
  $fldaddress = get_param("address");
  $fldphone = get_param("phone");
  $fldcard_type_id = get_param("card_type_id");
  $fldcard_number = get_param("card_number");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldmember_login))
      $sRegErr .= "The value in field Login* is required.<br>";
    
    if(!strlen($fldmember_password))
      $sRegErr .= "The value in field Password* is required.<br>";
    
    if(!strlen($fldmember_password2))
      $sRegErr .= "The value in field Confirm Password* is required.<br>";
    
    if(!strlen($fldfirst_name))
      $sRegErr .= "The value in field First Name* is required.<br>";
    
    if(!strlen($fldlast_name))
      $sRegErr .= "The value in field Last Name* is required.<br>";
    
    if(!strlen($fldemail))
      $sRegErr .= "The value in field Email* is required.<br>";
    
    if(!is_number($fldcard_type_id))
      $sRegErr .= "The value in field Credit Card Type is incorrect.<br>";
    
    if(strlen($fldmember_login) )
    {
      $iCount = 0;

      if($sAction == "insert")
        $iCount = dlookup("members", "count(*)", "member_login=" . tosql($fldmember_login, "Text"));
      else if($sAction == "update")
        $iCount = dlookup("members", "count(*)", "member_login=" . tosql($fldmember_login, "Text") . " and not(" . $sWhere . ")");
  
      if($iCount > 0)
        $sRegErr .= "The value in field Login* is already in database.<br>";
    }                                                                               
    
if (get_param("member_password") != get_param("member_password2"))
  $sRegErr .= "\nPassword and Confirm Password fields don't match";

    if(strlen($sRegErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "insert":
      
        $sSQL = "insert into members (" . 
          "member_login," . 
          "member_password," . 
          "first_name," . 
          "last_name," . 
          "email," . 
          "address," . 
          "phone," . 
          "card_type_id," . 
          "card_number)" . 
          " values (" . 
          tosql($fldmember_login, "Text") . "," .
          tosql($fldmember_password, "Text") . "," .
          tosql($fldfirst_name, "Text") . "," .
          tosql($fldlast_name, "Text") . "," .
          tosql($fldemail, "Text") . "," .
          tosql($fldaddress, "Text") . "," .
          tosql($fldphone, "Text") . "," .
          tosql($fldcard_type_id, "Number") . "," .
          tosql($fldcard_number, "Text") . ")";    
    break;
  }
  // Execute SQL statement
  if(strlen($sRegErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName);
  
}

function Reg_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sRegErr;

  $sWhere = "";
  
  $bPK = true;
  $fldmember_id = "";
  $fldmember_login = "";
  $fldmember_password = "";
  $fldfirst_name = "";
  $fldlast_name = "";
  $fldemail = "";
  $fldaddress = "";
  $fldphone = "";
  $fldcard_type_id = "";
  $fldcard_number = "";
  

  if($sRegErr == "")
  {
    // Load primary key and form parameters
    $pmember_id = get_param("member_id");
    $tpl->set_var("RegError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldmember_id = strip(get_param("member_id"));
    $fldmember_login = strip(get_param("member_login"));
    $fldmember_password = strip(get_param("member_password"));
    $fldfirst_name = strip(get_param("first_name"));
    $fldlast_name = strip(get_param("last_name"));
    $fldemail = strip(get_param("email"));
    $fldaddress = strip(get_param("address"));
    $fldphone = strip(get_param("phone"));
    $fldcard_type_id = strip(get_param("card_type_id"));
    $fldcard_number = strip(get_param("card_number"));
    $pmember_id = get_param("PK_member_id");
    $tpl->set_var("sRegErr", $sRegErr);
    $tpl->parse("RegError", false);
  }

  
  $fldmember_password2 = get_param("member_password2");
  if( !strlen($pmember_id)) $bPK = false;
  
  $sWhere .= "member_id=" . tosql($pmember_id, "Number");
  $tpl->set_var("PK_member_id", $pmember_id);

  $sSQL = "select * from members where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Reg"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldmember_id = $db->f("member_id");
    if($sRegErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldmember_login = $db->f("member_login");
      $fldmember_password = $db->f("member_password");
      $fldfirst_name = $db->f("first_name");
      $fldlast_name = $db->f("last_name");
      $fldemail = $db->f("email");
      $fldaddress = $db->f("address");
      $fldphone = $db->f("phone");
      $fldcard_type_id = $db->f("card_type_id");
      $fldcard_number = $db->f("card_number");
    }
    $tpl->set_var("RegDelete", "");
    $tpl->set_var("RegUpdate", "");
    $tpl->set_var("RegInsert", "");
  }
  else
  {
    $tpl->set_var("RegEdit", "");
    $tpl->parse("RegInsert", false);
  }
  $tpl->parse("RegCancel", false);

  // Show form field
  
    $tpl->set_var("member_id", tohtml($fldmember_id));
    $tpl->set_var("member_login", tohtml($fldmember_login));
    $tpl->set_var("member_password", tohtml($fldmember_password));
    $tpl->set_var("member_password2", tohtml($fldmember_password2));
    $tpl->set_var("first_name", tohtml($fldfirst_name));
    $tpl->set_var("last_name", tohtml($fldlast_name));
    $tpl->set_var("email", tohtml($fldemail));
    $tpl->set_var("address", tohtml($fldaddress));
    $tpl->set_var("phone", tohtml($fldphone));
    $tpl->set_var("LBcard_type_id", "");
    $tpl->set_var("ID", "");
    $tpl->set_var("Value", "");
    $tpl->parse("LBcard_type_id", true);
    $dbcard_type_id = new DB_Sql();
    $dbcard_type_id->Database = DATABASE_NAME;
    $dbcard_type_id->User     = DATABASE_USER;
    $dbcard_type_id->Password = DATABASE_PASSWORD;
    $dbcard_type_id->Host     = DATABASE_HOST;
  
    
    $dbcard_type_id->query("select card_type_id, name from card_types order by 2");
    while($dbcard_type_id->next_record())
    {
      $tpl->set_var("ID", $dbcard_type_id->f(0));
      $tpl->set_var("Value", $dbcard_type_id->f(1));
      if($dbcard_type_id->f(0) == $fldcard_type_id)
        $tpl->set_var("Selected", "SELECTED" );
      else 
        $tpl->set_var("Selected", "");
      $tpl->parse("LBcard_type_id", true);
    }
    
    $tpl->set_var("card_number", tohtml($fldcard_number));
  $tpl->parse("FormReg", false);
  

}

?>