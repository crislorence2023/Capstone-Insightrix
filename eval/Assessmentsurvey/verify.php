<?php
session_start();
require_once('admin_class.php');

$crud = new Action();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['verification_code'])) {
        $result = $crud->verify_email();
        echo $result;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    }
} else {
    // Display the verification form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Email Verification</title>
        <!-- Add your CSS here -->
    </head>
    <body>
        <div class="container">
            <h2>Email Verification</h2>
            <form id="verification-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="verification_code">Verification Code:</label>
                    <input type="text" id="verification_code" name="verification_code" required>
                </div>
                <button type="submit">Verify Email</button>
            </form>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#verification-form').submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: 'verify.php',
                        method: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(resp) {
                            if (resp.status == 'success') {
                                alert(resp.message);
                                window.location.href = 'index.php?page=home';
                            } else {
                                alert(resp.message);
                            }
                        }
                    });
                });
            });
        </script>
    </body>
    </html>
    <?php
}
?>