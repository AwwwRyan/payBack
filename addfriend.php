<?php
include 'db_connect.php';
session_start(); // Start the session

// Fetch user_id from URL
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Handle search query
$searchTerm = isset($_POST['search_term']) ? mysqli_real_escape_string($conn, $_POST['search_term']) : '';

// Fetch all friends of the current user
$friendsQuery = "SELECT friend_id FROM Friends WHERE user_id = $user_id";
$friendsResult = mysqli_query($conn, $friendsQuery);
$friends = [];
if ($friendsResult) {
    while ($row = mysqli_fetch_assoc($friendsResult)) {
        $friends[] = $row['friend_id'];
    }
}

// Convert friends array to a comma-separated string for the SQL query
$friendsList = implode(',', $friends);

// Build the query to fetch all users, excluding friends and the current user
$query = "SELECT user_id, name FROM Users WHERE user_id != $user_id" . (count($friends) > 0 ? " AND user_id NOT IN ($friendsList)" : "");

// Add search filtering if a search term is provided
if (!empty($searchTerm)) {
    $query .= " AND name LIKE '%$searchTerm%'";
}

$result = mysqli_query($conn, $query);
$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = [
            'user_id' => $row['user_id'],
            'name' => $row['name']
        ];
    }
}

// Handle friend addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['friend_id'])) {
    $friend_id = intval($_POST['friend_id']);
    if ($user_id > 0 && $friend_id > 0) {
        $insertQuery = "INSERT INTO Friends (user_id, friend_id) VALUES ($user_id, $friend_id)";
        if (mysqli_query($conn, $insertQuery)) {
            $_SESSION['success_message'] = "Friend added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding friend: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Invalid user IDs.";
    }
}

// Display messages
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear messages after displaying
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Page</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">

  <style>
    body {
      background-color: #1a1a1a;
      color: #ffffff;
      font-family: Arial, sans-serif;
    }

    .back-arrow {
      font-size: 24px;
      color: #ffffff;
      cursor: pointer;
    }

    .search-bar {
      margin: 20px 0;
      position: relative;
    }

    .search-bar input {
      width: 100%;
      max-width: 400px;
      background-color: #2b2b2b;
      border: none;
      border-radius: 8px;
      padding: 12px 20px;
      color: #ffffff;
      outline: none;
      font-size: 16px;
    }

    .search-bar input::placeholder {
      color: #888;
    }

    .list-group-item {
      background-color: #2b2b2b;
      border: none;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 10px;
      color: #ffffff;
    }

    .list-group-item .name {
      display: flex;
      gap: 5px;
    }

    .btn-grey {
  background-color: #888; /* Grey color */
  border: none;
  color: #ffffff;
}

.btn-grey:hover {
  background-color: #5a6268; /* Slightly darker grey on hover */
}


    .list-group-item .btn {
      color: white;
      background: #3a3a3a;
      border: none;
      font-size: 18px;
      border-radius: 50%;
      padding: 0;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .flex-container {
      display: flex;
      justify-content: space-between;
    }

    .container {
      max-width: 1000px;
      margin: auto;
    }

    .payback-text {
        font-family: 'Staatliches', sans-serif;
        font-weight: 400;
        font-size: 60px;
        line-height: 75px;
        color: #fff;
        text-align: left;
        display: flex;
        align-items: center;
        margin-right: 20px;
        text-decoration: none; 
    }
  </style>
</head>
<body>

  <div class="container mt-4">
    <!-- Display success or error messages -->
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div class="d-flex align-items-center mb-3">
      <a href="homepage.php?user_id=<?php echo $user_id; ?>" class="payback-text">PAYBACK</a>
      <form class="search-bar d-flex" method="POST" action="">
  <input type="text" name="search_term" id="searchInput" placeholder="Search" value="<?php echo htmlspecialchars($searchTerm); ?>" />
  <button type="submit" class="btn btn-grey ms-2">Search</button>
</form>

    </div>

    <div class="flex-container">
      <!-- Left List Group -->
      <ul class="list-group me-2" style="flex: 1;">
        <?php foreach ($users as $user): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="name">
                    <span><?php echo htmlspecialchars($user['name']); ?></span>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="friend_id" value="<?php echo $user['user_id']; ?>">
                    <button type="submit" class="btn">+</button>
                </form>
            </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>
