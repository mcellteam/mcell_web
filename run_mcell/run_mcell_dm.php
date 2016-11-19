<html>

<!--

This HTML/PHP file presents a web interface for running MCell
from a CellBlender Data Model file. It reads the Data Model and
presents the model_parameters list within an HTML form which can
be modified. The "Run MCell" button then reads the potentially
modified parameters from the HTML form and updates the data model
with these values. The modified data model is then written to a
new file and run using "python run_data_model_mcell.py". The
resulting reaction data output files are then read by PHP code
and converted into a JSON representation which is available to a
JavaScript plotting program "draw_data" which then plots the data
as the last step in rendering the page.

-->


<head>
<title>MCell Web Development - Run MCell Data Model</title>
<link rel="stylesheet" type="text/css" href="run_style.css">


<style type="text/css">

.hidden {
  display: none;
}
.visible {
  display: inline;
}
</style>


<script>

function sweep_checked ( s ) {
  // This function is called when one of the "Sweep" check boxes is toggled.
  // This function will then change the visibility status of the scalar and
  //   sweep fields to show the proper version and hide the other.

  sweep_checkboxes = document.getElementsByName(s);
  if (sweep_checkboxes.length == 1) {
    sweep_item_name = s.substr("sweep_".length);
    sweep_id = sweep_item_name + "_sweep";
    scalar_id = sweep_item_name + "_scalar";

    sweep_span = document.getElementById(sweep_id);
    scalar_span = document.getElementById(scalar_id);
    if ( sweep_checkboxes[0].checked ) {
      // Show the sweep field and hide the scalar field
      sweep_span.className = "visible";
      scalar_span.className = "hidden";
    } else {
      // Hide the sweep field and show the scalar field
      sweep_span.className = "hidden";
      scalar_span.className = "visible";
    }
  }
}

</script>

</head>


<body>

<?php
// Get the user's name and generate a display string ($users_name_shown)
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


<!--

This is the main form for the application. It is mostly populated with
PHP code that reads the data model, pulls out the parameters, and displays
them as fields in a table.

The data model is read every time to avoid storing it in the web page. When
the model is run (or the page is otherwise refreshed), the model is read based
on the current file name, and the values from the current form are merged into
the model. This does create the possibility of a conflict if the user should
change the data model name but not actually reload it. This might be fixed with
a hidden HTML field to store the name of the last data model that has been read.

-->

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


// Show the Data Model Selection regardless of the current command

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

$dm_pars = null;


// Collect the data from either the data model or from the data model with form data changes

if (strcmp($what,"load") == 0) {
  // Load the new data model from the specified file
  // echo "<p><h1>LOAD from FILE</h1></p>\n";

  if (strlen($model_file_name)>0) {

    $json_string = file_get_contents ( $model_file_name );
    $data_model = json_decode ( $json_string, true );

    $dm_pars = $data_model["mcell"]["parameter_system"]["model_parameters"];
  }

} else {
  // Load the data model and then update the $dm_pars array from the previous form data
  // echo "<p><h1>LOAD from POST</h1></p>\n";

  if (strlen($model_file_name)>0) {

    $json_string = file_get_contents ( $model_file_name );
    $data_model = json_decode ( $json_string, true );

    $dm_pars = $data_model["mcell"]["parameter_system"]["model_parameters"];

    // Update the parameters according to the fields found in the previous form data
    foreach ($dm_pars as &$par) {
      $par_name = $par["par_name"];
      if (in_array("sweep_".$par_name,array_keys($_POST))) {
        // If the name is found, that means it is true
        $par["sweep_enabled"] = true;
      } else {
        $par["sweep_enabled"] = false;
      }
      if (in_array($par_name."_scalar",array_keys($_POST))) {
        $par["par_expression"] = $_POST[$par_name."_scalar"];
      }
      if (in_array($par_name."_sweep",array_keys($_POST))) {
        $par["sweep_expression"] = $_POST[$par_name."_sweep"];
      }
    }

  }
}

