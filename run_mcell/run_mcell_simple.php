<html>

<head>
<title>MCell Web Development - Run MCell Data Model</title>
<link rel="stylesheet" type="text/css" href="run_style.css">
</head>


<body>

<?php
$users_name = "";
$users_name_shown = "";
if (in_array("REMOTE_USER",array_keys($_SERVER))) {
  $users_name = $_SERVER["REMOTE_USER"];
  $users_name_shown = " &nbsp [".$users_name."]";
}
?>

<hr/>

<center><h1 style="font-size:200%">MCell Web Development at <a href="../..">mcell.snl.salk.edu</a> <?php echo $users_name_shown; ?> </h1></center>

<hr/>

<center>

<form action="run_mcell_simple.php" method="post">

<p>

<?php

// Copy the values from the previous form to use in drawing this form

$model_file_name = "";
if (in_array("model_file_name",array_keys($_POST))) {
  $model_file_name = $_POST["model_file_name"];
}
$what = "";
if (in_array("what",array_keys($_POST))) {
  $what = $_POST["what"];
}

$model_files = glob("data_model_files/*.json");

echo "<b><span style=\"font-size:130%\">Data Model Name:</span></b> &nbsp; <select name=\"model_file_name\">\n";
echo "  <option value=\"\"></option>>";
for ($model_file_index=0; $model_file_index<count($model_files); $model_file_index++) {
  $sel = "";
  if ($model_files[$model_file_index] == $model_file_name) {
    $sel = " selected ";
  }
  echo "  <option ".$sel."value=".$model_files[$model_file_index].">".$model_files[$model_file_index]."</option>\n";
}
echo "</select>\n";

?>

&nbsp; <button type="submit" name="what" value="run">Run Model</button>
&nbsp; <button type="submit" name="what" value="clear">Clear Data</button>
&nbsp; <button type="submit" name="what" value="info">Information</button>

</p>

<?php

$output = "";

if (strcmp($what,"run") == 0) {
  $output = shell_exec ("echo run_mcell_simple.php  calling  python run_data_model_mcell.py;");
  $output = $output.shell_exec ("mkdir -p run_files/".$users_name.";");
  $output = $output."\n<hr/>\n";
  $command = "python run_data_model_mcell.py ".$model_file_name." -pd ".getcwd()."/run_files/".$users_name." -b ".getcwd()."/mcell";
  $output = $output."Command = ".$command."\n\n";
  $output = $output.shell_exec($command);
}

if (strcmp($what,"info") == 0) {
  $output = shell_exec ("echo python run_data_model_mcell.py --help; echo; python run_data_model_mcell.py --help; echo;");
  $output = $output.shell_exec ("echo Working Directory: ".getcwd()."; pwd; echo; ls -l; echo;");
}

if (strcmp($what,"clear") == 0) {
  $output =      "Removing all files from run_files/".$users_name." ...\n";
  $output = $output.shell_exec ("rm -rf run_files/".$users_name);
}

?>

</form>

</center>

<center id="mcellout"><table><tr><td><pre><?php echo $output; ?></pre></td></tr></table></center>

</body>
</html>
