<?php
include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "BookDetail.php";



check_security(1);

$tpl = new Template($app_path);
$tpl->load_file("BookDetail.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);


$sDetailErr = "";
$sOrderErr = "";
$sRatingErr = "";

$sAction = get_param("FormAction");
$sForm = get_param("FormName");
switch ($sForm) {
  case "Detail":
    Detail_action($sAction);
  break;
  case "Order":
    Order_action($sAction);
  break;
  case "Rating":
    Rating_action($sAction);
  break;
}Menu_show();
Footer_show();
Detail_show();
Order_show();
Rating_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function Detail_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sDetailErr;
  
  $sParams = "";
  $sActionFileName = "ShoppingCart.php";

  
  $sParams = "?";
  $sParams .= "item_id=" . tourl(get_param("Trn_item_id"));

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName . $sParams); 

  

  // Load all form fields into variables
  

  $sSQL = "";
  // Create SQL statement
  
  // Execute SQL statement
  if(strlen($sDetailErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName . $sParams);
  
}

function Detail_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sDetailErr;

  $sWhere = "";
  
  $bPK = true;
  $flditem_id = "";
  $fldname = "";
  $fldauthor = "";
  $fldcategory_id = "";
  $fldprice = "";
  $fldimage_url = "";
  $fldnotes = "";
  $fldproduct_url = "";
  

  if($sDetailErr == "")
  {
    // Load primary key and form parameters
    $flditem_id = get_param("item_id");
    $tpl->set_var("Trn_item_id", get_param("item_id"));
    $pitem_id = get_param("item_id");
    $tpl->set_var("DetailError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $flditem_id = strip(get_param("item_id"));
    $tpl->set_var("Trn_item_id", get_param("Trn_item_id"));
    $pitem_id = get_param("PK_item_id");
    $tpl->set_var("sDetailErr", $sDetailErr);
    $tpl->parse("DetailError", false);
  }

  
  if( !strlen($pitem_id)) $bPK = false;
  
  $sWhere .= "item_id=" . tosql($pitem_id, "Number");
  $tpl->set_var("PK_item_id", $pitem_id);

  $sSQL = "select * from items where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Detail"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $flditem_id = $db->f("item_id");
    $fldname = $db->f("name");
    $fldauthor = $db->f("author");
    $fldcategory_id = $db->f("category_id");
    $fldprice = $db->f("price");
    $fldimage_url = $db->f("image_url");
    $fldnotes = $db->f("notes");
    $fldproduct_url = $db->f("product_url");
    $tpl->set_var("DetailDelete", "");
    $tpl->set_var("DetailUpdate", "");
    $tpl->set_var("DetailInsert", "");
  }
  else
  {
    if($sDetailErr == "")
    {
      $flditem_id = tohtml(get_param("item_id"));
    }
    $tpl->set_var("DetailEdit", "");
    $tpl->set_var("DetailInsert", "");
  }
  $tpl->set_var("DetailCancel", "");
  // Set lookup fields
  $fldcategory_id = dlookup("categories", "name", "category_id=" . tosql($fldcategory_id, "Number"));
  if($sDetailErr == "")
  {
$fldimage_url="<img border=0 src=" . $fldimage_url . ">";
$fldproduct_url="Review this book on Amazon.com";
  }

  // Show form field
  
    $tpl->set_var("item_id", tohtml($flditem_id));
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("author", tohtml($fldauthor));
      $tpl->set_var("category_id", tohtml($fldcategory_id));
      $tpl->set_var("price", tohtml($fldprice));
      $tpl->set_var("image_url", $fldimage_url);
      $tpl->set_var("image_url_URLLink", $db->f("product_url"));
      $tpl->set_var("notes", $fldnotes);
      $tpl->set_var("product_url", tohtml($fldproduct_url));
      $tpl->set_var("product_url_URLLink", $db->f("product_url"));
  $tpl->parse("FormDetail", false);
  

}



