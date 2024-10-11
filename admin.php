<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['adusername'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user info from session
$adfirstname = $_SESSION['adfirstname'];
$adsurname = $_SESSION['adsurname'];
$healthWorker = $_SESSION['adfirstname'] . ' ' . $_SESSION['adsurname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STA. MARIA HCMS</title>
    <link rel="stylesheet" href="mamamoadmin.css">
    <link rel="stylesheet" href="style3.css">
   
</head>
<body>

<header>
    <h1>BRGY STA. MARIA HEALTH CENTER</h1>
</header>

<!-- SIDE BAR -->
<div id="sidebar">
    <div id="logo">
        <img src="mary.jpg" alt="Logo">
    </div>
    
    <p class="healthworker-info">
    <span class="healthworker-label">HEALTH WORKER:</span>
    <span class="healthworker-name"><?php echo htmlspecialchars($adfirstname . ' ' . $adsurname); ?></span>
</p>

    
    <ul>
    <li><a href="#" onclick="setActiveSection('dashboard')">Dashboard</a></li>
        <li><a href="#" onclick="setActiveSection('medical_supplies-inventory')">Medical & Emergency Supplies Inventory</a></li>
        <li><a href="#" onclick="setActiveSection('medicine-inventory')">Medicine Inventory</a></li>
        <li><a href="#" onclick="setActiveSection('archievelogs')">Archive</a></li>
        <li><a href="#" onclick="setActiveSection('patient-list')">Patient List</a></li>
        <li><a href="#" onclick="setActiveSection('patient-records')">Patient Records</a></li>
        <li><a href="#" onclick="setActiveSection('patient-appointment')">Patient Appointment</a></li>
        <li><a href="#" onclick="setActiveSection('patient-med')">Patient - Medication</a></li>

        
       
    </ul>
    <button id="logoutBtn">Logout</button>
</div>


<div id="content">
<section id="dashboard" class="section">
    <h2>DASHBOARD</h2>
    
    
    <div class="card-container">
        <div class="card">
            <h3>Total Registered Patients</h3>
            <p id="total-patients">Loading...</p>
        </div>
        <div class="card">
            <h3>Total Medicines</h3>
            <p id="total-medicines">Loading...</p>
        </div>
        <div class="card">
            <h3>Appointments Today</h3>
            <p id="appointments-today">Loading...</p>
        </div>
        <div class="card">
            <h3>Total Medical Supplies</h3>
            <p id="total-medications">Loading...</p>
        </div>
    </div>

    <div id="upcoming-events">
        <h2>Upcoming Events</h2>
        <div id="events-container" class="events-container"> <!-- Events container -->
           
        </div>
        <p id="no-events-message" style="display: none;">Events coming soon...</p>
    </div>
</section>




<?php
      
        include 'PATIENTLIST/patientlist.php';
        include 'MEDICAL_SUPPLY/medicalsupply.php';
        include 'MEDICINES/medicinelist.php';
        include 'ARCHIVES/archives.php';
        include 'APPOINTMENT/appointmentlist.php';
        include 'P_MEDICATION/pmedication.php';
?>




</div>
<script>const healthWorkerName = "<?php echo htmlspecialchars($healthWorker); ?>";</script>



<script src="functionforPATIENTLIST.js"></script>
<script src="functionforMEDICATION.js"></script>
<script src="SEARCH_FILTER.js"> </script>
<script> 



var addMedicalSupplyModal = document.getElementById("addMedicalSupplyModal"); //ADD MEDICAL SUPPLY 
    var editMedicalSupplyModal =document.getElementById("editMedicalSupplyModal") //EDIT MS

//MEDICAL SUPPLY
  // FUNCTION FOR ADDING MEDICAL SUPPLY 
function submitMedicalSupplyForm(event) {
    event.preventDefault(); 
    
    var formData = new FormData(document.getElementById('addMedicalSupplyForm'));

    fetch('MEDICAL_SUPPLY/add_medical_supply.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  // Get the raw text response
    .then(text => {
        console.log('Raw response:', text);  // Log the raw response
        try {
            var data = JSON.parse(text);  // Convert text to JSON
            console.log('Parsed JSON:', data);
            
            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                updateMedicalSupplyTable(data.data); 
                closeAddMedicalSupplyModal(); 
                updateDashboard();
            }
        } catch (error) {
            console.error('Error parsing JSON:', error);
            alert('Error: Invalid JSON response');
        }
    })
    .catch(error => console.error('Error submitting form:', error));
}

    
function updateMedicalSupplyTable(data) {
    var tableBody = document.querySelector('#medicalSuppliesTable tbody');
    tableBody.innerHTML = ''; 

    if (Array.isArray(data) && data.length > 0) {
        data.forEach(supply => {
            var row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(supply.sup_id)}</td> <!-- Assuming this is the correct property -->
                <td>${escapeHtml(supply.prod_name)}</td>
                <td>${escapeHtml(supply.stck_in)}</td>
                <td>${escapeHtml(supply.stck_expired)}</td>
                <td>${escapeHtml(supply.stck_avl)}</td>
                <td class='action-icons'>
                    <a href='#' class='edit-btn' onclick='openEditMedSupp(${supply.med_supId}, "${escapeHtml(supply.sup_id)}", "${escapeHtml(supply.prod_name)}", ${supply.stck_in}, "${supply.stck_expired}", ${supply.stck_avl})'>
                        <img src='edit_icon.png' alt='Edit' style='width: 20px; height: 20px;'>
                    </a>
                    <a href='#' class='delete-btn' onclick='deleteMedicalSupply(${supply.med_supId})'>
                        <img src='ARCHIVE.png' alt='Delete' style='width: 20px; height: 20px;'>
                    </a>
                </td>
            `;
            tableBody.appendChild(row);
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="6">No medical supplies found</td></tr>';
    }
}

// Helper function to escape HTML special characters
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

    
    // Function to escape special characters for HTML

        // Function to close the add medical supply modal
        function closeAddMedicalSupplyModal() {
            if (addMedicalSupplyModal) {
                addMedicalSupplyModal.style.display = 'none';
            }
        }
    
        // Function to open the add medical supply modal
        function openAddMedicalSupplyModal() {
            if (addMedicalSupplyModal) {
                addMedicalSupplyModal.style.display = 'block';
            }
        }
    
    
        function openEditMedSupp(medSupId, supplyId, supplyName, stockIn, stockExpired, stockAvailable) {

    // Populate the form fields in the edit modal
    document.getElementById('editSuppId').value = medSupId; // Hidden field for med_supId
    document.getElementById('editSuppId2').value = supplyId; // Supply ID field
    document.getElementById('editSupplyName').value = supplyName; // Supply Name
    document.getElementById('editStockIn2').value = stockIn; // Stock In
    document.getElementById('editStockExp2').value = stockExpired; // Expiration Date
    document.getElementById('editStockAvail2').value = stockAvailable; // Stock Available

    // Show the modal
    document.getElementById('editMedicalSupplyModal').style.display = 'block';
    console.log('Edit modal displayed.');
}



    
    function closeEditModal() {
        var modal = document.getElementById("editMedicalSupplyModal");
        if (modal) {
            modal.style.display = 'none';
        }
    }
    
    function submitEditMedicalSupplyForm(event) {
    event.preventDefault(); 

    var formData = new FormData(document.getElementById('editForm'));

    fetch('MEDICAL_SUPPLY/update_supply.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  // Get the raw text response
    .then(text => {
        console.log('Raw response:', text); // Log the raw response text
        
        // Parse the text as JSON
        try {
            const data = JSON.parse(text);  // Parse JSON
            console.log('Parsed Success:', data.data);
            
            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                updateMedicalSupplyTable(data.data); 
                closeEditModal(); 
                updateDashboard();
            }
        } catch (e) {
            console.error('Error parsing JSON:', e);
            alert('Failed to parse response as JSON.');
        }
    })
    .catch(error => console.error('Error submitting form:', error));
}

document.getElementById('editForm').addEventListener('submit', submitEditMedicalSupplyForm);

    
    // Function to handle delete MS
    function deleteMedicalSupply(medSupId) {
        if (confirm('Are you sure you want to archive this supply?')) {
            fetch('MEDICAL_SUPPLY/delete_supply.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    medSupId: medSupId
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    updateMedicalSupplyTable(data.supplies);
                    updateDashboard();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
    


    document.querySelectorAll('#medicalSuppliesTable th .resizer1').forEach(resizer => {
        let startX, startWidth;
    
        resizer.addEventListener('mousedown', e => {
            startX = e.clientX;
            startWidth = resizer.parentElement.offsetWidth;
            document.addEventListener('mousemove', handleMouseMove);
            document.addEventListener('mouseup', () => {
                document.removeEventListener('mousemove', handleMouseMove);
            });
        });
    
        function handleMouseMove(e) {
            const newWidth = startWidth + (e.clientX - startX);
            resizer.parentElement.style.width = `${newWidth}px`;
            const index = Array.from(resizer.parentElement.parentElement.children).indexOf(resizer.parentElement);
            Array.from(resizer.parentElement.parentElement.parentElement.querySelectorAll('tbody tr')).forEach(row => {
                row.children[index].style.width = `${newWidth}px`;
            });
        }
    });
    

//MEDICINES
var addMedicineModal = document.getElementById("addMedicineModal"); //ADD MEDICINE
    var editMedicineModal =document.getElementById("editMedicineModal") //EDIT MEDICINE

    //MEDICINE

    
    // FUNCTION FOR ADDING MEDICINE
    function submitMedicineForm(event) {
        event.preventDefault(); // Prevent the default form submission behavior
    
        // Get the form data from the form with ID 'addmedicine'
        var formData = new FormData(document.getElementById('addmedicine'));
    
        // Send a POST request to the 'add_meds.php' endpoint with the form data
        fetch('MEDICINES/add_meds.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Ensure the response is in JSON format
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            console.log('Success:', data);
    
            // Check if there is an error in the response
            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                // Update the medicine table with the new data
                updateMedicineTable(data.data);
                
                // Close the modal form after successful submission
                closeAddMedicineModal();
                updateDashboard();
            }
        })
        .catch(error => {
            console.error('Error submitting form:', error);
            alert('Error submitting form: ' + error.message); // Provide feedback to the user
        });
    }
    
    // Function to update the medicine table with new data
    function updateMedicineTable(medicines) {
        var tableBody = document.querySelector('#medTable tbody');
        tableBody.innerHTML = ''; // Clear existing table rows
    
        if (medicines.length > 0) {
            medicines.forEach(med => {
                var row = document.createElement('tr');
                
                // Format the expiration date to "September 5, 2026"
                var expirationDate = new Date(med.stock_exp);
                var options = { year: 'numeric', month: 'long', day: 'numeric' };
                var formattedExpirationDate = expirationDate.toLocaleDateString('en-US', options);
    
                row.innerHTML = `
                    <td>${med.meds_number}</td>
                    <td>${med.meds_name}</td>
                    <td>${med.med_dscrptn}</td>
                    <td>${med.stock_in}</td>
                    <td>${formattedExpirationDate}</td> <!-- Use formatted expiration date -->
                    <td>${med.stock_avail}</td>
                    <td class='action-icons'>
                        <a href='#' class='edit-btn' onclick="openEditMed(
                            '${med.med_id}', 
                            '${med.meds_number}', 
                            '${med.meds_name}', 
                            '${med.med_dscrptn}', 
                            ${med.stock_in}, 
                            '${med.stock_exp}', 
                            ${med.stock_avail}
                        )">
                            <img src='edit_icon.png' alt='Edit' style='width: 20px; height: 20px;'>
                        </a>
                        <a href='#' class='delete-btn' onclick="deleteMedicine(${med.med_id})">
                            <img src='ARCHIVE.png' alt='Delete' style='width: 20px; height: 20px;'>
                        </a>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="8">No medicines found</td></tr>'; // Adjust colspan to match the number of columns
        }
    }
    

    



function closeAddMedicineModal() {
    if (addMedicineModal) {
        addMedicineModal.style.display = 'none';
    }
}

function openAddMedicineModal() {
    if (addMedicineModal) {
        addMedicineModal.style.display = 'block'; 
    }
}

//Update MEds
// Update Medicine
function openEditMed(medId, medNumber, medName, medDesc, stockIn, stockExp, stockAvailable) {
    // Log each parameter to see what's being passed to the function


    // Populate the form fields in the edit modal
    document.getElementById('editMedId').value = medId;
    document.getElementById('editMedNumber').value = medNumber; // Added this line
    document.getElementById('editMedName').value = medName;
    document.getElementById('editMedDesc').value = medDesc;
    document.getElementById('editStockIn').value = stockIn;
    document.getElementById('editStockExp').value = stockExp; // Ensure this is a date format
    document.getElementById('editStockAvail').value = stockAvailable;

    // Show the modal
    console.log('Edit modal displayed.'); // Confirm modal is being displayed
    document.getElementById('editMedicineModal').style.display = 'block';
}



// Function to close the edit medicine modal
function closeEditMedModal() {
    var modal = document.getElementById("editMedicineModal");
    if (modal) {
        modal.style.display = 'none';
    }
}

// Function to submit the edit form data asynchronously
function submitEditMedicineForm(event) {
    event.preventDefault();  // Prevent form from reloading the page

    var formData = new FormData(document.getElementById('editForm2'));  

    fetch('MEDICINES/update_meds.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())  
    .then(data => {
        console.log('Success:', data);
        if (data.error) {
            alert('Error: ' + data.error);  
        } else {
            // Update table with the correct data field
            updateMedicineTable(data.medicines); 
            closeEditMedModal();  
            updateDashboard();
        }
    })
    .catch(error => console.error('Error submitting form:', error));
}

document.getElementById('editForm2').addEventListener('submit', submitEditMedicineForm);




// Function to handle delete medicine
function deleteMedicine(medId) {
    if (confirm('Are you sure you want to delete this item?')) {
        fetch('MEDICINES/delete_meds.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'medId': medId
            })
        })
        .then(response => {
            // Check if the response is in JSON format
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('Unexpected content type: ' + contentType);
            }
        })
        .then(data => {
            if (data.success) {
                updateMedicineTable(data.medicines); 
                updateDashboard();
                document.querySelector(`#medRow${medId}`).remove(); 
               
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error deleting record:', error));
    }
}


document.querySelectorAll('#medTable th .resizer2').forEach(resizer => {
    let startX, startWidth;

    resizer.addEventListener('mousedown', e => {
        startX = e.clientX;
        startWidth = resizer.parentElement.offsetWidth;
        document.addEventListener('mousemove', handleMouseMove);
        document.addEventListener('mouseup', () => {
            document.removeEventListener('mousemove', handleMouseMove);
        });
    });

    function handleMouseMove(e) {
        const newWidth = startWidth + (e.clientX - startX);
        resizer.parentElement.style.width = `${newWidth}px`;
        const index = Array.from(resizer.parentElement.parentElement.children).indexOf(resizer.parentElement);
        Array.from(resizer.parentElement.parentElement.parentElement.querySelectorAll('tbody tr')).forEach(row => {
            row.children[index].style.width = `${newWidth}px`;
        });
    }
});

  // Function to set and activate the desired section based on navigation clicks
function setActiveSection(sectionId) {
    window.location.hash = sectionId;
    console.log("section id: ", sectionId)  // Set URL hash
    toggleSection(sectionId);  // Show the selected section
    updateDashboard()
}

// Function to toggle visibility of sections
function toggleSection(sectionId) {
    var sections = document.querySelectorAll('.section');
    sections.forEach(function(section) {
        if (section.id === sectionId) {
            section.style.display = 'block';  // Show the selected section
        } else {
            section.style.display = 'none';  // Hide other sections
        }
    });
}

// Logout
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("logoutBtn").addEventListener("click", function() {
        window.location.href = "logout.php";
    });
});

