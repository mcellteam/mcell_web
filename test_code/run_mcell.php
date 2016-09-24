<html>

<head>
<title>MCell Web Development - Run MCell</title>
<link rel="stylesheet" type="text/css" href="../style/def.css" />
</head>

<body>

<style>

/* Body for the entire document */
body {
  background-color: #eeeeee;
  color: #000066;
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
$seed = 1;
if (in_array("seed",array_keys($_POST))) {
  $seed = $_POST["seed"];
}
$what = "";
if (in_array("what",array_keys($_POST))) {
  $what = $_POST["what"];
}

echo "<b>Model Name:</b> &nbsp; <input type=\"text\" name=\"model_name\" value=".$model_name.">";
echo " &nbsp; &nbsp; <b>Seed:</b> &nbsp; <input type=\"text\" name=\"seed\" value=".$seed.">";
// echo " &nbsp; &nbsp; <input type=\"submit\" value=\"Run MCell\">";
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"run\">Run MCell</button>";
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"clear\">Clear</button>";
$output = "";
if (strlen($what) > 0) {
  if (strcmp($what,"clear") == 0) {
    $output = shell_exec ("rm -Rf viz_data; ls -lR");
  } elseif (strcmp($what,"run") == 0) {
    if (strlen($model_name) > 0) {
      //$result = popen("/bin/ls", "r");
      echo "<br/><b>MCell output ...</b>";
      $sep = "=======================================================================================";
      $output = shell_exec ("./mcell -seed ".$seed." ".$model_name."; echo \" \"; echo ".$sep."; echo \" \"; ls -lR;");
    }
  }
}
echo "<div class=\"center\"><table><tr><td><pre>$output</pre></td></tr></table></div>";
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
