<?php
include '../connect.php';

header('Content-Type: application/json'); // Ensure the response is JSON

$response = array(); // Initialize response array

// Check if the connection was successful
if ($conn->connect_error) {
    $response['error'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    // Retrieve and sanitize form data
    $supplyid = $_POST['supplyId2'];
    $supplyName = $_POST['supplyName'];
    $stockIn = $_POST['stockIn'];
    $stockExpired = $_POST['stockExpired']; // This should be in YYYY-MM-DD format
    $stockAvailable = $_POST['stockAvailable'];
    
    // Validate the date format (optional, but recommended)
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $stockExpired)) {
        $response['error'] = "Invalid date format for Stock Expired.";
        echo json_encode($response);
        exit();
    }

    // Prepare and execute the insert query using prepared statements
    $stmt = $conn->prepare("INSERT INTO inv_medsup (sup_id, prod_name, stck_in, stck_out, stck_expired, stck_avl) VALUES (?, ?, ?, 0, ?, ?)");
    $stmt->bind_param("ssssi", $supplyid, $supplyName, $stockIn, $stockExpired, $stockAvailable);

    if ($stmt->execute()) {
        // Fetch updated data
        $sql = "SELECT * FROM inv_medsup";
        $result = $conn->query($sql);

        $supplies = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $supplies[] = $row;
            }
        }

        $response['success'] = true;
        $response['data'] = $supplies;
    } else {
        $response['error'] = "Error: " . $stmt->error;
    }
    
    // Close statement
    $stmt->close();
} else {
    $response['error'] = 'Invalid request method';
}

// Close connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);
?>
