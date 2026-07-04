<?php
$con = mysqli_connect("localhost", "root", "", "u464193275_srishrinjewels");
if (!$con) { die("Connection failed"); }

$r = mysqli_query($con, "SHOW TABLES LIKE '%config%'");
while($row = mysqli_fetch_array($r)) {
    echo $row[0] . "\n";
}
$r2 = mysqli_query($con, "SHOW TABLES LIKE '%settings%'");
while($row = mysqli_fetch_array($r2)) {
    echo $row[0] . "\n";
}
