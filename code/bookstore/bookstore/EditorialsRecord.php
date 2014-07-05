<?php
/*********************************************************************************
 *       Filename: EditorialsRecord.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "EditorialsRecord.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("EditorialsRecord.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$seditorialsErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "editorials":
    editorials_action($sAction);
  break;
}Menu_show();
Footer_show();
editorials_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function editorials_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $seditorialsErr;
  
  $sParams = "";
  $sActionFileName = "EditorialsGrid.php";

  

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName); 

  
  // Create WHERE statement
  if($sAction == "update" || $sAction == "delete") 
  {
    $pPKarticle_id = get_param("PK_article_id");
    if( !strlen($pPKarticle_id)) return;
    $sWhere = "article_id=" . tosql($pPKarticle_id, "Number");
  }

  // Load all form fields into variables
  
  $fldarticle_desc = get_param("article_desc");
  $fldarticle_title = get_param("article_title");
  $fldeditorial_cat_id = get_param("editorial_cat_id");
  $flditem_id = get_param("item_id");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldeditorial_cat_id))
      $seditorialsErr .= "The value in field Editorial Category is required.<br>";
    
    if(!is_number($fldeditorial_cat_id))
      $seditorialsErr .= "The value in field Editorial Category is incorrect.<br>";
    
    if(!is_number($flditem_id))
      $seditorialsErr .= "The value in field Item is incorrect.<br>";
    

    if(strlen($seditorialsErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "insert":
      
        $sSQL = "insert into editorials (" . 
          "article_desc," . 
          "article_title," . 
          "editorial_cat_id," . 
          "item_id)" . 
          " values (" . 
          tosql($fldarticle_desc, "Text") . "," .
          tosql($fldarticle_title, "Text") . "," .
          tosql($fldeditorial_cat_id, "Number") . "," .
          tosql($flditem_id, "Number") . ")";    
    break;
    case "update":
      
        $sSQL = "update editorials set " .
          "article_desc=" . tosql($fldarticle_desc, "Text") .
          ",article_title=" . tosql($fldarticle_title, "Text") .
          ",editorial_cat_id=" . tosql($fldeditorial_cat_id, "Number") .
          ",item_id=" . tosql($flditem_id, "Number");
        $sSQL .= " where " . $sWhere;
    break;
    case "delete":
      
        $sSQL = "delete from editorials where " . $sWhere;
    break;
  }
  // Execute SQL statement
  if(strlen($seditorialsErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName);
  
}

function editorials_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $seditorialsErr;

  $sWhere = "";
  
  $bPK = true;
  $fldarticle_id = "";
  $fldarticle_desc = "";
  $fldarticle_title = "";
  $fldeditorial_cat_id = "";
  $flditem_id = "";
  

  if($seditorialsErr == "")
  {
    // Load primary key and form parameters
    $fldarticle_id = get_param("article_id");
    $particle_id = get_param("article_id");
    $tpl->set_var("editorialsError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldarticle_id = strip(get_param("article_id"));
    $fldarticle_desc = strip(get_param("article_desc"));
    $fldarticle_title = strip(get_param("article_title"));
    $fldeditorial_cat_id = strip(get_param("editorial_cat_id"));
    $flditem_id = strip(get_param("item_id"));
    $particle_id = get_param("PK_article_id");
    $tpl->set_var("seditorialsErr", $seditorialsErr);
    $tpl->parse("editorialsError", false);
  }

  
  if( !strlen($particle_id)) $bPK = false;
  
  $sWhere .= "article_id=" . tosql($particle_id, "Number");
  $tpl->set_var("PK_article_id", $particle_id);

  $sSQL = "select * from editorials where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "editorials"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldarticle_id = $db->f("article_id");
    if($seditorialsErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldarticle_desc = $db->f("article_desc");
      $fldarticle_title = $db->f("article_title");
      $fldeditorial_cat_id = $db->f("editorial_cat_id");
      $flditem_id = $db->f("item_id");
    }
    $tpl->set_var("editorialsInsert", "");
    $tpl->parse("editorialsEdit", false);
  }
  else
  {
    if($seditorialsErr == "")
    {
      $fldarticle_id = tohtml(get_param("article_id"));
    }
    $tpl->set_var("editorialsEdit", "");
    $tpl->parse("editorialsInsert", false);
  }
  $tpl->parse("editorialsCancel", false);

  // Show form field
  
    $tpl->set_var("article_id", tohtml($fldarticle_id));
    $tpl->set_var("article_desc", tohtml($fldarticle_desc));
    $tpl->set_var("article_title", tohtml($fldarticle_title));
    $tpl->set_var("LBeditorial_cat_id", "");
    $dbeditorial_cat_id = new DB_Sql();
    $dbeditorial_cat_id->Database = DATABASE_NAME;
    $dbeditorial_cat_id->User     = DATABASE_USER;
    $dbeditorial_cat_id->Password = DATABASE_PASSWORD;
    $dbeditorial_cat_id->Host     = DATABASE_HOST;
  
    
    $dbeditorial_cat_id->query("select editorial_cat_id, editorial_cat_name from editorial_categories order by 2");
    while($dbeditorial_cat_id->next_record())
    {
      $tpl->set_var("ID", $dbeditorial_cat_id->f(0));
      $tpl->set_var("Value", $dbeditorial_cat_id->f(1));
      if($dbeditorial_cat_id->f(0) == $fldeditorial_cat_id)
        $tpl->set_var("Selected", "SELECTED" );
      else 
        $tpl->set_var("Selected", "");
      $tpl->parse("LBeditorial_cat_id", true);
    }
    
    $tpl->set_var("LBitem_id", "");
    $tpl->set_var("ID", "");
    $tpl->set_var("Value", "");
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
    
  $tpl->parse("Formeditorials", false);
  

}

?>