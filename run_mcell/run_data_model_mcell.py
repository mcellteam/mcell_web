#!/usr/bin/env python

import datetime
import sys
import multiprocessing
import os
import subprocess
import argparse
import data_model_to_mdl


def run_sim(arglist):
    """ Run the MCell simulations. """
    print ( "Sim Thread running from " + str(os.getcwd()) )
    print ( "Sim Thread using " + str(arglist) )

    mcell_binary, project_dir, base_name, error_file_option, log_file_option, seed = arglist
    mdl_filename = '%s.main.mdl' % (base_name)
    mdl_filepath = os.path.join(project_dir, mdl_filename)
    # Log filename will be log.year-month-day_hour:minute_seed.txt
    # (e.g. log.2013-03-12_11:45_1.txt)
    time_now = datetime.datetime.now().strftime("%Y-%m-%d_%H:%M")
    log_filename = "log.%s_%d.txt" % (time_now, seed)
    error_filename = "error.%s_%d.txt" % (time_now, seed)
    log_filepath = os.path.join(project_dir, log_filename)
    error_filepath = os.path.join(project_dir, error_filename)

    if error_file_option == 'none':
        error_file = subprocess.DEVNULL
    elif error_file_option == 'console':
        error_file = None

    if log_file_option == 'none':
        log_file = subprocess.DEVNULL
    elif log_file_option == 'console':
        log_file = None

    print("Running: " + mcell_binary + " " + mdl_filepath)
    subprocess_cwd = os.path.dirname(mdl_filepath)
    print("  Should run from cwd = " +  subprocess_cwd)

    # Both output and error log file
    if (log_file_option == 'file' and error_file_option == 'file'):
        with open(log_filepath, "w") as log_file:
            with open (error_filepath, "w") as error_file:
                subprocess.call(
                    [mcell_binary, '-seed', '%d' % seed, mdl_filepath],
                    cwd=subprocess_cwd,
                    stdout=log_file, stderr=error_file)
    # Only output log file
    elif log_file_option == 'file':
        with open(log_filepath, "w") as log_file:
            subprocess.call(
                [mcell_binary, '-seed', '%d' % seed, mdl_filepath],
                cwd=subprocess_cwd,
                stdout=log_file, stderr=error_file)
    # Only error log file
    elif error_file_option == 'file':
        with open(error_filepath, "w") as error_file:
            subprocess.call(
                [mcell_binary, '-seed', '%d' % seed, mdl_filepath],
                cwd=subprocess_cwd,
                stdout=log_file, stderr=error_file)
    # Neither error nor output log
    else:
        subprocess.call(
            [mcell_binary, '-seed', '%d' % seed, mdl_filepath],
            cwd=subprocess_cwd, stdout=log_file, stderr=error_file)


def build_sweep_list ( par_dict ):
    """ Count the number of runs that will be swept with this data model. """
    sweep_list = []
    print ( "Building sweep list... " )
    if 'model_parameters' in par_dict:
        for par in par_dict['model_parameters']:
            print ( "Checking par " + str(par) )
            if ('sweep_expression' in par) and ('sweep_enabled' in par):
                if par['sweep_enabled']:
                    sweep_item = {}
                    if 'par_name' in par:
                        sweep_item['par_name'] = par['par_name']
                    else:
                        sweep_item['par_name'] = "Unknown"
                    # Sweep expression example: "0, 2, 9, 10:20, 25:35:5, 50"
                    num_runs_for_this_parameter = 0
                    # Get the sweep expression sublists which are separated by commas
                    sw_items = []
                    if 'sweep_expression' in par:
                        sw_items = [ p.strip() for p in par['sweep_expression'].split(',') ]
                    #print ( "Sweep item list = " + str(sw_items) )
                    # Count the number of runs represented by each sweep item (either:  #  or  #:#  or  #:#:#  )
                    sweep_values = []
                    for sw_item in sw_items:
                        parts = [ p.strip() for p in sw_item.split(':') ]
                        if len(parts) <= 0:
                          # This would be two commas together?
                          pass
                        elif len(parts) == 1:
                          # This is a scalar
                          sweep_values.append ( float(parts[0]) )
                          #print ( "Added " + parts[0] )
                        elif len(parts) >= 2:
                          # This is a range with a start and stop
                          start = float(parts[0])
                          stop = float(parts[1])
                          step = 1
                          if len(parts) > 2:
                            step = float(parts[2])
                          if start > stop:
                            start = float(parts[1])
                            stop = float(parts[0])
                          if step < 0:
                            step = -step
                          if step == 0:
                            # Do something to keep it from an infinite loop
                            step = 1;
                          # Start with a pessimistic guess
                          num = int((stop-start) / step)
                          # Increase until equal or over
                          #print ( "Before increasing, num = " + str(num) )
                          while (start+((num-1)*step)) < (stop+(step/1000)):
                            num += 1
                            #print ( "Increased to num = " + str(num) )
                          # Reduce while actually over
                          while start+((num-1)*step) > (stop+(step/1000)):
                            num += -1
                            #print ( "Decreased to num = " + str(num) )
                          # Finally append the values
                          for n in range(num):
                            sweep_values.append ( start + (n*step) )
                    sweep_item['values'] = sweep_values
                    sweep_list.append ( sweep_item )
    return sweep_list


