<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Use the session variable
$user_id = $_SESSION['user_id'];

// Include the database connection file
include 'db_connect.php';

// Fetch user_id and friend_id from the URL
$friend_id = isset($_GET['friend_id']) ? $_GET['friend_id'] : null;

$friend_name = '';
$balance = 0.0;
$activities = [];

if ($user_id && $friend_id) {
    // Fetch the balance between the user and the friend
    $stmt = $conn->prepare("SELECT balance FROM balances WHERE user_id = ? AND friend_id = ?");
    $stmt->bind_param("ii", $user_id, $friend_id);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();

    // Fetch transactions where the user owes the friend
    $query = "
        SELECT tp.transaction_id, tp.amount_owed, t.description, t.created_at, u.name as payer_name
        FROM transactionparticipants tp
        JOIN transactions t ON tp.transaction_id = t.transaction_id
        JOIN users u ON t.payer_id = u.user_id
        WHERE tp.participant_id = $user_id AND t.payer_id = $friend_id
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

    // Fetch transactions where the friend owes the user
    $query = "
        SELECT tp.transaction_id, tp.amount_owed, t.description, t.created_at, u.name as participant_name
        FROM transactionparticipants tp
        JOIN transactions t ON tp.transaction_id = t.transaction_id
        JOIN users u ON tp.participant_id = u.user_id
        WHERE t.payer_id = $user_id AND tp.participant_id = $friend_id
    ";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $activities[] = [
                'description' => $row['description'],
                'amount' => $row['amount_owed'],
                'created_at' => $row['created_at'],
                'type' => 'paid',
                'participant_name' => $row['participant_name'],
            ];
        }
    }
}

if ($friend_id) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $friend_id);
    $stmt->execute();
    $stmt->bind_result($friend_name);
    $stmt->fetch();
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .activity-date {
            font-size: 0.8rem;
            color: #bbb; /* Slightly less bright for date */
        }
        .modal-confirm {
            background-color: #2c2c2c;
            color: white;
            border: 2px solid yellow;
            text-align: center; /* Center text in the modal */
        }
        .modal-body .amount {
            font-weight: bold;
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
        .balance-positive {
            color: #55f17a;
        }
        .balance-negative {
            color: #ff5667;
        }
        .balance-zero {
            color: #fff; /* White color for zero balance */
        }
    </style>
</head>
<body>
    <div class="container" style="padding-top: 20px;">
        <!-- Header -->
        <div class="d-flex align-items-center pb-3 border-bottom border-secondary">
            <div class="d-flex align-items-center">
                <a href="homepage.php" class="payback-text">PAYBACK</a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span class="header-title fs-5 fw-bold"><?php echo htmlspecialchars($friend_name); ?></span>
            </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <div class="balance fs-5 fw-bold <?php 
                if ($balance > 0) {
                    echo 'balance-positive';
                } elseif ($balance < 0) {
                    echo 'balance-negative';
                } else {
                    echo 'balance-zero'; // New class for zero balance
                }
            ?>">
                Rs. <?php echo number_format(abs($balance), 2); ?>
            </div>
        </div><br>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-start mt-2">
            <button class="btn btn-outline-warning me-2 btn-settle">Settle up</button>
            <button class="btn btn-outline-success btn-add-expense">Add Expenses</button>
        </div>
        <br><br>

        <!-- Activity Section -->
        <div class="activity-section row">
            <?php if (!empty($activities)): ?>
                <?php foreach ($activities as $activity): ?>
                    <div class="col-md-4 mb-4"> <!-- Use Bootstrap's grid system -->
                        <div class="activity-item">
                           <strong><p><?php echo htmlspecialchars($activity['description']); ?></p></strong> 
                            <?php if ($activity['type'] === 'owe'): ?>
                                <p>You owe Rs. <?php echo htmlspecialchars($activity['amount']); ?> to <?php echo htmlspecialchars($activity['payer_name']); ?></p>
                            <?php elseif ($activity['type'] === 'paid'): ?>
                                <p><?php echo htmlspecialchars($activity['participant_name']); ?> owes you Rs. <?php echo htmlspecialchars($activity['amount']); ?></p>
                            <?php endif; ?>
                            <p class="activity-date">
                                <?php echo date('d F, Y', strtotime($activity['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No activities to display.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="settleModal" tabindex="-1" aria-labelledby="settleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Center the modal vertically -->
            <div class="modal-content modal-confirm">
                <div class="modal-header">
                    <h5 class="modal-title " id="settleModalLabel">Settlement Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You paid <strong><?php echo htmlspecialchars($friend_name); ?></strong><br>
                    <span class="amount <?php echo $balance > 0 ? 'balance-positive' : 'balance-negative'; ?>">Rs. <?php echo number_format(abs($balance), 2); ?> </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.back-arrow').on('click', function() {
                alert('Going back!');
            });

            $('.btn-settle').on('click', function() {
                $('#settleModal').modal('show');
            });

            $('.btn-add-expense').on('click', function() {
                window.location.href = 'addexpense.php';
            });

            // Add this function to handle the confirm button click
            $('#settleModal .btn-dark').on('click', function() {
                $.ajax({
                    url: 'settle_balance.php', // The PHP script to handle the request
                    type: 'POST',
                    data: {
                        user_id: <?php echo $user_id; ?>,
                        friend_id: <?php echo $friend_id; ?>
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('Error settling balance.');
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>