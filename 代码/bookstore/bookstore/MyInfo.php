<?php
/*********************************************************************************
 *       Filename: MyInfo.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "MyInfo.php";



check_security(1);

$tpl = new Template($app_path);
$tpl->load_file("MyInfo.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sFormErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "Form":
    Form_action($sAction);
  break;
}Menu_show();
Footer_show();
Form_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Form_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sFormErr;
  
  $sParams = "";
  $sActionFileName = "ShoppingCart.php";

  

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName); 

  
  // Create WHERE statement
  if($sAction == "update" || $sAction == "delete") 
  {
    $pPKmember_id = get_param("PK_member_id");
    if( !strlen($pPKmember_id)) return;
    $sWhere = "member_id=" . tosql($pPKmember_id, "Number");
  }

  // Load all form fields into variables
  
  $fldUserID = get_session("UserID");
  $fldmember_password = get_param("member_password");
  $fldname = get_param("name");
  $fldlast_name = get_param("last_name");
  $fldemail = get_param("email");
  $fldaddress = get_param("address");
  $fldphone = get_param("phone");
  $fldnotes = get_param("notes");
  $fldcard_type_id = get_param("card_type_id");
  $fldcard_number = get_param("card_number");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldmember_password))
      $sFormErr .= "The value in field Password* is required.<br>";
    
    if(!strlen($fldname))
      $sFormErr .= "The value in field First Name* is required.<br>";
    
    if(!strlen($fldlast_name))
      $sFormErr .= "The value in field Last Name* is required.<br>";
    
    if(!strlen($fldemail))
      $sFormErr .= "The value in field Email* is required.<br>";
    
    if(!is_number($fldcard_type_id))
      $sFormErr .= "The value in field Credit Card Type is incorrect.<br>";
    

    if(strlen($sFormErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "update":
      
        $sSQL = "update members set " .
          "member_password=" . tosql($fldmember_password, "Text") .
          ",first_name=" . tosql($fldname, "Text") .
          ",last_name=" . tosql($fldlast_name, "Text") .
          ",email=" . tosql($fldemail, "Text") .
          ",address=" . tosql($fldaddress, "Text") .
          ",phone=" . tosql($fldphone, "Text") .
          ",notes=" . tosql($fldnotes, "Text") .
          ",card_type_id=" . tosql($fldcard_type_id, "Number") .
          ",card_number=" . tosql($fldcard_number, "Text");
        $sSQL .= " where " . $sWhere;
    break;
  }
  // Execute SQL statement
  if(strlen($sFormErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName);
  
}

function Form_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sFormErr;

  $sWhere = "";
  
  $bPK = true;
  $fldmember_id = "";
  $fldmember_login = "";
  $fldmember_password = "";
  $fldname = "";
  $fldlast_name = "";
  $fldemail = "";
  $fldaddress = "";
  $fldphone = "";
  $fldnotes = "";
  $fldcard_type_id = "";
  $fldcard_number = "";
  

  if($sFormErr == "")
  {
    // Load primary key and form parameters
    $tpl->set_var("FormError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldmember_id = strip(get_param("member_id"));
    $fldmember_password = strip(get_param("member_password"));
    $fldname = strip(get_param("name"));
    $fldlast_name = strip(get_param("last_name"));
    $fldemail = strip(get_param("email"));
    $fldaddress = strip(get_param("address"));
    $fldphone = strip(get_param("phone"));
    $fldnotes = strip(get_param("notes"));
    $fldcard_type_id = strip(get_param("card_type_id"));
    $fldcard_number = strip(get_param("card_number"));
    $tpl->set_var("sFormErr", $sFormErr);
    $tpl->parse("FormError", false);
  }

  
  $pmember_id = get_session("UserID");
  if( !strlen($pmember_id)) $bPK = false;
  
  $sWhere .= "member_id=" . tosql($pmember_id, "Number");
  $tpl->set_var("PK_member_id", $pmember_id);

  $sSQL = "select * from members where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Form"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldmember_id = $db->f("member_id");
    $fldmember_login = $db->f("member_login");
    if($sFormErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldmember_password = $db->f("member_password");
      $fldname = $db->f("first_name");
      $fldlast_name = $db->f("last_name");
      $fldemail = $db->f("email");
      $fldaddress = $db->f("address");
      $fldphone = $db->f("phone");
      $fldnotes = $db->f("notes");
      $fldcard_type_id = $db->f("card_type_id");
      $fldcard_number = $db->f("card_number");
    }
    $tpl->set_var("FormDelete", "");
    $tpl->set_var("FormInsert", "");
    $tpl->parse("FormEdit", false);
  }
  else
  {
    if($sFormErr == "")
    {
      $fldmember_id = tohtml(get_session("UserID"));
    }
    $tpl->set_var("FormEdit", "");
    $tpl->set_var("FormInsert", "");
  }
  $tpl->parse("FormCancel", false);

  // Show form field
  
    $tpl->set_var("member_id", tohtml($fldmember_id));
      $tpl->set_var("member_login", tohtml($fldmember_login));
    $tpl->set_var("member_password", tohtml($fldmember_password));
    $tpl->set_var("name", tohtml($fldname));
    $tpl->set_var("last_name", tohtml($fldlast_name));
    $tpl->set_var("email", tohtml($fldemail));
    $tpl->set_var("address", tohtml($fldaddress));
    $tpl->set_var("phone", tohtml($fldphone));
    $tpl->set_var("notes", tohtml($fldnotes));
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
  $tpl->parse("FormForm", false);
  

}

?>