MCell Web
===============================================================================

This set of files is designed to provide a simple interface for running MCell via a web server.

MCell models can be run from either MDL or JSON Data Model format.

The JSON Data Model version provides a mechanism for modifying or sweeping model parameters.

Parameter sweep values are given as a comma-separated list of ranges. Each range can be a single
value, or a start:end pair with an implied step of 1, or a start:end:step triplet.

The interface allows for multiple parameters to be swept and provides a total run limit to keep
from generating more runs than expected.


![MCellWeb](share/Screenshot_from_2016-10-14_9pm.png?raw=true "MCell running in a browser")