def count_sweep_runs ( sweep_list ):
    total_sweep_runs = 1
    for sweep_item in sweep_list:
      if 'values' in sweep_item:
        total_sweep_runs *= len(sweep_item['values'])
    return total_sweep_runs


def makedirs_exist_ok ( path_to_build, exist_ok=False ):
    # Needed for old python which doesn't have the exist_ok option!!!
    print ( " Make dirs for " + path_to_build )
    parts = path_to_build.split(os.sep)  # Variable "parts" should be a list of subpath sections. The first will be empty ('') if it was absolute.
    # print ( "  Parts = " + str(parts) )
    full = ""
    if len(parts[0]) == 0:
      full = os.sep
    for p in parts:
      full = os.path.join(full,p)
      # print ( "   " + full )
      if not os.path.exists(full):
        os.makedirs ( full )


if __name__ == "__main__":
    """
    Run one or more MCell processes from a potentially swept CellBlender Data Model
    """
    arg_parser = argparse.ArgumentParser(description='Run MCell with appropriate arguments')
    arg_parser.add_argument ( 'data_model_file_name',     type=str,                    help='the file name of the data model to run' )
    arg_parser.add_argument ( '-pd', '--proj_dir',        type=str, default='',        help='the directory where the program will run' )
    arg_parser.add_argument ( '-b',  '--binary',          type=str, default='mcell',   help='full path of binary file to run' )
    arg_parser.add_argument ( '-fs', '--first_seed',      type=int, default=1,         help='the first seed in a series of seeds to run' )
    arg_parser.add_argument ( '-ls', '--last_seed',       type=int, default=1,         help='the last seed in a series of seeds to run' )
    arg_parser.add_argument ( '-lf', '--log_file_opt',    type=str, default='console', help='the log file option for mcell' )
    arg_parser.add_argument ( '-ef', '--error_file_opt',  type=str, default='console', help='the error file option for mcell' )
    arg_parser.add_argument ( '-np', '--num_processors',  type=int, default=8,         help='the number of processors' )
    arg_parser.add_argument ( '-rl', '--run_limit',       type=int, default=-1,        help='limit the total number of runs' )
    
    parsed_args = arg_parser.parse_args() # Without any arguments this uses sys.argv automatically

    # Get the command line arguments (excluding the script name itself)
    print ( "Main of run_data_model_mcell.py running from " + str(os.getcwd()) )
    print ( "Arguments = " + str(sys.argv) )

    print ( "Data Model Name = " + parsed_args.data_model_file_name )
    print ( "Binary Name = " + parsed_args.binary )
    print ( "Project Directory = " + parsed_args.proj_dir )
    print ( "First Seed = " + str(parsed_args.first_seed) )
    print ( "Last Seed = " + str(parsed_args.last_seed) )
    print ( "Log File = " + parsed_args.log_file_opt )
    print ( "Error File = " + parsed_args.error_file_opt )
    print ( "Num Processors = " + str(parsed_args.num_processors) )
    print ( "Run Limit = " + str(parsed_args.run_limit) )
    
    # Create convenience variables from parsed_args:
    mcell_binary = parsed_args.binary
    start = parsed_args.first_seed
    end = parsed_args.last_seed
    project_dir = parsed_args.proj_dir
    if len(project_dir) <= 0:
      project_dir = os.path.join(os.getcwd(), 'project_name_files/mcell')
    base_name = 'Scene'
    error_file_option = parsed_args.error_file_opt
    log_file_option = parsed_args.log_file_opt
    mcell_processors = parsed_args.num_processors

    data_model_file_name = parsed_args.data_model_file_name

    # Read the data model specified on the command line
    dm = data_model_to_mdl.read_data_model ( data_model_file_name )
    # data_model_to_mdl.dump_data_model ( dm )

    # Build a sweep list and add a "current_index" of 0 to support the sweeping
    print ( "Building sweep list" )
    sweep_list = build_sweep_list( dm['mcell']['parameter_system'] )
    print ( "Sweep list = " + str(sweep_list) )
    for sw_item in sweep_list:
      sw_item['current_index'] = 0
      print ( "  Sweep list item = " + str(sw_item) )


    # Save the sweep list to a file for plotting, visualization, and other processing
    sweep_list_file_name = os.path.join ( project_dir, "data_layout.json" )
    sweep_list_file = open ( sweep_list_file_name, "w" )
    sweep_list_length = len(sweep_list)
    sweep_list_file.write ( "{\n" )
    sweep_list_file.write ( " \"version\": 0,\n" )
    sweep_list_file.write ( " \"data_layout\": [\n" )
    sweep_list_file.write ( "  [\"dir\", [\"output_data\"]],\n" )
    for i in range ( sweep_list_length ):
      sw_item = sweep_list[i]
      sweep_list_file.write ( "  [\"" + sw_item['par_name'] + "\", " + str(sw_item['values']) + "],\n" )
    # Include the file type
    sweep_list_file.write ( "  [\"file_type\", [\"react_data\", \"viz_data\"]],\n" )
    # Include the seeds in the sweep list file for plotting, etc.
    sweep_list_file.write ( "  [\"SEED\", " + str([s for s in range(start,end+1)]) + "]\n" )
    sweep_list_file.write ( " ]\n" )
    sweep_list_file.write ( "}\n" )
    sweep_list_file.close()


    # Count the number of sweep runs (could be done in build_sweep_list, but it's nice as a separate function) 
    num_sweep_runs = count_sweep_runs ( sweep_list )
    num_requested_runs = num_sweep_runs * (1 + parsed_args.last_seed - parsed_args.first_seed)
    print ( "Number of non-seed sweep runs = " + str(num_sweep_runs) )
    print ( "Total runs (sweep and seed) is " + str(num_requested_runs) )

    if parsed_args.run_limit >= 0:
      # Only check when run_limit is non-negative (0 is legal and means that no runs are allowed)
      if num_requested_runs > parsed_args.run_limit:
        print ( " " )
        print ( 100*"%" )
        print ( 100*"%" )
        print ( "Number of requested runs is " + str(num_requested_runs) + " which exceeds the current limit of " + str(parsed_args.run_limit) )
        print ( 100*"%" )
        print ( 100*"%" )
        sys.exit(111)

    # Build a list of "run commands" (one for each run) to be run by the multiprocessing pool and "run_sim" (above)
    # Note that the format of these came from the original "run_simulations.py" program and may not be what we want in the long run
    run_cmd_list = []
    # Build a sweep list with a "output_data" prefix directory
    for run in range (num_sweep_runs):
        sweep_path = "output_data"
        for sw_item in sweep_list:
            sweep_path += "/" + sw_item['par_name'] + "_index_" + str(sw_item['current_index'])
        print ( "Sweep path = " + sweep_path )
        # Set the data model parameters to the current parameter settings
        for par in dm['mcell']['parameter_system']['model_parameters']:
            if ('par_name' in par):
                for sweep_item in sweep_list:
                    if par['par_name'] == sweep_item['par_name']:
                        par['par_expression'] = str(sweep_item['values'][sweep_item['current_index']])
        # Sweep through the seeds for this set of parameters creating a run specification for each seed
        for seed in range(start,end+1):
            # Create the directories and write the MDL
            sweep_item_path = os.path.join(project_dir,sweep_path)
            makedirs_exist_ok ( sweep_item_path, exist_ok=True )
            makedirs_exist_ok ( os.path.join(sweep_item_path,'react_data'), exist_ok=True )
            makedirs_exist_ok ( os.path.join(sweep_item_path,'viz_data'), exist_ok=True )
            data_model_to_mdl.write_mdl ( dm, os.path.join(sweep_item_path, '%s.main.mdl' % (base_name) ) )
            run_cmd_list.append ( [mcell_binary, sweep_item_path, base_name, error_file_option, log_file_option, seed] )
        # Increment the current_index counters from rightmost side (deepest directory)
        i = len(sweep_list) - 1
        while i >= 0:
            sweep_list[i]['current_index'] += 1
            if sweep_list[i]['current_index'] >= len(sweep_list[i]['values']):
              sweep_list[i]['current_index'] = 0
            else:
              break
            i += -1

    # Print the run commands as a record of what's being done
    print ( "Run Cmds:" )
    for rc in run_cmd_list:
      print ( "  " + str(rc) )

    # Create a pool of mcell processes.
    pool = multiprocessing.Pool(processes=mcell_processors)
    pool.map(run_sim, run_cmd_list)