function Order_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sOrderErr;
  
  $sParams = "";
  $sActionFileName = "ShoppingCart.php";

  

  $sWhere = "";
  $bErr = false;

  if($sAction == "cancel")
    header("Location: " . $sActionFileName); 

  

  // Load all form fields into variables
  
  $fldUserID = get_session("UserID");
  $fldquantity = get_param("quantity");
  $flditem_id = get_param("item_id");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldquantity))
      $sOrderErr .= "The value in field Quantity is required.<br>";
    
    if(!is_number($fldquantity))
      $sOrderErr .= "The value in field Quantity is incorrect.<br>";
    
    if(!is_number($flditem_id))
      $sOrderErr .= "The value in field item_id is incorrect.<br>";
    

    if(strlen($sOrderErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "insert":
      
        $sSQL = "insert into orders (" . 
          "member_id," . 
          "quantity," . 
          "item_id)" . 
          " values (" . 
          tosql($fldUserID, "Number") . "," .
          tosql($fldquantity, "Number") . "," .
          tosql($flditem_id, "Number") . ")";    
    break;
  }
  // Execute SQL statement
  if(strlen($sOrderErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName);
  
}

function Order_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sOrderErr;

  $sWhere = "";
  
  $bPK = true;
  $fldorder_id = "";
  $fldquantity = "";
  $flditem_id = "";
  

  if($sOrderErr == "")
  {
    // Load primary key and form parameters
    $flditem_id = get_param("item_id");
    $porder_id = get_param("order_id");
    $tpl->set_var("OrderError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $fldorder_id = strip(get_param("order_id"));
    $fldquantity = strip(get_param("quantity"));
    $flditem_id = strip(get_param("item_id"));
    $porder_id = get_param("PK_order_id");
    $tpl->set_var("sOrderErr", $sOrderErr);
    $tpl->parse("OrderError", false);
  }

  
  if( !strlen($porder_id)) $bPK = false;
  
  $sWhere .= "order_id=" . tosql($porder_id, "Number");
  $tpl->set_var("PK_order_id", $porder_id);

  $sSQL = "select * from orders where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Order"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $fldorder_id = $db->f("order_id");
    $flditem_id = $db->f("item_id");
    if($sOrderErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldquantity = $db->f("quantity");
    }
    $tpl->set_var("OrderDelete", "");
    $tpl->set_var("OrderUpdate", "");
    $tpl->set_var("OrderInsert", "");
  }
  else
  {
    if($sOrderErr == "")
    {
      $flditem_id = tohtml(get_param("item_id"));
      $fldquantity= "1";
    }
    $tpl->set_var("OrderEdit", "");
    $tpl->parse("OrderInsert", false);
  }
  $tpl->set_var("OrderCancel", "");

  // Show form field
  
    $tpl->set_var("order_id", tohtml($fldorder_id));
    $tpl->set_var("quantity", tohtml($fldquantity));
    $tpl->set_var("item_id", tohtml($flditem_id));
  $tpl->parse("FormOrder", false);
  

}



function Rating_action($sAction)
{
  global $db;
  global $tpl;
  global $sForm;
  global $sRatingErr;
  
  $sParams = "";
  $sActionFileName = "BookDetail.php";

  
  $sParams = "?";
  $sParams .= "item_id=" . tourl(get_param("Trn_item_id"));

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
  
  $fldrating = get_param("rating");
  $fldrating_count = get_param("rating_count");
  // Validate fields
  if($sAction == "insert" || $sAction == "update") 
  {
    if(!strlen($fldrating))
      $sRatingErr .= "The value in field Your Rating is required.<br>";
    
    if(!is_number($fldrating))
      $sRatingErr .= "The value in field Your Rating is incorrect.<br>";
    
    if(!is_number($fldrating_count))
      $sRatingErr .= "The value in field rating_count is incorrect.<br>";
    

    if(strlen($sRatingErr)) return;
  }
  

  $sSQL = "";
  // Create SQL statement
  
  switch(strtolower($sAction)) 
  {
    case "update":
      
$sSQL = "update items set rating=rating+" . get_param("rating") . ", rating_count=rating_count+1 where item_id=" . get_param("item_id");
      if($sSQL == "")
			{
        $sSQL = "update items set " .
          "rating=" . tosql($fldrating, "Number") .
          ",rating_count=" . tosql($fldrating_count, "Number");
        $sSQL .= " where " . $sWhere;
      }
    break;
  }
  // Execute SQL statement
  if(strlen($sRatingErr)) return;
  $db->query($sSQL);
  
  header("Location: " . $sActionFileName . $sParams);
  
}

