<?php
/*********************************************************************************
 *       Filename: MembersInfo.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "MembersInfo.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("MembersInfo.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sRecordErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "Record":
    Record_action($sAction);
  break;
}Menu_show();
Footer_show();
Record_show();
Orders_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Record_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sRecordErr;
  
  $sParams = "";
  $sActionFileName = "AdminMenu.php";

  

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName); 

  

  // Load all form fields into variables
  

  $sSQL = "";
  // Create SQL statement
  
  // Execute SQL statement
  if(strlen($sRecordErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName);
  
}

function Record_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sRecordErr;

  $sWhere = "";
  
  $bPK = true;
  $fldmember_id = "";
  $fldmember_login = "";
  $fldmember_level = "";
  $fldname = "";
  $fldlast_name = "";
  $fldemail = "";
  $fldphone = "";
  $fldaddress = "";
  $fldnotes = "";
  

  if($sRecordErr == "")
  {
    // Load primary key and form parameters
    $pmember_id = get_param("member_id");
    $tpl->set_var("RecordError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldmember_id = strip(get_param("member_id"));
    $pmember_id = get_param("PK_member_id");
    $tpl->set_var("sRecordErr", $sRecordErr);
    $tpl->parse("RecordError", false);
  }

  
  if( !strlen($pmember_id)) $bPK = false;
  
  $sWhere .= "member_id=" . tosql($pmember_id, "Number");
  $tpl->set_var("PK_member_id", $pmember_id);

  $sSQL = "select * from members where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Record"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldmember_id = $db->f("member_id");
    $fldmember_login = $db->f("member_login");
    $fldmember_level = $db->f("member_level");
    $fldname = $db->f("first_name");
    $fldlast_name = $db->f("last_name");
    $fldemail = $db->f("email");
    $fldphone = $db->f("phone");
    $fldaddress = $db->f("address");
    $fldnotes = $db->f("notes");
    $tpl->set_var("RecordDelete", "");
    $tpl->set_var("RecordUpdate", "");
    $tpl->set_var("RecordInsert", "");
  }
  else
  {
    $tpl->set_var("RecordEdit", "");
    $tpl->set_var("RecordInsert", "");
  }
  $tpl->set_var("RecordCancel", "");

  // Show form field
  
    $tpl->set_var("member_id", tohtml($fldmember_id));
      $tpl->set_var("member_login", tohtml($fldmember_login));
      $tpl->set_var("member_login_URLLink", "MembersRecord.php");
      $tpl->set_var("Prm_member_id", tourl($db->f("member_id"))); 
      $tpl->set_var("member_level", tohtml($fldmember_level));
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("last_name", tohtml($fldlast_name));
      $tpl->set_var("email", tohtml($fldemail));
      $tpl->set_var("phone", tohtml($fldphone));
      $tpl->set_var("address", tohtml($fldaddress));
      $tpl->set_var("notes", tohtml($fldnotes));
  $tpl->parse("FormRecord", false);
  

}



function Orders_show()
{

  
  global $tpl;
  global $db;
  global $sOrdersErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "member_id=" . tourl(strip(get_param("member_id"))) . "&");
  $tpl->set_var("FormParams", "member_id=" . tourl(strip(get_param("member_id"))) . "&");
  $bReq = true;
  // Build WHERE statement
  
  $pmember_id = get_param("member_id");
  if(is_number($pmember_id) && strlen($pmember_id))
    $pmember_id = round($pmember_id);
  else 
    $pmember_id = "";
  if(strlen($pmember_id)) 
  {
    $HasParam = true;
    $sWhere .= "o.member_id=" . $pmember_id;
  }
  else
    $bReq = false;
  if($HasParam)
    $sWhere = " AND (" . $sWhere . ")";

  $sDirection = "";
  $sSortParams = "";
  
  // Build ORDER statement
  $iSort = get_param("FormOrders_Sorting");
  $iSorted = get_param("FormOrders_Sorted");
  if(!$iSort)
  {
    $tpl->set_var("Form_Sorting", "");
  }
  else
  {
    if($iSort == $iSorted)
    {
      $tpl->set_var("Form_Sorting", "");
      $sDirection = " DESC";
      $sSortParams = "FormOrders_Sorting=" . $iSort . "&FormOrders_Sorted=" . $iSort . "&";
    }
    else
    {
      $tpl->set_var("Form_Sorting", $iSort);
      $sDirection = " ASC";
      $sSortParams = "FormOrders_Sorting=" . $iSort . "&FormOrders_Sorted=" . "&";
    }
    
    if ($iSort == 1) $sOrder = " order by o.order_id" . $sDirection;
    if ($iSort == 2) $sOrder = " order by i.name" . $sDirection;
    if ($iSort == 3) $sOrder = " order by o.quantity" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select o.item_id as o_item_id, " . 
    "o.member_id as o_member_id, " . 
    "o.order_id as o_order_id, " . 
    "o.quantity as o_quantity, " . 
    "i.item_id as i_item_id, " . 
    "i.name as i_name " . 
    " from orders o, items i" . 
    " where i.item_id=o.item_id  ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("SortParams", $sSortParams);
  if(!$bReq)
  {
    $tpl->set_var("DListOrders", "");
    $tpl->parse("OrdersNoRecords", false);
    $tpl->parse("FormOrders", false);
    return;
  }

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldorder_id = $db->f("o_order_id");
			$flditem_id = $db->f("i_name");
			$fldquantity = $db->f("o_quantity");
      $tpl->set_var("order_id", tohtml($fldorder_id));
      $tpl->set_var("order_id_URLLink", "OrdersRecord.php");
      $tpl->set_var("Prm_order_id", tourl($db->f("o_order_id"))); 
      $tpl->set_var("item_id", tohtml($flditem_id));
      $tpl->set_var("quantity", tohtml($fldquantity));
      $tpl->parse("DListOrders", true);
    } while($db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListOrders", "");
    $tpl->parse("OrdersNoRecords", false);
    $tpl->parse("FormOrders", false);
    return;
  }
  
  $tpl->set_var("OrdersNoRecords", "");
  $tpl->parse("FormOrders", false);
  
}

?>