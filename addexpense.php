<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Body and Background */
        body {
            background-color: #141414;
            color: #FFFFFF;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        /* Page Header */
        .page-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .back-arrow {
            font-size: 24px;
            margin-right: 10px;
            cursor: pointer;
        }

        /* Form Labels */
        .label {
            font-weight: bold;
            margin-top: 15px;
        }

        /* Input Fields */
        .input-field {
            background: none;
            border: none;
            border-bottom: 1px solid #666;
            color: #FFF;
            width: 40%;
            padding: 5px;
            margin-top: 5px;
        }

        /* Expense Buttons */
        .expense-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .expense-button {
            border: 1px solid #333;
            color: #FFF;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 20px;
            background: none;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .expense-button.green-border {
            border-color: #00B389;
        }

        .expense-button.orange-border {
            border-color: #ff330a;
        }

        .expense-button:hover {
            border-color: #FFF;
        }

        /* Summary Text */
        .summary-text {
            margin-top: 20px;
            font-size: 16px;
            color: #FFF;
        }

        /* Add Expenses Button */
        .add-expenses-btn {
            border: 2px solid #00B389;
            color: #ffffff;
            font-weight: bold;
            border-radius: 20px;
            padding: 10px 20px;
            margin-top: 20px;
            background: none;
            display: block;
            width: fit-content;
        }

        /* Plus Icon */
        .plus-icon {
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }

        /* Box around FirstName */
        .name-box {
            display: inline-block;
            padding: 5px 10px;
            border: 2px solid #B3B3B3;
            border-radius: 5px;
            color: ##B3B3B3;
            font-weight: bold;
            
}

        
    </style>
</head>
<body>
<?php
    
    $dbHost = 'localhost';  
    $dbName = 'payback';
    $dbUsername = 'root';
    $dbPassword = '';

    $conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

    ?>

    <!-- Page Header -->
    <div class="page-header">
        <span class="back-arrow">&larr;</span>
        <span>Add Expense</span>
    </div>
    <br>
    <!-- Expense Form -->
    <div>
        <div class="label">With you and:
            <span class="name-box">FirstName</span>
            <span class="plus-icon">+</span>
        </div>
        <br>
        <br>
        <div class="label">Description:              
        <input type="text" class="input-field" placeholder="">
        </div>
        <br>
        <br>    
        <div class="label">Amount:   
        <input type="text" class="input-field" placeholder="">
        </div>
        <br>    
        <div class="label">How was the expense split?</div>
        <div class="expense-buttons">
            <button class="expense-button green-border">You paid and split equally</button>
            <button class="expense-button">You are owed the full amount</button>
            
        </div>
        <div class="expense-buttons">
            
            <button class="expense-button">FirstName is owed the full amount</button>
            <button class="expense-button orange-border">FirstName paid and split equally</button>
        </div>

        <div class="summary-text">
            You/FirstName owes Rs: 1200 to FirstName/You
        </div>

        <button class="add-expenses-btn">Add Expenses</button>
    </div>

</body>
</html>
