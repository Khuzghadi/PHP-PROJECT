<?php
include("db.php");

$zones_result = $conn->query("SELECT Zone_Name FROM Zone");
$venues_result = $conn->query("SELECT Venue_Name FROM Venue");
$heads_result = $conn->query("SELECT Name FROM Zonal_Head");
$stock_result = $conn->query("SELECT MaterialName, MaterialID FROM stock_items");
$stock_options = [];

while ($row = $stock_result->fetch_assoc()) {
    $stock_options[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Zone Dispatch Form</title>
    <style>
        .material-group { margin-bottom: 10px; }
        .remove-btn { color: red; cursor: pointer; margin-left: 10px; }
    </style>
    <script>
    const materialDropdown = <?= json_encode(
        '<select name="material[]" required>
            <option value="">-- Select Material --</option>' .
            implode('', array_map(fn($item) => "<option value=\"{$item['MaterialName']}\">{$item['MaterialName']}</option>", $stock_options)) .
        '</select>'
    ) ?>;

    const idCodeDropdown = <?= json_encode(
        '<select name="id_code[]" required>
            <option value="">-- Select ID/Code --</option>' .
            implode('', array_map(fn($item) => "<option value=\"{$item['MaterialID']}\">{$item['MaterialID']}</option>", $stock_options)) .
        '</select>'
    ) ?>;

    function addMaterialRow() {
        const container = document.getElementById('materials');
        const div = document.createElement('div');
        div.className = 'material-group';
        div.innerHTML = `
            ${materialDropdown}
            ${idCodeDropdown}
            <input type="number" name="qty[]" placeholder="Qty" required>
            <span class="remove-btn" onclick="this.parentElement.remove()">Remove</span>
        `;
        container.appendChild(div);
    }
</script>

</head>
<body>

    <h2>Zone Dispatch Entry Form</h2>
    <form action="req_form_process.php" method="POST">


        <label>Zone Name:</label>
        <select name="zone_name" required>
            <option value="">-- Select Zone --</option>
            <?php while($row = $zones_result->fetch_assoc()): ?>
                <option value="<?= $row['Zone_Name'] ?>">
                    <?= $row['Zone_Name'] ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>


        <label>Zonal Head Name:</label>
        <select name="zonal_head_name" required>
            <option value="">-- Select Zonal Head --</option>
            <?php while($row = $heads_result->fetch_assoc()): ?>
                <option value="<?= $row['Name'] ?>">
                    <?= $row['Name'] ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Date:</label>
        <input type="date" name="date" required><br><br>

        <label>Receiver Name:</label>
        <input type="text" name="receiver_name" required><br><br>

        <label>Mobile No.:</label>
        <input type="tel" name="mobile_no" pattern="[0-9]{10}" required><br><br>

        <label>Venue Name:</label>
        <select name="venue_name" required>
            <option value="">-- Select Venue --</option>
            <?php while($row = $venues_result->fetch_assoc()): ?>
                <option value="<?= $row['Venue_Name'] ?>">
                    <?= $row['Venue_Name'] ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <h3>Materials</h3>
        <div id="materials">
            <div class="material-group">
                <select name="material[]" required>
                    <option value="">-- Select Material --</option>
                    <?php foreach ($stock_options as $item): ?>
                        <option value="<?= $item['MaterialName'] ?>"><?= $item['MaterialName'] ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="id_code[]" required>
                    <option value="">-- Select ID/Code --</option>
                    <?php foreach ($stock_options as $item): ?>
                        <option value="<?= $item['MaterialID'] ?>"><?= $item['MaterialID'] ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="number" name="qty[]" placeholder="Qty" required>
            </div>
        </div>
        <button type="button" onclick="addMaterialRow()">+ Add Material</button><br><br>

        <input type="submit" value="Submit">
    </form>

</body>
</html>

<?php
$conn->close(); 
?>
