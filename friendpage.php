<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    </style>
</head>
<body>
    <div class="container" style="padding-top: 20px;">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-secondary">
            <div class="d-flex align-items-center">
                <span class="back-arrow me-3" style="cursor: pointer;">&larr;</span>
                <span class="header-title fs-5 fw-bold">FirstName LastName</span>
            </div>
            <div class="balance fs-5 fw-bold text-success">Rs. 1200.00</div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-end mt-2">
            <button class="btn btn-outline-warning me-2 btn-settle">Settle up</button>
            <button class="btn btn-outline-success btn-add-expense">Add Expenses</button>
        </div>

        <!-- Activity Section -->
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="p-3 border border-0 text-white">
                    <p>FirstName added Title</p>
                    <p>You owe Rs. 100 to FirstName</p>
                    <p class="activity-date">23 June, 2024</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-3 border border-0 text-white">
                    <p>FirstName paid FirstName</p>
                    <p>You get back Rs. 100</p>
                    <p class="activity-date">22 June, 2024</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-3 border border-0 text-white">
                    <p>FirstName added Title</p>
                    <p>You owe Rs. 50 to FirstName</p>
                    <p class="activity-date">21 June, 2024</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-3 border border-0 text-white">
                    <p>FirstName added Title</p>
                    <p>You owe Rs. 200 to FirstName</p>
                    <p class="activity-date">20 June, 2024</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-3 border border-0 text-white">
                    <p>FirstName paid FirstName</p>
                    <p>You get back Rs. 150</p>
                    <p class="activity-date">19 June, 2024</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="settleModal" tabindex="-1" aria-labelledby="settleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Center the modal vertically -->
            <div class="modal-content modal-confirm">
                <div class="modal-header">
                    <h5 class="modal-title" id="settleModalLabel">Settlement Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You paid <strong>FirstName</strong><br>
                    <span class="amount">Rs. 100</span>
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
                alert('Adding expenses!');
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>