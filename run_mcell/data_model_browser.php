<!DOCTYPE html5>
<html>
<title> Data Model Browser </title>
<head>


<style>



/* HTML for the entire document */
html {
  font-size: 16px;
  text-indent: 0px;
  font-family: verdana, helvetica, arial, sans-serif;
  font-weight: bold;
}


/* Body for the entire document */
body {
  background-color: white	;  /* Web-Safe Light Blue is ccccff */
  color: #000066;  /* Text Color - Web-Safe Dark Blue is 000066 */
}

/* Pane classes */
.leftside {
  margin: 10px;
  background-color: #aaaaaa;
}
.rightside {
  margin: 10px;
  background-color: #cccccc;
}

/* Headings */

h1 {
  font-size: 140%;
  font-weight: bold;
}

h2 {
  font-size: 110%;
  font-weight: bold;
}


/* Regular Text */

.indent1 {
  text-indent: 0px;
  margin-left: 10px;
}

.indent2 {
  text-indent: 0px;
  margin-left: 20px;
}

strong {
  font-weight: bold;
  font-size: 104%;
}

/* Global Classes */
.bright {
  background-color: #ffffff;
}

/* Special Text Classes */
.highlight {
  background-color: #ffff88;
}

/* Division and Span Classes */
div.regular {
  background-color: #888888;
}

/* Span Classes */
span.regular {
  background-color: #888888;
}


/* Links */
a:link {
  text-decoration: none;
  color: #bb0000;
  background-color: inherit;
}

a:hover {
  color: #ff4444;
  background-color: inherit;
}

a:visited {
  text-decoration: none;
  color: #aa00aa;
  background-color: inherit;
}

/* For the current page - especially useful for frame index pages! */
a:active {
  text-decoration: none;
  /* color: inherit; */
  color: #8800ee;
  /* color: #ff4444; */
  background-color: inherit;
}

/* Tables */

table {
  /* background-color: #888888; */
  border: 0;
}

td {
  font-weight: bold;
  margin: 100px;
}

/* Images */
img {
  /* border: 4, #8888cc; */
  border-style: none; /* ridge groove solid inset outset double none dotted dashed */
}

.zeromargins {
  margin-left: 0px;
  margin-right: 0px;
  margin-top: 0px;
  margin-bottom: 0px;
  /* cellpadding: 10px; */
  /* cellspacing: 10px; */
}


.treeview ul { /*CSS for Simple Tree Menu*/
  margin: 0;
  padding: 0;
}

.treeview li { /*Style for LI elements in general (excludes an LI that contains sub lists)*/
  background: white url(list.gif) no-repeat left center;
  list-style-type: none;
  padding-left: 22px;
  margin-bottom: 3px;
}

.treeview li.submenu { /* Style for LI that contains sub lists (other ULs). */
  background: white url(closed.gif) no-repeat left 1px;
  /* cursor: hand !important; */
  cursor: pointer !important;
}


.treeview li.submenu ul { /*Style for ULs that are children of LIs (submenu) */
  display: none; /*Hide them by default. Don't delete. */
}

.treeview .submenu ul li { /*Style for LIs of ULs that are children of LIs (submenu) */
  cursor: default;
}

</style>

<script>

/// Example from http://www.dynamicdrive.com/dynamicindex1/navigate1.htm

console.log ( "Top of script in head section\n" );

var persisteduls=new Object()
var ddtreemenu=new Object()


ddtreemenu.closefolder = "closed.gif" //set image path to "closed" folder image
ddtreemenu.openfolder  = "open.gif"   //set image path to "open" folder image


ddtreemenu.createTree = function(treeid, enablepersist, persistdays) {
  console.log ( " Top of createTree for " + treeid + "\n" )
  var ultags = document.getElementById(treeid).getElementsByTagName("ul")
  console.log ( "   ultags = " + ultags + " has " + ultags.length + " elements\n" )
  if (typeof persisteduls[treeid] == "undefined") {
    persisteduls[treeid] = (enablepersist==true && ddtreemenu.getCookie(treeid)!="") ? ddtreemenu.getCookie(treeid).split(",") : ""
  }
  for (var i=0; i<ultags.length; i++) {
    ddtreemenu.buildSubTree(treeid, ultags[i], i)
  }
  if (enablepersist==true) { //if enable persist feature
    var durationdays=(typeof persistdays=="undefined")? 1 : parseInt(persistdays)
    ddtreemenu.dotask ( window, function() { ddtreemenu.rememberstate(treeid, durationdays) }, "unload" ) //save opened UL indexes on body unload
  }
  console.log ( " Bottom of createTree for " + treeid + "\n" )
}


