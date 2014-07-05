<?php
/*********************************************************************************
 *       Filename: CategoriesRecord.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "CategoriesRecord.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("CategoriesRecord.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sCategoriesErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "Categories":
    Categories_action($sAction);
  break;
}Menu_show();
Footer_show();
Categories_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Categories_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sCategoriesErr;
  
  $sParams = "";
  $sActionFileName = "CategoriesGrid.php";

  

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName); 

  
  // Create WHERE statement
  if($sAction == "update" || $sAction == "delete") 
  {
    $pPKcategory_id = get_param("PK_category_id");
    if( !strlen($pPKcategory_id)) return;
    $sWhere = "category_id=" . tosql($pPKcategory_id, "Number");
  }

  // Load all form fields into variables
  
  $fldname = get_param("name");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldname))
      $sCategoriesErr .= "The value in field Name is required.<br>";
    

    if(strlen($sCategoriesErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "insert":
      
        $sSQL = "insert into categories (" . 
          "name)" . 
          " values (" . 
          tosql($fldname, "Text") . ")";    
    break;
    case "update":
      
        $sSQL = "update categories set " .
          "name=" . tosql($fldname, "Text");
        $sSQL .= " where " . $sWhere;
    break;
    case "delete":
      
        $sSQL = "delete from categories where " . $sWhere;
    break;
  }
  // Execute SQL statement
  if(strlen($sCategoriesErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName);
  
}

function Categories_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sCategoriesErr;

  $sWhere = "";
  
  $bPK = true;
  $fldcategory_id = "";
  $fldname = "";
  

  if($sCategoriesErr == "")
  {
    // Load primary key and form parameters
    $fldcategory_id = get_param("category_id");
    $pcategory_id = get_param("category_id");
    $tpl->set_var("CategoriesError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldcategory_id = strip(get_param("category_id"));
    $fldname = strip(get_param("name"));
    $pcategory_id = get_param("PK_category_id");
    $tpl->set_var("sCategoriesErr", $sCategoriesErr);
    $tpl->parse("CategoriesError", false);
  }

  
  if( !strlen($pcategory_id)) $bPK = false;
  
  $sWhere .= "category_id=" . tosql($pcategory_id, "Number");
  $tpl->set_var("PK_category_id", $pcategory_id);

  $sSQL = "select * from categories where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Categories"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldcategory_id = $db->f("category_id");
    if($sCategoriesErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldname = $db->f("name");
    }
    $tpl->set_var("CategoriesInsert", "");
    $tpl->parse("CategoriesEdit", false);
  }
  else
  {
    if($sCategoriesErr == "")
    {
      $fldcategory_id = tohtml(get_param("category_id"));
    }
    $tpl->set_var("CategoriesEdit", "");
    $tpl->parse("CategoriesInsert", false);
  }
  $tpl->parse("CategoriesCancel", false);

  // Show form field
  
    $tpl->set_var("category_id", tohtml($fldcategory_id));
    $tpl->set_var("name", tohtml($fldname));
  $tpl->parse("FormCategories", false);
  

}

?>