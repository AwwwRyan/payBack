<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $friend_id = $_POST['friend_id'];

    // Prepare and execute the query to set balance to 0
    $stmt = $conn->prepare("UPDATE balances SET balance = 0 WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "Success";
}
?>
