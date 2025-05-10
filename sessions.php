<?php
include_once './connect.php';
include_once './query.php';
include_once './cors.php';

$result = execute_query($conn, $query4);
//TODO echo the result objects 
//TODO what is the diffrence between br and n
$data = fetch_data_table_1($result);
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
mysqli_free_result($result); //releases the memory that was allocated for a query result.
closet_db($conn); //close db connection when ur done 
?>