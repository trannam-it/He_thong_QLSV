<!-- <?php
// require '../config/config.php';

// $query = "select * from student";

//  if (!empty($_GET["searchBox"])) {
//     $search = $_GET["searchBox"];
//     $query .= " WHERE name LIKE '%$search%'";
// }

// $result = mysqli_query($conn, $query);

// if (!$result) {
//     die("Error loading Data" . mysqli_errno($conn));
// }


require '../config/config.php';

$query = "
    SELECT 
        student_id AS id,
        CONCAT(last_name, ' ', first_name) AS name,
        email,
        phone
    FROM students
";

if (!empty($_GET["searchBox"])) {
    $search = "%" . $_GET["searchBox"] . "%";
    $stmt = $conn->prepare($query . " WHERE CONCAT(last_name,' ',first_name) LIKE ?");
    $stmt->bind_param("s", $search);
} else {
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die(mysqli_error($conn));
}

?> -->