function Rating_show()
{
  global $db;
  global $tpl;
  global $sAction;
  global $sForm;
  global $sRatingErr;

  $sWhere = "";
  
  $bPK = true;
  $flditem_id = "";
  $fldrating_view = "";
  $fldrating_count_view = "";
  $fldrating = "";
  $fldrating_count = "";
  

  if($sRatingErr == "")
  {
    // Load primary key and form parameters
    $flditem_id = get_param("item_id");
    $tpl->set_var("Trn_item_id", get_param("item_id"));
    $pitem_id = get_param("item_id");
    $tpl->set_var("RatingError", "");
  }
  else
  {
    // Load primary key, form parameters and form fields
    $flditem_id = strip(get_param("item_id"));
    $fldrating = strip(get_param("rating"));
    $fldrating_count = strip(get_param("rating_count"));
    $tpl->set_var("Trn_item_id", get_param("Trn_item_id"));
    $pitem_id = get_param("PK_item_id");
    $tpl->set_var("sRatingErr", $sRatingErr);
    $tpl->parse("RatingError", false);
  }

  
  if( !strlen($pitem_id)) $bPK = false;
  
  $sWhere .= "item_id=" . tosql($pitem_id, "Number");
  $tpl->set_var("PK_item_id", $pitem_id);

  $sSQL = "select * from items where " . $sWhere;

  

  if($bPK && !($sAction == "insert" && $sForm == "Rating"))
  {
    // Execute SQL statement
    $db->query($sSQL);
    $db->next_record();
    
    $flditem_id = $db->f("item_id");
    $fldrating_view = $db->f("rating");
    $fldrating_count_view = $db->f("rating_count");
    $fldrating_count = $db->f("rating_count");
    if($sRatingErr == "") 
    {
      // Load data from recordset when form displayed first time
      $fldrating = $db->f("rating");
    }
    $tpl->set_var("RatingDelete", "");
    $tpl->set_var("RatingInsert", "");
    $tpl->parse("RatingEdit", false);
  }
  else
  {
    if($sRatingErr == "")
    {
      $flditem_id = tohtml(get_param("item_id"));
    }
    $tpl->set_var("RatingEdit", "");
    $tpl->set_var("RatingInsert", "");
  }
  $tpl->set_var("RatingCancel", "");
  if($sRatingErr == "")
  {
if ($fldrating_view == 0)
      {
        $fldrating_view = "Not rated yet";
        $fldrating_count_view = "";
      }
      else
      {
        $fldrating_view = "<img src=\"images/" . round($fldrating/$fldrating_count) . "stars.gif\">";
      }
  }

  // Show form field
  
    $tpl->set_var("item_id", tohtml($flditem_id));
      $tpl->set_var("rating_view", $fldrating_view);
      $tpl->set_var("rating_count_view", tohtml($fldrating_count_view));
    $tpl->set_var("LBrating", "");
    $LOV = split(";", "1;Deficient;2;Regular;3;Good;4;Very Good;5;Excellent");
  
    if(sizeof($LOV)%2 != 0) 
      $array_length = sizeof($LOV) - 1;
    else
      $array_length = sizeof($LOV);
    reset($LOV);
    for($i = 0; $i < $array_length; $i = $i + 2)
    {
      $tpl->set_var("ID", $LOV[$i]);
      $tpl->set_var("Value", $LOV[$i + 1]);
      if($LOV[$i] == $fldrating) 
        $tpl->set_var("Selected", "SELECTED");
      else
        $tpl->set_var("Selected", "");
      $tpl->parse("LBrating", true);
    }
    $tpl->set_var("rating_count", tohtml($fldrating_count));
  $tpl->parse("FormRating", false);
  

}

?>