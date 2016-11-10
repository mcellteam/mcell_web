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
  display: inline;
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
$run_limit = 1;
if (in_array("run_limit",array_keys($_POST))) {
  $run_limit = $_POST["run_limit"];
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

$pars = NULL;

if (strlen($model_file_name)>0) {
  $json_string = file_get_contents ( $model_file_name );
  $data_model = json_decode ( $json_string, true );
  // echo "\nJSON File: ".$json_file."<br/>";

  $pars = $data_model["mcell"]["parameter_system"]["model_parameters"];

  if (strcmp($what,"load") != 0) {
    // This is NOT a new load
    // Change the data model parameters to match those in the form
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
      $par_value = $par["par_expression"];

      $sweep_checked = false;
      $start_val = "";
      $end_val = "";
      $step_val = "";
      if (strcmp($what,"load") == 0) {
        // Get sweep information from Data Model
        if (array_key_exists("sweep_expression",$par)) {
          $sweep_expr = $par["sweep_expression"];
          $num_seps = substr_count($sweep_expr,":");
          if ($num_seps <= 0) {
            // This is a scalar value
            $start_val = $sweep_expr;
            $end_val =  $sweep_expr;
            $step_val = "1";
          } else {
            $parts = preg_split("[:]", $sweep_expr);
            if ($num_seps == 1) {
              $start_val = $parts[0];
              $end_val = $parts[1];
              $step_val = "1";
            } else { // if ($num_seps >= 2) {
              $start_val = $parts[0];
              $end_val = $parts[1];
              $step_val = $parts[2];
            }
          }
        }
        if (array_key_exists("sweep_enabled",$par)) {
          if ($par["sweep_enabled"]) {
            $sweep_checked = true;
            if (strlen($start_val) > 0) {
              $par_value = $start_val;
            }
          }
        }
      } else {
        // Get sweep information from HTML Form
        if (in_array("sweep_".$par_name,array_keys($_POST))) {
          // print ( "sweep_".$par_name." is ".$_POST["sweep_".$par_name]."<br/>" );
          $sweep_checked = true;
        }
        if (in_array($par_name."_end",array_keys($_POST))) {
          $end_val = $_POST[$par_name."_end"];
        }
        if (in_array($par_name."_step",array_keys($_POST))) {
          $step_val = $_POST[$par_name."_step"];
        }
      }

      $sweep_visibility = "hidden";
      if ($sweep_checked) {
        $sweep_visibility = "visible";
        print ( "  <td><center><input type=\"checkbox\" name=\"sweep_".$par_name."\" value=\"1\" checked=\"1\" onclick=\"sweep_checked('sweep_".$par_name."')\"></center></td>" );
      } else {
        print ( "  <td><center><input type=\"checkbox\" name=\"sweep_".$par_name."\" value=\"1\"               onclick=\"sweep_checked('sweep_".$par_name."')\"></center></td>" );
      }
      print ( "  <td>" );
      print ( "<b>".$par_name."</b> = " );

      print ( "<input type=\"text\" size=\"12\" name=\"".$par_name."\" value=\"".$par_value."\">" );

      print ( "<span id=\"range_".$par_name."\" class=\"".$sweep_visibility."\"> &nbsp; to &nbsp; <input type=\"text\" size=\"12\" name=\"".$par_name."_end\" value=\"".$end_val."\">" );
      print ( " &nbsp; by  &nbsp; <input type=\"text\" size=\"12\" name=\"".$par_name."_step\" value=\"".$step_val."\"></span>" );

      if (strlen($par["par_units"]) > 0) {
        print ( " &nbsp; (".$par["par_units"].")" );
      }
      print ( "</td>\n" );
      print ( "  <td>" );
      if (strlen($par["par_description"]) > 0) {
        print ( $par["par_description"] );
      }
      print ( "</td>\n" );
      print ( "</tr>\n" );
    }
    unset($par);
    print ( "</table>\n" );
  }
}

