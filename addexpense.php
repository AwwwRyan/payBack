<?php
session_start();
include 'db_connect.php'; // Include your database connection

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;


// Handle selected friends (coming from addtolist.php)
$selected_friends = isset($_POST['friends']) ? $_POST['friends'] : [];

// Fetch the names of the selected friends
$friend_names = [];
if (!empty($selected_friends)) {
    $placeholders = implode(',', array_fill(0, count($selected_friends), '?'));
    $stmt = $conn->prepare("SELECT name FROM users WHERE user_id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($selected_friends)), ...$selected_friends);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $friend_names[] = $row['name'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['description'], $_POST['amount'], $_POST['split_option'])) {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $split_option = $_POST['split_option'];
    $selected_names = isset($_POST['selected_names']) ? explode(', ', $_POST['selected_names']) : [];

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #141414;
            color: #FFFFFF;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

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

        .label {
            font-weight: bold;
            margin-top: 15px;
        }

        .input-field {
            background: none;
            border: none;
            border-bottom: 1px solid #666;
            color: #FFF;
            width: 40%;
            padding: 5px;
            margin-top: 5px;
        }

        .friend-names {
            display: inline-flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .name-box {
            display: inline-block;
            padding: 5px 10px;
            border: 2px solid #B3B3B3;
            border-radius: 5px;
            color: #B3B3B3;
            font-weight: bold;
            cursor: pointer;
        }

        .name-box.selected {
            background-color: #00B389;
            color: #fff;
        }

        .split-option {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .expense-button {
            border: 1px solid #fff;
            color: #FFF;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 20px;
            background: none;
            cursor: pointer;
            transition: border-color 0.3s, color 0.3s;
        }

        .expense-button.selected {
            border-color: #00B389;
            color: #00B389;
        }

        .expense-button:hover {
            border-color: #FFF;
        }

        .summary-text {
            margin-top: 20px;
            font-size: 16px;
            color: #FFF;
        }

        .add-expenses-btn, .calculate-btn {
            border: 2px solid #00B389;
            color: #ffffff;
            font-weight: bold;
            border-radius: 20px;
            padding: 10px 20px;
            margin-top: 20px;
            background: none;
            display: inline-block;
        }

        .plus-icon {
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }

        /* Disabled state */
        .disabled {
            pointer-events: none;
            opacity: 0.5;
        }

        .error {
            color: #FF0000;
            margin-left: 10px;
        }
    </style>
    <script>
        function validateForm() {
            let isValid = true;
            const description = document.forms["expenseForm"]["description"].value;
            const amount = document.forms["expenseForm"]["amount"].value;
            const descriptionError = document.getElementById("descriptionError");
            const amountError = document.getElementById("amountError");

            descriptionError.textContent = "";
            amountError.textContent = "";

            if (description.trim() === "") {
                descriptionError.textContent = "Description is required.";
                isValid = false;
            }
            if (isNaN(amount) || amount.trim() === "") {
                amountError.textContent = "Amount must be a number.";
                isValid = false;
            }

            return isValid;
        }

        function selectPaidBy(element, name) {
            // Deselect all other options
            const nameBoxes = document.querySelectorAll('.friend-names .name-box');
            nameBoxes.forEach(box => box.classList.remove('selected'));

            // Select the clicked option
            element.classList.add('selected');

            // Update the hidden input for the payer
            document.getElementById('paid_by').value = name;
        }
    </script>
</head>
<body>

<!-- Page Header -->
<div class="page-header">
    <a href="homepage.php">
        <img src="arrow.png" alt="Back" class="back-arrow">
    </a>
    <span>Add Expense</span>
</div>
<br>

<!-- Expense Form -->
<div>
    <div class="label">With you and:
        <div class="friend-names">
            <?php foreach ($friend_names as $friend_name): ?>
                <div class="name-box disabled" onclick="selectName(this, '<?php echo htmlspecialchars($friend_name); ?>')">
                    <?php echo htmlspecialchars($friend_name); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="addtolist.php">
            <span class="plus-icon">+</span>
        </a>
    </div>
    <br>

    <form name="expenseForm" method="POST" action="" onsubmit="return validateForm()">
        <!-- Description Input -->
        <div class="label">Description:
            <input type="text" class="input-field" name="description" >
            <span class="error" id="descriptionError"></span>
        </div>
        <br><br>

        <!-- Amount Input -->
        <div class="label">Amount:
            <input type="text" class="input-field" name="amount" id="amountInput">
            <span class="error" id="amountError"></span>
        </div>
        <br>

        <!-- Paid By Section -->
        <div class="label">Paid by:
            <div class="friend-names">
                <div class="name-box" onclick="selectPaidBy(this, 'Me')">Me</div>
                <?php foreach ($friend_names as $friend_name): ?>
                    <div class="name-box" onclick="selectPaidBy(this, '<?php echo htmlspecialchars($friend_name); ?>')">
                        <?php echo htmlspecialchars($friend_name); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Split option -->
        <div class="label">How would you like to split the expense?</div>
<div class="split-option">
    <button type="button" class="expense-button" onclick="selectSplitOption(this, 'equal')">Equally</button>
    <button type="button" class="expense-button" onclick="selectSplitOption(this, 'individual')">By Individual</button>
    <span class="error" id="splitError"></span>
</div>



<!-- Names to Split Section -->
<div class="label">Names to Split:</div>
<div class="friend-names" id="namesToSplit">
    <div class="name-box" onclick="selectNameToSplit(this, 'You')">You</div>
    <?php foreach ($friend_names as $friend_name): ?>
        <div class="name-box" onclick="selectNameToSplit(this, '<?php echo htmlspecialchars($friend_name); ?>')">
            <?php echo htmlspecialchars($friend_name); ?>
        </div>
    <?php endforeach; ?>
</div>
<!-- Individual Amount Inputs -->
<div id="individualAmounts" style="display: none;">
    <div class="label">Enter amounts for each participant:</div>
    <div id="individualAmountInputs"></div>
</div>

        <div class="summary-text">
            <!-- Display the owed amount dynamically here based on user selection -->
            <span id="splitSummary"></span>
        </div>

        <!-- Hidden inputs for the form -->
        <input type="hidden" name="split_option" id="split_option">
        <input type="hidden" name="paid_by" id="paid_by" value="<?php echo $_SESSION['user_id']; ?>">
        <input type="hidden" name="selected_names" id="selected_names">
        
        <button type="button" class="add-expenses-btn" onclick="calculateAndSubmitForm()">Add Expenses</button>
    </form>
</div>

<script>
    let splitOption = '';
    let selectedNames = []; // Array to hold selected participant names for individual split

    function validateForm() {
        let isValid = true;
        const description = document.forms["expenseForm"]["description"].value.trim();
        const amount = document.forms["expenseForm"]["amount"].value.trim();
        const splitOption = document.getElementById('split_option').value;
        const descriptionError = document.getElementById("descriptionError");
        const amountError = document.getElementById("amountError");
        const splitError = document.getElementById("splitError");

        // Clear previous error messages
        descriptionError.textContent = "";
        amountError.textContent = "";
        splitError.textContent = "";

        // Validate description
        if (description === "") {
            descriptionError.textContent = "Description is required.";
            isValid = false;
        }

        // Validate amount
        if (isNaN(amount) || amount === "") {
            amountError.textContent = "Amount must be a number.";
            isValid = false;
        }

        // Validate split option selection
        if (!splitOption) {
            splitError.textContent = "Please select a split option.";
            isValid = false;
        }

        return isValid;
    }

    function selectSplitOption(button, value) {
        const buttons = document.querySelectorAll('.expense-button');
        buttons.forEach(btn => btn.classList.remove('selected'));
        button.classList.add('selected');
        splitOption = value;
        document.getElementById('split_option').value = value;

        const namesToSplit = document.getElementById('namesToSplit');

        if (value === 'equal') {
            namesToSplit.classList.add('disabled');
            selectedNames = []; // Clear selection if switching to equal
        } else {
            namesToSplit.classList.remove('disabled');
        }
    }

    function selectNameToSplit(element, name) {
        if (splitOption !== 'individual') return; // Only allow selection in individual split

        // Toggle selection state for the clicked name
        element.classList.toggle('selected');
        if (element.classList.contains('selected')) {
            selectedNames.push(name);
        } else {
            selectedNames = selectedNames.filter(n => n !== name);
        }

        // Update the hidden input for selected names
        document.getElementById('selected_names').value = selectedNames.join(", ");
    }

    function calculateAndSubmitForm() {
        if (!validateForm()) return;

        const amount = parseFloat(document.getElementById('amountInput').value);
        const description = document.forms["expenseForm"]["description"].value.trim();
        let payerId = document.getElementById('paid_by').value;
        const splitOption = document.getElementById('split_option').value;
        const splitSummary = document.getElementById("splitSummary");

        // Map "Me" to the actual user ID
        if (payerId === 'Me') {
            payerId = <?php echo json_encode($_SESSION['user_id']); ?>;
        }

        let summaryText = "";
        let participantAmounts = {};

        if (splitOption === 'equal') {
            const totalParticipants = <?php echo count($friend_names) + 1; ?>; // Including the payer
            const perPersonAmount = amount / totalParticipants;
            summaryText = "Each person owe Rs: " + perPersonAmount.toFixed(2);

            // Do not include the payer in the participant amounts
             participantAmounts[payerId] = perPersonAmount;
            summaryText += `<br>You owe Rs: ${perPersonAmount.toFixed(2)}`;

            <?php foreach ($friend_names as $friend_name): ?>
                participantAmounts["<?php echo $friend_name; ?>"] = perPersonAmount;
                summaryText += `<br><?php echo $friend_name; ?> owe Rs: ${perPersonAmount.toFixed(2)}`;
            <?php endforeach; ?>
        } else if (splitOption === 'individual') {
            const totalSelected = selectedNames.length;
            if (totalSelected > 0) {
                const perPersonAmount = amount / totalSelected;
                summaryText = "Selected participants owe Rs: " + perPersonAmount.toFixed(2) + " each";

                selectedNames.forEach(name => {
                    if (name !== 'Me' && name !== payerId) { 
                        participantAmounts[name] = perPersonAmount;
                        summaryText += `<br>${name} owe Rs: ${perPersonAmount.toFixed(2)}`;
                    }
                });
            } else {
                summaryText = "No participants selected for individual split.";
            }
        }

        splitSummary.innerHTML = summaryText;

        console.log({
            payer_id: payerId,
            description: description,
            amount: amount,
            split_option: splitOption,
            participantAmounts: participantAmounts
        });

    }
</script>



</body>
</html>