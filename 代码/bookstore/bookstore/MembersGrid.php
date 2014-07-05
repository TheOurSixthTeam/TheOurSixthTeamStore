<?php
include ("./common.php");
include ("./Header.php");
include ("./Footer.php");

session_start();

$filename = "MembersGrid.php";



check_security(2);

$tpl = new Template($app_path);
$tpl->load_file("MembersGrid.html", "main");
$tpl->load_file($header_filename, "Header");
$tpl->load_file($footer_filename, "Footer");

$tpl->set_var("FileName", $filename);

Menu_show();
Footer_show();
Search_show();
Members_show();

$tpl->parse("Header", false);
$tpl->parse("Footer", false);

$tpl->pparse("main", false);

//********************************************************************************


function Search_show()
{
  global $db;
  global $tpl;
  
  $tpl->set_var("ActionPage", "MembersGrid.php");
	
  // Set variables with search parameters
  $fldname = strip(get_param("name"));
    // Show fields
    $tpl->set_var("name", tohtml($fldname));
  $tpl->parse("FormSearch", false);
}



function Members_show()
{

  
  global $tpl;
  global $db;
  global $sMembersErr;
  $sWhere = "";
  $sOrder = "";
  $sSQL = "";
  $HasParam = false;

  
  $tpl->set_var("TransitParams", "name=" . tourl(strip(get_param("name"))) . "&");
  $tpl->set_var("FormParams", "name=" . tourl(strip(get_param("name"))) . "&");
  // Build WHERE statement
  
  $pname = get_param("name");
  if(strlen($pname))
  {
    $HasParam = true;
    $sWhere = "m.member_login like " . tosql("%".$pname ."%", "Text") . " or " . "m.first_name like " . tosql("%".$pname ."%", "Text") . " or " . "m.last_name like " . tosql("%".$pname ."%", "Text");
  }
  
  if($HasParam)
    $sWhere = " WHERE (" . $sWhere . ")";

  $sDirection = "";
  $sSortParams = "";
  
  // Build ORDER statement
  $sOrder = " order by m.member_login Asc";
  $iSort = get_param("FormMembers_Sorting");
  $iSorted = get_param("FormMembers_Sorted");
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
      $sSortParams = "FormMembers_Sorting=" . $iSort . "&FormMembers_Sorted=" . $iSort . "&";
    }
    else
    {
      $tpl->set_var("Form_Sorting", $iSort);
      $sDirection = " ASC";
      $sSortParams = "FormMembers_Sorting=" . $iSort . "&FormMembers_Sorted=" . "&";
    }
    
    if ($iSort == 1) $sOrder = " order by m.member_login" . $sDirection;
    if ($iSort == 2) $sOrder = " order by m.first_name" . $sDirection;
    if ($iSort == 3) $sOrder = " order by m.last_name" . $sDirection;
    if ($iSort == 4) $sOrder = " order by m.member_level" . $sDirection;
  }

  // Build full SQL statement
  
  $sSQL = "select m.first_name as m_first_name, " . 
    "m.last_name as m_last_name, " . 
    "m.member_id as m_member_id, " . 
    "m.member_level as m_member_level, " . 
    "m.member_login as m_member_login " . 
    " from members m ";
  
  $sSQL .= $sWhere . $sOrder;
  $tpl->set_var("FormAction", "MembersRecord.php");
  $tpl->set_var("SortParams", $sSortParams);

  // Execute SQL statement
  $db->query($sSQL);
  
  // Select current page
  $iPage = get_param("FormMembers_Page");
  if(!strlen($iPage)) $iPage = 1;
  $RecordsPerPage = 20;
  if(($iPage - 1) * $RecordsPerPage != 0)
    $db->seek(($iPage - 1) * $RecordsPerPage);
  $iCounter = 0;
  $amember_level = split(";", "1;Member;2;Administrator");

  if($db->next_record())
  {  
    // Show main table based on SQL query
    do
    {
			$fldmember_login = $db->f("m_member_login");
			$fldname = $db->f("m_first_name");
			$fldlast_name = $db->f("m_last_name");
			$fldmember_level = $db->f("m_member_level");
      $tpl->set_var("member_login", tohtml($fldmember_login));
      $tpl->set_var("member_login_URLLink", "MembersInfo.php");
      $tpl->set_var("Prm_member_id", tourl($db->f("m_member_id"))); 
      $tpl->set_var("name", tohtml($fldname));
      $tpl->set_var("last_name", tohtml($fldlast_name));
      $fldmember_level = get_lov_value($fldmember_level, $amember_level);
      $tpl->set_var("member_level", tohtml($fldmember_level));
      $tpl->parse("DListMembers", true);
      $iCounter++;
    } while($iCounter < $RecordsPerPage &&$db->next_record());
  }
  else
  {
    // No Records in DB
    $tpl->set_var("DListMembers", "");
    $tpl->parse("MembersNoRecords", false);
    $tpl->set_var("MembersScroller", "");
    $tpl->parse("FormMembers", false);
    return;
  }
  
  // Parse scroller
  if(@$db->next_record())
  {
    if ($iPage == 1)
    {
      $tpl->set_var("MembersScrollerPrevSwitch", "_");
    }
    else
    {
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("MembersScrollerPrevSwitch", "");
    }
    $tpl->set_var("NextPage", ($iPage + 1));
    $tpl->set_var("MembersScrollerNextSwitch", "");
    $tpl->set_var("MembersCurrentPage", $iPage);
    $tpl->parse("MembersScroller", false);
  }
  else
  {
    if ($iPage == 1)
    {
      $tpl->set_var("MembersScroller", "");
    }
    else
    {
      $tpl->set_var("MembersScrollerNextSwitch", "_");
      $tpl->set_var("PrevPage", ($iPage - 1));
      $tpl->set_var("MembersScrollerPrevSwitch", "");
      $tpl->set_var("MembersCurrentPage", $iPage);
      $tpl->parse("MembersScroller", false);
    }
  }
  $tpl->set_var("MembersNoRecords", "");
  $tpl->parse("FormMembers", false);
  
}

?>