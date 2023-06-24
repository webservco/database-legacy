# webservco/database-legacy

Helper for legacy projects that have to use a procedural approach.

Requires: `webservco/configuration-legacy`.

## Setup

Composer require:

```json
"webservco/database-legacy": "^0"
```

## Usage

```php
$result = Db::query(
    sprintf("SELECT something FROM somewhere WHERE id = %s"),
    Db::escape($validatedInput),
);
$something = Db::result($result, 0, 0);
```