// When the page loads, show the appropriate section based on the URL hash
window.onload = function() {
    var hash = window.location.hash.substring(1);  // Get hash value
    if (hash) {
        // Show the section corresponding to the hash
        toggleSection(hash);
    } else {
        // Default to 'dashboard' section if no hash is present
        setActiveSection('dashboard');
    }
};


//MEDICINE LIST
function fetchMedicines() {
    fetch('MEDICINES/fetch_medicine.php') // Adjust the path as necessary
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                console.log(result.data)
                updateMedicineTable(result.data); // Call the function to update the table
            } else {
                console.error('Error fetching medicines:', result.message);
            }
        })
        .catch(error => console.error('Error:', error));
}


//DASHBOARD
// DASHBOARD
function updateDashboard() {
    // Fetch dashboard counts
    fetch('DASHBOARD/get_dashboard_counts.php')
        .then(response => {
            console.log('Raw Dashboard Counts Response:', response);
            return response.text(); // Fetch the raw text response
        })
        .then(text => {
            console.log('Raw Response Text:', text); // Log the raw text for debugging
            
            // Parse the raw text into a JSON object
            const data = JSON.parse(text);
            
            // Update the counts on the dashboard
            document.getElementById('total-patients').textContent = data.total_patients || 0;
            document.getElementById('total-medicines').textContent = data.total_meds || 0;
            document.getElementById('appointments-today').textContent = data.total_appointments || 0;
            document.getElementById('total-medications').textContent = data.total_medications || 0;

            // Fetch upcoming events
            return fetch('DASHBOARD/get_upcoming_events.php'); // Add this line
        })
        .then(response => {
            console.log('Raw Upcoming Events Response:', response);
            return response.text(); // Fetch the raw text response for events
        })
       // Inside the then block after fetching upcoming events
.then(text => {
    console.log('Upcoming Events Raw Response Text:', text); // Log the raw text for events

    // Parse the JSON response
    const events = JSON.parse(text);
    
    const eventsContainer = document.getElementById('events-container');
    eventsContainer.innerHTML = '';  // Clear previous events

    console.log('Events found:', events); // Log the events array

    if (events.length > 0) {
        events.forEach(event => {
            const eventCard = document.createElement('div');
            eventCard.className = 'event-card';
            eventCard.style.border = '1px solid #ccc';
            eventCard.style.borderRadius = '5px';
            eventCard.style.padding = '10px';
            eventCard.style.margin = '10px';
            eventCard.style.width = '300px';
            eventCard.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.1)';
            
            eventCard.innerHTML = `
                <h4>${event.event_name}</h4>
                <p><strong>Date:</strong> ${new Date(event.datetime).toLocaleString()}</p>
            `;
            
            eventsContainer.appendChild(eventCard); // Append the event card to the container
            console.log('Event card appended:', eventCard); // Log appended card
        });
    } else {
        eventsContainer.innerHTML = '<p id="no-events-message">Events coming soon...</p>'; // Show message if no events
    }
})

        .catch(error => {
            console.error('Error fetching dashboard data:', error);
        });
}

