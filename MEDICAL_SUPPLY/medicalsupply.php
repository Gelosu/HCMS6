


<!-- MEDICAL SUPPLY INVENTORY -->
<section id="medical_supplies-inventory" class="section">
    <h2>Medical & Emergency Supplies Inventory</h2>
    <div class="search-and-add-container">
        <!-- Search bar container -->
        <div class="search-container">
        <input type="text" id="searchInput" onkeyup="searchTable1(this.value);" placeholder="Search for medical supplies...">
        </div>

        <!-- Button container -->
        <div class="add-button-container">
            <button onclick="openAddMedicalSupplyModal()">Add Medical Supply</button>
        </div>
    </div>

<!-- MEDICAL SUPPLY TABLE -->
<div class="table-container">
<table id="medicalSuppliesTable" >
    <thead>
        <tr>
            <th>Supply ID<div class="resizer1"></div></th>
            <th>Supply Name<div class="resizer1"></div></th>
            <th>Stock In<div class="resizer1"></div></th>
            <th>Expiration Date<div class="resizer1"></div></th>
            <th>Stock Available<div class="resizer1"></div></th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    include 'connect.php';

    
    $sql = "SELECT * FROM inv_medsup";
    $result = $conn->query($sql);

    
    if ($result->num_rows > 0) {
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["sup_id"] . "</td>"; 
            echo "<td>" . $row["prod_name"] . "</td>"; 
            echo "<td>" . $row["stck_in"] . "</td>"; 
            echo "<td>" . $row["stck_expired"] . "</td>"; 
            echo "<td>" . $row["stck_avl"] . "</td>"; 
            echo "<td class='action-icons'>";
            echo "<a onclick=\"openEditMedSupp('" . 
            $row["med_supId"] . "', '" . 
            $row["sup_id"] . "', '" . 
            $row["prod_name"] . "', '" . 
            $row["stck_in"] . "', '" . 
            $row["stck_expired"] . "', '" . 
            $row["stck_avl"] . "')\">";
       echo "<img src='edit_icon.png' alt='Edit' style='width: 20px; height: 20px;'></a>";
       
            echo "<a onclick=\"deleteMedicalSupply('" . $row["med_supId"] . "')\">";
            echo "<img src='ARCHIVE.png' alt='Delete' class='delete-btn' style='width: 20px; height: 20px;'></a>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No medical supplies found</td></tr>";
    }

    $conn->close();
    ?>
</tbody>

</table>
</div>
</section>
<!-- Modal for adding new medical supply -->
<div id="addMedicalSupplyModal" class="modal1">
    <div class="modal-content1">
        <span class="close" onclick="closeAddMedicalSupplyModal()">&times;</span>
        <h3>Add New Medical & Emergency Supply</h3>
        <form id="addMedicalSupplyForm" onsubmit="submitMedicalSupplyForm(event)">
        <label for="supplyName">Supply Id:</label>
        <input type="text" id="supplyId2" name="supplyId2" required><br><br>

            <label for="supplyName">Supply Name:</label>
            <input type="text" id="supplyName" name="supplyName" required><br><br>
            
            <label for="stockIn">Stock In:</label>
            <input type="number" id="stockIn" name="stockIn" required><br><br>
            
            
            <label for="stockExpired">Expiration Date:</label>
            <input type="date" id="stockExpired" name="stockExpired" required>

            
            <label for="stockAvailable">Stock Available:</label>
            <input type="number" id="stockAvailable" name="stockAvailable" required><br><br>
            
            <input type="submit" value="Submit">
        </form>
    </div>
</div>

<!-- Modal for editing medical supplies -->
<div id="editMedicalSupplyModal" class="modal1">
    <div class="modal-content1">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Edit Medical/Emergency Supply</h3>
        <form id="editForm" onsubmit="submitEditMedicalSupplyForm(event)">
            <input type="hidden" id="editSuppId" name="supplyId"> <!-- med_supId -->
            
            <label for="editSupplyId2">Supply ID:</label>
            <input type="text" id="editSuppId2" name="supplyId2" required><br><br>

            <label for="editSupplyName">Supply Name:</label>
            <input type="text" id="editSupplyName" name="supplyName" required><br><br>
            
            <label for="editStockIn">Stock In:</label>
            <input type="number" id="editStockIn2" name="stockIn2" required><br><br>

            <label for="editStockExp">Expired:</label>
            <input type="date" id="editStockExp2" name="stockExpired2" required><br><br>

            <label for="editStockAvail">Stock Available:</label>
            <input type="number" id="editStockAvail2" name="stockAvailable2" required><br><br>

            <input type="submit" value="Submit">
        </form>
    </div>
</div>


<button id="archiveBtn" class="btn btn-warning" style="display: none;">Archive Selected</button>