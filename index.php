<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 1.0 Transitional//EN">
<html>

<head>
<title>MCell Web Development Home</title>
<link rel="stylesheet" type="text/css" href="style/def.css" />

<style type="text/css">


/* body {background-color: #fff; color: #222; font-family: sans-serif;} */
.fixed_width {margin: 20; font-family: monospace;}
.pretext {font-size: 10; font-family: monospace;}


.fixed_width {border-collapse: collapse; border: 10; width: 934px; box-shadow: 2px 4px 6px #008;}
.fixed_width {text-align: left; margin: 20px;}
.fixed_width table {margin: 10em auto; text-align: left;}
.fixed_width th {text-align: center !important;}
.fixed_width th {border: 1px solid #666; font-size: 90%; vertical-align: baseline; padding: 4px 5px; background-color: #ccc;}
.fixed_width td {border: none; font-size: 60%; color: #000000; vertical-align: baseline; padding: 4px 5px; background-color: #eee;} 

.fixed_width table, .fixed_width th {
  border: none;
  margin-left: 10px;
  margin-right: 10px;
  margin-top: 10px;
  margin-bottom: 10px;
  /* border-collapse: collapse; */
}


</style>


</head>

<body>

<div class="zeromargins">
<center><img class="zeromargins" src="share/rat_nmj.jpg" width="100%"></center>
</div>

<table class="zeromargins" width="100%" height="100%" border="0">
 <colgroup>
  <col width="25%" />
  <col />
 </colgroup>
 <tr valign="top">
  <td class="leftside">
   <p class="leftside">Run MCell:</a></p>
   <p class="leftside1"><a href="run_mcell/run_mcell_mdl.php">MDL</a></p>
   <p class="leftside1"><a href="run_mcell/run_mcell_simple.php">Data Model</a></p>
   <p class="leftside1"><a href="run_mcell/run_mcell_dm_range.php">Data Model Range Sweep</a></p>
   <p class="leftside1"><a href="run_mcell/run_mcell_dm.php">Data Model General Sweep</a></p>
   <hr />
   <p class="leftside">Browse Files:</a></p>
   <p class="leftside1"><a href="run_mcell/mdl_file_browser.php">MDL File Browser</a></p>
   <p class="leftside1"><a href="run_mcell/data_model_browser.php">Data Model Browser</a></p>
   <hr />
   <p class="leftside">Web Site:</a></p>
   <p class="leftside1"><a href="mysql-edit.php">Database Administration</a></p>
   <p class="leftside1"><a href="run_mcell/php_show_info.php">PHP Information</a></p>
  </td>
  <td class="rightside">


<div class="rightside">
<!----------------------------------------------------------------->

<h1>MCell Web Development at mcell.snl.salk.edu</h1>
<p class="indent1">
This site is being used for MCell Web Development.
</p>
<p class="indent1">
Projects include:
</p>
<ul>
<li>Running MCell with a web interface</li>
<li>A local Galaxy Server</li>
</ul>
<p class="indent1">
These are just a start.
</p>
<hr />
<h2>Project History:</h2>
<table class="fixed_width"><tr><td><pre class="pretext">
Date:   Thu Sep 22 17:17:00 2016 -0700

    Initial version of files for this project.

Date:   Thu Sep 22 17:32:54 2016 -0700

    Added a loop to show the top-level data model items.

Date:   Thu Sep 22 18:34:31 2016 -0700

    Checking for types of objects, and cleaned up example data model.

Date:   Thu Sep 22 18:52:36 2016 -0700

    Converted inner loop to a function (not recursive yet).

Date:   Thu Sep 22 20:36:20 2016 -0700

    Added a case to test deeper nesting (doesn't work for data model yet).

Date:   Thu Sep 22 21:09:47 2016 -0700

    Appended tag name to variables to make it easier to track.

Date:   Thu Sep 22 21:25:05 2016 -0700

    This version appears to work properly for dictionaries .. not lists yet.

Date:   Thu Sep 22 21:53:18 2016 -0700

    This version appears to show the data model properly.
    This version still has the test cases, but they will be removed next.

Date:   Thu Sep 22 22:23:42 2016 -0700

    Made a few small refinements to the look.

Date:   Thu Sep 22 22:30:05 2016 -0700

    Changed the images from + and - to open and close triangles.

Date:   Thu Sep 22 23:24:54 2016 -0700

    Slightly nicer formatting ... preparing to read from server.

Date:   Fri Sep 23 00:21:35 2016 -0700

    Added an external JSON file which is read from the web server.

Date:   Fri Sep 23 01:03:24 2016 -0700

    Added some CSS styling.

Date:   Fri Sep 23 17:32:25 2016 -0700

    Added some more files for the web development project.

Date:   Fri Sep 23 17:34:07 2016 -0700

    Improved readability for mysql-edit.php by separating functions.

Date:   Fri Sep 23 18:38:00 2016 -0700

    Added link to mcell internal home page and improved readability.

Date:   Fri Sep 23 20:59:50 2016 -0700

    This version has a "Run MCell" button that tries to run MCell.

    This version uses the "shell_exec" call in PHP. It seemed to work for
    running a small program, but the output appears to be truncated. There
    might be another method needed.

Date:   Fri Sep 23 21:22:17 2016 -0700

    Cleaned up the output a bit. MCell still doesn't seem to finish.

Date:   Fri Sep 23 21:46:59 2016 -0700

    The problem with the previous version appears to have been permissions.

    The web server runs as user www-data which didn't have permissions to
    write output to the viz_data folder. By setting the permissions on the
    "viz_data" directory to 777, the program worked.

Date:   Fri Sep 23 22:28:50 2016 -0700

    Added a "clear" button to remove the old runs.

Date:   Fri Sep 23 23:22:29 2016 -0700

    Added start and end seeds and handled output for multiple seeds.

Date:   Fri Sep 23 23:43:43 2016 -0700

    Added some more Model files for testing.

Date:   Fri Sep 23 23:56:17 2016 -0700

    Added a Model with both plot data and sparse viz output for testing.

Date:   Sat Sep 24 00:12:09 2016 -0700

    Added the JavaScript drawing that I had done for Ben a few years back.

Date:   Sat Sep 24 01:07:24 2016 -0700

    Added plotting from PHP using fixed data. Need to read files next.

Date:   Sat Sep 24 01:30:14 2016 -0700

    Fixed up the plotting to add colors and scale inside the boundaries.

Date:   Sat Sep 24 04:15:06 2016 -0700

    This version appears to plot properly, but needs testing and clean up.

Date:   Mon Sep 26 18:55:18 2016 -0700

    Added the ability to select a model and a number of other small changes.

Date:   Mon Sep 26 21:27:24 2016 -0700

    Added some new descriptive models and removed some older test models.

Date:   Mon Sep 26 23:33:35 2016 -0700

    Added ability to show and hide the MCell text output, fixed newlines.

Date:   Mon Sep 26 23:56:29 2016 -0700

    Added a simple timed release and decay example.

Date:   Tue Sep 27 00:18:05 2016 -0700

    Eliminated the directory listing and moved MCell to top of options.

Date:   Fri Oct 7 17:27:17 2016 -0700

    Updated web site to offer a data model option.

Date:   Fri Oct 7 17:52:48 2016 -0700

    Moved test_code directory to run_mcell directory.

Date:   Fri Oct 7 17:54:11 2016 -0700

    Updated index.php to use the new directory.

Date:   Fri Oct 7 17:55:02 2016 -0700

    Added the MySQL files to the repository.

Date:   Fri Oct 7 17:56:09 2016 -0700

    Added the mcell binary to the repository.

    It's not clear whether it's a good idea to add a binary file to a GIT
    repository or not. But it's being added for now for completeness.

Date:   Fri Oct 7 18:11:06 2016 -0700

    Renamed to data_model_browser.php

Date:   Fri Oct 7 19:06:14 2016 -0700

    Moved the mdl files to their own subdirectory.

Date:   Fri Oct 7 19:18:40 2016 -0700

    Renamed run_mcell.php to run_mcell_mdl.php

Date:   Fri Oct 7 20:21:18 2016 -0700

    Better support for reaction data output.

Date:   Fri Oct 7 20:22:42 2016 -0700

    Added a data model file for Lotka-Volterra demo.

Date:   Fri Oct 7 20:25:39 2016 -0700

    Added a .gitignore file to ignore mcell's output directories.

Date:   Fri Oct 7 20:29:36 2016 -0700

    Removed some transient/test files that shouldn't be in the repository

Date:   Fri Oct 7 20:32:04 2016 -0700

    Added the transient data_model.mdl file to .gitignore

Date:   Fri Oct 7 20:56:41 2016 -0700

    Implemented Release in data_model_to_mdl and added Release Decay data model.

Date:   Fri Oct 7 21:18:16 2016 -0700

    Added the Release Patterns demo as a data model to use for sweeping.

Date:   Fri Oct 7 22:10:32 2016 -0700

    Modified Data Model Tree to open a JSON file from host.
    Added some JSON data model files.

Date:   Fri Oct 7 22:45:30 2016 -0700

    Data Model Browser reads JSON, Data Model Runner prepared for parameters

Date:   Sat Oct 8 00:10:45 2016 -0700

    Began pulling parameters out of JSON model file for display / edit

    This version uses PHP to find the parameter system part of the data
    model and display all of the parameters when a new model is loaded.

    The parameters are just drawn in a simple form (no table yet).

Date:   Sat Oct 8 01:48:37 2016 -0700

    Much spiffing up of the CSS.

Date:   Sat Oct 8 02:25:15 2016 -0700

    This version changes one of the parameters (dr) to a hard-coded value.
    This works!!

Date:   Sat Oct 8 02:40:27 2016 -0700

    This version supports parameter changes!!!!

    All user-defined parameters in the data model are displayed with their
    default values. The user can change any of them and run to see the
    results. However, because the original data model is then re-read, the
    parameter values will change back to defaults after each run. This needs
    to be addressed ... later.

Date:   Sat Oct 8 03:08:51 2016 -0700

    This version saves changes between runs by reloading from the POST data.

Date:   Sun Oct 9 15:51:24 2016 -0700

    Added screenshot from late Friday (actually early Saturday).

Date:   Sun Oct 9 15:53:43 2016 -0700

    Added the generated data_model.json file to gitignore file.

Date:   Sun Oct 9 16:11:03 2016 -0700

    Reorganized main menu to reflect model building / model running

Date:   Sun Oct 9 16:51:21 2016 -0700

    Added parameters to Lotka Volterra and other minor modifications

Date:   Sun Oct 9 22:21:58 2016 -0700

    Added code to display names of objects (contain "name" as a field).

Date:   Sun Oct 9 22:23:22 2016 -0700

    Added two new data model files.

Date:   Wed Oct 12 16:45:59 2016 -0700

    Attempt to use class to show and hide the end and step fields.

    For some reason, changing the class on the fly appears to cause the
    browser to break the line. An earlier version used a span element to
    containt the optional end and step fields, but that also suffered from
    the line breaking. This version changed the class in the input elements
    themselves to hopefully get around the line breaking problem. But since
    it didn't work, and the span element is a cleaner solution, the earlier
    version will be committed after this one (effectively a roll back to an
    earlier version that wasn't committed).

Date:   Wed Oct 12 17:18:13 2016 -0700

    This version uses a span element to hide and show the end and step

    As mentioned in the previous commit, changing the class of the input
    fields seemed to insert a line break. This version reverts back to
    wrapping the end and step fields (as well as text) in a span which is
    hidden or visible. This results in a single line break instead of one
    for each of the input fields. This is good enough for now.

Date:   Wed Oct 12 17:47:41 2016 -0700

    Commented out some console.log debug statements.

Date:   Wed Oct 12 19:01:33 2016 -0700

    Working on sweep. This version shows and preserves sweep information.

Date:   Wed Oct 12 19:21:14 2016 -0700

    Show total MCell runs based on seed range on "Run MCell" button.

Date:   Wed Oct 12 19:32:58 2016 -0700

    Added a warning color to the run button for runs over 1 and 10.

Date:   Wed Oct 12 19:51:42 2016 -0700

    Removed hard-coded limits and added a Run Limit (only checks seeds now).

Date:   Wed Oct 12 23:16:47 2016 -0700

    Completed the parameter sweep looping ... need to handle subdirectories

    This version sweeps through all parameters and the seed. It runs MCell
    for each unique case. However, the results are written over the same
    files for each case, so there's only one case left in the end.

    The next step is to create the subdirectories and place the MDL in the
    correct subdirectories and run MCell from each of those locations. That
    will eventually be replaced with a job list, but the best way to be sure
    that the job list is correct ... is to actually run it.

Date:   Thu Oct 13 15:50:15 2016 -0700

    Changed block style to inline ... Thanks Lee!!

Date:   Thu Oct 13 16:06:57 2016 -0700

    Set width of plot to 95%

Date:   Thu Oct 13 16:39:30 2016 -0700

    Catch attempt to step by 0 or negative value

Date:   Thu Oct 13 18:31:46 2016 -0700

    Began using a "run_files" folder for output.

Date:   Thu Oct 13 18:44:18 2016 -0700

    Set default MCell output back to hidden.

Date:   Thu Oct 13 19:43:35 2016 -0700

    Removed the Viz Out from data model and enabled single run plotting.

Date:   Thu Oct 13 20:47:31 2016 -0700

    Added type="button" to button to keep it from submitting the form.

Date:   Thu Oct 13 21:08:53 2016 -0700

    Got the sweeping to work with subdirectories per parameter value.

    This version still has lots of debug statements. This commit preserves
    them in case there's a reason to go back and use them.

Date:   Thu Oct 13 21:20:31 2016 -0700

    Commented out many of the debug statements.

Date:   Thu Oct 13 22:18:07 2016 -0700

    Removed some comments and added a better Reslease Pattern Demo

Date:   Fri Oct 14 18:32:06 2016 -0700

    Support directories per user, Updated password file and gitignore file

Date:   Fri Oct 14 18:33:21 2016 -0700

    Added icinga.php file created by Brock Meyer ... not sure what it is.

Date:   Fri Oct 14 18:34:30 2016 -0700

    Added a screen image of status (sent to Tom).

Date:   Fri Oct 14 18:44:53 2016 -0700

    Handle the case when there is no data model (used to show a PHP error).

Date:   Fri Oct 14 21:06:10 2016 -0700

    Added a run_parameters.json output file.

    Each run will generate a JSON file containing a top level dictionary
    with keys that match the parameters being generated. The values that
    each parameter takes is included in a list which represents the value
    for that key.

    Note that this might not be the best representation because the keys in
    a dictionary are not ordered (even though they appear to be in the JSON
    file itself). The order is somewhat important because the subdirectory
    structure containing the data depends on the order (first parameter is
    at the top level, second parameter at the second, etc).

    More discussion with Tom might be helpful.

Date:   Fri Oct 14 21:34:25 2016 -0700

    Added another screen shot...

Date:   Fri Oct 14 22:04:02 2016 -0700

    Changed top level item to a list to preserve ordering of parameters.

    Since the ordering is used to determine the nesting level of the
    subdirectories, the parameter file was changed to provide that even when
    the JSON is read into a language that treats objects as unordered
    dictionaries.

Date:   Wed Oct 26 21:49:06 2016 -0700

    Modified the data_model_to_mdl.py file.

Date:   Wed Oct 26 21:50:15 2016 -0700

    Added a data model containing sweep information.

Date:   Wed Oct 26 23:02:50 2016 -0700

    Import of sweep parameters works, but needs to be loaded twice.

    Some of the sweep information seems to be retained in the form. This is
    not surprising since that was the only place it had been stored in the
    previous version that didn't look for sweep information in the data
    model. A little investigation might reveal the problem, but for now,
    it's helpful to push the "Refresh" button after loading a data model to
    get the proper number of runs. Otherwise it seems to remember sweep
    settings from the previous form settings.

Date:   Fri Oct 28 17:05:43 2016 -0700

    Added the Release_Patterns_Sweep.txt data model to the project.

Date:   Tue Nov 8 16:17:02 2016 -0800

    Moved main code under __main__ for clarity.

Date:   Tue Nov 8 16:18:45 2016 -0800

    Added a file with both comma-separated sweep points and a range.

    The CellBlender sweep runner can handle this format, but the PHP code
    cannot at this time.

Date:   Wed Nov 9 18:15:43 2016 -0800

    Added the "output_data" subdirectory to the path creation code.

    This was added to make the output conform to our current specification.

Date:   Thu Nov 10 12:47:46 2016 -0800

    Updated to run properly using "run_data_model_mcell.py" from web

    This version properly runs a data model by using the same run code that
    is used from CellBlender (run_data_model_mcell.py). This is a interim
    step in using that same code from within Galaxy.

    Note that there were some relative/absolute path issues that were difficult
    to resolve in a web environment. A simple "fake_mcell" was generated
    that reported its parameters to help with debugging. That code is
    included below.

    This commit also included a number of JSON data models used in testing
    the sweeping behavior. Note that not all of those data models can be
    properly represented in this simple web interface because it limits
    sweeps to start,end,step rather than a list of start,end,step values as
    allowed in CellBlender. Those features will be supported in the Galaxy
    version.

    ====== fake_mcell.c ======

    int main ( int argc, char *argv[] ) {

      // ##### Start by reading the command line parameters which includes
      // the data model file name
      int i;
      char cwd[1024];
      getcwd(cwd, sizeof(cwd));
      printf ( "\nFake MCell running from %s\n  # args = %d", cwd, argc );
      for (i=0; i&lt;argc; i++) {
        printf ( "\n    arg %d = %s", i, argv[i] );
      }
      printf ( "\n\n" );

    }

Date:   Thu Nov 10 20:12:39 2016 -0800

    Updates to run in more modes and with a number of fixes.

Date:   Wed Nov 16 21:20:25 2016 -0800

    Updated to sort parameters by dependency if information available in DM.

Date:   Wed Nov 16 21:21:42 2016 -0800

    Added some more testing JSON files.

Date:   Thu Nov 17 01:11:05 2016 -0800

    Updated to include a "Run All" option to run a swept data model.

    This version works, but it does not allow modification of parameters. It
    just runs the data model directly with the seeds as stored in the file.

Date:   Thu Nov 17 13:56:04 2016 -0800

    A few cosmetic changes before fixing up the general sweep code.</pre></td></tr></table>
</p>

<!----------------------------------------------------------------->
<!-- This is just a way of extending the main page so it fills the window vertically -->
<pre>

</pre>
</div>

<tr><td></td></tr>

</table>

</body>


</html>
