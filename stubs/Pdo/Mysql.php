<?php

/**
 * Stub PHP 8.5 — Pdo\Mysql
 * Permet à Intelephense de reconnaître la nouvelle classe native PDO MySQL
 * introduite en PHP 8.5 (non encore disponible dans les stubs officiels).
 *
 * @see https://wiki.php.net/rfc/pdo_driver_specific_subclasses
 */

namespace Pdo;

class Mysql extends \PDO
{
    /** @var int SSL CA attribute constant (remplace PDO::MYSQL_ATTR_SSL_CA) */
    public const ATTR_SSL_CA = 1019;

    /** @var int SSL cert attribute constant */
    public const ATTR_SSL_CERT = 1014;

    /** @var int SSL key attribute constant */
    public const ATTR_SSL_KEY = 1015;

    /** @var int SSL cipher attribute constant */
    public const ATTR_SSL_CIPHER = 1016;

    /** @var int SSL verify server cert */
    public const ATTR_SSL_VERIFY_SERVER_CERT = 1035;
}
