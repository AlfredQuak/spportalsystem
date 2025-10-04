<?php
// Ensure bootstrap is loaded even when PHPUnit runs with --no-configuration
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;
use spCore\CDatabase;

/**
 * Unit tests for spCore\CDatabase
 *
 * Hinweise:
 * - Diese Tests setzen KEINE laufende MySQL/MariaDB-Instanz voraus.
 * - Es wird vor allem das Verhalten ohne Datenbankverbindung geprüft
 *   (z. B. Rückgabewerte, Fehlermeldungen, Escaping).
 */
class CDatabaseTest extends TestCase
{
    public function testSingletonReturnsSameInstance()
    {
        $db1 = CDatabase::getInstance();
        $db2 = CDatabase::getInstance();
        $this->assertSame($db1, $db2, 'CDatabase::getInstance() muss ein Singleton sein.');
    }

    public function testGetConnectionIsFalseWhenDbUnavailable()
    {
        $db = CDatabase::getInstance();
        $conn = $db->getConnection();
        // In CI/ohne DB erwarten wir false
        $this->assertFalse($conn, 'Ohne erreichbare Datenbank sollte getConnection() false liefern.');
    }

    public function testQueryReturnsNullWithoutConnection()
    {
        $db = CDatabase::getInstance();
        $result = $db->query('SELECT 1');
        $this->assertNull($result, 'Ohne Verbindung sollte query() null zurückgeben.');
    }

    public function testGetErrorReturnsNoConnectionMessage()
    {
        $db = CDatabase::getInstance();
        $err = $db->getError();
        $this->assertIsString($err);
        $this->assertStringContainsString('No database connection', $err);
    }

    public function testCheckValueEscapingWithoutConnection()
    {
        $db = CDatabase::getInstance();
        $raw = "O'Reilly";
        $escaped = $db->checkValue($raw);

        // Ohne Verbindung fällt die Methode auf addslashes() zurück
        $this->assertSame("O\'Reilly", $escaped);
        $this->assertNotSame($raw, $escaped);
    }

    public function testImportFileDoesNotThrowWithoutConnection()
    {
        $db = CDatabase::getInstance();
        $sqlFile = __DIR__ . '/fixtures/sample.sql';
        $this->assertFileExists($sqlFile);
        // Erwartung: Kein Throwable, auch wenn keine DB-Verbindung vorhanden ist.
        $db->importFile($sqlFile);
        $this->assertTrue(true, 'importFile() sollte ohne Ausnahme durchlaufen.');
    }
}
