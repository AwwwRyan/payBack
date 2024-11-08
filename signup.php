<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password === $confirmPassword) {
        $query = "INSERT INTO Users (username, password, name) VALUES ('$username', '$password', '$name')";
        if (mysqli_query($conn, $query)) {
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Input focus and blur animations
            $('.custom-input').on('focus', function() {
                $(this).animate({ backgroundColor: '#333' }, 300);
            }).on('blur', function() {
                $(this).animate({ backgroundColor: '#1e1e1e' }, 300);
            });
        });

        function validateForm() {
            let valid = true;

            // Clear previous invalid classes and messages
            document.querySelectorAll('.custom-input').forEach(input => {
                input.classList.remove('is-invalid');
                const errorMessage = input.nextElementSibling;
                if (errorMessage) {
                    errorMessage.innerHTML = ''; // Clear previous messages
                }
            });

            // Validate Name
            const name = document.querySelector('input[placeholder="Name"]').value;
            if (name.trim() === '') {
                valid = false;
                showError('Username cannot be empty', 'Name');
            }

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

            // Validate Confirm Password
            const confirmPassword = document.querySelector('input[placeholder="Confirm Password"]').value;
            if (password !== confirmPassword) {
                valid = false;
                showError('Passwords do not match.', 'Confirm Password');
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
                if (!validateForm()) {
                    e.preventDefault(); // Prevent form submission if validation fails
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
                <img src="download.png" alt="Sign Up Illustration" class="img-fluid" style="max-width: 80%;">
            </div>
            <div class="col-lg-6">
            <h3 class="mb-4 payback-text">PAYBACK</h3>
            <h3 class="mb-4">SIGN UP</h3>
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control custom-input" placeholder="Name" >
                    </div>
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control custom-input" placeholder="Username" >
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control custom-input" placeholder="Password" >
                    </div>
                    <div class="mb-3">
                        <input type="password" name="confirmPassword" class="form-control custom-input" placeholder="Confirm Password" >
                    </div>
                    <button type="submit" class="btn btn-light btn-lg w-100">Sign Up</button>
                </form>
                <div class="mt-3">
                    <small>Already have an account? <a href="login.php" class="text-light">Login</a></small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
