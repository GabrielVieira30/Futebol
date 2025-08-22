<?php
include_once 'config/database.php';

echo "Testing database connection...\n";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "✓ Database connection successful!\n";
        
        // Check if database exists
        $stmt = $db->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'futebol_db'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Database 'futebol_db' exists\n";
            
            // Check if tables exist
            $db->exec("USE futebol_db");
            $tables = ['times', 'jogadores', 'partidas'];
            foreach ($tables as $table) {
                $stmt = $db->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    echo "✓ Table '$table' exists\n";
                } else {
                    echo "✗ Table '$table' does not exist\n";
                }
            }
        } else {
            echo "✗ Database 'futebol_db' does not exist\n";
            echo "Please run the SQL script: db/futebol_db.sql\n";
        }
    } else {
        echo "✗ Database connection failed\n";
    }
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
