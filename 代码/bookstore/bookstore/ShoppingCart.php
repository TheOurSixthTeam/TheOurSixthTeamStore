<?php
include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "ShoppingCart.php";



check_security(1);

$tpl = new Template($app_path);
$tpl->load_file("ShoppingCart.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sMemberErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "Member":
    Member_action($sAction);
  break;
}Menu_show();
Footer_show();
Items_show();
Total_show();
Member_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Items_show()
{

  
  global $tpl;
  global $db;
  global $sItemsErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "");
  $tpl->set_var("FormParams", "");
  $bReq = true;
  // Build WHERE statement
  
  $pUserID = get_session("UserID");
  if(is_number($pUserID) && strlen($pUserID))
    $pUserID = round($pUserID);
  else 
    $pUserID = "";
  if(strlen($pUserID)) 
  {
    $HasParam = true;
    $sWhere .= "member_id=" . $pUserID;
  }
  else
    $bReq = false;
  if($HasParam)
    $sWhere = " AND (" . $sWhere . ")";

  $sDirection = "";
  $sSortParams = "";
  

  // Build full SQL statement
  
  $sSQL = "SELECT order_id, name, price, quantity, member_id, quantity*price as sub_total FROM items, orders WHERE orders.item_id=items.item_id" . $sWhere . " ORDER BY order_id";
  
  if(!$bReq)
  {
    $tpl->set_var("DListItems", "");
    $tpl->parse("ItemsNoRecords", false);
    $tpl->parse("FormItems", false);
    return;
  }

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldorder_id = $db->f("order_id");
			$flditem_id = $db->f("name");
			$fldprice = $db->f("price");
			$fldquantity = $db->f("quantity");
			$fldsub_total = $db->f("sub_total");
      $fldField1= "Details";
      $tpl->set_var("Field1", tohtml($fldField1));
      $tpl->set_var("Field1_URLLink", "ShoppingCartRecord.php");
      $tpl->set_var("Prm_order_id", tourl($db->f("order_id"))); 
      $tpl->set_var("order_id", tohtml($fldorder_id));
      $tpl->set_var("item_id", tohtml($flditem_id));
      $tpl->set_var("price", tohtml($fldprice));
      $tpl->set_var("quantity", tohtml($fldquantity));
      $tpl->set_var("sub_total", tohtml($fldsub_total));
      $tpl->parse("DListItems", true);
    } while($db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListItems", "");
    $tpl->parse("ItemsNoRecords", false);
    $tpl->parse("FormItems", false);
    return;
  }
  
  $tpl->set_var("ItemsNoRecords", "");
  $tpl->parse("FormItems", false);
  
}



function Total_show()
{

  
  global $tpl;
  global $db;
  global $sTotalErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "");
  $tpl->set_var("FormParams", "");
  $bReq = true;
  // Build WHERE statement
  
  $pUserID = get_session("UserID");
  if(is_number($pUserID) && strlen($pUserID))
    $pUserID = round($pUserID);
  else 
    $pUserID = "";
  if(strlen($pUserID)) 
  {
    $HasParam = true;
    $sWhere .= "member_id=" . $pUserID;
  }
  else
    $bReq = false;
  if($HasParam)
    $sWhere = " AND (" . $sWhere . ")";

  $sDirection = "";
  $sSortParams = "";
  

  // Build full SQL statement
  
  $sSQL = "SELECT member_id, sum(quantity*price) as sub_total FROM items, orders WHERE orders.item_id=items.item_id" . $sWhere . " GROUP BY member_id";
  
  if(!$bReq)
  {
    $tpl->set_var("DListTotal", "");
    $tpl->parse("TotalNoRecords", false);
    $tpl->parse("FormTotal", false);
    return;
  }

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldsub_total = $db->f("sub_total");
      $tpl->set_var("sub_total", tohtml($fldsub_total));
      $tpl->parse("DListTotal", true);
    } while($db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListTotal", "");
    $tpl->parse("TotalNoRecords", false);
    $tpl->parse("FormTotal", false);
    return;
  }
  
  $tpl->set_var("TotalNoRecords", "");
  $tpl->parse("FormTotal", false);
  
}



function Member_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sMemberErr;
  
  $sParams = "";
  $sActionFileName = "AdminMenu.php";

  
  $sParams = "?";
  $sParams .= "UserID=" . tourl(get_param("Trn_UserID"));

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName . $sParams); 

  

  // Load all form fields into variables
  
  $fldUserID = get_session("UserID");

  $sSQL = "";
  // Create SQL statement
  
  // Execute SQL statement
  if(strlen($sMemberErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName . $sParams);
  
}

function Member_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sMemberErr;

  $sWhere = "";
  
  $bPK = true;
  $fldmember_id = "";
  $fldmember_login = "";
  $fldname = "";
  $fldlast_name = "";
  $fldaddress = "";
  $fldemail = "";
  $fldphone = "";
  

  if($sMemberErr == "")
  {
    // Load primary key and form parameters
    $tpl->set_var("MemberError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldmember_id = strip(get_param("member_id"));
    $tpl->set_var("sMemberErr", $sMemberErr);
    $tpl->parse("MemberError", false);
  }

  
  $pmember_id = get_session("UserID");
  if( !strlen($pmember_id)) $bPK = false;
  
  $sWhere .= "member_id=" . tosql($pmember_id, "Number");
  $tpl->set_var("PK_member_id", $pmember_id);

  $sSQL = "select * from members where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Member"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldmember_id = $db->f("member_id");
    $fldmember_login = $db->f("member_login");
    $fldname = $db->f("first_name");
    $fldlast_name = $db->f("last_name");
    $fldaddress = $db->f("address");
    $fldemail = $db->f("email");
    $fldphone = $db->f("phone");
    $tpl->set_var("MemberDelete", "");
    $tpl->set_var("MemberUpdate", "");
    $tpl->set_var("MemberInsert", "");
  }
  else
  {
    if($sMemberErr == "")
    {
      $fldmember_id = tohtml(get_session("UserID"));
    }
    $tpl->set_var("MemberEdit", "");
    $tpl->set_var("MemberInsert", "");
  }
  $tpl->set_var("MemberCancel", "");

  // Show form field
  
    $tpl->set_var("member_id", tohtml($fldmember_id));
      $tpl->set_var("member_login", tohtml($fldmember_login));
      $tpl->set_var("member_login_URLLink", "MyInfo.php");
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("last_name", tohtml($fldlast_name));
      $tpl->set_var("address", tohtml($fldaddress));
      $tpl->set_var("email", tohtml($fldemail));
      $tpl->set_var("phone", tohtml($fldphone));
  $tpl->parse("FormMember", false);
  

}

?>