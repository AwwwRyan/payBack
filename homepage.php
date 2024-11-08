<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Use the session variable
$user_id = $_SESSION['user_id'];
include 'db_connect.php';

$friends = [];
$activities = [];

if ($user_id > 0) {
    // Fetch friends with balance
    $query = "
        SELECT u.user_id, u.name, b.balance 
        FROM friends f 
        JOIN users u ON f.friend_id = u.user_id 
        LEFT JOIN balances b ON b.user_id = $user_id AND b.friend_id = u.user_id
        WHERE f.user_id = $user_id
    ";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $friends[] = $row;
        }
    }

    // Fetch activities where user is a participant
    $query = "
        SELECT tp.transaction_id, tp.amount_owed, t.description, t.created_at, t.payer_id, u.name as payer_name
        FROM transactionparticipants tp
        JOIN transactions t ON tp.transaction_id = t.transaction_id
        JOIN users u ON t.payer_id = u.user_id
        WHERE tp.participant_id = $user_id
    ";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $activities[] = [
                'description' => $row['description'],
                'amount' => $row['amount_owed'],
                'created_at' => $row['created_at'],
                'type' => 'owe',
                'payer_name' => $row['payer_name'],
            ];
        }
    }

    // Fetch activities where user is the payer
    $query = "
        SELECT t.transaction_id, t.amount, t.description, t.created_at, GROUP_CONCAT(u.name SEPARATOR ', ') as participant_names
        FROM transactions t
        JOIN transactionparticipants tp ON t.transaction_id = tp.transaction_id
        JOIN users u ON tp.participant_id = u.user_id
        WHERE t.payer_id = $user_id
        GROUP BY t.transaction_id
    ";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $activities[] = [
                'description' => $row['description'],
                'amount' => $row['amount'],
                'created_at' => $row['created_at'],
                'type' => 'paid',
                'participant_names' => $row['participant_names'],
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding-top: 2rem;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding-bottom: 1rem;
            border-bottom: 1px solid #333;
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
            text-decoration: none; 

            margin-right: 20px;
        }
        .btn-add-friends {
            background-color: #121212;
            color: #fff;
            border: 1px solid #00b530;
            border-radius: 20px;
            padding: 0.5rem 1.5rem;
            margin-left: 20px;
            transition: all 0.3s ease;
        }

        .btn-add-friends:hover {
            background-color: #00b530;
            color: #121212;
            border-color: #00b530;
        }

        .friends-section, .activity-section {
            padding-top: 1.5rem;
        }

        .friends-section h2, .activity-section h2 {
            font-size: 1.25rem;
            color: #aaa;
        }

        .friend-item {
            background-color: #121212;
            border-radius: 8px;
            padding: 0.20rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            border: 1px solid #fff;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .friend-item:hover {
            transform: scale(1.005);
            background-color: #1a1a1a;
        }

        .friend-item .friend-name, 
        .friend-item .friend-status {
            padding: 0.5rem; 
        }

        .friend-item .friend-name {
            font-weight: bold;
            color: #fff;
        }

        .friend-item .friend-status {
            color: #888;
        }

        .friend-item .amount {
            font-weight: bold;
            font-size: 1rem;
        }

        .friend-item .amount, 
        .friend-item .friend-status {
            padding: 0.5rem; 
        }

        .amount-positive {
            color: #55f17a;
        }

        .amount-negative {
            color: #ff5667;
        }

        .amount-zero {
            color: #fff; /* White color for zero balance */
        }

        .activity-item {
            color: #FFFFFF;
            padding: 0.5rem 0;
            border-bottom: 1px solid #333;
            max-width: 350px;
        }

        .activity-item .activity-date {
            font-size: 0.8rem;
            color: #666;
        }

        .btn-add-expense {
            background-color: #121212;
            color: #fff;
            border: 1px solid #00b530;
            border-radius: 20px;
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .btn-add-expense:hover {
            background-color: #00b530;
            color: #121212;
            border-color: #00b530;
        }

        .friend-item-link {
            text-decoration: none; /* Remove underline from links */
            color: inherit; /* Inherit text color */
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
        <a href="homepage.php ?>" class="payback-text">PAYBACK</a>
        <a href="addfriend.php?user_id=<?php echo $user_id; ?>">
                <button class="btn btn-add-friends">Add Friends</button>
            </a>
            <a href="logout.php" class="ms-auto text-light" style="text-decoration: none;">Logout</a>
        </div>

        <div class="d-flex justify-content-between">
    <div class="friends-section col-md-7">
        <?php if (!empty($friends)): ?>
            <div class="d-flex justify-content-between align-items-center">
                <h2>Your Friends</h2>
                <a href="addexpense.php">
                    <button class="btn btn-add-expense">Add Expenses</button>
                </a>
            </div>
            <br>
            <!-- Friend Items -->
            <?php foreach ($friends as $friend): ?>
                <a href="friendpage.php?user_id=<?php echo $user_id; ?>&friend_id=<?php echo $friend['user_id']; ?>" class="friend-item-link">
                    <div class="friend-item">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <p class="friend-name"><?php echo htmlspecialchars($friend['name']); ?></p>
                                <p class="friend-status">
                                    <?php 
                                    if ($friend['balance'] > 0) {
                                        echo 'Owes you';
                                    } elseif ($friend['balance'] < 0) {
                                        echo 'You owe';
                                    } else {
                                        echo 'No one owes anything';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div>
                                <p class="amount <?php 
                                    if ($friend['balance'] > 0) {
                                        echo 'amount-positive';
                                    } elseif ($friend['balance'] < 0) {
                                        echo 'amount-negative';
                                    } else {
                                        echo 'amount-zero'; // New class for zero balance
                                    }
                                ?>">
                                    Rs. <?php echo number_format(abs($friend['balance']), 2); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Activity Section -->
    <div class="activity-section col-md-4">
        <?php if (!empty($activities)): ?>
            <h2>Activity</h2>
            <?php foreach ($activities as $activity): ?>
                <div class="activity-item">
                    <p><?php echo htmlspecialchars($activity['description']); ?></p>
                    <?php if ($activity['type'] === 'owe'): ?>
                        <p>You owe Rs. <?php echo htmlspecialchars($activity['amount']); ?> to <?php echo htmlspecialchars($activity['payer_name']); ?></p>
                    <?php elseif ($activity['type'] === 'paid'): ?>
                        <p>You paid Rs. <?php echo htmlspecialchars($activity['amount']); ?> for <?php echo htmlspecialchars($activity['participant_names']); ?></p>
                    <?php endif; ?>
                    <p class="activity-date">
                        <?php echo date('d F, Y', strtotime($activity['created_at'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

    </div>
</body>
</html>