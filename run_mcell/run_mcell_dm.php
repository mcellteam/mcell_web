<html>

<head>
<title>MCell Web Development - Run MCell Data Model</title>
<link rel="stylesheet" type="text/css" href="../style/def.css" />

<style type="text/css">

/* From phpinfo style */

body {background-color: #fff; color: #222; font-family: sans-serif;}
pre {margin: 0; font-family: monospace;}
a:link {color: #009; text-decoration: none;}
a:hover {text-decoration: underline;}
table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 2px 4px 6px #008;}
.center {text-align: center;}
.center table {margin: 1em auto; text-align: left;}
.center th {text-align: center !important;}
th {border: 1px solid #666; font-size: 90%; vertical-align: baseline; padding: 4px 5px; background-color: #ccc;}
td {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px; background-color: #eee;}
h1 {font-size: 150%;}
h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccf; width: 300px; font-weight: bold;}
.h {background-color: #99c; font-weight: bold;}
.v {background-color: #ddd; max-width: 300px; overflow-x: auto;}
.v i {color: #999;}
img {float: right; border: 0;}
hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
input[type=text] { font-weight: bold; padding: 4px 8px 2px 4px; } /* padding is top, right, bottom, left */
button[type=submit], input[type=button], input[type=submit], input[type=reset] { font-weight: bold; font-size: 110%; padding: 4px 8px 2px 6px; box-shadow: 1px 2px 3px #008; }
select { font-weight: bold; font-size: 110%; padding: 4px 8px 2px 6px; box-shadow: 1px 2px 3px #008; }

/* Additional styles */
/* Body for the entire document */
body {
  background-color: #def;
  color: #000033;
  margin-left: 10px;
  margin-right: 10px;
  margin-top: 10px;
  margin-bottom: 10px;
}

table, th, td {
  border: 1px solid black;
  margin-left: 10px;
  margin-right: 10px;
  margin-top: 10px;
  margin-bottom: 10px;
  /* border-collapse: collapse; */
}

.hidden {
  display: none;
}
.visible {
  display: block;
}

</style>


<script>

function sweep_checked ( s ) {
  // console.log ( s );
  // alert( s );
  sweep_checkboxes = document.getElementsByName(s);
  if (sweep_checkboxes.length == 1) {
    sweep_item_name = s.substr("sweep_".length);
    range_id = "range_" + sweep_item_name;
    range_span = document.getElementById(range_id);
    if ( sweep_checkboxes[0].checked ) {
      // Show the range fields
      range_span.className = "visible";
      // console.log( s + " is checked" );
    } else {
      // Hide the range fields
      range_span.className = "hidden";
      // console.log( s + " is NOT checked" );
    }
  }
}

</script>

</head>





<body>

<hr/>

<center><h1 style="font-size:200%">MCell Web Development at <a href="../..">mcell.snl.salk.edu</a></h1></center>

<hr/>



<form action="run_mcell_dm.php" method="post">

<?php

echo "<center>";


// Copy the values from the previous form to use in drawing this form

$model_file_name = "";
if (in_array("model_file_name",array_keys($_POST))) {
  $model_file_name = $_POST["model_file_name"];
}
$model_file = "";
if (in_array("model_file",array_keys($_POST))) {
  $model_file = $_POST["model_file"];
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
if ($start_seed > 20) {
  print ( "<br/>Warning: starting seed is limited to 20<br/>" );
  $start_seed = 20;
}
if ($end_seed > 20) {
  print ( "<br/>Warning: ending seed is limited to 20<br/>" );
  $end_seed = 20;
}
if ($end_seed < $start_seed) {
  $end_seed = $start_seed;
}



$json_string = "";
$model_files = glob("data_model_files/*.json");

echo "<p>";
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
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"load\">Load Model</button>\n";
echo "</p>";

if (strlen($model_file_name)>0) {
  $json_string = file_get_contents ( $model_file_name );
  $data_model = json_decode ( $json_string, true );
  // echo "\nJSON File: ".$json_file."<br/>";

  $pars = $data_model["mcell"]["parameter_system"]["model_parameters"];

  if (strcmp($what,"load") != 0) {

    // Change the data model parameters to match any already in the form
    $npars = count($pars);
    for ($i=0; $i<$npars; $i++) {
      if (in_array($pars[$i]["par_name"],array_keys($_POST))) {
        $data_model["mcell"]["parameter_system"]["model_parameters"][$i]["par_expression"] = $_POST[$pars[$i]["par_name"]];
      }
    }

    // This reassignment appears to be needed!!
    $pars = $data_model["mcell"]["parameter_system"]["model_parameters"];

  }

  if (count($pars) > 0) {
    // var_dump ( $pars );
    print ( "<table style=\"width:95%\">\n" );
    print ( "<tr><th style=\"width:5%\">Sweep</th><th style=\"width:70%\">Name &nbsp; = &nbsp; Value &nbsp; (units)</th><th>Description</th></tr>\n" );
    foreach ($pars as &$par) {
      print ( "<tr>\n" );
      $par_name = $par["par_name"];
      $sweep_checked = false;
      if (in_array("sweep_".$par_name,array_keys($_POST))) {
        // print ( "sweep_".$par_name." is ".$_POST["sweep_".$par_name]."<br/>" );
        $sweep_checked = true;
      }
      $hidden_status = "hidden";
      if ($sweep_checked) {
        $hidden_status = "visible";
        print ( "  <td><center><input type=\"checkbox\" name=\"sweep_".$par_name."\" value=\"1\" checked=\"1\" onclick=\"sweep_checked('sweep_".$par_name."')\"></center></td>" );
      } else {
        print ( "  <td><center><input type=\"checkbox\" name=\"sweep_".$par_name."\" value=\"1\" onclick=\"sweep_checked('sweep_".$par_name."')\"></center></td>" );
      }
      //print ( "  <td><center><input type=\"checkbox\" name=\"sweep_".$par_name."\" value=\"1\" checked=\"1\"></center></td>" );
      print ( "  <td>" );
      print ( "<b>".$par_name."</b> = " ); // .$par["par_expression"] );

      print ( "<input type=\"text\" size=\"12\" name=\"".$par_name."\" value=\"".$par["par_expression"]."\">" );

      $end_val = "";
      if (in_array($par_name."_end",array_keys($_POST))) {
        $end_val = $_POST[$par_name."_end"];
      }
      $step_val = "";
      if (in_array($par_name."_step",array_keys($_POST))) {
        $step_val = $_POST[$par_name."_step"];
      }

      print ( "<span id=\"range_".$par_name."\" class=\"".$hidden_status."\"> &nbsp; &nbsp; to <input type=\"text\" size=\"12\" name=\"".$par_name."_end\" value=\"".$end_val."\">" );
      print ( " by  <input type=\"text\" size=\"12\" name=\"".$par_name."_step\" value=\"".$step_val."\"></span>" );

      if (strlen($par["par_units"]) > 0) {
        print ( " (".$par["par_units"].")" );
      }
      print ( "</td>\n" );
      print ( "  <td>" );
      if (strlen($par["par_description"]) > 0) {
        print ( $par["par_description"] );
      }
      print ( "</td>\n" );
      print ( "</tr>\n" );
      // var_dump ( $par );
    }
    unset($par);
    print ( "</table>\n" );
  }
}

$output = "";
if (strlen($what) > 0) {
  $sep = "=======================================================================================";
  if (strcmp($what,"clear") == 0) {
    // $output = "\n\n".$sep."\n  Directory Listing After Clear \n".$sep."\n\n".shell_exec ("rm -Rf viz_data; rm -Rf react_data; ls -lR");
    shell_exec ("rm -Rf viz_data; rm -Rf react_data; rm -f mdl_files/data_model.mdl; ls -lR");
    // shell_exec ( "rm -f \"viz_data/seed_00001/.nfs0000000001322d0200000001\"" );
    $output = "\n";
  } elseif (strcmp($what,"load") == 0) {
    $output = "\n";
  } elseif (strcmp($what,"run") == 0) {
    if (strlen($model_file_name) > 0) {
      //$result = popen("/bin/ls", "r");
      $output = "";
      $result = "";
      // Re-read the data model
      $json_string = file_get_contents ( $model_file_name );
      $data_model = json_decode ( $json_string, true );
      // Get the parameters
      $pars = $data_model["mcell"]["parameter_system"]["model_parameters"];

      // Sweep through the parameter space

      $sweep_pars = array();

      // Load parameters from the default form settings while building the $sweep_pars list
      $npars = count($pars);
      for ($i=0; $i<$npars; $i++) {
        if (in_array($pars[$i]["par_name"],array_keys($_POST))) {
          $par_name = $pars[$i]["par_name"];
          $par_expr = $_POST[$par_name];
          $data_model["mcell"]["parameter_system"]["model_parameters"][$i]["par_expression"] = $par_expr;
          $sweep_flag_name = "sweep_".$par_name;
          if (in_array($sweep_flag_name,array_keys($_POST))) {
            print ( "<br/>Sweep parameter ".$par_name." based on ".$sweep_flag_name );
          }
        }
      }

      // Overwrite parameters in the data model that are being swept with current values for this pass

      // Encode the data model as JSON and write it to the file
      $json_output = json_encode ( $data_model );
      file_put_contents ( "mdl_files/data_model.json", $json_output );
      for ($seed = $start_seed; $seed <= $end_seed; $seed++) {
        $dm_out = shell_exec ("python data_model_to_mdl.py mdl_files/data_model.json mdl_files/data_model.mdl");
        $output = $output."\n\n".$sep."\n".$dm_out.$sep."\n";
        $mcell_command = "./mcell -seed ".$seed." mdl_files/data_model.mdl";
        $output = $output."\n\n".$sep."\n    ".$mcell_command."\n".$sep."\n";
        $result = shell_exec ($mcell_command);
        $output = $output.$result."\n\n";
      }
      // $result = shell_exec ("ls -lR;");
      // $output = $output."\n\n".$sep."\n  Directory Listing After All Runs \n".$sep."\n\n".$result;
    }
  }
}

$mcell_run_label = "Run MCell";
$total_mcell_runs = 1 + $end_seed - $start_seed;
if ($total_mcell_runs > 1) {
  $mcell_run_label = sprintf("Run MCell x %d", $total_mcell_runs);
}

echo "<p style=\"padding-top:20\">";
echo " &nbsp; &nbsp; <b>Seed Range:</b> &nbsp; <input type=\"text\" size=\"4\" min=\"1\" max=\"2000\" name=\"start_seed\" value=".$start_seed.">\n";
echo " &nbsp; to &nbsp;                        <input type=\"text\" size=\"4\" min=\"1\" max=\"2000\" name=\"end_seed\" value=".$end_seed.">\n";
echo " &nbsp; &nbsp;  &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"run\">".$mcell_run_label."</button>\n";
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"clear\">Clear</button>\n";
echo "</p>";

echo "</center>";


$plot_data = array();
$plot_file_num = 0;

$seed_folders = glob("react_data/*");
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

<h1>Results Plot</h1>

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
    // console.log ( "New Plot" );
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