ddtreemenu.buildSubTree = function ( treeid, ulelement, index ) {
  console.log ( "  Top of buildSubTree for " + treeid + "\n" )
  ulelement.parentNode.className="submenu"

  if (typeof persisteduls[treeid]=="object") {
    //if cookie exists (persisteduls[treeid] is an array versus "" string)
    if (ddtreemenu.searcharray(persisteduls[treeid], index)) {
      ulelement.setAttribute("rel", "open")
      ulelement.style.display="block"
      ulelement.parentNode.style.backgroundImage="url("+ddtreemenu.openfolder+")"
    } else {
      ulelement.setAttribute("rel", "closed")
    }
    //end cookie persist code
  } else if (ulelement.getAttribute("rel")==null || ulelement.getAttribute("rel")==false) { //if no cookie and UL has NO rel attribute explicted added by user
    ulelement.setAttribute("rel", "closed")
  } else if (ulelement.getAttribute("rel")=="open") { //else if no cookie and this UL has an explicit rel value of "open"
    ddtreemenu.expandSubTree(treeid, ulelement) //expand this UL plus all parent ULs (so the most inner UL is revealed!)
  }

  ulelement.parentNode.onclick = function(e) {
    var submenu=this.getElementsByTagName("ul")[0]
    if (submenu.getAttribute("rel")=="closed") {
      submenu.style.display="block"
      submenu.setAttribute("rel", "open")
      ulelement.parentNode.style.backgroundImage="url("+ddtreemenu.openfolder+")"
    } else if (submenu.getAttribute("rel")=="open") {
      submenu.style.display="none"
      submenu.setAttribute("rel", "closed")
      ulelement.parentNode.style.backgroundImage="url("+ddtreemenu.closefolder+")"
    }
    ddtreemenu.preventpropagate(e)
  }
  ulelement.onclick = function(e) {
    ddtreemenu.preventpropagate(e)
  }
  console.log ( "  Bottom of buildSubTree for " + treeid + "\n" )
}


ddtreemenu.expandSubTree = function(treeid, ulelement) { //expand a UL element and any of its parent ULs
  var rootnode=document.getElementById(treeid)
  var currentnode=ulelement
  currentnode.style.display="block"
  currentnode.parentNode.style.backgroundImage="url("+ddtreemenu.openfolder+")"
  while (currentnode!=rootnode) {
    if (currentnode.tagName=="UL") { //if parent node is a UL, expand it too
      currentnode.style.display="block"
      currentnode.setAttribute("rel", "open") //indicate it's open
      currentnode.parentNode.style.backgroundImage="url("+ddtreemenu.openfolder+")"
    }
    currentnode=currentnode.parentNode
  }
}


ddtreemenu.flatten=function(treeid, action){ //expand or contract all UL elements
  var ultags=document.getElementById(treeid).getElementsByTagName("ul")
  for (var i=0; i<ultags.length; i++){
    ultags[i].style.display=(action=="expand")? "block" : "none"
    var relvalue=(action=="expand")? "open" : "closed"
    ultags[i].setAttribute("rel", relvalue)
    ultags[i].parentNode.style.backgroundImage=(action=="expand")? "url("+ddtreemenu.openfolder+")" : "url("+ddtreemenu.closefolder+")"
  }
}


ddtreemenu.rememberstate=function(treeid, durationdays) { //store index of opened ULs relative to other ULs in Tree into cookie
  var ultags=document.getElementById(treeid).getElementsByTagName("ul")
  var openuls=new Array()
  for (var i=0; i<ultags.length; i++) {
    if (ultags[i].getAttribute("rel")=="open") {
      openuls[openuls.length]=i //save the index of the opened UL (relative to the entire list of ULs) as an array element
    }
  }
  if (openuls.length==0) //if there are no opened ULs to save/persist
    openuls[0]="none open" //set array value to string to simply indicate all ULs should persist with state being closed
  ddtreemenu.setCookie(treeid, openuls.join(","), durationdays) //populate cookie with value treeid=1,2,3 etc (where 1,2... are the indexes of the opened ULs)
}

////A few utility functions below//////////////////////

