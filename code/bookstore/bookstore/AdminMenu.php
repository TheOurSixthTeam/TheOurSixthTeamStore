<?php
/*********************************************************************************
 *       Filename: AdminMenu.php
 *       Generated with CodeCharge 1.1.19
 *       PHP & Templates build 03/28/2001
 *********************************************************************************/

include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "AdminMenu.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("AdminMenu.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
Form_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************


function Form_show()
{
  
  global $tpl;
  // Set URLs
  $fldField1 = "MembersGrid.php";
  $fldField2 = "OrdersGrid.php";
  $fldField3 = "AdminBooks.php";
  $fldField4 = "CategoriesGrid.php";
  $fldField5 = "EditorialsGrid.php";
  $fldField6 = "EditorialCatGrid.php";
  $fldField = "CardTypesGrid.php";
  // Show fields
  $tpl->set_var("Field1", $fldField1);
  $tpl->set_var("Field2", $fldField2);
  $tpl->set_var("Field3", $fldField3);
  $tpl->set_var("Field4", $fldField4);
  $tpl->set_var("Field5", $fldField5);
  $tpl->set_var("Field6", $fldField6);
  $tpl->set_var("Field", $fldField);
  $tpl->parse("FormForm", false);
}

?>