$total_mcell_runs = 1;

$sweep_pars = array();

if ($pars != NULL) {
  // Load parameters from the default form settings while building the $sweep_pars list (start, step, count)
  $npars = count($pars);
  for ($i=0; $i<$npars; $i++) {
    if (in_array($pars[$i]["par_name"],array_keys($_POST))) {
      $par_name = $pars[$i]["par_name"];
      $par_expr = $_POST[$par_name];
      $data_model["mcell"]["parameter_system"]["model_parameters"][$i]["par_expression"] = $par_expr;
      $sweep_flag_name = "sweep_".$par_name;
      if (in_array($sweep_flag_name,array_keys($_POST))) {
        // The checkbox field only appears to be in the $_POST when it is checked, otherwise there should be another value check here
        $sweep_start_name = $par_name;
        $sweep_end_name = $par_name."_end";
        $sweep_step_name = $par_name."_step";
        $sweep_start = 0.0;        sscanf($_POST[$par_name],        "%f", $sweep_start);
        $sweep_end = $sweep_start; sscanf($_POST[$sweep_end_name],  "%f", $sweep_end  );
        $sweep_step  = 1.0;        sscanf($_POST[$sweep_step_name], "%f", $sweep_step );
        if ($sweep_step > 0) {
          $num_steps = 1 + floor ( ($sweep_end - $sweep_start) / $sweep_step );
        } else {
          $num_steps = 1;
        }
        array_push ( $sweep_pars, array("sweep_name"=>$par_name,"sweep_start"=>$sweep_start, "sweep_step"=>$sweep_step, "num_steps"=>$num_steps, "step_num"=>0) );
        $total_mcell_runs = $total_mcell_runs * $num_steps;
      }
    }
  }
}

$total_mcell_runs = $total_mcell_runs * ( 1 + $end_seed - $start_seed );

$output = "";

$run_folders = array();

/*
  Old Format (pure dictionary does not preserve order):
    {
      "dr" : [ 10000, 30000, 50000 ],
      "delay" : [ 0.0001, 0.0051 ],
      "offset" : [ 1.0e-5, 0.00081, 0.00161 ]
    }

  New Format (list preserves order):
    [
      { "name": "dr", "values" : [ 10000, 30000, 50000 ] },
      { "name": "delay", "values" : [ 0.0001, 0.0051 ] },
      { "name": "offset", "values" : [ 1.0e-5, 0.00081, 0.00161 ] }
    ]
*/

