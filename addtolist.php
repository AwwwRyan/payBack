<?php
session_start();
include 'db_connect.php'; // Include your database connection

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Fetch friends of the current user
$sql = "SELECT u.user_id, u.name 
        FROM users u 
        JOIN Friends f ON u.user_id = f.friend_id 
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Friends</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #141414;
            color: #FFF;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
        }

        /* Flex container for back arrow and search box */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Back Arrow Styling */
        .back-arrow {
            font-size: 24px;
            cursor: pointer;
        }

        .search-bar {
            position: relative;
            flex-grow: 1;
            margin-left: 20px; /* Add some spacing between the arrow and search box */
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

        .checkbox {
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #fff;
            outline: none;
            cursor: pointer;
            background-color: #2b2b2b;
            position: absolute;
            right: 20px; /* Move the checkbox to the right */
        }

        .checkbox:checked {
            background-color: #4CAF50;
            border: 2px solid #4CAF50;
        }

        /* Remove the white dot inside the checkbox when selected */
        .checkbox:checked::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
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
            color: #fff;
            position: relative;
        }

        .friend-name {
            flex-grow: 1;
        }

        /* Back Arrow with image */
        .back-arrow img {
            width: 24px;
            height: 24px;
            vertical-align: middle;
        }

    </style>
</head>
<body>

<div class="container">
    <!-- Header with back arrow and search box -->
    <div class="header-container">
        <a href="addexpense.php" class="back-arrow">
            <img src="arrow.png" alt="Back Arrow">
        </a> 
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for a friend..." />
        </div>
    </div>

    <form action="addexpense.php" method="POST">
        <ul class="list-group" id="friendList">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <li class="list-group-item">
                    <span class="friend-name"><?php echo $row['name']; ?></span>
                    <input type="checkbox" name="friends[]" class="checkbox" value="<?php echo $row['user_id']; ?>">
                </li>
            <?php } ?>
        </ul>
        <button type="submit" class="btn btn-primary mt-3">Select Friends</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        // Search functionality to filter the friend list
        $('#searchInput').on('input', function() {
            const query = $(this).val().toLowerCase();
            $('.list-group-item').each(function() {
                const name = $(this).find('.friend-name').text().toLowerCase();
                $(this).toggle(name.includes(query));
            });
        });
    });
</script>

</body>
</html>