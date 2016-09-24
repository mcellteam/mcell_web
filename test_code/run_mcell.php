<html>

<head>
<title>MCell Web Development - Run MCell</title>
<link rel="stylesheet" type="text/css" href="../style/def.css" />
</head>

<body>

<style>

/* Body for the entire document */
body {
  background-color: #eeeeee	;  /* Web-Safe Light Blue is ccccff */
  color: #000066;  /* Text Color - Web-Safe Dark Blue is 000066 */
  margin-left: 10px;
  margin-right: 10px;
  margin-top: 10px;
  margin-bottom: 10px;
}

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}

</style>


<?php echo '<center><h1>MCell Web Development at mcell.snl.salk.edu</h1></center>'; ?>

<hr/>

<form action="run_mcell.php" method="post">
<?php
$model_name = "";
if (in_array("model_name",array_keys($_POST))) {
  $model_name = $_POST["model_name"];
}
echo "<b>Model Name:</b> &nbsp; <input type=\"text\" name=\"model_name\" value=".$model_name.">";
echo "<input type=\"submit\" value=\"Run MCell\">";
if (strlen($model_name) > 0) {
  //$result = popen("/bin/ls", "r");
  echo "<br/><b>MCell is running ...</b>";
  $output = shell_exec("./mcell -seed 2 Scene.main.mdl");
  echo "<pre>$output</pre>";
}
?>
</form>

<?php
echo "<div class=\"center\"><h2>PHP GLOBALS</h2><table>";
echo "<tr class=\"h\"><th colspan=\"2\">Global Variables from \"foreach\"</th></tr>";
echo "<tr class=\"h\"><th>Key</th><th>Value</th></tr>";
foreach ( $GLOBALS as $gl_key => $gl_val ) {
  echo "<tr>";
  echo "<td class=\"e\">" . $gl_key . "</td><td class=\"v\">";
  var_dump ( $gl_val );
  echo "</td>";
  echo "</tr>";
}
echo "</table></div>";
?>  

<?php phpinfo(); ?>

</body>
</html>
