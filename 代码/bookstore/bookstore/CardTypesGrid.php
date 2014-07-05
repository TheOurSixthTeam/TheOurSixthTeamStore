<?php
/*********************************************************************************
 *       Filename: CardTypesGrid.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "CardTypesGrid.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("CardTypesGrid.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
CardTypes_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************



function CardTypes_show()
{

  
  global $tpl;
  global $db;
  global $sCardTypesErr;
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
  $sOrder = " order by c.name Asc";
  $iSort = get_param("FormCardTypes_Sorting");
  $iSorted = get_param("FormCardTypes_Sorted");
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
      $sSortParams = "FormCardTypes_Sorting=" . $iSort . "&FormCardTypes_Sorted=" . $iSort . "&";
    }
    else
    {
      $tpl->set_var("Form_Sorting", $iSort);
      $sDirection = " ASC";
      $sSortParams = "FormCardTypes_Sorting=" . $iSort . "&FormCardTypes_Sorted=" . "&";
    }
    
    if ($iSort == 1) $sOrder = " order by c.name" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select c.card_type_id as c_card_type_id, " . 
    "c.name as c_name " . 
    " from card_types c ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("FormAction", "CardTypesRecord.php");
  $tpl->set_var("SortParams", $sSortParams);

  // Execute SQL statement
  $db->query($sSQL);
  

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldname = $db->f("c_name");
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("name_URLLink", "CardTypesRecord.php");
      $tpl->set_var("Prm_card_type_id", tourl($db->f("c_card_type_id"))); 
      $tpl->parse("DListCardTypes", true);
    } while($db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListCardTypes", "");
    $tpl->parse("CardTypesNoRecords", false);
    $tpl->parse("FormCardTypes", false);
    return;
  }
  
  $tpl->set_var("CardTypesNoRecords", "");
  $tpl->parse("FormCardTypes", false);
  
}

?>