<?php
include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "Books.php";




$tpl = new Template($app_path);
$tpl->load_file("Books.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
Results_show();
Search_show();
AdvMenu_show();
Total_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Results_show()
{

  
  global $tpl;
  global $db;
  global $sResultsErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "category_id=" . tourl(strip(get_param("category_id"))) . "&name=" . tourl(strip(get_param("name"))) . "&pricemin=" . tourl(strip(get_param("pricemin"))) . "&pricemax=" . tourl(strip(get_param("pricemax"))) . "&author=" . tourl(strip(get_param("author"))) . "&");
  $tpl->set_var("FormParams", "category_id=" . tourl(strip(get_param("category_id"))) . "&name=" . tourl(strip(get_param("name"))) . "&pricemin=" . tourl(strip(get_param("pricemin"))) . "&pricemax=" . tourl(strip(get_param("pricemax"))) . "&author=" . tourl(strip(get_param("author"))) . "&");
  // Build WHERE statement
  
  $pauthor = get_param("author");
  if(strlen($pauthor)) 
  {
    $HasParam = true;
    $sWhere .= "i.author like " . tosql("%".$pauthor ."%", "Text");
  }
  $pcategory_id = get_param("category_id");
  if(is_number($pcategory_id) && strlen($pcategory_id))
    $pcategory_id = round($pcategory_id);
  else 
    $pcategory_id = "";
  if(strlen($pcategory_id)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "i.category_id=" . $pcategory_id;
  }
  $pname = get_param("name");
  if(strlen($pname)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "i.name like " . tosql("%".$pname ."%", "Text");
  }
  $ppricemax = get_param("pricemax");
  if(is_number($ppricemax) && strlen($ppricemax))
    $ppricemax = round($ppricemax);
  else 
    $ppricemax = "";
  if(strlen($ppricemax)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "i.price<" . $ppricemax;
  }
  $ppricemin = get_param("pricemin");
  if(is_number($ppricemin) && strlen($ppricemin))
    $ppricemin = round($ppricemin);
  else 
    $ppricemin = "";
  if(strlen($ppricemin)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "i.price>" . $ppricemin;
  }
  if($HasParam)
    $sWhere = " AND (" . $sWhere . ")";

  $sDirection = "";
  $sSortParams = "";
  
  // Build ORDER statement
  $sOrder = " order by i.name Asc";
  $iSort = get_param("FormResults_Sorting");
  $iSorted = get_param("FormResults_Sorted");
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
      $sSortParams = "FormResults_Sorting=" . $iSort . "&FormResults_Sorted=" . $iSort . "&";
    }
    else
    {
      $tpl->set_var("Form_Sorting", $iSort);
      $sDirection = " ASC";
      $sSortParams = "FormResults_Sorting=" . $iSort . "&FormResults_Sorted=" . "&";
    }
    
    if ($iSort == 1) $sOrder = " order by i.name" . $sDirection;
    if ($iSort == 2) $sOrder = " order by i.author" . $sDirection;
    if ($iSort == 3) $sOrder = " order by i.price" . $sDirection;
    if ($iSort == 4) $sOrder = " order by c.name" . $sDirection;
    if ($iSort == 5) $sOrder = " order by i.image_url" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select i.author as i_author, " . 
    "i.category_id as i_category_id, " . 
    "i.image_url as i_image_url, " . 
    "i.item_id as i_item_id, " . 
    "i.name as i_name, " . 
    "i.price as i_price, " . 
    "c.category_id as c_category_id, " . 
    "c.name as c_name " . 
    " from items i, categories c" . 
    " where c.category_id=i.category_id  ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("SortParams", $sSortParams);

  // Execute SQL statement
  $db->query($sSQL);
  
  // Select current page
  $iPage = get_param("FormResults_Page");
  if(!strlen($iPage)) $iPage = 1;
  $RecordsPerPage = 20;
  if(($iPage - 1) * $RecordsPerPage != 0)
    $db->seek(($iPage - 1) * $RecordsPerPage);
  $iCounter = 0;

  if($db->next_record())
  {  
	echo "find record you search";//测试添加
    // Show main table based on SQL query
    do
    {
			$fldname = $db->f("i_name");
			$fldauthor = $db->f("i_author");
			$fldprice = $db->f("i_price");
			$fldcategory_id = $db->f("c_name");
			$fldimage_url = $db->f("i_image_url");
$fldimage_url="<img border=0 src=" . $fldimage_url . ">";
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("name_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("i_item_id"))); 
      $tpl->set_var("author", tohtml($fldauthor));
      $tpl->set_var("price", tohtml($fldprice));
      $tpl->set_var("category_id", tohtml($fldcategory_id));
      $tpl->set_var("image_url", $fldimage_url);
      $tpl->set_var("image_url_URLLink", "BookDetail.php");
      $tpl->set_var("Prm_item_id", tourl($db->f("i_item_id"))); 
      $tpl->parse("DListResults", true);
      $iCounter++;
    } while($iCounter < $RecordsPerPage &&$db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListResults", "");
    $tpl->parse("ResultsNoRecords", false);
    $tpl->set_var("ResultsScroller", "");
    $tpl->parse("FormResults", false);
    return;
  }
  
  // Parse scroller
  if(@$db->next_record())
  {
    if ($iPage == 1)
    {
      $tpl->set_var("ResultsScrollerPrevSwitch", "_");
    }
    else
    {
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("ResultsScrollerPrevSwitch", "");
    }
    $tpl->set_var("NextPage", ($iPage + 1));
    $tpl->set_var("ResultsScrollerNextSwitch", "");
    $tpl->set_var("ResultsCurrentPage", $iPage);
    $tpl->parse("ResultsScroller", false);
  }
  else
  {
    if ($iPage == 1)
    {
      $tpl->set_var("ResultsScroller", "");
    }
    else
    {
      $tpl->set_var("ResultsScrollerNextSwitch", "_");
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("ResultsScrollerPrevSwitch", "");
      $tpl->set_var("ResultsCurrentPage", $iPage);
      $tpl->parse("ResultsScroller", false);
    }
  }
  $tpl->set_var("ResultsNoRecords", "");
  $tpl->parse("FormResults", false);
  
}


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
  $tpl->set_var("FormParams", "category_id=" . tourl(strip(get_param("category_id"))) . "&name=" . tourl(strip(get_param("name"))) . "&author=" . tourl(strip(get_param("author"))) . "&pricemin=" . tourl(strip(get_param("pricemin"))) . "&pricemax=" . tourl(strip(get_param("pricemax"))) . "&");
  // Build WHERE statement
  
  $pauthor = get_param("author");
  if(strlen($pauthor)) 
  {
    $HasParam = true;
    $sWhere .= "i.author like " . tosql("%".$pauthor ."%", "Text");
  }
  $pcategory_id = get_param("category_id");
  if(is_number($pcategory_id) && strlen($pcategory_id))
    $pcategory_id = round($pcategory_id);
  else 
    $pcategory_id = "";
  if(strlen($pcategory_id)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "i.category_id=" . $pcategory_id;
  }
  $pname = get_param("name");
  if(strlen($pname)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "i.name like " . tosql("%".$pname ."%", "Text");
  }
  $ppricemax = get_param("pricemax");
  if(is_number($ppricemax) && strlen($ppricemax))
    $ppricemax = round($ppricemax);
  else 
    $ppricemax = "";
  if(strlen($ppricemax)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "i.price<=" . $ppricemax;
  }
  $ppricemin = get_param("pricemin");
  if(is_number($ppricemin) && strlen($ppricemin))
    $ppricemin = round($ppricemin);
  else 
    $ppricemin = "";
  if(strlen($ppricemin)) 
  {
    if ($sWhere != "") $sWhere .= " and ";
    $HasParam = true;
    $sWhere .= "i.price>=" . $ppricemin;
  }
  if($HasParam)
    $sWhere = " WHERE (" . $sWhere . ")";

  $sDirection = "";
  $sSortParams = "";
  

  // Build full SQL statement
  
  $sSQL = "select i.author as i_author, " . 
    "i.category_id as i_category_id, " . 
    "i.item_id as i_item_id, " . 
    "i.name as i_name, " . 
    "i.price as i_price " . 
    " from items i ";
  
$sSQL="select count(item_id) as i_item_id from items as i";
  $sSQL .= $sWhere . $sOrder;

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$flditem_id = $db->f("i_item_id");
      $tpl->set_var("item_id", tohtml($flditem_id));
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

?>