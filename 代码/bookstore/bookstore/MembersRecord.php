<?php
/*********************************************************************************
 *       Filename: MembersRecord.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "MembersRecord.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("MembersRecord.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sMembersErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "Members":
    Members_action($sAction);
  break;
}Menu_show();
Footer_show();
Members_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Members_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sMembersErr;
  
  $sParams = "";
  $sActionFileName = "MembersGrid.php";

  
  $sParams = "?";
  $sParams .= "member_login=" . tourl(get_param("Trn_member_login"));

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName . $sParams); 

  
  // Create WHERE statement
  if($sAction == "update" || $sAction == "delete") 
  {
    $pPKmember_id = get_param("PK_member_id");
    if( !strlen($pPKmember_id)) return;
    $sWhere = "member_id=" . tosql($pPKmember_id, "Number");
  }

  // Load all form fields into variables
  
  $fldmember_login = get_param("member_login");
  $fldmember_password = get_param("member_password");
  $fldmember_level = get_param("member_level");
  $fldname = get_param("name");
  $fldlast_name = get_param("last_name");
  $fldemail = get_param("email");
  $fldphone = get_param("phone");
  $fldaddress = get_param("address");
  $fldnotes = get_param("notes");
  $fldcard_type_id = get_param("card_type_id");
  $fldcard_number = get_param("card_number");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldmember_login))
      $sMembersErr .= "The value in field Login* is required.<br>";
    
    if(!strlen($fldmember_password))
      $sMembersErr .= "The value in field Password* is required.<br>";
    
    if(!strlen($fldmember_level))
      $sMembersErr .= "The value in field Level* is required.<br>";
    
    if(!strlen($fldname))
      $sMembersErr .= "The value in field First Name* is required.<br>";
    
    if(!strlen($fldlast_name))
      $sMembersErr .= "The value in field Last Name* is required.<br>";
    
    if(!strlen($fldemail))
      $sMembersErr .= "The value in field Email* is required.<br>";
    
    if(!is_number($fldmember_level))
      $sMembersErr .= "The value in field Level* is incorrect.<br>";
    
    if(!is_number($fldcard_type_id))
      $sMembersErr .= "The value in field Credit Card Type is incorrect.<br>";
    
    if(strlen($fldmember_login) )
    {
      $iCount = 0;

      if($sAction == "insert")
        $iCount = dlookup("members", "count(*)", "member_login=" . tosql($fldmember_login, "Text"));
      else if($sAction == "update")
        $iCount = dlookup("members", "count(*)", "member_login=" . tosql($fldmember_login, "Text") . " and not(" . $sWhere . ")");
  
      if($iCount > 0)
        $sMembersErr .= "The value in field Login* is already in database.<br>";
    }                                                                               
    

    if(strlen($sMembersErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "insert":
      
        $sSQL = "insert into members (" . 
          "member_login," . 
          "member_password," . 
          "member_level," . 
          "first_name," . 
          "last_name," . 
          "email," . 
          "phone," . 
          "address," . 
          "notes," . 
          "card_type_id," . 
          "card_number)" . 
          " values (" . 
          tosql($fldmember_login, "Text") . "," .
          tosql($fldmember_password, "Text") . "," .
          tosql($fldmember_level, "Number") . "," .
          tosql($fldname, "Text") . "," .
          tosql($fldlast_name, "Text") . "," .
          tosql($fldemail, "Text") . "," .
          tosql($fldphone, "Text") . "," .
          tosql($fldaddress, "Text") . "," .
          tosql($fldnotes, "Text") . "," .
          tosql($fldcard_type_id, "Number") . "," .
          tosql($fldcard_number, "Text") . ")";    
    break;
    case "update":
      
        $sSQL = "update members set " .
          "member_login=" . tosql($fldmember_login, "Text") .
          ",member_password=" . tosql($fldmember_password, "Text") .
          ",member_level=" . tosql($fldmember_level, "Number") .
          ",first_name=" . tosql($fldname, "Text") .
          ",last_name=" . tosql($fldlast_name, "Text") .
          ",email=" . tosql($fldemail, "Text") .
          ",phone=" . tosql($fldphone, "Text") .
          ",address=" . tosql($fldaddress, "Text") .
          ",notes=" . tosql($fldnotes, "Text") .
          ",card_type_id=" . tosql($fldcard_type_id, "Number") .
          ",card_number=" . tosql($fldcard_number, "Text");
        $sSQL .= " where " . $sWhere;
    break;
    case "delete":
      
        $sSQL = "delete from members where " . $sWhere;
    break;
  }
  // Execute SQL statement
  if(strlen($sMembersErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName . $sParams);
  
}

function Members_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sMembersErr;

  $sWhere = "";
  
  $bPK = true;
  $fldmember_id = "";
  $fldmember_login = "";
  $fldmember_password = "";
  $fldmember_level = "";
  $fldname = "";
  $fldlast_name = "";
  $fldemail = "";
  $fldphone = "";
  $fldaddress = "";
  $fldnotes = "";
  $fldcard_type_id = "";
  $fldcard_number = "";
  

  if($sMembersErr == "")
  {
    // Load primary key and form parameters
    $fldmember_login = get_param("member_login");
    $fldmember_id = get_param("member_id");
    $tpl->set_var("Trn_member_login", get_param("member_login"));
    $pmember_id = get_param("member_id");
    $tpl->set_var("MembersError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldmember_id = strip(get_param("member_id"));
    $fldmember_login = strip(get_param("member_login"));
    $fldmember_password = strip(get_param("member_password"));
    $fldmember_level = strip(get_param("member_level"));
    $fldname = strip(get_param("name"));
    $fldlast_name = strip(get_param("last_name"));
    $fldemail = strip(get_param("email"));
    $fldphone = strip(get_param("phone"));
    $fldaddress = strip(get_param("address"));
    $fldnotes = strip(get_param("notes"));
    $fldcard_type_id = strip(get_param("card_type_id"));
    $fldcard_number = strip(get_param("card_number"));
    $tpl->set_var("Trn_member_login", get_param("Trn_member_login"));
    $pmember_id = get_param("PK_member_id");
    $tpl->set_var("sMembersErr", $sMembersErr);
    $tpl->parse("MembersError", false);
  }

  
  if( !strlen($pmember_id)) $bPK = false;
  
  $sWhere .= "member_id=" . tosql($pmember_id, "Number");
  $tpl->set_var("PK_member_id", $pmember_id);

  $sSQL = "select * from members where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Members"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldmember_id = $db->f("member_id");
    if($sMembersErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldmember_login = $db->f("member_login");
      $fldmember_password = $db->f("member_password");
      $fldmember_level = $db->f("member_level");
      $fldname = $db->f("first_name");
      $fldlast_name = $db->f("last_name");
      $fldemail = $db->f("email");
      $fldphone = $db->f("phone");
      $fldaddress = $db->f("address");
      $fldnotes = $db->f("notes");
      $fldcard_type_id = $db->f("card_type_id");
      $fldcard_number = $db->f("card_number");
    }
    $tpl->set_var("MembersInsert", "");
    $tpl->parse("MembersEdit", false);
  }
  else
  {
    if($sMembersErr == "")
    {
      $fldmember_id = tohtml(get_param("member_id"));
      $fldmember_login = tohtml(get_param("member_login"));
    }
    $tpl->set_var("MembersEdit", "");
    $tpl->parse("MembersInsert", false);
  }
  $tpl->parse("MembersCancel", false);

  // Show form field
  
    $tpl->set_var("member_id", tohtml($fldmember_id));
    $tpl->set_var("member_login", tohtml($fldmember_login));
    $tpl->set_var("member_password", tohtml($fldmember_password));
    $tpl->set_var("LBmember_level", "");
    $LOV = split(";", "1;Member;2;Administrator");
  
    if(sizeof($LOV)%2 != 0) 
      $array_length = sizeof($LOV) - 1;
    else
      $array_length = sizeof($LOV);
    reset($LOV);
    for($i = 0; $i < $array_length; $i = $i + 2)
    {
      $tpl->set_var("ID", $LOV[$i]);
      $tpl->set_var("Value", $LOV[$i + 1]);
      if($LOV[$i] == $fldmember_level) 
        $tpl->set_var("Selected", "SELECTED");
      else
        $tpl->set_var("Selected", "");
      $tpl->parse("LBmember_level", true);
    }
    $tpl->set_var("name", tohtml($fldname));
    $tpl->set_var("last_name", tohtml($fldlast_name));
    $tpl->set_var("email", tohtml($fldemail));
    $tpl->set_var("phone", tohtml($fldphone));
    $tpl->set_var("address", tohtml($fldaddress));
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
  $tpl->parse("FormMembers", false);
  

}

?>