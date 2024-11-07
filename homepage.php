<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            justify-content: space-between;
            padding-bottom: 1rem;
            border-bottom: 1px solid #333;
        }
        .header h1 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        .header .nav-link {
            color: #bbb;
        }
        .search-bar {
            width: 100%;
            max-width: 800px; /* Increased width */
            background-color: #121212;
            color: #fff;
            border: 1px solid #121212;
            border-radius: 20px;
            padding: 0.5rem 1rem;
        }
        .btn-add-expense {
            background-color: #121212;
            color: #fff;
            border: 1px solid #00b530;
            border-radius: 20px;
            padding: 0.5rem 1.5rem;
            margin-top: 1rem; /* Added margin for gap */
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
            padding: 0.20rem; /* Decreased padding for height */
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            border: 1px solid #fff; /* White outline */
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
        .amount-positive {
            color: #55f17a;
        }
        .amount-negative {
            color: #ff5667;
        }
        .activity-item {
            color: #FFFFFF;
            padding: 0.5rem 0;
            border-bottom: 1px solid #333;
            max-width: 350px; /* Decreased width */
        }
        .activity-item .activity-date {
            font-size: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <input type="text" class="search-bar" placeholder="Search for people...">
            <h1>APP NAME</h1>
            <a href="#" class="nav-link">Contact Us</a>
        </div>

        <div class="d-flex justify-content-between">
            <!-- Friends Section -->
            <div class="friends-section col-md-7">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Your Friends</h2>
                    <button class="btn btn-add-expense">Add Expenses</button>
                </div>
                <br>
                <!-- Friend Items -->
                <div class="friend-item">
                    <div>
                        <p class="friend-name">FirstName LastName</p>
                        <p class="friend-status">Owes you</p>
                    </div>
                    <p class="amount amount-positive">Rs. 100</p>
                </div>
                <div class="friend-item">
                    <div>
                        <p class="friend-name">FirstName LastName</p>
                        <p class="friend-status">Owes you</p>
                    </div>
                    <p class="amount amount-negative">Rs. 100</p>
                </div>
                <div class="friend-item">
                    <div>
                        <p class="friend-name">FirstName LastName</p>
                        <p class="friend-status">Owes you</p>
                    </div>
                    <p class="amount amount-positive">Rs. 100</p>
                </div>
                <div class="friend-item">
                    <div>
                        <p class="friend-name">FirstName LastName</p>
                        <p class="friend-status">Owes you</p>
                    </div>
                    <p class="amount amount-negative">Rs. 100</p>
                </div>
                <div class="friend-item">
                    <div>
                        <p class="friend-name">FirstName LastName</p>
                        <p class="friend-status">Owes you</p>
                    </div>
                    <p class="amount amount-positive">Rs. 100</p>
                </div>
            </div>

            <!-- Activity Section -->
            <div class="activity-section col-md-4">
                <h2>Activity</h2>
                <div class="activity-item">
                    <p>FirstName added Title</p>
                    <p>You owe Rs. 100 to FirstName</p>
                    <p class="activity-date">23 June, 2024</p>
                </div>
                <div class="activity-item">
                    <p>FirstName paid FirstName</p>
                    <p>You get back Rs. 100</p>
                    <p class="activity-date">23 June, 2024</p>
                </div>
                <div class="activity-item">
                    <p>FirstName deleted Title</p>
                    <p>You owe Rs. 100 to FirstName</p>
                    <p class="activity-date">23 June, 2024</p>
                </div>
                <div class="activity-item">
                    <p>FirstName updated Title</p>
                    <p>You owe Rs. 200 to FirstName</p>
                    <p class="activity-date">24 June, 2024</p>
                </div>
                <div class="activity-item">
                    <p>FirstName shared Title</p>
                    <p>You get back Rs. 50</p>
                    <p class="activity-date">25 June, 2024</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>