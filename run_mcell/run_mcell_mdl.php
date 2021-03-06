<html>

<head>
<title>MCell Web Development - Run MCell MDL</title>
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

<?php echo '<center><h1>MCell Web Development at <a href="../..">mcell.snl.salk.edu</a>'.$users_name_shown.'</h1></center>'; ?>

<hr/>

<form action="run_mcell_mdl.php" method="post">

<?php

echo "<center>";

$model_file_name = "";
if (in_array("model_file_name",array_keys($_POST))) {
  $model_file_name = $_POST["model_file_name"];
}
$start_seed = 1;
if (in_array("start_seed",array_keys($_POST))) {
  $start_seed = $_POST["start_seed"];
}
$end_seed = 1;
if (in_array("end_seed",array_keys($_POST))) {
  $end_seed = $_POST["end_seed"];
}
$what = "";
if (in_array("what",array_keys($_POST))) {
  $what = $_POST["what"];
}
$show_text = 0;
if (in_array("show_text",array_keys($_POST))) {
  $show_text = $_POST["show_text"];
}

if ($start_seed < 1) {
  $start_seed = 1;
}
if ($end_seed > 20) {
  $end_seed = 20;
}
if ($end_seed < $start_seed) {
  $end_seed = $start_seed;
}

$model_files = glob("mdl_files/*.mdl");

echo "<b>MDL Name:</b> &nbsp; <select name=\"model_file_name\">\n";
echo "  <option value=\"\"></option>>";
for ($model_file_index=0; $model_file_index<count($model_files); $model_file_index++) {
  $sel = "";
  if ($model_files[$model_file_index] == $model_file_name) {
    $sel = " selected ";
  }
  echo "  <option ".$sel."value=".$model_files[$model_file_index].">".$model_files[$model_file_index]."</option>\n";
}
echo "</select>\n";

echo " &nbsp; &nbsp; <b>Seeds:</b> &nbsp; <input type=\"text\" size=\"4\" min=\"1\" max=\"2000\" name=\"start_seed\" value=".$start_seed.">\n";
echo " &nbsp; to &nbsp;                   <input type=\"text\" size=\"4\" min=\"1\" max=\"2000\" name=\"end_seed\" value=".$end_seed.">\n";
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"run\">Run MCell</button>\n";
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"clear\">Clear</button>\n";

$output = "";
if (strlen($what) > 0) {
  $sep = "=======================================================================================";
  if (strcmp($what,"clear") == 0) {
    $output = shell_exec ("rm -Rf run_files/".$users_name."/*; ls -lR");
    $output = "\n";
  } elseif (strcmp($what,"run") == 0) {
    if (strlen($model_file_name) > 0) {
      //$result = popen("/bin/ls", "r");
      $output = $output.shell_exec ("mkdir -p run_files/".$users_name.";");
      $output = "";
      $result = "";
      for ($seed = $start_seed; $seed <= $end_seed; $seed++) {
        $mcell_command = "cd run_files/".$users_name."; ../../mcell -seed ".$seed." ../../".$model_file_name;
        $output = $output."\n\n".$sep."\n    ".$mcell_command."\n".$sep."\n";
        $result = shell_exec ($mcell_command);
        $output = $output.$result."\n\n";
      }
      // $result = shell_exec ("ls -lR;");
      // $output = $output."\n\n".$sep."\n  Directory Listing After All Runs \n".$sep."\n\n".$result;
    }
  }
}
echo "</center>";


$plot_data = array();
$plot_file_num = 0;