if (strlen($what) > 0) {
  $sep = "=======================================================================================";
  if (strcmp($what,"clear") == 0) {
    $output = shell_exec ("rm -Rf run_files/".$users_name."/*; ls -lR");
    $output = "\n";
  } elseif (strcmp($what,"load") == 0) {
    $output = "\n";
  } elseif (strcmp($what,"run") == 0) {
    if (strlen($model_file_name) > 0) {
      $output = "";
      $result = "";
      // Re-read the data model
      $json_string = file_get_contents ( $model_file_name );
      $data_model = json_decode ( $json_string, true );
      // Get the parameters
      $pars = $data_model["mcell"]["parameter_system"]["model_parameters"];

      // Sweep through the parameter space
      
      // Start by writing the [ {"name:"p1", "values":[values] }, {"name:"p1", "values":[values] } ] JSON list
      $run_pars_file = fopen ( "run_files/".$users_name."/run_parameters.json", "w" );
      fwrite ( $run_pars_file, "[\n" );

      $comma = ",";
      $sweep_count = count($sweep_pars);
      for ($i=0; $i<$sweep_count; $i++) {
        $sw_name = $sweep_pars[$i]["sweep_name"];
        $sw_steps = $sweep_pars[$i]["num_steps"];
        $sw_start = $sweep_pars[$i]["sweep_start"];
        $sw_step = $sweep_pars[$i]["sweep_step"];
        $sw_end = $sw_start + (($sw_steps-1) * $sw_step);
        fwrite ( $run_pars_file, "  { \"name\": \"".$sw_name."\", \"values\" : [" );
        for ($index=0; $index<$sw_steps; $index++ ) {
          $comma = ",";
          if ($index == $sw_steps-1) {
            $comma = "";
          }
          fprintf ( $run_pars_file, " %.15g".$comma, $sw_start + ($index * $sw_step) );
        }
        $comma = ",";
        if ($i == $sweep_count-1) {
          $comma = "";
        }
        fwrite ( $run_pars_file, " ] }".$comma."\n" );
      }

      fwrite ( $run_pars_file, "]\n" );
      fclose ( $run_pars_file );

      $run_num = 0;
      while ($run_num < $total_mcell_runs) {

        // Load parameters from the default form settings

        $npars = count($pars);
        for ($i=0; $i<$npars; $i++) {
          if (in_array($pars[$i]["par_name"],array_keys($_POST))) {
            $par_name = $pars[$i]["par_name"];
            $par_expr = $_POST[$par_name];
            $data_model["mcell"]["parameter_system"]["model_parameters"][$i]["par_expression"] = $par_expr;
            $sweep_flag_name = "sweep_".$par_name;
          }
        }

        // Overwrite parameters in the data model for all parameters that are being swept with current values for this pass
        
        $run_from_path = "run_files/".$users_name."/output_data";
        $mcell_path = "../../../mcell";

        for ($i=0; $i<count($sweep_pars); $i++) {
          $sw_name = $sweep_pars[$i]["sweep_name"];
          $sw_start = $sweep_pars[$i]["sweep_start"];
          $sw_step = $sweep_pars[$i]["sweep_step"];
          $step_num = $sweep_pars[$i]["step_num"];
          $val_now = $sw_start + ($step_num*$sw_step);
          $str_val_now = sprintf("%g", $val_now);
          $run_from_path = $run_from_path."/".$sw_name."_index_".$step_num;
          $mcell_path = "../".$mcell_path;
          // print ( "<br/>Parameter <b>".$sw_name."</b> is <b>".$val_now." (\"".$str_val_now."\")</b> at step <b>".$step_num."</b>\n" );

          for ($j=0; $j<$npars; $j++) {
            if ($pars[$j]["par_name"] == $sw_name) {
              $data_model["mcell"]["parameter_system"]["model_parameters"][$j]["par_expression"] = $str_val_now;
              break;
            }
          }
        }

        array_push ( $run_folders, $run_from_path );

        // Create directories as needed
        shell_exec ("mkdir -p ".$run_from_path);

        // Encode the data model as JSON and write it to the file
        $json_output = json_encode ( $data_model );
        file_put_contents ( $run_from_path."/data_model.json", $json_output );
        for ($seed = $start_seed; $seed <= $end_seed; $seed++) {
          // print ( "<br/>Seed is <b>".$seed."</b>\n" );
          $dm_out = shell_exec ("python data_model_to_mdl.py ".$run_from_path."/data_model.json ".$run_from_path."/data_model.mdl");
          $output = $output."\n\n".$sep."\n".$dm_out.$sep."\n";
          $mcell_command = "cd ".$run_from_path."; ls -l; ".$mcell_path." -seed ".$seed." data_model.mdl";
          $output = $output."\n\n".$sep."\n    ".$mcell_command."\n".$sep."\n";
          $result = shell_exec ($mcell_command);
          $output = $output.$result."\n\n";
          $run_num += 1;
        }


        // Increment the step number(s) in the sweep parameters

        $num_sweep_pars = count($sweep_pars);
        for ($spi=0; $spi<$num_sweep_pars; $spi++) {
          $sweep_pars[$spi]["step_num"] += 1;
          if ($sweep_pars[$spi]["step_num"] < $sweep_pars[$spi]["num_steps"]) {
            // We have not rolled over, so we're done and can exit the for loop
            break;
          } else {
            // We have rolled over, so reset to 0 and allow the loop to continue
            $sweep_pars[$spi]["step_num"] = 0;
          }
        }

      }

    }
  }
}