updateDashboard()






//APPOINTMENT 

// Function to open the Add Appointment modal
function openAddAppointmentModal() {
    // Fetch patient names and populate dropdown
    fetch('APPOINTMENT/fetch_patient.php')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                console.log("check result: ", result.data);
                populatePatientDropdown2(result.data);
                // Set the health worker's name
                document.getElementById('healthWorker').value = healthWorkerName;
                document.getElementById('addAppointmentModal').style.display = 'block';
            } else {
                alert('Error fetching patients: ' + result.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Function to populate the patient dropdown
function populatePatientDropdown2(patients) {
    var dropdown = document.getElementById('patientName');
    dropdown.innerHTML = ''; // Clear existing options

    var defaultOption = document.createElement('option');
    defaultOption.text = 'Select a patient';
    defaultOption.value = '';
    dropdown.add(defaultOption);

    // Iterate over the list of patient names
    patients.forEach(patient => {
        var option = document.createElement('option');
        option.text = patient; // Use the patient name directly
        option.value = patient; // Use the patient name directly as the value
        dropdown.add(option);
    });
}

// Function to close the Add Appointment modal
function closeAddAppointmentModal() {
    document.getElementById('addAppointmentModal').style.display = 'none';
}

// Function to handle form submission for adding an appointment
function submitAddAppointmentForm(event) {
    event.preventDefault();

    // Append health worker to form data
    var formData = new FormData(document.getElementById('addAppointmentForm'));
    formData.append('healthWorker', healthWorkerName);

    fetch('APPOINTMENT/add_appointment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            closeAddAppointmentModal();
            updateAppointmentTable(result.data);
            updateDashboard(); // Refresh the table with the updated data
        } else {
            alert('Error: ' + result.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Function to format date and time
function formatDateTime(datetime) {
    const date = new Date(datetime);
    // Format date to "Month Day, Year Time AM/PM"
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    };
    return date.toLocaleString('en-US', options);
}

// Function to update the appointment table
function updateAppointmentTable(appointments) {
    var tableBody = document.querySelector('#patient-appointment table tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    appointments.forEach(appointment => {
        // Format datetime before displaying
        const formattedDateTime = formatDateTime(appointment.datetime);

        var row = document.createElement('tr');
        row.innerHTML = `
            <td>${appointment.p_name}</td>
            <td>${appointment.p_purpose}</td>
            <td>${formattedDateTime}</td>
            <td>${appointment.a_healthworker}</td>
            <td>
                <a href='#' class='edit-btn' onclick="openEditAppointmentModal(
                    '${appointment.id}',
                    '${appointment.p_name}',
                    '${appointment.p_purpose}',
                    '${appointment.datetime}',
                    '${appointment.a_healthworker}'
                )">
                    <img src='edit_icon.png' alt='Edit' style='width: 20px; height: 20px;'>
                </a>
                <a href='#' class='delete-btn' onclick="deleteAppointment('${appointment.id}')">
                    <img src='delete_icon.png' alt='Delete' style='width: 20px; height: 20px;'>
                </a>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Function to open the Edit Appointment modal
function openEditAppointmentModal(id, patientName, purpose, datetime, healthWorker) {
    console.log(id, patientName, purpose, datetime, healthWorker);

    document.getElementById('editAppointmentId').value = id;
    document.getElementById('editPatientName').value = patientName;
    document.getElementById('editPurpose').value = purpose;

    // Convert datetime to 'YYYY-MM-DDTHH:MM' format
    const formattedDateTime = new Date(datetime).toISOString().slice(0, 16);
    document.getElementById('editAppointmentDateTime').value = formattedDateTime;

    document.getElementById('editHealthWorker').value = healthWorker;
    document.getElementById('editAppointmentModal').style.display = 'block';
}

// Function to close the modal
function closeEditAppointmentModal() {
    document.getElementById('editAppointmentModal').style.display = 'none';
}

// Function to handle the form submission for editing an appointment
function submitEditAppointmentForm(event) {
    event.preventDefault(); // Prevent the default form submission

    console.log("Submitting form...");

    const id = document.getElementById('editAppointmentId').value;
    const patientName = document.getElementById('editPatientName').value;
    const purpose = document.getElementById('editPurpose').value;
    const appointmentDateTime = document.getElementById('editAppointmentDateTime').value;
    const healthWorker = document.getElementById('editHealthWorker').value;

    console.log("Form data:", { id, patientName, purpose, appointmentDateTime, healthWorker });

    const xhr = new XMLHttpRequest();
    xhr.open("GET", `/HCMS/APPOINTMENT/edit_appointment.php?editAppointmentId=${encodeURIComponent(id)}&editPatientName=${encodeURIComponent(patientName)}&editPurpose=${encodeURIComponent(purpose)}&editAppointmentDateTime=${encodeURIComponent(appointmentDateTime)}&editHealthWorker=${encodeURIComponent(healthWorker)}`, true);

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            console.log("Response:", xhr.responseText);
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                updateAppointmentTable(response.appointments);
                updateDashboard();
                alert(response.message);
                document.getElementById('editAppointmentModal').style.display = 'none';
            } else {
                alert(response.message);
            }
        } else {
            alert('Error: ' + xhr.statusText);
        }
    };

    xhr.onerror = function() {
        alert('Request failed.');
    };

    xhr.send();
}

// Delete function
function deleteAppointment(appointmentId) {
    if (confirm('Are you sure you want to delete this appointment?')) {
        fetch('APPOINTMENT/delete_appointment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'appointmentId': appointmentId
            })
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('Unexpected content type: ' + contentType);
            }
        })
        .then(data => {
            if (data.success) {
                updateAppointmentTable(data.appointments); // Refresh the table
                updateDashboard();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error deleting record:', error));
    }
}



</script>
<footer>
    <p>&copy; 2024 BRGY STA. MARIA HEALTH CENTER. All rights reserved.</p>
</footer>

</body>
</html>
