<html>

<head>
<title>MCell Web Development - Run MCell</title>
<link rel="stylesheet" type="text/css" href="../style/def.css" />
</head>

<body>

<style type="text/css">
/* From phpinfo style */
body {background-color: #fff; color: #222; font-family: sans-serif;}
pre {margin: 0; font-family: monospace;}
a:link {color: #009; text-decoration: none; background-color: #fff;}
a:hover {text-decoration: underline;}
table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
.center {text-align: center;}
.center table {margin: 1em auto; text-align: left;}
.center th {text-align: center !important;}
td, th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
h1 {font-size: 150%;}
h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccf; width: 300px; font-weight: bold;}
.h {background-color: #99c; font-weight: bold;}
.v {background-color: #ddd; max-width: 300px; overflow-x: auto;}
.v i {color: #999;}
img {float: right; border: 0;}
hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}

/* Additional styles */
/* Body for the entire document */
body {
  background-color: #ffffff;
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
</style>


<?php echo '<center><h1>MCell Web Development at mcell.snl.salk.edu</h1></center>'; ?>

<hr/>

<form action="run_mcell.php" method="post">

<?php

$model_name = "";
if (in_array("model_name",array_keys($_POST))) {
  $model_name = $_POST["model_name"];
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

if ($start_seed < 1) {
  $start_seed = 1;
}
if ($end_seed > 20) {
  $end_seed = 20;
}
if ($end_seed < $start_seed) {
  $end_seed = $start_seed;
}

echo "<b>Model Name:</b> &nbsp; <input type=\"text\" name=\"model_name\" value=".$model_name.">";
echo " &nbsp; &nbsp; <b>Seeds:</b> &nbsp; <input type=\"text\" size=\"4\" min=\"1\" max=\"2000\" name=\"start_seed\" value=".$start_seed.">";
echo " &nbsp; to &nbsp;                   <input type=\"text\" size=\"4\" min=\"1\" max=\"2000\" name=\"end_seed\" value=".$end_seed.">";
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"run\">Run MCell</button>";
echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"clear\">Clear</button>";
$output = "";
if (strlen($what) > 0) {
  $sep = "=======================================================================================";
  if (strcmp($what,"clear") == 0) {
    $output = "\n\n".$sep."\n  Directory Listing After Clear \n".$sep."\n\n".shell_exec ("rm -Rf viz_data; rm -Rf react_data; ls -lR");
  } elseif (strcmp($what,"run") == 0) {
    if (strlen($model_name) > 0) {
      //$result = popen("/bin/ls", "r");
      echo "<br/><p><b>MCell output:</b></p>";
      $output = "";
      $result = "";
      for ($seed = $start_seed; $seed <= $end_seed; $seed++) {
        $result = shell_exec ("./mcell -seed ".$seed." ".$model_name."; echo \" \"; echo ".$sep."; echo \" \";");
        if ($seed == $start_seed) {
          $output = $result;
        } else {
          $output = $output."\n\n".$sep."\n\n".$result;
        }
      }
      $result = shell_exec ("ls -lR;");
      $output = $output."\n\n".$sep."\n  Directory Listing After All Runs \n".$sep."\n\n".$result;
    }
  }
}
echo "<center><table><tr><td><pre>$output</pre></td></tr></table></center>";
/*
if (strlen($what) > 0) {
  // Draw a plot
  echo "<center><canvas id=\"drawing_area\" width=\"800\" height=\"500\" style=\"border:1px solid #d3d3d3;\">";
  echo "Your browser does not support the HTML5 canvas tag.</canvas></center>";
}
*/

/*

$seed_folders = glob("react_data/*");

$plot_data = array_fill ( 0, count($seed_folders), array ( array(0,1), array(0,1) ) );
for ($seed_folder_index=0; $seed_folder_index<count($seed_folders); $seed_folder_index++) {
  echo "Seed Folders = ".$seed_folders[$seed_folder_index]."<br/>";
  $seed_files = glob($seed_folders[$seed_folder_index]."/*");
  $plot_data[$seed_folder_index] = array_fill ( 0, count($seed_files), array(0,1) );
  for ($seed_file_index=0; $seed_file_index<count($seed_files); $seed_file_index++) {
    $seed_file_lines = explode ( "\n", file_get_contents ( $seed_files[$seed_file_index] ) );
    $num_lines = count($seed_file_lines);



    echo " &nbsp;  &nbsp;  &nbsp; Seed File contains ".$num_lines." lines.<br/>";
    $plot_data[$seed_folder_index][$seed_file_index][0] = array_fill ( 0, $num_lines, 0 );
    $plot_data[$seed_folder_index][$seed_file_index][1] = array_fill ( 0, $num_lines, 0 );
    for ($seed_line_index=0; $seed_line_index<$num_lines; $seed_line_index++) {
      // echo " &nbsp;  &nbsp;  &nbsp;  &nbsp; ".$seed_line_parts[0].", ".$seed_line_parts[1]."<br/>";
      $seed_line_parts = explode ( " ", trim($seed_file_lines[$seed_line_index]) );
      // Good here: echo " &nbsp;  &nbsp;  &nbsp;  &nbsp; ".$seed_line_parts[0].", ".$seed_line_parts[1]."<br/>";
      $x = 0;
      $y = 0;
      if (count($seed_line_parts) == 2) {
        $x = floatval(trim($seed_line_parts[0]));
        $y = floatval(trim($seed_line_parts[1]));
      }
      $plot_data[$seed_folder_index][$seed_file_index][0][$seed_line_index] = $x;
      $plot_data[$seed_folder_index][$seed_file_index][1][$seed_line_index] = $y;
      echo " &nbsp;  &nbsp;  &nbsp;  &nbsp; ".$x.", ".$y."<br/>";
    }
  }
}


*/

/*
$plot_data = array ( 
                 array (
                     array(0.1,1.1,2.1,3.1,4.1,5.1,6.1,7.1),
                     array(0.1,1.1,3.1,3.1,3.1,5.1,7.1,9.1)
                 ),
                 array (
                     array(0.1,1.1,2.1,3.1,4.1,5.1,6.1,7.1),
                     array(8.1,8.1,9.1,9.1,8.1,8.1,7.1,7.1)
                 ),
                 array (
                     array(0.1,1.1,2.1,3.1,4.1,5.1,6.1,7.1),
                     array(0.1,0.1,0.1,0.1,9.1,0.1,0.1,0.1)
                 ),
                 array (
                     array(0.1,1.1,2.1,3.1,4.1,5.1,6.1,7.1),
                     array(9.1,7.1,6.1,5.1,4.1,3.1,3.1,2.1)
                 )
             );
*/

$plot_data = array ();
for ($i=0;$i<4;$i++) {
  $plot_data[$i] = array();
  for ($j=0;$j<2;$j++) {
    $plot_data[$i][$j] = array();
    for ($k=0;$k<8;$k++) {
      $x = $k;
      if ($j > 0) {
        $x = (5*$i) + ($k * $k);
      }
      $plot_data[$i][$j][$k] = $x;
    }
  }
}



$plot_data = array();
$plot_file_num = 0;

$seed_folders = glob("react_data/*");
for ($seed_folder_index=0; $seed_folder_index<count($seed_folders); $seed_folder_index++) {
  echo "Seed Folder = ".$seed_folders[$seed_folder_index]."<br/>";

  $mol_files = glob($seed_folders[$seed_folder_index]."/*");

  for ($mol_file_index=0; $mol_file_index<count($mol_files); $mol_file_index++) {

    $plot_data[$plot_file_num] = array();
    $plot_data[$plot_file_num][0] = array();
    $plot_data[$plot_file_num][1] = array();

    $seed_file_lines = explode ( "\n", file_get_contents ( $mol_files[$mol_file_index] ) );
    $num_lines = count($seed_file_lines);

    echo " &nbsp;  &nbsp;  &nbsp; Seed File contains ".$num_lines." lines.<br/>";

    $plot_line_num = 0;
    for ($seed_line_index=0; $seed_line_index<$num_lines; $seed_line_index++) {

      // echo " &nbsp;  &nbsp;  &nbsp;  &nbsp; ".$seed_line_parts[0].", ".$seed_line_parts[1]."<br/>";
      $seed_line_parts = explode ( " ", trim($seed_file_lines[$seed_line_index]) );
      // echo " &nbsp;  &nbsp;  &nbsp;  &nbsp; ".$seed_line_parts[0].", ".$seed_line_parts[1]."<br/>";
      if (count($seed_line_parts) == 2) {
        $x = floatval(trim($seed_line_parts[0]));
        $y = floatval(trim($seed_line_parts[1]));
        $plot_data[$plot_file_num][0][$plot_line_num] = $x;
        $plot_data[$plot_file_num][1][$plot_line_num] = $y;
        $plot_line_num = $plot_line_num + 1;
      }
      // echo " &nbsp;  &nbsp;  &nbsp;  &nbsp; ".$x.", ".$y."<br/>";
    }

    $plot_file_num = $plot_file_num + 1;

  }
}


// Dump the plot data

for ($ver1=0; $ver1<count($plot_data); $ver1++) {
  echo "<br/>";
  for ($ver2=0; $ver2<count($plot_data[$ver1]); $ver2++) {
    echo "<br/>";
    for ($ver3=0; $ver3<count($plot_data[$ver1][$ver2]); $ver3++) {
      $coord = "x";
      if ($ver2 != 0) {
        $coord = "y";
      }
      echo " &nbsp;  &nbsp; ".$coord." [".$ver1."][".$ver2."][".$ver3."] = ".$plot_data[$ver1][$ver2][$ver3]."<br/>";
    }
  }
}

?>

</form>


<center>

<h1>Count Output Plot</h1>

<canvas id="drawing_area" width="800" height="500" style="border:1px solid #d3d3d3;">
Your browser does not support the HTML5 canvas tag.</canvas>
<p/>
</center>

<script>

var plot_data = <?php echo json_encode($plot_data); ?>;

// console.log ( plot_data );

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
      console.log ( "  point " + x + "," + y );
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


<!--
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

-->

</body>
</html>
