<!-- MEDICAL SUPPLY ARCHIVE -->
<section id="archievelogs" class="section">
    <h2>Medical & Emergency Supplies Archive</h2>

    <!-- Dropdown for Archive Type Selection -->
    <div class="dropdown-container">
        <label for="archiveTypeSelect">Select Archive Type:</label>
        <select id="archiveTypeSelect" onchange="changeArchiveTable()">
            <option value="medicalsupply">Medical Supply Archive</option>
            <option value="medicine">Medicine Archive</option>
        </select>
    </div>

    <div class="search-and-add-container">
        <!-- Search bar container -->
        <div class="search-container">
            <input type="text" id="searchArchiveInput" onkeyup="searchTable6(this.value);" placeholder="Search archived items...">
        </div>
    </div>

    <!-- MEDICAL SUPPLY ARCHIVE TABLE -->
    <div class="table-container" id="archiveTables">
        <!-- Medical Supply Archive Table -->
        <table id="medicalSuppliesArchiveTable">
            <thead>
                <tr>
                    <th>Supply ID<div class="resizer1"></div></th>
                    <th>Supply Name<div class="resizer1"></div></th>
                    <th>Expiration Date<div class="resizer1"></div></th>
                    <th>Stock Available<div class="resizer1"></div></th>
                    <th>Status<div class="resizer1"></div></th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'connect.php';

                // Select all records from the archived medical supply table
                $sql = "SELECT * FROM a_medsup";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["supplyid"] . "</td>"; // Supply ID
                        echo "<td>" . $row["prdctname"] . "</td>"; // Product Name
                        echo "<td>" . $row["expdate"] . "</td>"; // Expiration Date
                        echo "<td>" . $row["stck_avail"] . "</td>"; // Stock Available
                        echo "<td>" . $row["status"] . "</td>"; // Status (default: Archived)
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No archived medical supplies found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>

        <!-- Medicine Archive Table -->
        <table id="medicineArchiveTable" style="display: none;">
            <thead>
                <tr>
                    <th>Medicine Number<div class="resizer1"></div></th>
                    <th>Medicine Name<div class="resizer1"></div></th>
                    <th>Description<div class="resizer1"></div></th>
                    <th>Expiration Date<div class="resizer1"></div></th>
                    <th>Stock Available<div class="resizer1"></div></th>
                    <th>Status<div class="resizer1"></div></th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'connect.php';

                // Select all records from the archived medicine table
                $sql = "SELECT * FROM a_meds";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["meds_num"] . "</td>"; // Medicine Number
                        echo "<td>" . $row["meds_name"] . "</td>"; // Medicine Name
                        echo "<td>" . $row["meds_dcrptn"] . "</td>"; // Description
                        echo "<td>" . $row["stock_exp"] . "</td>"; // Expiration Date
                        echo "<td>" . $row["stck_avail"] . "</td>"; // Stock Available
                        echo "<td>" . $row["status"] . "</td>"; // Status (default: Archived)
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No archived medicines found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</section>

<!-- JavaScript for handling dropdown change -->
<script>
function changeArchiveTable() {
    var archiveType = document.getElementById("archiveTypeSelect").value;
    var medicalSuppliesTable = document.getElementById("medicalSuppliesArchiveTable");
    var medicineTable = document.getElementById("medicineArchiveTable");

    if (archiveType === "medicalsupply") {
        medicalSuppliesTable.style.display = "table"; // Show medical supplies table
        medicineTable.style.display = "none"; // Hide medicine table
    } else {
        medicalSuppliesTable.style.display = "none"; // Hide medical supplies table
        medicineTable.style.display = "table"; // Show medicine table
    }
}

function searchTable6(inputValue) {
    var searchQuery = inputValue.toLowerCase().trim();
    
    // Get the dropdown value to determine which table is currently visible
    var archiveType = document.getElementById("archiveTypeSelect").value;
    var table;
    
    if (archiveType === "medicalsupply") {
        table = document.getElementById("medicalSuppliesArchiveTable");
    } else {
        table = document.getElementById("medicineArchiveTable");
    }

    var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var cells = row.getElementsByTagName('td');
        var rowContainsQuery = false;

        // Check each cell in the row
        for (var j = 0; j < cells.length; j++) {
            var cellValue = cells[j].textContent || cells[j].innerText; 

            // If the cell contains the search query, mark the row as found
            if (cellValue.toLowerCase().indexOf(searchQuery) > -1) {
                rowContainsQuery = true;
                break; // No need to check other cells if one matches
            }
        }

        // Show or hide the row based on the search query
        if (rowContainsQuery) {
            row.style.display = ''; // Show the row
        } else {
            row.style.display = 'none'; // Hide the row
        }
    }
}
</script>
