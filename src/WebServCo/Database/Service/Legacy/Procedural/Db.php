<?php

declare(strict_types=1);

namespace WebServCo\Database\Service\Legacy\Procedural;

use mysqli;
use mysqli_result;
use OutOfBoundsException;
use RuntimeException;
use WebServCo\Configuration\Service\Legacy\Procedural\Cfg;

use function array_key_exists;
use function is_array;
use function is_string;
use function mysqli_data_seek;
use function mysqli_fetch_array;
use function mysqli_fetch_assoc;
use function mysqli_report;

use const MYSQLI_NUM;
use const MYSQLI_REPORT_ERROR;
use const MYSQLI_REPORT_STRICT;

/**
 * Database abstraction class using ext/mysqli.
 *
 * Legacy code patching/migration; uses static methods in order to avoid manually modifying all procedural functions.
 */
final class Db
{
    private static ?mysqli $mysqli = null;

    /**
     * Begins a transaction.
     */
    public static function beginTransaction(): bool
    {
        $mysqli = self::link();

        return $mysqli->begin_transaction();
    }

    /**
     * Commits the current transaction.
     */
    public static function commitTransaction(): bool
    {
        $mysqli = self::link();

        return $mysqli->commit();
    }

    /**
     * Escapes special characters in a string for use in an SQL statement.
     */
    public static function escape(?string $string): ?string
    {
        if ($string === null) {
            return $string;
        }

        $mysqli = self::link();

        return $mysqli->real_escape_string($string);
    }

    /**
     * Fetch the next row of a result set.
     *
     * @return array<string,bool|int|float|string|null>|false|null
     */
    public static function fetchRow(mysqli_result $result): array|null|false
    {
        return $result->fetch_assoc();
    }

    public static function link(): mysqli
    {
        if (self::$mysqli instanceof mysqli) {
            return self::$mysqli;
        }

        try {
            $port = Cfg::getInt('DB_PORT');
        } catch (OutOfBoundsException) {
            // Configuration key "APP_DB_PORT" does not exist.
            $port = 3306;
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        self::$mysqli = new mysqli(
            Cfg::getString('DB_HOST'),
            Cfg::getString('DB_USER'),
            Cfg::getString('DB_PASSWORD'),
            Cfg::getString('DB_NAME'),
            $port,
        );

        self::$mysqli->set_charset('utf8mb4');

        return self::$mysqli;
    }

    /**
     * Get the Id generated from the previous INSERT operation.
     */
    public static function insertId(): int|string
    {
        $mysqli = self::link();

        return $mysqli->insert_id;
    }

    /**
     * Gets the number of rows in the result set.
     */
    public static function numRows(mysqli_result $result): int|string
    {
        return $result->num_rows;
    }

    /**
     * Gets the affected rows after a query like: INSERT, UPDATE, DELETE.
     * 
     * @return int|string Number of affected rows. Returns a string if the number of affected rows exceeds PHP_INT_MAX. 
     *                    See: https://www.php.net/manual/en/mysqli.affected-rows.php#refsect1-mysqli.affected-rows-returnvalues
     */
    public static function affectedRows(): int|string
    {
        $mysqli = self::link();
        
        return $mysqli->affected_rows;
    }


    /**
     * Performs a query on the database.
     */
    public static function query(string $query): mysqli_result|bool
    {
        $mysqli = self::link();

        return $mysqli->query($query);
    }

    /**
     * Get result data.
     *
     * Equivalent of `mysql_result`
     */
    public static function result(mysqli_result $result, int $offset, int|string $field): bool|int|float|string|null
    {
        mysqli_data_seek($result, $offset);
        $row = is_string($field)
            ? mysqli_fetch_assoc($result)
            : mysqli_fetch_array($result, MYSQLI_NUM);
        if (!is_array($row)) {
            throw new RuntimeException('Row is not an array.');
        }
        if (!array_key_exists($field, $row)) {
            throw new RuntimeException('Field not found.');
        }

        return $row[$field];
    }

    /**
     * Rolls back current transaction.
     */
    public static function rollbackTransaction(): bool
    {
        $mysqli = self::link();

        return $mysqli->rollback();
    }
}
