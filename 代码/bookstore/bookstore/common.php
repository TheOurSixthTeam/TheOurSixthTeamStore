<?php error_reporting(0); ?>
<?php
error_reporting (E_ALL ^ E_NOTICE);
include("./template.php");

// Database Parameters
//include("./db_odbc.inc");

include("./db_mysql.inc");
define("DATABASE_NAME","bookstore");
define("DATABASE_USER","root");
define("DATABASE_PASSWORD","a1234");
define("DATABASE_HOST","localhost:3306");


// Database Initialize
$db = new DB_Sql();
$db->Database = DATABASE_NAME;
$db->User     = DATABASE_USER;
$db->Password = DATABASE_PASSWORD;
$db->Host     = DATABASE_HOST;


$app_path = ".";


$header_filename = "Header.html";
$footer_filename = "Footer.html";

function tohtml($strValue)
{
  return htmlspecialchars($strValue);
}

function tourl($strValue)
{
  return urlencode($strValue);
}

function get_param($param_name)
{
  global $HTTP_POST_VARS;
  global $HTTP_GET_VARS;

  $param_value = "";
  if(isset($HTTP_POST_VARS[$param_name]))
    $param_value = $HTTP_POST_VARS[$param_name];
  else if(isset($HTTP_GET_VARS[$param_name]))
    $param_value = $HTTP_GET_VARS[$param_name];

  return $param_value;
}
function session_is_registered($param_name){
	return true;
}
function get_session($param_name)
{
  global $HTTP_POST_VARS;
  global $HTTP_GET_VARS;
  global ${$param_name};

  $param_value = "";
  if(!isset($HTTP_POST_VARS[$param_name]) && !isset($HTTP_GET_VARS[$param_name]) && session_is_registered($param_name)) 
    $param_value = ${$param_name};

  return $param_value;
}

function set_session($param_name, $param_value)
{
  global ${$param_name};
  if(session_is_registered($param_name)) 
    session_unregister($param_name);
  ${$param_name} = $param_value;
  session_register($param_name);
}

function is_number($string_value)
{
  if(is_numeric($string_value) || !strlen($string_value))
    return true;
  else 
    return false;
}

function tosql($value, $type)
{
  if($value == "")
    return "NULL";
  else
    if($type == "Number")
      return doubleval($value);
    else
    {
      if(get_magic_quotes_gpc() == 0)
      {
        $value = str_replace("'","''",$value);
        $value = str_replace("\\","\\\\",$value);
      }
      else
      {
        $value = str_replace("\\'","''",$value);
        $value = str_replace("\\\"","\"",$value);
      }

      return "'" . $value . "'";
    }
}

function strip($value)
{
  if(get_magic_quotes_gpc() == 0)
    return $value;
  else
    return stripslashes($value);
}

function db_fill_array($sql_query)
{
  $db_fill = new DB_Sql();
  $db_fill->Database = DATABASE_NAME;
  $db_fill->User     = DATABASE_USER;
  $db_fill->Password = DATABASE_PASSWORD;
  $db_fill->Host     = DATABASE_HOST;

  $db_fill->query($sql_query);
  if ($db_fill->next_record())
  {
    do
    {
      $ar_lookup[$db_fill->f(0)] = $db_fill->f(1);
    } while ($db_fill->next_record());
    return $ar_lookup;
  }
  else
    return false;

}

function dlookup($table_name, $field_name, $where_condition)
{
  $db_look = new DB_Sql();
  $db_look->Database = DATABASE_NAME;
  $db_look->User     = DATABASE_USER;
  $db_look->Password = DATABASE_PASSWORD;
  $db_look->Host     = DATABASE_HOST;

  $db_look->query("SELECT " . $field_name . " FROM " . $table_name . " WHERE " . $where_condition);
  if($db_look->next_record())
    return $db_look->f(0);
  else 
    return "";
}


function get_checkbox_value($value, $checked_value, $unchecked_value, $type)
{
  if(!strlen($value))
    return tosql($unchecked_value, $type);
  else
    return tosql($checked_value, $type);
}

function get_lov_value($value, $array)
{
  $return_result = "";

  if(sizeof($array) % 2 != 0) 
    $array_length = sizeof($array) - 1;
  else
    $array_length = sizeof($array);
  reset($array);

  for($i = 0; $i < $array_length; $i = $i + 2)
  {
    if($value == $array[$i]) $return_result = $array[$i+1];
  }

  return $return_result;
}




function check_security($security_level)
{
  global $UserRights;
  if(!session_is_registered("UserID"))
    header ("Location: Login.php?querystring=" . tourl(getenv("QUERY_STRING")) . "&ret_page=" . tourl(getenv("REQUEST_URI")));
  else
    if(!session_is_registered("UserRights") || $UserRights < $security_level)
      header ("Location: Login.php?querystring=" . tourl(getenv("QUERY_STRING")) . "&ret_page=" . tourl(getenv("REQUEST_URI")));
}

?>