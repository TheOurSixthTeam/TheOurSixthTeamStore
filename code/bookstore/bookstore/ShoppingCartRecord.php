<?php

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "ShoppingCartRecord.php";



check_security(1);

$tpl = new Template($app_path);
$tpl->load_file("ShoppingCartRecord.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sShoppingCartRecordErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "ShoppingCartRecord":
    ShoppingCartRecord_action($sAction);
  break;
}Menu_show();
Footer_show();
ShoppingCartRecord_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function ShoppingCartRecord_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sShoppingCartRecordErr;
  
  $sParams = "";
  $sActionFileName = "ShoppingCart.php";

  

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName); 

  
  // Create WHERE statement
  if($sAction == "update" || $sAction == "delete") 
  {
    $pPKorder_id = get_param("PK_order_id");
    if( !strlen($pPKorder_id)) return;
    $sWhere = "order_id=" . tosql($pPKorder_id, "Number");
  }

  // Load all form fields into variables
  
  $fldUserID = get_session("UserID");
  $fldmember_id = get_param("member_id");
  $fldquantity = get_param("quantity");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldquantity))
      $sShoppingCartRecordErr .= "The value in field Quantity is required.<br>";
    
    if(!is_number($fldmember_id))
      $sShoppingCartRecordErr .= "The value in field member_id is incorrect.<br>";
    
    if(!is_number($fldquantity))
      $sShoppingCartRecordErr .= "The value in field Quantity is incorrect.<br>";
    

    if(strlen($sShoppingCartRecordErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "update":
      
        $sSQL = "update orders set " .
          "member_id=" . tosql($fldmember_id, "Number") .
          ",quantity=" . tosql($fldquantity, "Number");
        $sSQL .= " where " . $sWhere;
    break;
    case "delete":
      
        $sSQL = "delete from orders where " . $sWhere;
    break;
  }
  // Execute SQL statement
  if(strlen($sShoppingCartRecordErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName);
  
}

function ShoppingCartRecord_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sShoppingCartRecordErr;

  $sWhere = "";
  
  $bPK = true;
  $fldorder_id = "";
  $fldmember_id = "";
  $flditem_id = "";
  $fldquantity = "";
  

  if($sShoppingCartRecordErr == "")
  {
    // Load primary key and form parameters
    $porder_id = get_param("order_id");
    $tpl->set_var("ShoppingCartRecordError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldorder_id = strip(get_param("order_id"));
    $fldmember_id = strip(get_param("member_id"));
    $fldquantity = strip(get_param("quantity"));
    $porder_id = get_param("PK_order_id");
    $tpl->set_var("sShoppingCartRecordErr", $sShoppingCartRecordErr);
    $tpl->parse("ShoppingCartRecordError", false);
  }

  
  if( !strlen($porder_id)) $bPK = false;
  
  $sWhere .= "order_id=" . tosql($porder_id, "Number");
  $tpl->set_var("PK_order_id", $porder_id);

  $sSQL = "select * from orders where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "ShoppingCartRecord"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldorder_id = $db->f("order_id");
    $fldmember_id = $db->f("member_id");
    $flditem_id = $db->f("item_id");
    if($sShoppingCartRecordErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldquantity = $db->f("quantity");
    }
    $tpl->set_var("ShoppingCartRecordInsert", "");
    $tpl->parse("ShoppingCartRecordEdit", false);
  }
  else
  {
    if($sShoppingCartRecordErr == "")
    {
      $fldmember_id = tohtml(get_session("UserID"));
    }
    $tpl->set_var("ShoppingCartRecordEdit", "");
    $tpl->set_var("ShoppingCartRecordInsert", "");
  }
  $tpl->parse("ShoppingCartRecordCancel", false);
  // Set lookup fields
  $flditem_id = dlookup("items", "name", "item_id=" . tosql($flditem_id, "Number"));

  // Show form field
  
    $tpl->set_var("order_id", tohtml($fldorder_id));
    $tpl->set_var("member_id", tohtml($fldmember_id));
      $tpl->set_var("item_id", tohtml($flditem_id));
    $tpl->set_var("quantity", tohtml($fldquantity));
  $tpl->parse("FormShoppingCartRecord", false);
  

}

?>