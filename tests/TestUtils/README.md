# TestUtils

These testing utitilities will replace the testing library that does much too
much stuff that is terribly slowing down testing. Nevertheless it is necessary
to do some setup before and some clean up after running a test. These
utitilties help to facilitate these tasks und handle some quirks of the
OXID framework.

## General utitilities

### `bootstrap.php`

The minimal bootstrapping necessary for the tests to run. It sets some
necessary constants, starts autoloading, defines some special functions
like the infamous `oxnew()` and finally sets the configuration file into
the `Registry` class. This is reasonably fast.

### `TestContainerFactory`

This factory is used for setting up a DI container that may be used
in integration tests. This DI container for tests differs from the
original container in several respects needed for testing:

* All services are public
* The BasicContext and the Context services are replaced by their stubs
  so it is easy to tweak context information in the test setup
* The database connection is replaced by a connection to a test database
  (currently TestCE). More details may be found below in the *Database*
  section.
* Services may be replaced with mocks when calling the `create()` method
  of the factory. The (optional) parameter for `create()` is an
  associative array with the container key as key and the service
  object as value.
  
## Database

### `prepare_in_memory_schema.sh`

For integration tests an in memory database should be used, to speed
up the tests. MySql and MariaDb support in memory tables, so the main
task is to change the engine from InnoDB to MEMORY. This script helps
to do this. It takes the original schema file as first parameter, the
schema file for the in memory database as second parameter.

There is one additional quirk: In memory tables do not allow `text`
columns, so it replaces all `text` columns with `varchar(2048)` columns.
For testing purposes this should be no problem.

### `in_memory_schema_ce.sql`

This file is generated with the `prepare_in_memory_schema.sh` script
and used for setting up the `TestCE` database.

### `TestConnectionFactory.php`

This factory is used in the `TestContainerFactory` to replace the
default connection of the shop with a connection to the testing
database. This database is expected only to have the schema set up
and no data in it.

Actually some data is needed for tests to run at all, so a
`basic_fixtures.yaml` file is provided that is always loaded when
setting up the database connection.

And also this `TestConnectionFactory` sets up the connection not
only for the container, but also for the traditional OXID code.

### `FixtureLoader`

This class is responsible for loading fixtures. The fixture file
is a simple yaml file containing an array tables that again contain
an array of database rows to be saved. There is one special field
value `UUID` that can be used when the fixture loader should create
a unique id (mostly used for `OXID` columns).

Before loading data into a table, the complete data in the table
is deleted. If now rows are provided, only the table contents are
removed.

This `FixtureLoader` should not be used directly but through the
`DatabaseTestingTrait`.

### `basic_fixtures.yaml`

The fixture file that always will be loaded into the testing
database. It is also an example for the format.

## `Traits`
