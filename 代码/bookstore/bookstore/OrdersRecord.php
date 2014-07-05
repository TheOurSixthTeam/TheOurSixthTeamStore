<?php
include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "OrdersRecord.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("OrdersRecord.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sOrdersErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "Orders":
    Orders_action($sAction);
  break;
}Menu_show();
Footer_show();
Orders_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Orders_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sOrdersErr;
  
  $sParams = "";
  $sActionFileName = "OrdersGrid.php";

  
  $sParams = "?";
  $sParams .= "item_id=" . tourl(get_param("Trn_item_id")) . "&";
  $sParams .= "member_id=" . tourl(get_param("Trn_member_id"));

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName . $sParams); 

  
  // Create WHERE statement
  if($sAction == "update" || $sAction == "delete") 
  {
    $pPKorder_id = get_param("PK_order_id");
    if( !strlen($pPKorder_id)) return;
    $sWhere = "order_id=" . tosql($pPKorder_id, "Number");
  }

  // Load all form fields into variables
  
  $fldmember_id = get_param("member_id");
  $flditem_id = get_param("item_id");
  $fldquantity = get_param("quantity");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldmember_id))
      $sOrdersErr .= "The value in field Member is required.<br>";
    
    if(!strlen($flditem_id))
      $sOrdersErr .= "The value in field Item is required.<br>";
    
    if(!strlen($fldquantity))
      $sOrdersErr .= "The value in field Quantity is required.<br>";
    
    if(!is_number($fldmember_id))
      $sOrdersErr .= "The value in field Member is incorrect.<br>";
    
    if(!is_number($flditem_id))
      $sOrdersErr .= "The value in field Item is incorrect.<br>";
    
    if(!is_number($fldquantity))
      $sOrdersErr .= "The value in field Quantity is incorrect.<br>";
    

    if(strlen($sOrdersErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "insert":
      
        $sSQL = "insert into orders (" . 
          "member_id," . 
          "item_id," . 
          "quantity)" . 
          " values (" . 
          tosql($fldmember_id, "Number") . "," .
          tosql($flditem_id, "Number") . "," .
          tosql($fldquantity, "Number") . ")";    
    break;
    case "update":
      
        $sSQL = "update orders set " .
          "member_id=" . tosql($fldmember_id, "Number") .
          ",item_id=" . tosql($flditem_id, "Number") .
          ",quantity=" . tosql($fldquantity, "Number");
        $sSQL .= " where " . $sWhere;
    break;
    case "delete":
      
        $sSQL = "delete from orders where " . $sWhere;
    break;
  }
  // Execute SQL statement
  if(strlen($sOrdersErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName . $sParams);
  
}

function Orders_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sOrdersErr;

  $sWhere = "";
  
  $bPK = true;
  $fldorder_id = "";
  $fldmember_id = "";
  $flditem_id = "";
  $fldquantity = "";
  

  if($sOrdersErr == "")
  {
    // Load primary key and form parameters
    $flditem_id = get_param("item_id");
    $fldmember_id = get_param("member_id");
    $fldorder_id = get_param("order_id");
    $tpl->set_var("Trn_item_id", get_param("item_id"));
    $tpl->set_var("Trn_member_id", get_param("member_id"));
    $porder_id = get_param("order_id");
    $tpl->set_var("OrdersError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldmember_id = strip(get_param("member_id"));
    $flditem_id = strip(get_param("item_id"));
    $fldquantity = strip(get_param("quantity"));
    $tpl->set_var("Trn_item_id", get_param("Trn_item_id"));
    $tpl->set_var("Trn_member_id", get_param("Trn_member_id"));
    $porder_id = get_param("PK_order_id");
    $tpl->set_var("sOrdersErr", $sOrdersErr);
    $tpl->parse("OrdersError", false);
  }

  
  if( !strlen($porder_id)) $bPK = false;
  
  $sWhere .= "order_id=" . tosql($porder_id, "Number");
  $tpl->set_var("PK_order_id", $porder_id);

  $sSQL = "select * from orders where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Orders"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldorder_id = $db->f("order_id");
    if($sOrdersErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldmember_id = $db->f("member_id");
      $flditem_id = $db->f("item_id");
      $fldquantity = $db->f("quantity");
    }
    $tpl->set_var("OrdersInsert", "");
    $tpl->parse("OrdersEdit", false);
  }
  else
  {
    if($sOrdersErr == "")
    {
      $fldorder_id = tohtml(get_param("order_id"));
      $fldmember_id = tohtml(get_param("member_id"));
      $flditem_id = tohtml(get_param("item_id"));
    }
    $tpl->set_var("OrdersEdit", "");
    $tpl->parse("OrdersInsert", false);
  }
  $tpl->parse("OrdersCancel", false);

  // Show form field
  
      $tpl->set_var("order_id", tohtml($fldorder_id));
    $tpl->set_var("LBmember_id", "");
    $dbmember_id = new DB_Sql();
    $dbmember_id->Database = DATABASE_NAME;
    $dbmember_id->User     = DATABASE_USER;
    $dbmember_id->Password = DATABASE_PASSWORD;
    $dbmember_id->Host     = DATABASE_HOST;
  
    
    $dbmember_id->query("select member_id, member_login from members order by 2");
    while($dbmember_id->next_record())
    {
      $tpl->set_var("ID", $dbmember_id->f(0));
      $tpl->set_var("Value", $dbmember_id->f(1));
      if($dbmember_id->f(0) == $fldmember_id)
        $tpl->set_var("Selected", "SELECTED" );
      else 
        $tpl->set_var("Selected", "");
      $tpl->parse("LBmember_id", true);
    }
    
    $tpl->set_var("LBitem_id", "");
    $dbitem_id = new DB_Sql();
    $dbitem_id->Database = DATABASE_NAME;
    $dbitem_id->User     = DATABASE_USER;
    $dbitem_id->Password = DATABASE_PASSWORD;
    $dbitem_id->Host     = DATABASE_HOST;
  
    
    $dbitem_id->query("select item_id, name from items order by 2");
    while($dbitem_id->next_record())
    {
      $tpl->set_var("ID", $dbitem_id->f(0));
      $tpl->set_var("Value", $dbitem_id->f(1));
      if($dbitem_id->f(0) == $flditem_id)
        $tpl->set_var("Selected", "SELECTED" );
      else 
        $tpl->set_var("Selected", "");
      $tpl->parse("LBitem_id", true);
    }
    
    $tpl->set_var("quantity", tohtml($fldquantity));
  $tpl->parse("FormOrders", false);
  

}

?>