<?php
//********************************************************************************


function Footer_show()
{
  
  global $tpl;
  // Set URLs
  $fldField1 = "Default.php";
  $fldField3 = "Registration.php";
  $fldField5 = "ShoppingCart.php";
  $fldField2 = "Login.php";
  $fldField4 = "AdminMenu.php";
  // Show fields
  $tpl->set_var("Field1", $fldField1);
  $tpl->set_var("Field3", $fldField3);
  $tpl->set_var("Field5", $fldField5);
  $tpl->set_var("Field2", $fldField2);
  $tpl->set_var("Field4", $fldField4);
  $tpl->parse("FormFooter", false);
}

?>