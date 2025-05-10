<?php
//TODO handle casses where there is no data 
/*header("Content-Type: application/json; charset=UTF-8");
header ("Access-Control-Allow-Origin: *");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Allow-Credentials: true");*/


//ensure that the client enterept the response as json
//provide issues where the client missinterept the responce as plain text or HTML.
//what ever we outpur should be json 
//charset=UTF-8 
$server = "localhost";
$user = "root";
$pass = "";
$db = "quran";
function connect($server, $user, $pass, $db) : bool|mysqli 
{
    $conn = mysqli_connect($server, $user, $pass,$db );
    $conn->set_charset("utf8");
    if (!$conn) {
        http_response_code(500);
        die();
        //exit(); and die()are the same both are used to exit the script
    } else {
        http_response_code(200);
    }
    return $conn;
}

/*function select_db($conn, $db)
{
    $selected_db = mysqli_select_db($conn, $db);
    if (!$selected_db) {
        die("Database selection failed: " . mysqli_error($conn));
    } else {
        echo "Database selected successfully";
    }
}*/

function closet_db($conn)
{
    mysqli_close($conn);
}
// joins= combine rows from 2 or morerelated tables based on a related column between them inner, full, left, right
// full join = left join union right join , num of column must be the same 


function execute_query($conn, $query): bool|mysqli_result
{
    $result = mysqli_query($conn, $query);
    if (!$result) { 
        echo "execution failed: " ;
        //json_encode() just converts data to JSON format but does not output it unless you use echo.
        die();
    } else {
        if ($result->num_rows == 0) {
            echo "0 results";
        }
        // echo "execution success"; This causes an issue because any non-JSON output will break the JSON response.
    }
    return $result;
}

function fetch_data_table_1($result): array
{ 
    //In php we ether return or json obj or a list of json objects 
    $data= [];
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) { //each while loop will go through a row 
            $data[]=$row; //add each row to the array
        }
    }
    return $data;
    // JSON_UNESCAPED_UNICODE to prevent converting arabic to unicode 
}
/*
$result is a mysqli_result object, not a string.

echo cannot directly print an object, so it throws an error:
"Object of class mysqli_result cannot be converted to string"
*/

$conn = connect($server, $user, $pass, $db);
//tip = run the query in mysql first then copy the query here
//LEFT JOIN and RIGHT JOIN determine which table is considered the main table based on its position in the FROM clause.
/*
LEFT JOIN → Keeps all rows from the table on the left (after FROM).
RIGHT JOIN → Keeps all rows from the table on the right (before JOIN).
*/
//if any of the tables return zero rows then your INNER JOIN won't work because it requires matching rows in all tables.

?>