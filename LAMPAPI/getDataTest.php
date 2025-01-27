<?php
// Sample php for testing
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');

$data = json_encode([
    [
        'name' => 'John Doe',
        'phone' => '123-456-7890',
        'address' => '123 Main St, Springfield, IL',
        'email' => 'john.doe@example.com'
    ],
    [
        'name' => 'Jane Smith',
        'phone' => '987-654-3210',
        'address' => '456 Oak Ave, Denver, CO',
        'email' => 'jane.smith@example.com'
    ],
    [
        'name' => 'Alice Johnson',
        'phone' => '555-123-4567',
        'address' => '789 Pine Rd, Austin, TX',
        'email' => 'alice.johnson@example.com'
    ]
]);

// Encode the array into a JSON string

// Send JSON response to the client
echo $data;
?>