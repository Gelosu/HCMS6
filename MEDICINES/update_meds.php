<?php
header('Content-Type: application/json');
include '../connect.php';

// Initialize response array
$response = array();

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required POST variables are set and not empty
    if (
        isset($_POST['medId'], $_POST['medNumber'], $_POST['medName'], $_POST['medDesc'], $_POST['stockIn'], $_POST['stockExp'], $_POST['stockAvail']) && 
        !empty($_POST['medId']) && 
        !empty($_POST['medNumber']) &&  // Added medNumber
        !empty($_POST['medName']) && 
        !empty($_POST['medDesc']) && 
        !empty($_POST['stockIn']) && 
        !empty($_POST['stockExp']) && 
        !empty($_POST['stockAvail'])
    ) {
        // Retrieve form data and sanitize inputs
        $medId = (int) $_POST['medId'];  // Ensure it's an integer
        $medNumber = trim($_POST['medNumber']);  // Added medNumber
        $medName = trim($_POST['medName']);
        $medDesc = trim($_POST['medDesc']);
        $stockIn = (int) $_POST['stockIn']; // Ensure it's an integer
        $stockExp = $_POST['stockExp']; // Ensure this is a valid date format
        $stockAvail = (int) $_POST['stockAvail']; // Ensure it's an integer

        // Debugging: Output received values
        error_log("Received values: medId: $medId, medNumber: $medNumber, medName: $medName, medDesc: $medDesc, stockIn: $stockIn, stockExp: $stockExp, stockAvail: $stockAvail");

        // Prepare the SQL update query with placeholders
        $sql = "UPDATE inv_meds SET 
                meds_number = ?,  
                meds_name = ?, 
                med_dscrptn = ?, 
                stock_in = ?, 
                stock_exp = ?, 
                stock_avail = ? 
                WHERE med_id = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters to the statement
            $stmt->bind_param("ssssssi", $medNumber, $medName, $medDesc, $stockIn, $stockExp, $stockAvail, $medId);

            // Execute statement and check for success
            if ($stmt->execute()) {
                // Fetch the updated data after successful update
                $result = $conn->query("SELECT * FROM inv_meds");

                if ($result) {
                    $medicines = $result->fetch_all(MYSQLI_ASSOC);

                    $response['success'] = true;
                    $response['message'] = 'Medicine updated successfully.';
                    $response['medicines'] = $medicines; // Include updated medicines data
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error fetching updated medicines list: ' . $conn->error;
                }
            } else {
                // Error during update
                $response['success'] = false;
                $response['message'] = 'Error updating record: ' . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error preparing the statement
            $response['success'] = false;
            $response['message'] = 'Error preparing statement: ' . $conn->error;
        }
    } else {
        // Required fields are missing
        $response['success'] = false;
        $response['message'] = 'Missing or empty required fields.';
    }

    // Close the database connection
    $conn->close();
} else {
    // Invalid request method
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
}

// Output JSON response
echo json_encode($response);
?>
