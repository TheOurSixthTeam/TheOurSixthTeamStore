<?php
/*********************************************************************************
 *       Filename: EditorialsGrid.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "EditorialsGrid.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("EditorialsGrid.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
editorials_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function editorials_show()
{

  
  global $tpl;
  global $db;
  global $seditorialsErr;
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
  $sOrder = " order by e.article_title Asc";
  $iSort = get_param("Formeditorials_Sorting");
  $iSorted = get_param("Formeditorials_Sorted");
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
      $sSortParams = "Formeditorials_Sorting=" . $iSort . "&Formeditorials_Sorted=" . $iSort . "&";
    }
    else
    {
      $tpl->set_var("Form_Sorting", $iSort);
      $sDirection = " ASC";
      $sSortParams = "Formeditorials_Sorting=" . $iSort . "&Formeditorials_Sorted=" . "&";
    }
    
    if ($iSort == 1) $sOrder = " order by e.article_title" . $sDirection;
    if ($iSort == 2) $sOrder = " order by e1.editorial_cat_name" . $sDirection;
    if ($iSort == 3) $sOrder = " order by i.name" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select e.article_id as e_article_id, " . 
    "e.article_title as e_article_title, " . 
    "e.editorial_cat_id as e_editorial_cat_id, " . 
    "e.item_id as e_item_id, " . 
    "e1.editorial_cat_id as e1_editorial_cat_id, " . 
    "e1.editorial_cat_name as e1_editorial_cat_name, " . 
    "i.item_id as i_item_id, " . 
    "i.name as i_name " . 
    " from editorials e, editorial_categories e1, items i" . 
    " where e1.editorial_cat_id=e.editorial_cat_id and i.item_id=e.item_id  ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("FormAction", "EditorialsRecord.php");
  $tpl->set_var("SortParams", $sSortParams);

  // Execute SQL statement
  $db->query($sSQL);
  
  // Select current page
  $iPage = get_param("Formeditorials_Page");
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
			$fldarticle_id = $db->f("e_article_id");
			$fldarticle_title = $db->f("e_article_title");
			$fldeditorial_cat_id = $db->f("e1_editorial_cat_name");
			$flditem_id = $db->f("i_name");
    $tpl->set_var("article_id", tohtml($fldarticle_id));
      $tpl->set_var("article_title", tohtml($fldarticle_title));
      $tpl->set_var("article_title_URLLink", "EditorialsRecord.php");
      $tpl->set_var("Prm_article_id", tourl($db->f("e_article_id"))); 
      $tpl->set_var("editorial_cat_id", tohtml($fldeditorial_cat_id));
      $tpl->set_var("item_id", tohtml($flditem_id));
      $tpl->parse("DListeditorials", true);
      $iCounter++;
    } while($iCounter < $RecordsPerPage &&$db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListeditorials", "");
    $tpl->parse("editorialsNoRecords", false);
    $tpl->set_var("editorialsScroller", "");
    $tpl->parse("Formeditorials", false);
    return;
  }
  
  // Parse scroller
  if(@$db->next_record())
  {
    if ($iPage == 1)
    {
      $tpl->set_var("editorialsScrollerPrevSwitch", "_");
    }
    else
    {
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("editorialsScrollerPrevSwitch", "");
    }
    $tpl->set_var("NextPage", ($iPage + 1));
    $tpl->set_var("editorialsScrollerNextSwitch", "");
    $tpl->set_var("editorialsCurrentPage", $iPage);
    $tpl->parse("editorialsScroller", false);
  }
  else
  {
    if ($iPage == 1)
    {
      $tpl->set_var("editorialsScroller", "");
    }
    else
    {
      $tpl->set_var("editorialsScrollerNextSwitch", "_");
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("editorialsScrollerPrevSwitch", "");
      $tpl->set_var("editorialsCurrentPage", $iPage);
      $tpl->parse("editorialsScroller", false);
    }
  }
  $tpl->set_var("editorialsNoRecords", "");
  $tpl->parse("Formeditorials", false);
  
}

?>