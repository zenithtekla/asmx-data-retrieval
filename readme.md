								ASMX-DATA-SYNC

    -----------------------------------------------------------------

ASMX-DATA-SYNC, this repo dedicates to resolve questions of synchronization between two SQL servers the Manex and the Mantis in production state.

The repo also expands into dynamic retrieval of query result from user input and further handling of that dynamic data in the non-synchronized approach. That is
an approach where the Mantis front-end will process query result and stay pending further user action: e.g. submit of the current Mantis system.

The repo stresses on providing back-end functions and processing. The outcome of this project is to extend the current production Mantis version. The user interface is fulfilled by the powerful AngularJS framework.

Design logic:
- The project aims at updating the existing SQL dB of MantisBT, applying OOP and various programming paradigms to sync 2 databases using AngularPHP-ADOdB(db_query_bound)-modelling(mantis_db_query methods) -> slightly heading towards ORM dB.

## Use:
- Copy over the entire project folder.
- Ensure right setup of database tables
- Run, test and enjoy the compared, collective, automatic + pending-approval result.