$seed_folders = glob("run_files/".$users_name."/react_data/*");
for ($seed_folder_index=0; $seed_folder_index<count($seed_folders); $seed_folder_index++) {
  // echo "Folder = \"".$seed_folders[$seed_folder_index]."\"<br/>";

  $mol_files = glob($seed_folders[$seed_folder_index]."/*");

  for ($mol_file_index=0; $mol_file_index<count($mol_files); $mol_file_index++) {

    $plot_data[$plot_file_num] = array();
    $plot_data[$plot_file_num][0] = array();
    $plot_data[$plot_file_num][1] = array();

    $seed_file_lines = explode ( "\n", file_get_contents ( $mol_files[$mol_file_index] ) );
    $num_lines = count($seed_file_lines);

    // echo " &nbsp;  &nbsp;  &nbsp; File \"".$mol_files[$mol_file_index]."\" contains ".$num_lines." lines.<br/>";

    $plot_line_num = 0;
    for ($seed_line_index=0; $seed_line_index<$num_lines; $seed_line_index++) {

      $seed_line_parts = explode ( " ", trim($seed_file_lines[$seed_line_index]) );

      if (count($seed_line_parts) == 2) {
        $x = floatval(trim($seed_line_parts[0]));
        $y = floatval(trim($seed_line_parts[1]));
        $plot_data[$plot_file_num][0][$plot_line_num] = $x;
        $plot_data[$plot_file_num][1][$plot_line_num] = $y;
        $plot_line_num = $plot_line_num + 1;
      }

    }

    $plot_file_num = $plot_file_num + 1;

  }
}

// phpinfo();

?>

</form>


<center>

<br/>

<h1>Count Output Plot</h1>

<canvas id="drawing_area" width="800" height="600" style="border:1px solid #d3d3d3;">
Your browser does not support the HTML5 canvas tag.</canvas>
<p/>

<br/><button id="show_hide_control" onclick="toggle_mcell_output()"><b>Show MCell Text Output</b></button>

<center id="mcellout" class="hidden"><table><tr><td><pre><?php echo $output; ?></pre></td></tr></table></center>

</center>

<script>

function toggle_mcell_output() {
  // console.log ( "toggle mcell output!!" );
  mco = document.getElementById("mcellout");
  sh = document.getElementById("show_hide_control")
  if (mco.getAttribute('class') == "visible") {
    mco.setAttribute('class', 'hidden');
    sh.innerHTML = "<b>Show MCell Text Output</b>";
  } else if (mco.getAttribute('class') == "hidden") {
    mco.setAttribute('class', 'visible');
    sh.innerHTML = "<b>Hide MCell Text Output</b>";
  }
}

// PHP will have filled in the $plot_data variable with the plot data (above).
// This next line generates an in-line JavaScript statement containing all of that data.
// The resulting 3D array will be indexed by Plot#, x/y, step

var plot_data = <?php echo json_encode($plot_data); ?>;

// This function just draws a plot of plot_data

function draw_data() {

  var xmin=plot_data[0][0][0];
  var xmax=plot_data[0][0][0];
  var ymin=plot_data[0][1][0];
  var ymax=plot_data[0][1][0];

  for (var pd=0; pd<plot_data.length; pd++) {
    for (var i=0; i<plot_data[pd][0].length; i++) {
      x = plot_data[pd][0][i];
      y = plot_data[pd][1][i];
      if (x < xmin) xmin = x;
      if (x > xmax) xmax = x;
      if (y < ymin) ymin = y;
      if (y > ymax) ymax = y;
    }
  }
  if (ymin == ymax) {
    ymax +=  1;
    ymin += -1;
  }

  c = document.getElementById ( "drawing_area" );
  w = c.width;
  h = c.height;
  
  var ctx = c.getContext("2d");
  ctx.fillStyle = "#000000";
  ctx.fillRect(0,0,w,h);

  for (var pd=0; pd<plot_data.length; pd++) {
    console.log ( "New Plot" );
    var colors = [ "#ff0000", "#00ff00", "#0000ff", "#ffff00", "#ff00ff", "#00ffff" ];
    ctx.strokeStyle = colors[pd%colors.length];
    ctx.beginPath();
    for (var i=0; i<plot_data[pd][0].length; i++) {
      x = plot_data[pd][0][i];
      y = plot_data[pd][1][i];
      // console.log ( "  point " + x + "," + y );
      x = w * (x-xmin) / (xmax-xmin);
      y = h * (y-ymin) / (ymax-ymin);
      y = h - y;
      x = (0.05*w) + (0.9*x);
      y = (0.05*h) + (0.9*y);
      if (i==0) {
        ctx.moveTo(x,y);
      } else {
        ctx.lineTo(x,y);
      }
    }
    ctx.stroke();
  }

}

draw_data();

</script>

</body>
</html>
