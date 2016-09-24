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
?>

</form>


<center>

<h1>Parameterized Sine and Cosine</h1>

<canvas id="drawing_area" width="800" height="500" style="border:1px solid #d3d3d3;">
Your browser does not support the HTML5 canvas tag.</canvas>
<p/>
<table>
<tr>
<td><input type="range" id="m" min="1" max="10" value="1" onchange="draw_curve()"></input></td>
<td><input type="range" id="n" min="1" max="10" value="2" onchange="draw_curve()"></input></td>
<td><input type="range" id="d" min="0.001" max="0.1" step=0.001 value="0.01" onchange="draw_curve()"></input></td>
</tr>
<tr>
<td align="center"><span id="m_out"></span></td>
<td align="center"><span id="n_out"></span></td>
<td align="center"><span id="d_out"></span></td>
</tr>
</center>


<script>

function draw_curve() {
    c = document.getElementById ( "drawing_area" );
    w = c.width;
    h = c.height;
    xc = w/2;
    yc = h/2;
    out = document.getElementById("out");
    m_slider = document.getElementById ( "m" );
    n_slider = document.getElementById ( "n" );
    d_slider = document.getElementById ( "d" );


    m = 1 * m_slider.value;
    n = 1 * n_slider.value;
    delta = 1 * d_slider.value;
    // delta = 0.01;

    document.getElementById("m_out").innerHTML = "m = " + m;
    document.getElementById("n_out").innerHTML = "n = " + n;
    document.getElementById("d_out").innerHTML = "d = " + delta;

    var ctx = c.getContext("2d");
    ctx.fillStyle = "#eeeeee";
    ctx.fillRect(0,0,w,h);

    ctx.beginPath();
    for (p=0; p<delta+(2*Math.PI); p+=delta) {
      x = Math.sin(p*m);
      y = Math.sin(p*n);
      x = xc + (xc*x);
      y = yc + (yc*y);
      if (p==0) {
        ctx.moveTo(x,y);
      } else {
        ctx.lineTo(x,y);
      }
    }
    ctx.stroke();
}

draw_curve()

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
