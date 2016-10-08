<html>
<body>
<?php echo '<center><h1>MCell Web Development at mcell.snl.salk.edu</h1></center>'; ?>

<hr/>

<?php phpinfo(); ?>

<hr/>

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

</body>
</html>
