<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the content type to JSON
header('Content-Type: application/json');

require_once 'db_connect.php';

function outputJSON($data) {
    echo json_encode($data);
    exit;
}

try {
    $mysqli = db_connect();

    // Fetch inventory transaction logs with user information
    $logsQuery = "SELECT 
                      it.id, 
                      p.name AS product_name, 
                      it.quantity_change, 
                      it.transaction_type, 
                      it.created_at, 
                      u.name AS created_by
                  FROM inventory_transactions it
                  JOIN products p ON it.product_id = p.id
                  JOIN users u ON it.created_by = u.id
                  ORDER BY it.created_at DESC";
    $logsResult = $mysqli->query($logsQuery);
    $logsData = $logsResult->fetch_all(MYSQLI_ASSOC);

    outputJSON(['success' => true, 'logs' => $logsData]);

} catch(Exception $e) {
    outputJSON(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
