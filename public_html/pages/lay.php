<?php
$conn = mysqli_connect("localhost","root","","hoahuongduongphone"); 

$id = $_GET['id'];
$action = $_GET['action'];

if($action == "show"){
    $sql = "UPDATE sanpham SET hienthi = 1 WHERE id = $id";
}else{
    $sql = "UPDATE sanpham SET hienthi = 0 WHERE id = $id";
}

mysqli_query($conn, $sql);


header("Location: admin.php");
?>