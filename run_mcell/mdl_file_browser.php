<!DOCTYPE html5>

<html>

<head>
<title> MDL File Browser </title>
<link rel="stylesheet" type="text/css" href="browse_style.css">

<style>
pre {margin: 0; font-family: monospace;}
table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 2px 4px 6px #008;}
.center {text-align: center;}
.center table {margin: 1em auto; text-align: left;}
.center th {text-align: center !important;}
th {border: 1px solid #666; font-size: 90%; vertical-align: baseline; padding: 4px 5px; background-color: #ccc;}
td {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px; background-color: #eee;}
.p {text-align: left;}
.v i {color: #999;}
hr {background-color: #ccc; border: 0; height: 3px;}

table, th, td {
  color: #000000;  /* Text Color - black */
  border: 1px solid black;
  margin-left: 10px;
  margin-right: 10px;
  margin-top: 10px;
  margin-bottom: 10px;
  /* border-collapse: collapse; */
}

</style>

</head>


<body>

<form action="mdl_file_browser.php" method="post">

<a href="../.."> <b>Home</b> </a> &nbsp; &nbsp; &nbsp; &nbsp;

<?php

$model_file_name = "";
if (in_array("model_file_name",array_keys($_POST))) {
  $model_file_name = $_POST["model_file_name"];
}

$model_files = glob("mdl_files/*.mdl");

echo "<b>MDL File Name:</b> &nbsp; <select id=\"data_model_name_id\" name=\"model_file_name\">\n";
echo "  <option value=\"\"></option>>";
for ($model_file_index=0; $model_file_index<count($model_files); $model_file_index++) {
  $sel = "";
  if ($model_files[$model_file_index] == $model_file_name) {
    $sel = " selected ";
  }
  echo "  <option ".$sel."value=".$model_files[$model_file_index].">".$model_files[$model_file_index]."</option>\n";
}
echo "</select>\n";

echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"load\">Load</button>\n";

?>


</form>


<hr/>

<?php if (strlen($model_file_name)>0) { echo "<h1>MDL File: &nbsp; ".$model_file_name."</h1>"; } ?>

<?php
$output = "";
if (strlen($model_file_name)>0) {
  $output = file_get_contents ( $model_file_name );
  echo "<center id=\"mcellout\"><table><tr><td><pre>".$output."</pre></td></tr></table></center>";
}
?>



</body>
</html>