ddtreemenu.getCookie=function(Name) { //get cookie value
  var re=new RegExp(Name+"=[^;]+", "i"); //construct RE to search for target name/value pair
  if (document.cookie.match(re)) //if cookie found
  return document.cookie.match(re)[0].split("=")[1] //return its value
  return ""
}

ddtreemenu.setCookie=function(name, value, days) { //set cookei value
  var expireDate = new Date()
  //set "expstring" to either future or past date, to set or delete cookie, respectively
  var expstring=expireDate.setDate(expireDate.getDate()+parseInt(days))
  document.cookie = name+"="+value+"; expires="+expireDate.toGMTString()+"; path=/";
}


ddtreemenu.searcharray=function(thearray, value) { //searches an array for the entered value. If found, delete value from array
  var isfound=false
  for (var i=0; i<thearray.length; i++) {
    if (thearray[i]==value) {
      isfound=true
      thearray.shift() //delete this element from array for efficiency sake
      break
    }
  }
  return isfound
}

ddtreemenu.preventpropagate=function(e) { //prevent action from bubbling upwards
  if (typeof e!="undefined")
    e.stopPropagation()
  else
    event.cancelBubble=true
}

ddtreemenu.dotask=function(target, functionref, tasktype) { //assign a function to execute to an event handler (ie: onunload)
  var tasktype=(window.addEventListener)? tasktype : "on"+tasktype
  if (target.addEventListener)
    target.addEventListener(tasktype, functionref, false)
  else if (target.attachEvent)
    target.attachEvent(tasktype, functionref)
}

console.log ( "Bottom of script in head section\n" );

</script>


</head>


<body>

<form action="data_model_browser.php" method="post">

<a href="../.."> <b>Home</b> </a> &nbsp; &nbsp; &nbsp; &nbsp;

<?php


$model_file_name = "";
if (in_array("model_file_name",array_keys($_POST))) {
  $model_file_name = $_POST["model_file_name"];
}

$model_files = glob("data_model_files/*.json");

echo "<b>Data Model Name:</b> &nbsp; <select id=\"data_model_name_id\" name=\"model_file_name\">\n";
echo "  <option value=\"\"></option>>";
for ($model_file_index=0; $model_file_index<count($model_files); $model_file_index++) {
  $sel = "";
  if ($model_files[$model_file_index] == $model_file_name) {
    $sel = " selected ";
  }
  echo "  <option ".$sel."value=".$model_files[$model_file_index].">".$model_files[$model_file_index]."</option>\n";
}
echo "</select>\n";

echo " &nbsp; &nbsp; <button type=\"submit\" name=\"what\" value=\"load\">Load</button>\n";

?>


</form>


<hr/>

<h1>CellBlender Data Model Tree</h1>
 &nbsp; &nbsp; &nbsp; &nbsp; <a href="javascript:ddtreemenu.flatten('datamodel', 'expand')">Expand All</a>
 &nbsp; <a href="javascript:ddtreemenu.flatten('datamodel', 'contract')">Collapse All</a>


<ul id="datamodel" class="treeview">
  <!-- <li>Data Model (hard-coded into JavaScript source)</li> -->
</ul>


<script type="text/javascript">

console.log ( "\n\nTop of script in body section\n" );