$mcell_run_label = "Run MCell";
if ($total_mcell_runs > 1) {
  $mcell_run_label = sprintf("Run MCell x %d", $total_mcell_runs);
}

echo "<p style=\"padding-top:20\">";
echo " &nbsp; &nbsp; <b>Seed Range:</b> &nbsp; <input type=\"text\" size=\"4\" min=\"1\" name=\"start_seed\" value=".$start_seed.">\n";
echo " &nbsp; to &nbsp;                        <input type=\"text\" size=\"4\" min=\"1\" name=\"end_seed\" value=".$end_seed.">\n";

echo " &nbsp; &nbsp; <b>Run Limit:</b> &nbsp; <input type=\"text\" size=\"4\" min=\"1\" name=\"run_limit\" value=".$run_limit.">\n";

$button_style = "";
if (($total_mcell_runs > 1) && ($total_mcell_runs >= (3 * $run_limit / 4) )) {
  $button_style = "style=\"background-color: #ee6;\"";
}
if ($total_mcell_runs > $run_limit) {
  $button_style = "disabled style=\"background-color: #f88;\"";
}
echo " &nbsp; &nbsp;  &nbsp; &nbsp; <button ".$button_style." type=\"submit\" name=\"what\" value=\"run\">".$mcell_run_label."</button>\n";
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"clear\">Refresh</button>\n";
echo "</p>";

echo "</center>";

// Build the plot data to put in the JavaScript when returning the page

$seed_folders = array();
for ($i=0; $i<count($run_folders); $i++) {
  $seed_folders = array_merge ( $seed_folders, glob($run_folders[$i]."/react_data/*") );
}


$plot_data = array();
$plot_file_num = 0;

for ($seed_folder_index=0; $seed_folder_index<count($seed_folders); $seed_folder_index++) {

  $mol_files = glob($seed_folders[$seed_folder_index]."/*");

  for ($mol_file_index=0; $mol_file_index<count($mol_files); $mol_file_index++) {

    $plot_data[$plot_file_num] = array();
    $plot_data[$plot_file_num][0] = array();
    $plot_data[$plot_file_num][1] = array();

    $seed_file_lines = explode ( "\n", file_get_contents ( $mol_files[$mol_file_index] ) );
    $num_lines = count($seed_file_lines);

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

?>

</form>


<center>

<br/>

<h1>Results Plot</h1>

<!-- This fills the width, but is blurry: <canvas id="drawing_area" class="visible" width="800" height="600" style="width:95%; border:1px solid #d3d3d3;"> -->
<canvas id="drawing_area" class="visible" width="1200" height="600" style="border:1px solid #d3d3d3;">
Your browser does not support the HTML5 canvas tag.</canvas>
<p/>


<br/><button type="button" id="show_hide_control" onclick="toggle_mcell_output()"><b>Show MCell Text Output</b></button>

</center>

<center id="mcellout" class="hidden"><table><tr><td><pre><?php echo $output; ?></pre></td></tr></table></center>

<script>

function toggle_mcell_output() {
  // console.log ( "toggle mcell output!!" );
  mco = document.getElementById("mcellout");
  sh = document.getElementById("show_hide_control")
  if (mco.getAttribute('class') != "hidden") {
    mco.setAttribute('class', 'hidden');
    sh.innerHTML = "<b>Show MCell Text Output</b>";
  } else if (mco.getAttribute('class') == "hidden") {
    mco.setAttribute('class', 'visible');
    sh.innerHTML = "<b>Hide MCell Text Output</b>";
  }
}

// PHP will have filled in the $plot_data variable with the plot data (above).
// This next line generates an in-line JavaScript array containing all of that data.
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
