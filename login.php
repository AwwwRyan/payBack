<?php
include 'db_connect.php';
session_start(); // Start the session

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT password FROM Users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $storedPassword = $row['password'];

        if ($password === $storedPassword) {
            $queryUserId = "SELECT user_id FROM Users WHERE username = '$username'";
            $resultUserId = mysqli_query($conn, $queryUserId);
            $rowUserId = mysqli_fetch_assoc($resultUserId);
            $userId = $rowUserId['user_id'];
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            header("Location: homepage.php");
            exit();
        } else {
            $errorMessage = "Incorrect password.";
        }
    } else {
        $errorMessage = "Username not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Input focus and blur animations
            $('.custom-input').on('focus', function() {
                $(this).animate({
                    backgroundColor: '#333'
                }, 300);
            }).on('blur', function() {
                $(this).animate({
                    backgroundColor: '#1e1e1e'
                }, 300);
            });
        });

        function validateLoginForm() {
            let valid = true;

            document.querySelectorAll('.custom-input').forEach(input => {
                input.classList.remove('is-invalid');
                const errorMessage = input.nextElementSibling;
                if (errorMessage) {
                    errorMessage.innerHTML = ''; 
                }
            });

            // Validate Username
            const username = document.querySelector('input[placeholder="Username"]').value;
            if (username.trim() === '') {
                valid = false;
                showError('Username cannot be empty', 'Username');
            }

            // Validate Password
            const password = document.querySelector('input[placeholder="Password"]').value;
            if (password.trim() === '') {
                valid = false;
                showError('Password cannot be empty', 'Password');
            } else {
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
                if (!passwordRegex.test(password)) {
                    valid = false;
                    showError('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character', 'Password');
                }
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

        // Attach validation to the form submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                if (!validateLoginForm()) {
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
                <img src="download.png" alt="Login Illustration" class="img-fluid" style="max-width: 80%;">
            </div>
            <div class="col-lg-6">

                <h3 class="mb-4 payback-text">PAYBACK</h3>
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control custom-input" placeholder="Username">
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control custom-input" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-light btn-lg w-100">Login</button>
                </form>
                <?php if (!empty($errorMessage)): ?>
                    <div class="mt-3 text-danger"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
                <div class="mt-3">
                    <small>Don't have an account? <a href="signup.php" class="text-light">Sign Up</a></small>
                </div>
                <div class="mt-1">
                    <small><a href="forgot.php" class="text-light">Forgot Password?</a></small>
                </div>
            </div>
        </div>
    </div>
</body>

</html>