// Uncomment this to use an internal data model
/*
var data_model = { "mcell": {

                 "TEST_LIST":[11,22,33,44, {"a":111, "b":222}],
                 "TEST_DICT": {
                   "name": "Test Dictionary",
                   "sub_dict": {
                     "k1":{"k1v1":"v11","k1v2":12},
                     "k2":{"k2v1":21, "k2v2":"v22"},
                     "k3":[44, 33, 22, 11]
                   },
                 },

                 "cellblender_version": "0.1.54",
                 "simulation_control": {
                   "name": "",
                   "processes_list": [
                     {
                       "name": "PID: 32375, MDL: Scene.main.mdl, Seed: 1",
                       "data_model_version": "DM_2015_04_23_1753"
                     },
                     {
                       "name": "PID: 32699, MDL: Scene.main.mdl, Seed: 1",
                       "data_model_version": "DM_2015_04_23_1753"
                     }
                   ],
                   "end_seed": "1",
                   "start_seed": "1",
                   "data_model_version": "DM_2016_04_15_1430"
                 },
                 "mol_viz": {
                   "file_start_index": 0,
                   "file_dir": "../../../../../../../home/bobkuczewski/proj/MCell/tutorials/intro/2016_09_21/libMCellPP_Test_files/mcell/viz_data/seed_00001",
                   "file_stop_index": 9,
                   "manual_select_viz_dir": false,
                   "file_num": 10,
                   "file_step_index": 1,
                   "viz_list": [
                     "mol_a",
                     "mol_b"
                   ],
                   "file_index": 0,
                   "color_index": 0,
                   "color_list": [
                     [0.8, 0.0, 0.0],
                     [0.0, 0.8, 0.0],
                     [0.0, 0.0, 0.8],
                     [0.0, 0.8, 0.8],
                     [0.8, 0.0, 0.8],
                     [0.8, 0.8, 0.0],
                     [1.0, 1.0, 1.0],
                     [0.0, 0.0, 0.0]
                   ],
                   "render_and_save": false,
                   "viz_enable": true,
                   "file_name": "Scene.cellbin.00.dat",
                   "data_model_version": "DM_2015_04_13_1700",
                   "active_seed_index": 0,
                   "seed_list": ["seed_00001"]
                 },
                 "model_objects": {
                   "model_object_list": [
                     {"name": "Cube"}
                   ],
                   "data_model_version": "DM_2014_10_24_1638"
                 },
                 "define_surface_classes": {"surface_class_list": [], "data_model_version": "DM_2014_10_24_1638"},
                 "viz_output": {"all_iterations": true, "end": "1", "export_all": true, "start": "0", "step": "1", "data_model_version": "DM_2014_10_24_1638"},
                 "define_release_patterns": {"release_pattern_list": [], "data_model_version": "DM_2014_10_24_1638"},
                 "define_reactions": {"reaction_list": [{"reactants": "b", "name": "b -> NULL", "variable_rate": "", "bkwd_rate": "", "rxn_name": "", "variable_rate_text": "", "rxn_type": "irreversible", "products": "NULL", "variable_rate_valid": false, "fwd_rate": "3e5", "variable_rate_switch": false, "data_model_version": "DM_2014_10_24_1638"}], "data_model_version": "DM_2014_10_24_1638"},
                 "release_sites": {"release_site_list": [{"location_x": ".1", "name": "Release_Site_1", "release_probability": "1", "points_list": [], "object_expr": "Cube", "quantity_type": "NUMBER_TO_RELEASE", "pattern": "", "stddev": "0", "location_z": "0", "location_y": "0", "molecule": "a", "site_diameter": ".01", "orient": "'", "shape": "OBJECT", "quantity": "10", "data_model_version": "DM_2015_11_11_1717"}, {"location_x": "0", "name": "Release_Site_2", "release_probability": "1", "points_list": [], "object_expr": "", "quantity_type": "GAUSSIAN_RELEASE_NUMBER", "pattern": "", "stddev": "0", "location_z": "0.1", "location_y": "0", "molecule": "b", "site_diameter": ".1", "orient": "'", "shape": "SPHERICAL", "quantity": "2", "data_model_version": "DM_2015_11_11_1717"}], "data_model_version": "DM_2014_10_24_1638"},
                 "blender_version": [2, 77, 0],
                 "cellblender_source_sha1": "d868a471cf8a3329dffa38bf7391c52d05fd82f9",
                 "api_version": 0,
                 "geometrical_objects": {
                   "object_list": [
                     {
                       "name": "Cube",
                       "location": [0.09687475115060806, 0.0, 0.0],
                       "element_connections": [[3, 0, 1], [7, 2, 3], [5, 6, 7], [1, 4, 5], [2, 4, 0], [7, 1, 5], [3, 2, 0], [7, 6, 2], [5, 4, 6], [1, 0, 4], [2, 6, 4], [7, 3, 1]],
                       "vertex_list": [[-0.025, -0.025, -0.025], [-0.025, -0.025, 0.025], [-0.025, 0.025, -0.025], [-0.025, 0.025, 0.025], [0.025, -0.025, -0.025], [0.025, -0.025, 0.025], [0.025, 0.025, -0.025], [0.025, 0.025, 0.025]]
                     }
                   ]
                 },
                 "modify_surface_regions": {"modify_surface_regions_list": [], "data_model_version": "DM_2014_10_24_1638"},
                 "initialization": {"space_step": "", "radial_subdivisions": "", "center_molecules_on_grid": false, "iterations": "9", "surface_grid_density": "10000",
                                    "notifications": {"molecule_collision_report": false, "partition_location_report": false, "box_triangulation_report": false, "final_summary": true, "file_output_report": false, "all_notifications": "INDIVIDUAL", "progress_report": true, "probability_report": "ON", "release_event_report": true, "diffusion_constant_report": "BRIEF", "iteration_report": true, "varying_probability_report": true, "probability_report_threshold": "0"},
                                    "time_step_max": "", "microscopic_reversibility": "OFF",
                                    "warnings": {"useless_volume_orientation": "WARNING", "high_probability_threshold": "1", "lifetime_threshold": "50", "degenerate_polygons": "WARNING", "missed_reactions": "WARNING", "negative_diffusion_constant": "WARNING", "negative_reaction_rate": "WARNING", "missing_surface_orientation": "ERROR", "high_reaction_probability": "IGNORED", "all_warnings": "INDIVIDUAL", "lifetime_too_short": "WARNING", "missed_reaction_threshold": "0.001"},
                                    "time_step": "1e-6", "radial_directions": "", "interaction_radius": "", "accurate_3d_reactions": true,
                                    "partitions": {"x_end": "1", "z_end": "1", "z_start": "-1", "y_end": "1", "recursion_flag": false, "y_step": "0.02", "include": false, "x_start": "-1", "z_step": "0.02", "x_step": "0.02", "y_start": "-1", "data_model_version": "DM_2016_04_15_1600"},
                                    "vacancy_search_distance": "", "data_model_version": "DM_2014_10_24_1638"
                 },
                 "parameter_system": {"model_parameters": [], "_extras": {"ordered_id_names": []}},
                 "scripting": {"force_property_update": true, "scripting_list": [], "show_simulation_scripting": false, "script_texts": {}, "dm_external_file_name": "", "show_data_model_scripting": false, "dm_internal_file_name": "", "data_model_version": "DM_2016_03_15_1900"},
                 "define_molecules": {"molecule_list": [{"display": {"glyph": "Sphere_1", "color": [1.0, 0.0, 0.0], "scale": 1.0, "emit": 0.0}, "mol_type": "3D", "mol_name": "a", "export_viz": false, "maximum_step_length": "", "custom_time_step": "", "custom_space_step": "", "target_only": false, "mol_bngl_label": "", "diffusion_constant": "1e-6", "data_model_version": "DM_2016_01_13_1930"}, {"display": {"glyph": "Sphere_1", "color": [0.0, 1.0, 0.0], "scale": 1.0, "emit": 0.0}, "mol_type": "3D", "mol_name": "b", "export_viz": false, "maximum_step_length": "", "custom_time_step": "", "custom_space_step": "", "target_only": false, "mol_bngl_label": "", "diffusion_constant": "1e-7", "data_model_version": "DM_2016_01_13_1930"}], "data_model_version": "DM_2014_10_24_1638"},
                 "materials": {"material_dict": {}},
                 "reaction_data_output": {"combine_seeds": true, "rxn_step": "", "plot_layout": " plot ", "output_buf_size": "", "plot_legend": "0", "mol_colors": false, "always_generate": true, "reaction_output_list": [], "data_model_version": "DM_2016_03_15_1800"},
                 "data_model_version": "DM_2014_10_24_1638"}
             }

console.log ( "\n\nData Model version is " + data_model["mcell"]["cellblender_version"] + "\n\n" );
*/

