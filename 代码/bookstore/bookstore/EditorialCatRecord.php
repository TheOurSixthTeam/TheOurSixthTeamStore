<?php
/*********************************************************************************
 *       Filename: EditorialCatRecord.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "EditorialCatRecord.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("EditorialCatRecord.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$seditorial_categoriesErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "editorial_categories":
    editorial_categories_action($sAction);
  break;
}Menu_show();
Footer_show();
editorial_categories_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function editorial_categories_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $seditorial_categoriesErr;
  
  $sParams = "";
  $sActionFileName = "EditorialCatGrid.php";

  

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName); 

  
  // Create WHERE statement
  if($sAction == "update" || $sAction == "delete") 
  {
    $pPKeditorial_cat_id = get_param("PK_editorial_cat_id");
    if( !strlen($pPKeditorial_cat_id)) return;
    $sWhere = "editorial_cat_id=" . tosql($pPKeditorial_cat_id, "Number");
  }

  // Load all form fields into variables
  
  $fldeditorial_cat_name = get_param("editorial_cat_name");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {

    if(strlen($seditorial_categoriesErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "insert":
      
        $sSQL = "insert into editorial_categories (" . 
          "editorial_cat_name)" . 
          " values (" . 
          tosql($fldeditorial_cat_name, "Text") . ")";    
    break;
    case "update":
      
        $sSQL = "update editorial_categories set " .
          "editorial_cat_name=" . tosql($fldeditorial_cat_name, "Text");
        $sSQL .= " where " . $sWhere;
    break;
    case "delete":
      
        $sSQL = "delete from editorial_categories where " . $sWhere;
    break;
  }
  // Execute SQL statement
  if(strlen($seditorial_categoriesErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName);
  
}

function editorial_categories_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $seditorial_categoriesErr;

  $sWhere = "";
  
  $bPK = true;
  $fldeditorial_cat_id = "";
  $fldeditorial_cat_name = "";
  

  if($seditorial_categoriesErr == "")
  {
    // Load primary key and form parameters
    $fldeditorial_cat_id = get_param("editorial_cat_id");
    $peditorial_cat_id = get_param("editorial_cat_id");
    $tpl->set_var("editorial_categoriesError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldeditorial_cat_id = strip(get_param("editorial_cat_id"));
    $fldeditorial_cat_name = strip(get_param("editorial_cat_name"));
    $peditorial_cat_id = get_param("PK_editorial_cat_id");
    $tpl->set_var("seditorial_categoriesErr", $seditorial_categoriesErr);
    $tpl->parse("editorial_categoriesError", false);
  }

  
  if( !strlen($peditorial_cat_id)) $bPK = false;
  
  $sWhere .= "editorial_cat_id=" . tosql($peditorial_cat_id, "Number");
  $tpl->set_var("PK_editorial_cat_id", $peditorial_cat_id);

  $sSQL = "select * from editorial_categories where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "editorial_categories"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldeditorial_cat_id = $db->f("editorial_cat_id");
    if($seditorial_categoriesErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldeditorial_cat_name = $db->f("editorial_cat_name");
    }
    $tpl->set_var("editorial_categoriesInsert", "");
    $tpl->parse("editorial_categoriesEdit", false);
  }
  else
  {
    if($seditorial_categoriesErr == "")
    {
      $fldeditorial_cat_id = tohtml(get_param("editorial_cat_id"));
    }
    $tpl->set_var("editorial_categoriesEdit", "");
    $tpl->parse("editorial_categoriesInsert", false);
  }
  $tpl->parse("editorial_categoriesCancel", false);

  // Show form field
  
    $tpl->set_var("editorial_cat_id", tohtml($fldeditorial_cat_id));
    $tpl->set_var("editorial_cat_name", tohtml($fldeditorial_cat_name));
  $tpl->parse("Formeditorial_categories", false);
  

}

?>