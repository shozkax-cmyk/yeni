<?php
// This script adds the missing 'hidden' column to the confessions table
require_once 'config.php';

try {
    // Check if the hidden column already exists
    $stmt = $pdo->query("DESCRIBE confessions");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('hidden', $columns)) {
        // Add the hidden column
        $pdo->exec("ALTER TABLE confessions ADD COLUMN hidden TINYINT(1) DEFAULT 0 AFTER is_approved");
        echo "✅ 'hidden' kolonu başarıyla eklendi!\n";
    } else {
        echo "ℹ️  'hidden' kolonu zaten mevcut.\n";
    }
    
    echo "✅ Veritabanı güncellemesi tamamlandı!\n";
    
} catch (PDOException $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}
?>