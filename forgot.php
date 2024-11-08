<?php
include 'db_connect.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $checkQuery = "SELECT * FROM Users WHERE username = '$username'";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $query = "UPDATE Users SET password = '$newPassword' WHERE username = '$username'";
        if (mysqli_query($conn, $query)) {
            header("Location: login.php");
            exit();        } 
        else {
            $errorMessage = 'Error updating password';
        }
    } else {
        $errorMessage = 'Username not found';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            
            $('.custom-input').on('focus', function() {
                $(this).animate({ backgroundColor: '#333' }, 300);
            }).on('blur', function() {
                $(this).animate({ backgroundColor: '#1e1e1e' }, 300);
            });
        });

        function validateForgotPasswordForm() {
            let valid = true;

            // Clear previous invalid classes and messages
            document.querySelectorAll('.custom-input').forEach(input => {
                input.classList.remove('is-invalid');
                const errorMessage = input.nextElementSibling;
                if (errorMessage) {
                    errorMessage.innerHTML = ''; // Clear previous messages
                }
            });

            // Validate Username
            const username = document.querySelector('input[placeholder="Username"]').value;
            if (username.trim() === '') {
                valid = false;
                showError('Username cannot be empty', 'Username');
            }

            // Validate New Password
            const newPassword = document.querySelector('input[name="new_password"]').value;
            if (newPassword.trim() === '') {
                valid = false;
                showError('Password cannot be empty', 'New Password');
            } else {
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
                if (!passwordRegex.test(newPassword)) {
                    valid = false;
                    showError('Password must be at least 6 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.', 'New Password');
                }
            }

            // Validate Confirm Password
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            if (newPassword !== confirmPassword) {
                valid = false;
                showError('Passwords do not match.', 'Confirm New Password');
            }

            return valid;
        }

        function showError(message, field) {
            const inputField = document.querySelector(`input[placeholder="${field}"]`);
            inputField.classList.add('is-invalid');
            const errorMessage = document.createElement('div');
            errorMessage.className = 'invalid-feedback';
            errorMessage.innerHTML = message;
            inputField.parentNode.insertBefore(errorMessage, inputField.nextSibling);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                if (!validateForgotPasswordForm()) {
                    e.preventDefault(); 
                }
            });
        });
    </script>
    <style>
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
<body class="bg-dark text-white">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="row">
            <div class="col-lg-6 d-flex justify-content-center align-items-center">
                <img src="download.png" alt="Forgot Password Illustration" class="img-fluid" style="max-width: 80%;">
            </div>
            <div class="col-lg-6">
            <h3 class="mb-4 payback-text">PAYBACK</h3>
            <h3 class="mb-4">FORGOT PASSWORD</h3>
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control custom-input" placeholder="Username">
                    </div>
                    <div class="mb-3">
                        <input type="password" name="new_password" class="form-control custom-input" placeholder="New Password">
                    </div>
                    <div class="mb-3">
                        <input type="password" name="confirm_password" class="form-control custom-input" placeholder="Confirm New Password">
                    </div>
                    <button type="submit" class="btn btn-light btn-lg w-100">Reset Password</button>
                </form>
                <?php if (!empty($errorMessage)): ?>
                    <div class="mt-3 text-danger"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
                <div class="mt-3">
                    <small>Remembered your password? <a href="login.php" class="text-light">Login</a></small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
