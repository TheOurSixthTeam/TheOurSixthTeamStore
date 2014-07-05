<?php
/*********************************************************************************
 *       Filename: BookMaint.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "BookMaint.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("BookMaint.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sBookErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "Book":
    Book_action($sAction);
  break;
}Menu_show();
Footer_show();
Book_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Book_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sBookErr;
  
  $sParams = "";
  $sActionFileName = "AdminBooks.php";

  
  $sParams = "?";
  $sParams .= "category_id=" . tourl(get_param("Trn_category_id")) . "&";
  $sParams .= "is_recommended=" . tourl(get_param("Trn_is_recommended"));

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName . $sParams); 

  
  // Create WHERE statement
  if($sAction == "update" || $sAction == "delete") 
  {
    $pPKitem_id = get_param("PK_item_id");
    if( !strlen($pPKitem_id)) return;
    $sWhere = "item_id=" . tosql($pPKitem_id, "Number");
  }

  // Load all form fields into variables
  
  $fldname = get_param("name");
  $fldauthor = get_param("author");
  $fldcategory_id = get_param("category_id");
  $fldprice = get_param("price");
  $fldproduct_url = get_param("product_url");
  $fldimage_url = get_param("image_url");
  $fldnotes = get_param("notes");
  $fldis_rec = get_checkbox_value(get_param("is_rec"), "1", "0", "Number");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldname))
      $sBookErr .= "The value in field Title is required.<br>";
    
    if(!strlen($fldcategory_id))
      $sBookErr .= "The value in field Category is required.<br>";
    
    if(!strlen($fldprice))
      $sBookErr .= "The value in field Price is required.<br>";
    
    if(!is_number($fldcategory_id))
      $sBookErr .= "The value in field Category is incorrect.<br>";
    
    if(!is_number($fldprice))
      $sBookErr .= "The value in field Price is incorrect.<br>";
    

    if(strlen($sBookErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "insert":
      
        $sSQL = "insert into items (" . 
          "name," . 
          "author," . 
          "category_id," . 
          "price," . 
          "product_url," . 
          "image_url," . 
          "notes," . 
          "is_recommended)" . 
          " values (" . 
          tosql($fldname, "Text") . "," .
          tosql($fldauthor, "Text") . "," .
          tosql($fldcategory_id, "Number") . "," .
          tosql($fldprice, "Number") . "," .
          tosql($fldproduct_url, "Text") . "," .
          tosql($fldimage_url, "Text") . "," .
          tosql($fldnotes, "Text") . "," .
          $fldis_rec . ")";    
    break;
    case "update":
      
        $sSQL = "update items set " .
          "name=" . tosql($fldname, "Text") .
          ",author=" . tosql($fldauthor, "Text") .
          ",category_id=" . tosql($fldcategory_id, "Number") .
          ",price=" . tosql($fldprice, "Number") .
          ",product_url=" . tosql($fldproduct_url, "Text") .
          ",image_url=" . tosql($fldimage_url, "Text") .
          ",notes=" . tosql($fldnotes, "Text") .
          ",is_recommended=" . $fldis_rec;
        $sSQL .= " where " . $sWhere;
    break;
    case "delete":
      
        $sSQL = "delete from items where " . $sWhere;
    break;
  }
  // Execute SQL statement
  if(strlen($sBookErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName . $sParams);
  
}

function Book_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sBookErr;

  $sWhere = "";
  
  $bPK = true;
  $flditem_id = "";
  $fldname = "";
  $fldauthor = "";
  $fldcategory_id = "";
  $fldprice = "";
  $fldproduct_url = "";
  $fldimage_url = "";
  $fldnotes = "";
  $fldis_rec = "";
  

  if($sBookErr == "")
  {
    // Load primary key and form parameters
    $fldcategory_id = get_param("category_id");
    $flditem_id = get_param("item_id");
    $fldis_recommended = get_param("is_recommended");
    $tpl->set_var("Trn_category_id", get_param("category_id"));
    $tpl->set_var("Trn_is_recommended", get_param("is_recommended"));
    $pitem_id = get_param("item_id");
    $tpl->set_var("BookError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $flditem_id = strip(get_param("item_id"));
    $fldname = strip(get_param("name"));
    $fldauthor = strip(get_param("author"));
    $fldcategory_id = strip(get_param("category_id"));
    $fldprice = strip(get_param("price"));
    $fldproduct_url = strip(get_param("product_url"));
    $fldimage_url = strip(get_param("image_url"));
    $fldnotes = strip(get_param("notes"));
    $fldis_rec = strip(get_param("is_rec"));
    $tpl->set_var("Trn_category_id", get_param("Trn_category_id"));
    $tpl->set_var("Trn_is_recommended", get_param("Trn_is_recommended"));
    $pitem_id = get_param("PK_item_id");
    $tpl->set_var("sBookErr", $sBookErr);
    $tpl->parse("BookError", false);
  }

  
  if( !strlen($pitem_id)) $bPK = false;
  
  $sWhere .= "item_id=" . tosql($pitem_id, "Number");
  $tpl->set_var("PK_item_id", $pitem_id);

  $sSQL = "select * from items where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Book"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $flditem_id = $db->f("item_id");
    if($sBookErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldname = $db->f("name");
      $fldauthor = $db->f("author");
      $fldcategory_id = $db->f("category_id");
      $fldprice = $db->f("price");
      $fldproduct_url = $db->f("product_url");
      $fldimage_url = $db->f("image_url");
      $fldnotes = $db->f("notes");
      $fldis_rec = $db->f("is_recommended");
    }
    $tpl->set_var("BookInsert", "");
    $tpl->parse("BookEdit", false);
  }
  else
  {
    if($sBookErr == "")
    {
      $flditem_id = tohtml(get_param("item_id"));
      $fldcategory_id = tohtml(get_param("category_id"));
      $fldis_rec = tohtml(get_param("is_recommended"));
      $fldis_rec= "0";
    }
    $tpl->set_var("BookEdit", "");
    $tpl->parse("BookInsert", false);
  }
  $tpl->parse("BookCancel", false);

  // Show form field
  
    $tpl->set_var("item_id", tohtml($flditem_id));
    $tpl->set_var("name", tohtml($fldname));
    $tpl->set_var("author", tohtml($fldauthor));
    $tpl->set_var("LBcategory_id", "");
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
    
    $tpl->set_var("price", tohtml($fldprice));
    $tpl->set_var("product_url", tohtml($fldproduct_url));
    $tpl->set_var("image_url", tohtml($fldimage_url));
    $tpl->set_var("notes", tohtml($fldnotes));
      if(strtolower($fldis_rec) == strtolower("1")) 
        $tpl->set_var("is_rec_CHECKED", "CHECKED");
      else
        $tpl->set_var("is_rec_CHECKED", "");

  $tpl->parse("FormBook", false);
  

}

?>