<!DOCTYPE html>
<html lang="en">
<head>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</head>
<body></body>
</html>


<?php
include("db.php");


echo"<center><h1>Stock List</h1></center>";
$sql = "SELECT * FROM STOCK_ITEMS";
$result = mysqli_query($conn, $sql);
$num = mysqli_num_rows($result);
echo "<table border=2px solid black  class='table table-bordered table-striped'>
        <colgroup>
    <col style='width: 14.28%;'>
    <col style='width: 14.28%;'>
    <col style='width: 14.28%;'>
    <col style='width: 14.28%;'>
    <col style='width: 14.28%;'>
    <col style='width: 14.28%;'>
    <col style='width: 14.28%;'>
  </colgroup>
        <tr>        
            <th>Sr No.</th>
            <th>Material Name</th>
            <th>Material ID</th>
            <th>Qty</th>
            <th>Current Qty</th>
            <th>Loss-Damage-Used</th>
            <th>Stored At</th>
        </tr>
    </table>";
while($row = mysqli_fetch_assoc( $result )){
    // echo var_dump($row);
    echo"<table border = 2px solid black class='table table-bordered table-striped'>
    <colgroup>
        <col style='width: 14.28%;'>
        <col style='width: 14.28%;'>
        <col style='width: 14.28%;'>
        <col style='width: 14.28%;'>
        <col style='width: 14.28%;'>
        <col style='width: 14.28%;'>
        <col style='width: 14.28%;'>
    </colgroup>
    <tr>
            <td>$row[SrNo]</td>
            <td>$row[MaterialName]</td>
            <td>$row[MaterialID]</td>
            <td>$row[Quantity]</td>
            <td>$row[CurrentQuantity]</td>
            <td>$row[LossDamageUsed]</td>
            <td>$row[StoredAt]</td>
        </tr>
    </table>";
    // .$row[""]."".$row[""].
}

?>