// Comment this when using the internal data model

var source_filename = window.location.href;
var source_path = source_filename.substring(0,source_filename.lastIndexOf("/"));
var json_file = source_path + "/" + "data_model.json";

json_file = source_path + "/" + document.getElementById("data_model_name_id").value;
console.log ( "Opening: " + json_file );

var xmlhttp = new XMLHttpRequest();
var url = json_file;
xmlhttp.onreadystatechange=function() {
    console.log ( "state changed to " + this.readyState );
    if (this.readyState == 4 && this.status == 200) {
        // readyState 4 is complete
        myFunction(this.responseText);

        doc_dm = document.getElementById("datamodel");

        /*
        dyn_item_li = document.createElement("li");
        dyn_text = document.createTextNode("mcell");
        dyn_item_li.appendChild(dyn_text);
        doc_dm.appendChild(dyn_item_li);
        */

        dyn_sublist_ul = document.createElement("ul");

        build_elements_from_object ( data_model["mcell"], dyn_sublist_ul, 0 );

        dyn_item_li.appendChild(dyn_sublist_ul);
        doc_dm.appendChild(dyn_item_li);


        ddtreemenu.createTree("datamodel", true);

        console.log ( "Done loading Data Model");
    }
}
xmlhttp.open("GET", url, true);
xmlhttp.send();


