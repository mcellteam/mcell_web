<html>

<head>
<title>MCell Web Development - Run MCell Data Model</title>
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

<form action="run_mcell_hc.php" method="post">

<button type=\"submit\" name="what" value="run">Run Model</button>
<button type=\"submit\" name="what" value="clear">Clear Data</button>

<?php

$what = "";
if (in_array("what",array_keys($_POST))) {
  $what = $_POST["what"];
}

print ( "What = ".$what );

$json_string = "";
#$model_files = glob("data_model_files/*.json");

$model_file_name = "data_model_files/Release_Decay.json";

$output = "";
if (strcmp($what,"run") == 0) {
  $output = shell_exec ("echo Run run_mcell_hc.php; pwd; mkdir -p run_files/".$users_name."; ls -l; python run_data_model_mcell.py --help; python run_data_model_mcell.py Release_Decay_Sweep_2.json -pd ".getcwd()."/run_files/".$users_name." -b ../../../../../mcell");
}

if (strcmp($what,"clear") == 0) {
  $output = shell_exec ("pwd; rm -rf run_files/".$users_name);
}

#$dm_out = shell_exec ("python data_model_to_mdl.py ".$run_from_path."/data_model.json ".$run_from_path."/data_model.mdl");
#$output = $output."\n\n".$sep."\n".$dm_out.$sep."\n";
#$mcell_command = "cd ".$run_from_path."; ls -l; ".$mcell_path." -seed ".$seed." data_model.mdl";
#$output = $output."\n\n".$sep."\n    ".$mcell_command."\n".$sep."\n";
#$result = shell_exec ($mcell_command);
#$output = $output.$result."\n\n";
#$run_num += 1;

?>

</form>

<center id="mcellout"><table><tr><td><pre><?php echo $output; ?></pre></td></tr></table></center>

<center><?php phpinfo(INFO_VARIABLES); ?></center>
<center><?php phpinfo(); ?></center>


</body>
</html>