if ($dm_pars != null) {

  // Display the parameters in a table with a sweep control check box for each one
  // Each row should have:  sweep_parName   parName_sweep | parName_scalar   parName_units   parName_descr
  // The choice to display parName_sweep or parName_scalar is based on the sweep_parName check box.
  // Note that both fields are always in the form, but one is visible while the other is hidden.

  $npars = count($dm_pars);
  if ($npars > 0) {

    print ( "<table style=\"width:95%\">\n" );
    print ( "<tr><th style=\"width:5%\">Sweep</th><th style=\"width:70%\">Name &nbsp; = &nbsp; Value &nbsp; (units)</th><th>Description</th></tr>\n" );
    foreach ($dm_pars as &$par) {
      print ( "<tr>\n" );
      $par_name = $par["par_name"];
      $par_value = $par["par_expression"];
      $sweep_expr = "";
      if (array_key_exists("sweep_expression",$par)) {
        $sweep_expr = $par["sweep_expression"];
      }
      if (strlen($sweep_expr) == 0) {
        // Initialize it with the default for convenience
        $sweep_expr = $par_value;
      }
      $sweep_checked = false;
      $sweep_visibility = "hidden";
      $scalar_visibility = "visible";
      if (array_key_exists("sweep_enabled",$par)) {
        if ($par["sweep_enabled"]) {
          $sweep_checked = true;
          $sweep_visibility = "visible";
          $scalar_visibility = "hidden";
        }
      }
      if ($sweep_checked) {
        print ( "  <td><center><input type=\"checkbox\" name=\"sweep_".$par_name."\" value=\"1\" checked=\"1\" onclick=\"sweep_checked('sweep_".$par_name."')\"></center></td>" );
      } else {
        print ( "  <td><center><input type=\"checkbox\" name=\"sweep_".$par_name."\" value=\"1\"               onclick=\"sweep_checked('sweep_".$par_name."')\"></center></td>" );
      }
      print ( "  <td><b>".$par_name."</b> = " );

      print ( "<span id=\"".$par_name."_sweep\" class=\"".$sweep_visibility."\"><input type=\"text\" size=\"40\" name=\"".$par_name."_sweep\" value=\"".$sweep_expr."\"></span>" );
      print ( "<span id=\"".$par_name."_scalar\" class=\"".$scalar_visibility."\"><input type=\"text\" size=\"20\" name=\"".$par_name."_scalar\" value=\"".$par_value."\"></span>" );

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

//$total_mcell_runs = shell_exec ("python run_data_model_mcell.py ".$model_file_name." --get_num_runs");

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
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"clear\">Clear</button>\n";
echo "</p>";

$output = "";

$run_folders = array();

if (strlen($what) > 0) {

  $sep = "=======================================================================================";

  if (strcmp($what,"clear") == 0) {

    $output = shell_exec ("rm -Rf run_files/".$users_name."/*; ls -lR");
    $output = "\n";

  } elseif (strcmp($what,"load") == 0) {

    $output = "\n";

  } elseif (strcmp($what,"run") == 0) {

    // Run the data model as it is without any changes from the form (ignore parameter settings and seed settings) 

    // Delete everything first
    shell_exec ("rm -Rf run_files/".$users_name."/*; ls -lR");

    $output = "CWD = ".getcwd()."\n";
    if (strlen($model_file_name) > 0) {

      $run_from_path = getcwd()."/run_files/".$users_name;

      // Read the data model
      $json_string = file_get_contents ( $model_file_name );
      $data_model = json_decode ( $json_string, true );

      // Substitute the updated parameters
      $dm_pars = $data_model["mcell"]["parameter_system"]["model_parameters"] = $dm_pars;

      // Set the seed range in the data model
      $data_model["mcell"]["simulation_control"]["start_seed"] = $start_seed;
      $data_model["mcell"]["simulation_control"]["end_seed"]= $end_seed;

      // Encode the data model as JSON and write it to the file
      $json_output = json_encode ( $data_model );
      file_put_contents ( $run_from_path."/data_model.json", $json_output );

      $result = shell_exec ("python run_data_model_mcell.py ".$run_from_path."/data_model.json -pd ".$run_from_path." -b ".getcwd()."/mcell -fs ".$start_seed." -ls ".$end_seed." -rl ".$run_limit);
      $output = $output.$result."\n\n";

      $output = $output."<hr/> Plot data layout:<br/>";

      $json_string = file_get_contents ( $run_from_path."/data_layout.json", "r" );
      $data_layout_dict = json_decode ( $json_string, true );
      $data_layout_list = $data_layout_dict["data_layout"];
      $layout_list_count = count($data_layout_list);

      $index_counting_array = array();
      $num_runs = 1;
      for ($i=0; $i<$layout_list_count; $i++) {
        array_push ( $index_counting_array, 0 );
        $layout_level_name = $data_layout_list[$i][0];
        $layout_level_item_list = $data_layout_list[$i][1];
        $output = $output."<br/>".$layout_level_name."<br/>";
        if ( ($layout_level_name != "dir") && ($layout_level_name != "file_type") && ($layout_level_name != "SEED") ) {
          $num_runs = $num_runs * count($layout_level_item_list);
          $output = $output."<br/>".$layout_level_name." is a level!!<br/>";
        }
      }
      $output = $output."<br/>Total Runs = ".$num_runs."<br/>";
      
      $run_num = 0;

      while ($run_num < $num_runs) {
        $run_path = $run_from_path."";
        for ($i=0; $i<$layout_list_count; $i++) {
          $layout_level_name = $data_layout_list[$i][0];
          if ($layout_level_name == "dir") {
            // A "dir" level is just a directory path that needs to be added.
            $run_path = $run_path."/".$data_layout_list[$i][1][0];
          } elseif ($layout_level_name == "file_type") {
            // Don't do anything with the file type since it's handled explicitly later on
            // $run_path = $run_path."/".$data_layout_list[$i][1][0];
          } elseif ($layout_level_name == "SEED") {
            // Don't do anything with seeds since they're handled by the "glob" later on
          } else {
            $run_path = $run_path."/".$data_layout_list[$i][0]."_index_".$index_counting_array[$i];
          }
        }
        $output = $output."<br/>Run Path ".$run_num." is ".$run_path."<br/>";
        array_push ( $run_folders, $run_path );

        // Increment the counters and carry as needed
        $index_counting_array[$layout_list_count-1] += 1;
        for ($i=$layout_list_count-1; $i>=0; $i=$i-1) {
          if ( ($data_layout_list[$i][0]=="file_type") || ($index_counting_array[$i] >= count($data_layout_list[$i][1])) ) {
            $index_counting_array[$i] = 0;
            if (($i-1) >= 0) {
              $index_counting_array[$i-1] += 1;
            }
          }
        }
        $run_num = $run_num + 1;
      }

    } else {
      $output = "No Data Model";
    }

  }
}

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

<!--
<center><?php phpinfo(INFO_VARIABLES); ?></center>

<center><?php phpinfo(); ?></center>
-->

<br/>

<?php

if (count($plot_data) > 0) {

  echo "<h1>Results Plot</h1>";

  # <!-- This fills the width, but is blurry: <canvas id=\"drawing_area\" class=\"visible\" width=\"800\" height=\"600\" style=\"width:95%; border:1px solid #d3d3d3;\"> -->
  echo "<canvas id=\"drawing_area\" class=\"visible\" width=\"1200\" height=\"600\" style=\"border:1px solid #d3d3d3;\">";
  echo "Your browser does not support the HTML5 canvas tag.";
  echo "</canvas>";

} elseif (strcmp($what,"run") == 0) {

  echo "<h1>No Plot Data</h1>";
  echo "( <b>Check Run Limit</b> &nbsp; -or- &nbsp; <b>Show MCell Text Output</b> &nbsp; for errors )";

}
?>

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


// This function calculates an appropriate grid spacing in application units

function delta_per_division_integer ( value_range, pixel_range, nominal_pixels_per_division ) {
  // We generally want our divisions to be 1's, 2's, or 5's
  //  (for example, every .01, or every .02, or every .05, or every .1 ...)

  var nominal_divisions_per_window = (pixel_range + (nominal_pixels_per_division/2)) / nominal_pixels_per_division;
  var nominal_delta_per_division = value_range / nominal_divisions_per_window;

  // First normalize the value between 1 and 10
  var factors_of_10 = 0;
  var tpd = nominal_delta_per_division;
  while (tpd > 1) {
    tpd = tpd / 10;
    factors_of_10 ++;
  }
  while (tpd < 1) {
    tpd = tpd * 10;
    factors_of_10 --;
  }
  // Now we have tpd as a floating value >= 1 and less than 10 so make some decisions
  var tpdi = 0;
  if (tpd < 1.5) {
    tpdi = 1;
  } else if (tpd < 3) {
    tpdi = 2;
  } else {
    tpdi = 5;
  }
  // Now restore the magnitude
  while (factors_of_10 > 0) {
    tpdi = tpdi * 10;
    factors_of_10 --;
  }
  while (factors_of_10 < 0) {
    tpdi = tpdi / 10;
    factors_of_10 ++;
  }
  return ( tpdi );
}

// This function tries to produce reasonably compact strings from numbers
function get_compact_string ( x ) {
  // Start with toPrecision
  xp = x.toPrecision(3);
  if (xp.indexOf('.') >= 0) {
    // It has a decimal point, so might be able to remove trailing zeros
    if (xp.indexOf('e') < 0) {
      // It doesn't have an "e", so trailing zeros can be removed
      while (xp.endsWith('0')) {
        xp = xp.slice(0,-1);
      }
    }
    if (xp.endsWith('.')) {
      xp = xp.slice(0,-1);
    }
  }
  // Check if exponential is smaller
  xe = x.toExponential(2);
  if (xe.length < xp.length) {
    xp = xe;
  }
  return ( xp );
}

// This function just draws a plot of plot_data

function draw_data() {

  // This is some test data ... uncomment the following line and it will be used.
  // var plot_data = [ [ [0.28, 0.4, 0.5, 0.7], [-0.3,11,10,5] ], [ [0.28, 0.4, 0.5, 0.7], [0,10,15,0] ] ];

  var font_spec =  "18px Arial"; // Arial or Times
  var font_height = 18;

  // alert ( "draw_data() plot_data.length = " + plot_data.length );
  if (plot_data.length > 0) {
    // plot_data [curve_index][x/y][point#]

    var xmin=plot_data[0][0][0];
    var xmax=plot_data[0][0][0];
    var ymin=plot_data[0][1][0];
    var ymax=plot_data[0][1][0];
    var x, y;

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
    var w = c.width;
    var h = c.height;

    var ctx = c.getContext("2d");
    ctx.fillStyle = "#000000";
    ctx.fillRect(0,0,w,h);

    // Calculate the margins
    var left_margin = 0.07;
    var right_margin = 0.05;
    var top_margin = 0.05;
    var bottom_margin = 0.07;

    var xL = left_margin*w;           // Left side
    var yB = (1.0 - bottom_margin)*h; // Bottom
    var xR = (1-right_margin)*w;      // Right side
    var yT = top_margin*h;            // Top

    // Draw the grid lines and labels

    var x_delta = delta_per_division_integer ( xmax-xmin, xR-xL, 120 );
    var y_delta = delta_per_division_integer ( ymax-ymin, yB-yT, 120 );

    var x_start = Math.floor(xmin/x_delta) * x_delta;
    while (x_start > xmin) {
      x_start = x_start - x_delta;
    }
    while (x_start < xmin) {
      x_start = x_start + x_delta;
    }

    for (var x_line=x_start; x_line<xmax; x_line+=x_delta) {
      x = w * (x_line-xmin) / (xmax-xmin);
      x = (left_margin*w) + ((1-(right_margin+left_margin))*x);
      ctx.strokeStyle = "#444444";
      ctx.beginPath();
      ctx.moveTo(x,yB);
      ctx.lineTo(x,yT);
      ctx.stroke();
      ctx.font=font_spec; // Arial or Times
      ctx.fillStyle="#888888";
      var label = get_compact_string(x_line);
      ctx.fillText(label, x-(ctx.measureText(label).width/2), yB+font_height);
    }

    var y_start = Math.floor(ymin/y_delta) * y_delta;
    while (y_start > ymin) {
      y_start = y_start - y_delta;
    }
    while (y_start < ymin) {
      y_start = y_start + y_delta;
    }

    for (var y_line=y_start; y_line<ymax; y_line+=y_delta) {
      y = h * (y_line-ymin) / (ymax-ymin);
      y = h - y;
      y = (top_margin*h) + ((1-(top_margin+bottom_margin))*y);
      ctx.strokeStyle = "#444444";
      ctx.beginPath();
      ctx.moveTo(xL,y);
      ctx.lineTo(xR,y);
      ctx.stroke();
      ctx.font=font_spec; // Arial or Times
      ctx.fillStyle="#888888";
      var label = get_compact_string(y_line);
      ctx.fillText(label, xL-(ctx.measureText(label).width+7), y+(font_height/3));
    }

    // Draw the surrounding box

    ctx.strokeStyle = "#FFFFFF";
    ctx.beginPath();
    ctx.moveTo(xL,yB);
    ctx.lineTo(xR,yB);
    ctx.lineTo(xR,yT);
    ctx.lineTo(xL,yT);
    ctx.lineTo(xL,yB);
    ctx.stroke();

    // Plot the actual data (easiest part!!)

    for (var pd=0; pd<plot_data.length; pd++) {
      // console.log ( "New Plot" );
      var colors = [ "#ff0000", "#00ff00", "#0000ff", "#ffff00", "#ff00ff", "#00ffff" ];
      ctx.strokeStyle = colors[pd%colors.length];
      ctx.beginPath();
      for (var i=0; i<plot_data[pd][0].length; i++) {
        x = plot_data[pd][0][i];
        y = plot_data[pd][1][i];
        x = w * (x-xmin) / (xmax-xmin);
        y = h * (y-ymin) / (ymax-ymin);
        y = h - y;
        x = (left_margin*w) + ((1-(right_margin+left_margin))*x);
        y = (top_margin*h) + ((1-(top_margin+bottom_margin))*y);
        if (i==0) {
          ctx.moveTo(x,y);
        } else {
          ctx.lineTo(x,y);
        }
      }
      ctx.stroke();
    }
  }

}

draw_data();

</script>

</body>
</html>
