
<?php error_reporting(0); ?>
<?php
/*********************************************************************************
 *       Filename: Default.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "Default.php";




$tpl = new Template($app_path);
$tpl->load_file("Default.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
Search_show();
AdvMenu_show();
Recommended_show();
What_show();
Categories_show();
New_show();
Weekly_show();
Specials_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************


function Search_show()
{
  global $db;
  global $tpl;
  
  $tpl->set_var("ActionPage", "Books.php");
	
  // Set variables with search parameters
  $fldcategory_id = strip(get_param("category_id"));
  $fldname = strip(get_param("name"));
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
    
    $tpl->set_var("name", tohtml($fldname));
  $tpl->parse("FormSearch", false);
}


function AdvMenu_show()
{
  
  global $tpl;
  // Set URLs
  $fldField1 = "AdvSearch.php";
  // Show fields
  $tpl->set_var("Field1", $fldField1);
  $tpl->parse("FormAdvMenu", false);
}



function Recommended_show()
{

  
  global $tpl;
  global $db;
  global $sRecommendedErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "");
  $tpl->set_var("FormParams", "");
  // Build WHERE statement
  
  $sWhere = " WHERE is_recommended=1";
  

  $sDirection = "";
  $sSortParams = "";
  
  // Build ORDER statement
  $iSort = get_param("FormRecommended_Sorting");
  $iSorted = get_param("FormRecommended_Sorted");
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
      $sSortParams = "FormRecommended_Sorting=" . $iSort . "&FormRecommended_Sorted=" . $iSort . "&";
    }
    else
    {
      $tpl->set_var("Form_Sorting", $iSort);
      $sDirection = " ASC";
      $sSortParams = "FormRecommended_Sorting=" . $iSort . "&FormRecommended_Sorted=" . "&";
    }
    
    if ($iSort == 1) $sOrder = " order by i.name" . $sDirection;
    if ($iSort == 2) $sOrder = " order by i.author" . $sDirection;
    if ($iSort == 3) $sOrder = " order by i.image_url" . $sDirection;
    if ($iSort == 4) $sOrder = " order by i.price" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select i.author as i_author, " . 
    "i.image_url as i_image_url, " . 
    "i.item_id as i_item_id, " . 
    "i.name as i_name, " . 
    "i.price as i_price " . 
    " from items i ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("SortParams", $sSortParams);

  // Execute SQL statement
  $db->query($sSQL);
  
  // Select current page
  $iPage = get_param("FormRecommended_Page");
  if(!strlen($iPage)) $iPage = 1;
  $RecordsPerPage = 5;
  if(($iPage - 1) * $RecordsPerPage != 0)
    $db->seek(($iPage - 1) * $RecordsPerPage);
  $iCounter = 0;

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldname = $db->f("i_name");
			$fldauthor = $db->f("i_author");
			$fldimage_url = $db->f("i_image_url");
			$fldprice = $db->f("i_price");
$fldimage_url="<img border=0 src=" . $fldimage_url . ">";
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("name_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("i_item_id"))); 
      $tpl->set_var("author", tohtml($fldauthor));
      $tpl->set_var("image_url", $fldimage_url);
      $tpl->set_var("image_url_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("i_item_id"))); 
      $tpl->set_var("price", tohtml($fldprice));
      $tpl->parse("DListRecommended", true);
      $iCounter++;
    } while($iCounter < $RecordsPerPage &&$db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListRecommended", "");
    $tpl->parse("RecommendedNoRecords", false);
    $tpl->set_var("RecommendedScroller", "");
    $tpl->parse("FormRecommended", false);
    return;
  }
  
  // Parse scroller
  if(@$db->next_record())
  {
    if ($iPage == 1)
    {
      $tpl->set_var("RecommendedScrollerPrevSwitch", "_");
    }
    else
    {
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("RecommendedScrollerPrevSwitch", "");
    }
    $tpl->set_var("NextPage", ($iPage + 1));
    $tpl->set_var("RecommendedScrollerNextSwitch", "");
    $tpl->set_var("RecommendedCurrentPage", $iPage);
    $tpl->parse("RecommendedScroller", false);
  }
  else
  {
    if ($iPage == 1)
    {
      $tpl->set_var("RecommendedScroller", "");
    }
    else
    {
      $tpl->set_var("RecommendedScrollerNextSwitch", "_");
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("RecommendedScrollerPrevSwitch", "");
      $tpl->set_var("RecommendedCurrentPage", $iPage);
      $tpl->parse("RecommendedScroller", false);
    }
  }
  $tpl->set_var("RecommendedNoRecords", "");
  $tpl->parse("FormRecommended", false);
  
}



function What_show()
{

  
  global $tpl;
  global $db;
  global $sWhatErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "");
  $tpl->set_var("FormParams", "");
  // Build WHERE statement
  
  $sWhere = " AND editorial_cat_id=1";
  

  $sDirection = "";
  $sSortParams = "";
  

  // Build full SQL statement
  
  $sSQL = "select e.article_desc as e_article_desc, " . 
    "e.article_title as e_article_title, " . 
    "e.item_id as e_item_id, " . 
    "i.item_id as i_item_id, " . 
    "i.image_url as i_image_url " . 
    " from editorials e, items i" . 
    " where i.item_id=e.item_id  ";
  
  $sSQL .= $sWhere . $sOrder;

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldarticle_title = $db->f("e_article_title");
			$fldarticle_desc = $db->f("e_article_desc");
			$flditem_id = $db->f("i_image_url");
$flditem_id="<img border=0 src=" . $flditem_id . ">";
      $tpl->set_var("article_title", tohtml($fldarticle_title));
      $tpl->set_var("article_title_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("e_item_id"))); 
      $tpl->set_var("article_desc", $fldarticle_desc);
      $tpl->set_var("item_id", $flditem_id);
      $tpl->set_var("item_id_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("e_item_id"))); 
      $tpl->parse("DListWhat", true);
    } while($db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListWhat", "");
    $tpl->parse("WhatNoRecords", false);
    $tpl->parse("FormWhat", false);
    return;
  }
  
  $tpl->set_var("WhatNoRecords", "");
  $tpl->parse("FormWhat", false);
  
}



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
  

  // Build full SQL statement
  
  $sSQL = "select c.category_id as c_category_id, " . 
    "c.name as c_name " . 
    " from categories c ";
  
  $sSQL .= $sWhere . $sOrder;

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldname = $db->f("c_name");
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("name_URLLink", "Books.php");
      $tpl->set_var("Prm_category_id", tourl($db->f("c_category_id"))); 
      $tpl->parse("DListCategories", true);
    } while($db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListCategories", "");
    $tpl->parse("CategoriesNoRecords", false);
    $tpl->parse("FormCategories", false);
    return;
  }
  
  $tpl->set_var("CategoriesNoRecords", "");
  $tpl->parse("FormCategories", false);
  
}



function New_show()
{

  
  global $tpl;
  global $db;
  global $sNewErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "");
  $tpl->set_var("FormParams", "");
  // Build WHERE statement
  
  $sWhere = " AND editorial_cat_id=2";
  

  $sDirection = "";
  $sSortParams = "";
  

  // Build full SQL statement
  
  $sSQL = "select e.article_desc as e_article_desc, " . 
    "e.article_title as e_article_title, " . 
    "e.item_id as e_item_id, " . 
    "i.item_id as i_item_id, " . 
    "i.image_url as i_image_url " . 
    " from editorials e, items i" . 
    " where i.item_id=e.item_id  ";
  
  $sSQL .= $sWhere . $sOrder;

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldarticle_title = $db->f("e_article_title");
			$flditem_id = $db->f("i_image_url");
			$fldarticle_desc = $db->f("e_article_desc");
$flditem_id="<img border=0 src=" . $flditem_id . ">";
      $tpl->set_var("article_title", tohtml($fldarticle_title));
      $tpl->set_var("article_title_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("e_item_id"))); 
      $tpl->set_var("item_id", $flditem_id);
      $tpl->set_var("item_id_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("e_item_id"))); 
      $tpl->set_var("article_desc", tohtml($fldarticle_desc));
      $tpl->parse("DListNew", true);
    } while($db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListNew", "");
    $tpl->parse("NewNoRecords", false);
    $tpl->parse("FormNew", false);
    return;
  }
  
  $tpl->set_var("NewNoRecords", "");
  $tpl->parse("FormNew", false);
  
}



function Weekly_show()
{

  
  global $tpl;
  global $db;
  global $sWeeklyErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "");
  $tpl->set_var("FormParams", "");
  // Build WHERE statement
  
  $sWhere = " AND editorial_cat_id=3";
  

  $sDirection = "";
  $sSortParams = "";
  

  // Build full SQL statement
  
  $sSQL = "select e.article_desc as e_article_desc, " . 
    "e.article_title as e_article_title, " . 
    "e.item_id as e_item_id, " . 
    "i.item_id as i_item_id, " . 
    "i.image_url as i_image_url " . 
    " from editorials e, items i" . 
    " where i.item_id=e.item_id  ";
  
  $sSQL .= $sWhere . $sOrder;

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldarticle_title = $db->f("e_article_title");
			$flditem_id = $db->f("i_image_url");
			$fldarticle_desc = $db->f("e_article_desc");
$flditem_id="<img border=0 src=" . $flditem_id . ">";
      $tpl->set_var("article_title", tohtml($fldarticle_title));
      $tpl->set_var("article_title_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("e_item_id"))); 
      $tpl->set_var("item_id", $flditem_id);
      $tpl->set_var("item_id_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("e_item_id"))); 
      $tpl->set_var("article_desc", tohtml($fldarticle_desc));
      $tpl->parse("DListWeekly", true);
    } while($db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListWeekly", "");
    $tpl->parse("WeeklyNoRecords", false);
    $tpl->parse("FormWeekly", false);
    return;
  }
  
  $tpl->set_var("WeeklyNoRecords", "");
  $tpl->parse("FormWeekly", false);
  
}



function Specials_show()
{

  
  global $tpl;
  global $db;
  global $sSpecialsErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "");
  $tpl->set_var("FormParams", "");
  // Build WHERE statement
  
  $sWhere = " WHERE editorial_cat_id=4";
  

  $sDirection = "";
  $sSortParams = "";
  

  // Build full SQL statement
  
  $sSQL = "select e.article_desc as e_article_desc, " . 
    "e.article_title as e_article_title " . 
    " from editorials e ";
  
  $sSQL .= $sWhere . $sOrder;

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldarticle_title = $db->f("e_article_title");
			$fldarticle_desc = $db->f("e_article_desc");
      $tpl->set_var("article_title", $fldarticle_title);
      $tpl->set_var("article_desc", $fldarticle_desc);
      $tpl->parse("DListSpecials", true);
    } while($db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListSpecials", "");
    $tpl->parse("SpecialsNoRecords", false);
    $tpl->parse("FormSpecials", false);
    return;
  }
  
  $tpl->set_var("SpecialsNoRecords", "");
  $tpl->parse("FormSpecials", false);
  
}

?>