function myFunction(response) {
    data_model = JSON.parse(response);
}


function build_elements_from_array ( arr, parent_ul, depth ) {
  var i;
  var x;
  var item_li;
  var item_text;
  var sublist_ul;
  var spaces = "                                                                         ";
  var indent = spaces.substring(0,depth);
  i = 0;
  for (x in arr) { // The "x" appears to be the index for arrays!!
    console.log ( indent + "Array contains " + arr[x] + "\n" );
    item_li = document.createElement("li");
    if (typeof(arr[x]) != "object") {
      console.log ( indent + "  Scalar at index " + x + " is " + arr[x] + "\n" );
      // This is not an array or dictionary (both are "objects" in JavaScript)
      item_text = document.createTextNode(" [" + x + "] = \"" + arr[x] + "\" is of type " + typeof(arr[x]) );
      item_li.appendChild(item_text);
    } else {
      // An "object" can be an array or dictionary
      if (arr[x] instanceof Array) {  // Note that instanceof may not always work ... check for problems!!
        item_text = document.createTextNode("Item at [" + x + "] is an array of length " + arr[x].length + "\n" );
        item_li.appendChild(item_text);
        sublist_ul = document.createElement("ul");
        build_elements_from_array ( arr[x], sublist_ul, depth+1 );
        item_li.appendChild(sublist_ul);
      } else {
        item_text = document.createTextNode("[" + x + "] is an object of length " + Object.keys(arr[x]).length + "\n" );
        item_li.appendChild(item_text);
        sublist_ul = document.createElement("ul");
        build_elements_from_object ( arr[x], sublist_ul, depth+1 );
        item_li.appendChild(sublist_ul);
      }
    }
    parent_ul.appendChild(item_li);
    i += 1;
  }
}


function build_elements_from_object ( obj, parent_ul, depth ) {
  var x;
  var item_li;
  var item_text;
  var sublist_ul;
  var spaces = "                                                                         ";
  var indent = spaces.substring(0,depth);
  for (x in obj) { // The "x" appears to be the "key" for objects
    console.log ( indent + "Object contains " + x + "\n" );
    item_li = document.createElement("li");
    if (typeof(obj[x]) != "object") {
      console.log ( indent + "  Scalar " + x + " is " + obj[x] + "\n" );
      // This is not an array or dictionary (both are "objects" in JavaScript)
      item_text = document.createTextNode("[" + x + "] is \"" + obj[x] + "\" of type " + typeof(obj[x]) );
      item_li.appendChild(item_text);
    } else {
      // An "object" can be an array or dictionary
      if (obj[x] instanceof Array) {  // Note that instanceof may not always work ... check for problems!!
        item_text = document.createTextNode("[" + x + "] is an array of length " + obj[x].length + "\n" );
        item_li.appendChild(item_text);
        sublist_ul = document.createElement("ul");
        build_elements_from_array ( obj[x], sublist_ul, depth+1 );
        item_li.appendChild(sublist_ul);
      } else {
        item_text = document.createTextNode("[" + x + "] is an object of length " + Object.keys(obj[x]).length + "\n" );
        item_li.appendChild(item_text);
        sublist_ul = document.createElement("ul");
        build_elements_from_object ( obj[x], sublist_ul, depth+1 );
        item_li.appendChild(sublist_ul);
      }
    }
    parent_ul.appendChild(item_li);
  }
}


doc_dm = document.getElementById("datamodel");


dyn_item_li = document.createElement("li");
dyn_text = document.createTextNode("mcell");
dyn_item_li.appendChild(dyn_text);
doc_dm.appendChild(dyn_item_li);

dyn_sublist_ul = document.createElement("ul");

build_elements_from_object ( data_model["mcell"], dyn_sublist_ul, 0 );

dyn_item_li.appendChild(dyn_sublist_ul);
doc_dm.appendChild(dyn_item_li);


ddtreemenu.createTree("datamodel", true);

console.log ( "Bottom of script in body section\n" );

</script>

</body>
</html>

