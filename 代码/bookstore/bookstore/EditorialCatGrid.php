<?php
/*********************************************************************************
 *       Filename: EditorialCatGrid.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "EditorialCatGrid.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("EditorialCatGrid.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
editorial_categories_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function editorial_categories_show()
{

  
  global $tpl;
  global $db;
  global $seditorial_categoriesErr;
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
  $sOrder = " order by e.editorial_cat_name Asc";
  $iSort = get_param("Formeditorial_categories_Sorting");
  $iSorted = get_param("Formeditorial_categories_Sorted");
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
      $sSortParams = "Formeditorial_categories_Sorting=" . $iSort . "&Formeditorial_categories_Sorted=" . $iSort . "&";
    }
    else
    {
      $tpl->set_var("Form_Sorting", $iSort);
      $sDirection = " ASC";
      $sSortParams = "Formeditorial_categories_Sorting=" . $iSort . "&Formeditorial_categories_Sorted=" . "&";
    }
    
    if ($iSort == 1) $sOrder = " order by e.editorial_cat_name" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select e.editorial_cat_id as e_editorial_cat_id, " . 
    "e.editorial_cat_name as e_editorial_cat_name " . 
    " from editorial_categories e ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("FormAction", "EditorialCatRecord.php");
  $tpl->set_var("SortParams", $sSortParams);

  // Execute SQL statement
  $db->query($sSQL);
  
  // Select current page
  $iPage = get_param("Formeditorial_categories_Page");
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
			$fldeditorial_cat_id = $db->f("e_editorial_cat_id");
			$fldeditorial_cat_name = $db->f("e_editorial_cat_name");
    $tpl->set_var("editorial_cat_id", tohtml($fldeditorial_cat_id));
      $tpl->set_var("editorial_cat_name", tohtml($fldeditorial_cat_name));
      $tpl->set_var("editorial_cat_name_URLLink", "EditorialCatRecord.php");
      $tpl->set_var("Prm_editorial_cat_id", tourl($db->f("e_editorial_cat_id"))); 
      $tpl->parse("DListeditorial_categories", true);
      $iCounter++;
    } while($iCounter < $RecordsPerPage &&$db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListeditorial_categories", "");
    $tpl->parse("editorial_categoriesNoRecords", false);
    $tpl->set_var("editorial_categoriesScroller", "");
    $tpl->parse("Formeditorial_categories", false);
    return;
  }
  
  // Parse scroller
  if(@$db->next_record())
  {
    if ($iPage == 1)
    {
      $tpl->set_var("editorial_categoriesScrollerPrevSwitch", "_");
    }
    else
    {
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("editorial_categoriesScrollerPrevSwitch", "");
    }
    $tpl->set_var("NextPage", ($iPage + 1));
    $tpl->set_var("editorial_categoriesScrollerNextSwitch", "");
    $tpl->set_var("editorial_categoriesCurrentPage", $iPage);
    $tpl->parse("editorial_categoriesScroller", false);
  }
  else
  {
    if ($iPage == 1)
    {
      $tpl->set_var("editorial_categoriesScroller", "");
    }
    else
    {
      $tpl->set_var("editorial_categoriesScrollerNextSwitch", "_");
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("editorial_categoriesScrollerPrevSwitch", "");
      $tpl->set_var("editorial_categoriesCurrentPage", $iPage);
      $tpl->parse("editorial_categoriesScroller", false);
    }
  }
  $tpl->set_var("editorial_categoriesNoRecords", "");
  $tpl->parse("Formeditorial_categories", false);
  
}

?>