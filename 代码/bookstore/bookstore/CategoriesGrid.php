<?php
/*********************************************************************************
 *       Filename: CategoriesGrid.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "CategoriesGrid.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("CategoriesGrid.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
Categories_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Categories_show()
{

  
  global $tpl;
  global $db;
  global $sCategoriesErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "");
  $tpl->set_var("FormParams", "");
  // Build WHERE statement
  

  $sDirection = "";
  $sSortParams = "";
  
  // Build ORDER statement
  $sOrder = " order by c.name Asc";
  $iSort = get_param("FormCategories_Sorting");
  $iSorted = get_param("FormCategories_Sorted");
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
      $sSortParams = "FormCategories_Sorting=" . $iSort . "&FormCategories_Sorted=" . $iSort . "&";
    }
    else
    {
      $tpl->set_var("Form_Sorting", $iSort);
      $sDirection = " ASC";
      $sSortParams = "FormCategories_Sorting=" . $iSort . "&FormCategories_Sorted=" . "&";
    }
    
    if ($iSort == 1) $sOrder = " order by c.name" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select c.category_id as c_category_id, " . 
    "c.name as c_name " . 
    " from categories c ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("FormAction", "CategoriesRecord.php");
  $tpl->set_var("SortParams", $sSortParams);

  // Execute SQL statement
  $db->query($sSQL);
  
  // Select current page
  $iPage = get_param("FormCategories_Page");
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
			$fldname = $db->f("c_name");
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("name_URLLink", "CategoriesRecord.php");
      $tpl->set_var("Prm_category_id", tourl($db->f("c_category_id"))); 
      $tpl->parse("DListCategories", true);
      $iCounter++;
    } while($iCounter < $RecordsPerPage &&$db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListCategories", "");
    $tpl->parse("CategoriesNoRecords", false);
    $tpl->set_var("CategoriesScroller", "");
    $tpl->parse("FormCategories", false);
    return;
  }
  
  // Parse scroller
  if(@$db->next_record())
  {
    if ($iPage == 1)
    {
      $tpl->set_var("CategoriesScrollerPrevSwitch", "_");
    }
    else
    {
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("CategoriesScrollerPrevSwitch", "");
    }
    $tpl->set_var("NextPage", ($iPage + 1));
    $tpl->set_var("CategoriesScrollerNextSwitch", "");
    $tpl->set_var("CategoriesCurrentPage", $iPage);
    $tpl->parse("CategoriesScroller", false);
  }
  else
  {
    if ($iPage == 1)
    {
      $tpl->set_var("CategoriesScroller", "");
    }
    else
    {
      $tpl->set_var("CategoriesScrollerNextSwitch", "_");
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("CategoriesScrollerPrevSwitch", "");
      $tpl->set_var("CategoriesCurrentPage", $iPage);
      $tpl->parse("CategoriesScroller", false);
    }
  }
  $tpl->set_var("CategoriesNoRecords", "");
  $tpl->parse("FormCategories", false);
  
}

?>