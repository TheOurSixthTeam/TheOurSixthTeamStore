<?php
//********************************************************************************


function Menu_show()
{
  
  global $tpl;
  // Set URLs
  $fldField2 = "Default.php";
  $fldHome = "Default.php";
  $fldReg = "Registration.php";
  $fldShop = "ShoppingCart.php";
  $fldField1 = "Login.php";
  $fldAdmin = "AdminMenu.php";
  // Show fields
  $tpl->set_var("Field2", $fldField2);
  $tpl->set_var("Home", $fldHome);
  $tpl->set_var("Reg", $fldReg);
  $tpl->set_var("Shop", $fldShop);
  $tpl->set_var("Field1", $fldField1);
  $tpl->set_var("Admin", $fldAdmin);
  $tpl->parse("FormMenu", false);
}

?>