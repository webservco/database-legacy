# webservco/database-legacy

Helper for legacy projects that have to use a procedural approach.

Can we used when upgrading code from PHP 5 to PHP 8.

Requires: `webservco/configuration-legacy`.

## Setup

Composer require:

```json
"webservco/database-legacy": "^0"
```

## Usage

```php
use \WebServCo\Database\Service\Legacy\Procedural\Db;

$result = Db::query(
    sprintf("SELECT something FROM somewhere WHERE id = %s"),
    Db::escape($validatedInput),
);
$something = Db::result($result, 0, 0);
```

---

## Convert ext/mysql to ext/mysqli using helper `Db` class

```shell
# mysql_query
ag --php -l -Q 'mysql_query(' public | xargs sed -i -e "s#mysql_query(#\\\WebServCo\\\Database\\\Service\\\Legacy\\\Procedural\\\Db::query(#g"

# mysql_escape_string
ag --php -l -Q 'mysql_escape_string(' public | xargs sed -i -e "s#mysql_escape_string(#\\\WebServCo\\\Database\\\Service\\\Legacy\\\Procedural\\\Db::escape(#g"

# mysql_fetch_array
ag --php -l -Q 'mysql_fetch_array(' public | xargs sed -i -e "s#mysql_fetch_array(#\\\WebServCo\\\Database\\\Service\\\Legacy\\\Procedural\\\Db::fetchRow(#g"

# mysql_fetch_assoc
ag --php -l -Q 'mysql_fetch_assoc(' public | xargs sed -i -e "s#mysql_fetch_assoc(#\\\WebServCo\\\Database\\\Service\\\Legacy\\\Procedural\\\Db::fetchRow(#g"

# mysql_insert_id
ag --php -l -Q 'mysql_insert_id(' public | xargs sed -i -e "s#mysql_insert_id(#\\\WebServCo\\\Database\\\Service\\\Legacy\\\Procedural\\\Db::insertId(#g"

# mysql_num_rows
ag --php -l -Q 'mysql_num_rows(' public | xargs sed -i -e "s#mysql_num_rows(#\\\WebServCo\\\Database\\\Service\\\Legacy\\\Procedural\\\Db::numRows(#g"

# mysql_result
ag --php -l -Q 'mysql_result(' public | xargs sed -i -e "s#mysql_result(#\\\WebServCo\\\Database\\\Service\\\Legacy\\\Procedural\\\Db::result(#g"
```
