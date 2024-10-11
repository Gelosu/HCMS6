<?php
include '../connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Log POST data
    error_log(print_r($_POST, true));

    // Get form data from POST request
    $medSupId = $_POST['supplyId']; // The hidden field for med_supId (unique ID)
    $supId = $_POST['supplyId2']; // The input field for sup_id
    $supplyName = $_POST['supplyName'];
    $stockIn = $_POST['stockIn2'];
    $stockExpired = $_POST['stockExpired2'];
    $stockAvailable = $_POST['stockAvailable2'];

    // Initialize response array
    $response = [];

    // Prepare update query with parameter binding
    $stmt = $conn->prepare("UPDATE inv_medsup SET 
                            sup_id = ?, 
                            prod_name = ?, 
                            stck_in = ?, 
                            stck_expired = ?, 
                            stck_avl = ? 
                            WHERE med_supId = ?"); // Using med_supId for identification

    // Bind parameters (change types according to your actual data types)
    $stmt->bind_param("ssssis", $supId, $supplyName, $stockIn, $stockExpired, $stockAvailable, $medSupId);

    if ($stmt->execute()) {
        // Fetch all medical supplies after update
        $sql = "SELECT * FROM inv_medsup";
        $result = $conn->query($sql);

        $supplies = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $supplies[] = $row;
            }
        }

        // Set success response
        $response['data'] = $supplies;
        $response['success'] = true;
        $response['message'] = "Update successful.";
    } else {
        // Log detailed error for debugging
        error_log("Update failed: " . $stmt->error);
        $response['error'] = "Update failed: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    $response['error'] = 'Invalid request method';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Close the connection
$conn->close();
?>
