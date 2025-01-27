<?php
// sample php for testing
// echoes the form data receives from add contact form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phoneNum']);
    $address = htmlspecialchars($_POST['address']);
    $email = htmlspecialchars($_POST['email']);

    $response = [
        'name' => $name,
        'phone' => $phone,
        'address' => $address,
        'email' => $email,
    ];

    header('Content-Type: application/json');

    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    echo json_encode(['error' => 'Invalid request'], JSON_PRETTY_PRINT);
}
?>