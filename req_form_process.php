<?php
include("db.php");


$zone_name = $conn->real_escape_string($_POST['zone_name']);
$zonal_head_name = $conn->real_escape_string($_POST['zonal_head_name']);
$date = $_POST['date'];
$receiver_name = $conn->real_escape_string($_POST['receiver_name']);
$mobile_no = $conn->real_escape_string($_POST['mobile_no']);
$venue_name = $conn->real_escape_string($_POST['venue_name']);


$dispatch_sql = "INSERT INTO Dispatch_request (Zone_Name, Zonal_Head_Name, Dispatch_Date, Receiver_Name, Mobile_No, Venue_Name) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($dispatch_sql);
$stmt->bind_param("ssssss", $zone_name, $zonal_head_name, $date, $receiver_name, $mobile_no, $venue_name);

if ($stmt->execute()) {
    $dispatch_id = $conn->insert_id; 
} else {
    die("❌ Error inserting dispatch: " . $stmt->error);
}
$stmt->close();


$material_array = $_POST['material'];  
$id_code_array = $_POST['id_code'];    
$qty_array = $_POST['qty'];

$items_sql = "INSERT INTO Dispatch_request (Dispatch_ID, Material, ID_Code, Qty) VALUES (?, ?, ?, ?)";
$item_stmt = $conn->prepare($items_sql);
if (!$item_stmt) {
    die("❌ Prepare failed for items_sql: " . $conn->error);
}


for ($i = 0; $i < count($material_array); $i++) {
    $material = $conn->real_escape_string($material_array[$i]);
    $id_code = $conn->real_escape_string($id_code_array[$i]);
    $qty = (int)$qty_array[$i];

    $item_stmt->bind_param("isii", $dispatch_id, $material, $id_code, $qty);

    if (!$item_stmt->execute()) {
        echo "❌ Error inserting item $i: " . $item_stmt->error . "<br>";
    }
}

$item_stmt->close();

echo "✅ Dispatch request and materials submitted successfully!";
?>