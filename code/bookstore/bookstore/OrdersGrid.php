<?php
include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "OrdersGrid.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("OrdersGrid.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
Search_show();
Orders_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************


function Search_show()
{
  global $db;
  global $tpl;
  
  $tpl->set_var("ActionPage", "OrdersGrid.php");
	
  // Set variables with search parameters
  $flditem_id = strip(get_param("item_id"));
  $fldmember_id = strip(get_param("member_id"));
    // Show fields
    $tpl->set_var("LBitem_id", "");
    $tpl->set_var("ID", "");
    $tpl->set_var("Value", "All");
    $tpl->parse("LBitem_id", true);
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
    
    $tpl->set_var("LBmember_id", "");
    $tpl->set_var("ID", "");
    $tpl->set_var("Value", "All");
    $tpl->parse("LBmember_id", true);
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
    
  $tpl->parse("FormSearch", false);
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

  
  $tpl->set_var("TransitParams", "item_id=" . tourl(strip(get_param("item_id"))) . "&member_id=" . tourl(strip(get_param("member_id"))) . "&");
  $tpl->set_var("FormParams", "item_id=" . tourl(strip(get_param("item_id"))) . "&member_id=" . tourl(strip(get_param("member_id"))) . "&");
  // Build WHERE statement
  
  $pitem_id = get_param("item_id");
  if(is_number($pitem_id) && strlen($pitem_id))
    $pitem_id = round($pitem_id);
  else 
    $pitem_id = "";
  if(strlen($pitem_id)) 
  {
    $HasParam = true;
    $sWhere .= "o.item_id=" . $pitem_id;
  }
  $pmember_id = get_param("member_id");
  if(is_number($pmember_id) && strlen($pmember_id))
    $pmember_id = round($pmember_id);
  else 
    $pmember_id = "";
  if(strlen($pmember_id)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "o.member_id=" . $pmember_id;
  }
  if($HasParam)
    $sWhere = " AND (" . $sWhere . ")";

  $sDirection = "";
  $sSortParams = "";
  
  // Build ORDER statement
  $sOrder = " order by o.order_id Asc";
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
    
    if ($iSort == 1) $sOrder = " order by i.name" . $sDirection;
    if ($iSort == 2) $sOrder = " order by m.member_login" . $sDirection;
    if ($iSort == 3) $sOrder = " order by o.quantity" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select o.item_id as o_item_id, " . 
    "o.member_id as o_member_id, " . 
    "o.order_id as o_order_id, " . 
    "o.quantity as o_quantity, " . 
    "i.item_id as i_item_id, " . 
    "i.name as i_name, " . 
    "m.member_id as m_member_id, " . 
    "m.member_login as m_member_login " . 
    " from orders o, items i, members m" . 
    " where i.item_id=o.item_id and m.member_id=o.member_id  ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("FormAction", "OrdersRecord.php");
  $tpl->set_var("SortParams", $sSortParams);

  // Execute SQL statement
  $db->query($sSQL);
  
  // Select current page
  $iPage = get_param("FormOrders_Page");
  if(!strlen($iPage)) $iPage = 1;
  $RecordsPerPage = 20;
  if(($iPage - 1) * $RecordsPerPage != 0)
    $db->seek(($iPage - 1) * $RecordsPerPage);
  $iCounter = 0;

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldorder_id = $db->f("o_order_id");
			$flditem_id = $db->f("i_name");
			$fldmember_id = $db->f("m_member_login");
			$fldquantity = $db->f("o_quantity");
      $fldField1= "Edit";
      $tpl->set_var("Field1", tohtml($fldField1));
      $tpl->set_var("Field1_URLLink", "OrdersRecord.php");
      $tpl->set_var("Prm_order_id", tourl($db->f("o_order_id"))); 
    $tpl->set_var("order_id", tohtml($fldorder_id));
      $tpl->set_var("item_id", tohtml($flditem_id));
      $tpl->set_var("member_id", tohtml($fldmember_id));
      $tpl->set_var("quantity", tohtml($fldquantity));
      $tpl->parse("DListOrders", true);
      $iCounter++;
    } while($iCounter < $RecordsPerPage &&$db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListOrders", "");
    $tpl->parse("OrdersNoRecords", false);
    $tpl->set_var("OrdersScroller", "");
    $tpl->parse("FormOrders", false);
    return;
  }
  
  // Parse scroller
  if(@$db->next_record())
  {
    if ($iPage == 1)
    {
      $tpl->set_var("OrdersScrollerPrevSwitch", "_");
    }
    else
    {
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("OrdersScrollerPrevSwitch", "");
    }
    $tpl->set_var("NextPage", ($iPage + 1));
    $tpl->set_var("OrdersScrollerNextSwitch", "");
    $tpl->set_var("OrdersCurrentPage", $iPage);
    $tpl->parse("OrdersScroller", false);
  }
  else
  {
    if ($iPage == 1)
    {
      $tpl->set_var("OrdersScroller", "");
    }
    else
    {
      $tpl->set_var("OrdersScrollerNextSwitch", "_");
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("OrdersScrollerPrevSwitch", "");
      $tpl->set_var("OrdersCurrentPage", $iPage);
      $tpl->parse("OrdersScroller", false);
    }
  }
  $tpl->set_var("OrdersNoRecords", "");
  $tpl->parse("FormOrders", false);
  
}

?>