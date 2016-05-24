ASMX-DATA-RETRIEVAL

    -----------------------------------------------------------------


ASMX-DATA-RETRIEVAL, this repo dedicates to resolve questions of synchronization between two SQL servers the Manex and the Mantis at production site.
The repo also expands into dynamic retrieval of query result from user input and further handling of that dynamic data in the non-synchronized approach. That is
an approach where the Mantis front-end will process query result and stay pending further user action: e.g. submit of the current Mantis system.

The repo stresses on providing back-end functions and processing. The outcome of this project is to extend the current production Mantis version. The user interface is not yet provided.

## Use cases:

1) Testing connection to Manex server with cURLs through index.html (script.js is called).

2) SYNC (UI-required, not yet provided): test_fn.php provides loading configuration from conf.ini and processing (through util_fn.php) of query input, retrieving result and inserting into a Mantis' table.


3) NON-SYNC (UI-required, not yet provided): load conf.ini, retrieve data from getCurlData function in util_fn.php, the resulting data remains pending further actions.