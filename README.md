# webservco/database-legacy

A PHP component/library. Please see [webservco/sandbox](https://webservco.github.io/sandbox/) for an example project using these types of components.

Static mysqli helper for moving old ext/mysql code to PHP 8 without rewriting it.

- Requires: `ext-mysqli`, database settings loaded with `webservco/configuration-legacy`
- Related: `webservco/database` (PDO, for new code)

---

## Installation

```shell
composer require webservco/database-legacy
```

---

## Usage

Load the configuration first (see `webservco/configuration-legacy`). The connection opens on the first query.

```php
use WebServCo\Database\Service\Legacy\Procedural\Db;

$result = Db::query(
    sprintf("SELECT something FROM somewhere WHERE id = '%s'", Db::escape($validatedInput)),
);
$something = Db::result($result, 0, 0);
```

---

## Contracts

| Interface | Implementation | Purpose |
| --- | --- | --- |
| none | `Service\Legacy\Procedural\Db` | Static replacements for `mysql_*` functions, plus transactions. |

Namespaces are relative to `WebServCo\Database\`.

---

## Configuration

```ini
[Database]

DB_HOST = 127.0.0.1
; Optional, defaults to 3306.
DB_PORT = 3306
DB_USER = db
DB_PASSWORD = db
DB_NAME = db
```

---

## Notes

### Convert ext/mysql calls to the `Db` helper

Each command finds the PHP files under `public` that use the function and rewrites the calls. It needs `ag` (The Silver Searcher). Commit or back up before running them.

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

---

## Development

```shell
# Start project.
ddev start
# Install dependencies.
ddev c u
# Run all validations.
ddev c c
# Run unit tests, with coverage (needs Xdebug, which DDEV leaves off).
ddev xdebug on && ddev c test:dox; ddev xdebug off
```

---

## License

[MIT](LICENSE.md)
