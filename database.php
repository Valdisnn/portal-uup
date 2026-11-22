<?php
declare(strict_types=1);

function portalDatabase(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $cfg = require __DIR__ . '/config.php';

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $cfg['host'],
        (int) $cfg['port'],
        $cfg['dbname']
    );

    try {
        $pdo = new PDO($dsn, (string) $cfg['user'], (string) $cfg['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);

        $check = $pdo->query("SHOW TABLES LIKE 'profile'");
        if (!$check || !$check->fetchColumn()) {
            throw new RuntimeException(
                "База '{$cfg['dbname']}' существует, но таблица profile не найдена. Импортируйте schema.sql."
            );
        }

        return $pdo;
    } catch (Throwable $e) {
        $pdo = null;
        throw new RuntimeException('Не удалось подключиться к БД: ' . $e->getMessage(), 0, $e);
    }
}

function saveParts(PDO $db, int $attachmentId, string $bytes): void
{
    $chunkSize = 512 * 1024;
    $partNo = 1;
    $length = strlen($bytes);

    for ($offset = 0; $offset < $length; $offset += $chunkSize) {
        $chunk = substr($bytes, $offset, $chunkSize);

        $stmt = $db->prepare(
            'INSERT INTO attachment_part (attachment_id, part_no, content) VALUES (?, ?, ?)'
        );
        $stmt->bindValue(1, $attachmentId, PDO::PARAM_INT);
        $stmt->bindValue(2, $partNo, PDO::PARAM_INT);
        $stmt->bindValue(3, $chunk, PDO::PARAM_LOB);
        $stmt->execute();

        $partNo++;
    }
}