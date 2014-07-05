<?php
/*********************************************************************************
 *       Filename: AdvSearch.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "AdvSearch.php";




$tpl = new Template($app_path);
$tpl->load_file("AdvSearch.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
Search_show();

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
  $fldname = strip(get_param("name"));
  $fldauthor = strip(get_param("author"));
  $fldcategory_id = strip(get_param("category_id"));
  $fldpricemin = strip(get_param("pricemin"));
  $fldpricemax = strip(get_param("pricemax"));
    // Show fields
    $tpl->set_var("name", tohtml($fldname));
    $tpl->set_var("author", tohtml($fldauthor));
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
    
    $tpl->set_var("pricemin", tohtml($fldpricemin));
    $tpl->set_var("pricemax", tohtml($fldpricemax));
  $tpl->parse("FormSearch", false);
}

?>