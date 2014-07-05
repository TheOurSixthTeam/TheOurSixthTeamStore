<?php
/*********************************************************************************
 *       Filename: AdminBooks.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "AdminBooks.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("AdminBooks.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
Search_show();
Items_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************


function Search_show()
{
  global $db;
  global $tpl;
  
  $tpl->set_var("ActionPage", "AdminBooks.php");
	
  // Set variables with search parameters
  $fldcategory_id = strip(get_param("category_id"));
  $fldis_recommended = strip(get_param("is_recommended"));
    // Show fields
    $tpl->set_var("LBcategory_id", "");
    $tpl->set_var("ID", "");
    $tpl->set_var("Value", "All");
    $tpl->parse("LBcategory_id", true);
    $dbcategory_id = new DB_Sql();
    $dbcategory_id->Database = DATABASE_NAME;
    $dbcategory_id->User     = DATABASE_USER;
    $dbcategory_id->Password = DATABASE_PASSWORD;
    $dbcategory_id->Host     = DATABASE_HOST;
  
    
    $dbcategory_id->query("select category_id, name from categories order by 2");
    while($dbcategory_id->next_record())
    {
      $tpl->set_var("ID", $dbcategory_id->f(0));
      $tpl->set_var("Value", $dbcategory_id->f(1));
      if($dbcategory_id->f(0) == $fldcategory_id)
        $tpl->set_var("Selected", "SELECTED" );
      else 
        $tpl->set_var("Selected", "");
      $tpl->parse("LBcategory_id", true);
    }
    
    $tpl->set_var("LBis_recommended", "");
    $LOV = split(";", ";All;0;No;1;Yes");
  
    if(sizeof($LOV)%2 != 0) 
      $array_length = sizeof($LOV) - 1;
    else
      $array_length = sizeof($LOV);
    reset($LOV);
    for($i = 0; $i < $array_length; $i = $i + 2)
    {
      $tpl->set_var("ID", $LOV[$i]);
      $tpl->set_var("Value", $LOV[$i + 1]);
      if($LOV[$i] == $fldis_recommended) 
        $tpl->set_var("Selected", "SELECTED");
      else
        $tpl->set_var("Selected", "");
      $tpl->parse("LBis_recommended", true);
    }
  $tpl->parse("FormSearch", false);
}



function Items_show()
{

  
  global $tpl;
  global $db;
  global $sItemsErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "category_id=" . tourl(strip(get_param("category_id"))) . "&is_recommended=" . tourl(strip(get_param("is_recommended"))) . "&");
  $tpl->set_var("FormParams", "category_id=" . tourl(strip(get_param("category_id"))) . "&is_recommended=" . tourl(strip(get_param("is_recommended"))) . "&");
  // Build WHERE statement
  
  $pcategory_id = get_param("category_id");
  if(is_number($pcategory_id) && strlen($pcategory_id))
    $pcategory_id = round($pcategory_id);
  else 
    $pcategory_id = "";
  if(strlen($pcategory_id)) 
  {
    $HasParam = true;
    $sWhere .= "i.category_id=" . $pcategory_id;
  }
  $pis_recommended = get_param("is_recommended");
  if(is_number($pis_recommended) && strlen($pis_recommended))
    $pis_recommended = round($pis_recommended);
  else 
    $pis_recommended = "";
  if(strlen($pis_recommended)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "i.is_recommended=" . $pis_recommended;
  }
  if($HasParam)
    $sWhere = " AND (" . $sWhere . ")";

  $sDirection = "";
  $sSortParams = "";
  
  // Build ORDER statement
  $iSort = get_param("FormItems_Sorting");
  $iSorted = get_param("FormItems_Sorted");
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
      $sSortParams = "FormItems_Sorting=" . $iSort . "&FormItems_Sorted=" . $iSort . "&";
    }
    else
    {
      $tpl->set_var("Form_Sorting", $iSort);
      $sDirection = " ASC";
      $sSortParams = "FormItems_Sorting=" . $iSort . "&FormItems_Sorted=" . "&";
    }
    
    if ($iSort == 1) $sOrder = " order by i.name" . $sDirection;
    if ($iSort == 2) $sOrder = " order by i.author" . $sDirection;
    if ($iSort == 3) $sOrder = " order by i.price" . $sDirection;
    if ($iSort == 4) $sOrder = " order by c.name" . $sDirection;
    if ($iSort == 5) $sOrder = " order by i.is_recommended" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select i.author as i_author, " . 
    "i.category_id as i_category_id, " . 
    "i.is_recommended as i_is_recommended, " . 
    "i.item_id as i_item_id, " . 
    "i.name as i_name, " . 
    "i.price as i_price, " . 
    "c.category_id as c_category_id, " . 
    "c.name as c_name " . 
    " from items i, categories c" . 
    " where c.category_id=i.category_id  ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("FormAction", "BookMaint.php");
  $tpl->set_var("SortParams", $sSortParams);

  // Execute SQL statement
  $db->query($sSQL);
  
  // Select current page
  $iPage = get_param("FormItems_Page");
  if(!strlen($iPage)) $iPage = 1;
  $RecordsPerPage = 20;
  if(($iPage - 1) * $RecordsPerPage != 0)
    $db->seek(($iPage - 1) * $RecordsPerPage);
  $iCounter = 0;
  $ais_recommended = split(";", "0;No;1;Yes");

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldname = $db->f("i_name");
			$fldauthor = $db->f("i_author");
			$fldprice = $db->f("i_price");
			$fldcategory_id = $db->f("c_name");
			$fldis_recommended = $db->f("i_is_recommended");
      $fldField1= "Edit";
      $tpl->set_var("Field1", tohtml($fldField1));
      $tpl->set_var("Field1_URLLink", "BookMaint.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("i_item_id"))); 
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("author", tohtml($fldauthor));
      $tpl->set_var("price", tohtml($fldprice));
      $tpl->set_var("category_id", tohtml($fldcategory_id));
      $fldis_recommended = get_lov_value($fldis_recommended, $ais_recommended);
      $tpl->set_var("is_recommended", tohtml($fldis_recommended));
      $tpl->parse("DListItems", true);
      $iCounter++;
    } while($iCounter < $RecordsPerPage &&$db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListItems", "");
    $tpl->parse("ItemsNoRecords", false);
    $tpl->set_var("ItemsScroller", "");
    $tpl->parse("FormItems", false);
    return;
  }
  
  // Parse scroller
  if(@$db->next_record())
  {
    if ($iPage == 1)
    {
      $tpl->set_var("ItemsScrollerPrevSwitch", "_");
    }
    else
    {
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("ItemsScrollerPrevSwitch", "");
    }
    $tpl->set_var("NextPage", ($iPage + 1));
    $tpl->set_var("ItemsScrollerNextSwitch", "");
    $tpl->set_var("ItemsCurrentPage", $iPage);
    $tpl->parse("ItemsScroller", false);
  }
  else
  {
    if ($iPage == 1)
    {
      $tpl->set_var("ItemsScroller", "");
    }
    else
    {
      $tpl->set_var("ItemsScrollerNextSwitch", "_");
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("ItemsScrollerPrevSwitch", "");
      $tpl->set_var("ItemsCurrentPage", $iPage);
      $tpl->parse("ItemsScroller", false);
    }
  }
  $tpl->set_var("ItemsNoRecords", "");
  $tpl->parse("FormItems", false);
  
}

?>