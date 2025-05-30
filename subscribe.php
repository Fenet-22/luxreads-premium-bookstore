<?php
session_start();

// Database connection details
$servername = "sql105.infinityfree.com";
$username = "if0_39050947";
$password = "awK48vQuF51H"; // Adjust if you have a password for MySQL
$dbname = "if0_39050947_luxreads";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    $error_message = "Failed to connect to the database. Please try again later.";
} else {
    $error_message = ""; // Initialize error message
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && empty($error_message)) {
    // Get common form data
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $payment_method = filter_var($_POST['payment_method'] ?? '', FILTER_SANITIZE_STRING);

    // Initialize payment-specific variables
    $card_number = null;
    $card_type = null;
    $expiry_month_year = null;
    $cvc = null;
    $cardholder_name = null;
    $paypal_email = null; // New field for PayPal
    $bank_account_info = null; // New field for Bank Transfer

    // Collect payment method specific data
    if ($payment_method === 'Credit Card') {
        $card_number = filter_var($_POST['card_number'] ?? '', FILTER_SANITIZE_STRING);
        $card_type = filter_var($_POST['card_type'] ?? '', FILTER_SANITIZE_STRING);
        $expiry_month_year = filter_var($_POST['expiry_month_year'] ?? '', FILTER_SANITIZE_STRING);
        $cvc = filter_var($_POST['cvc'] ?? '', FILTER_SANITIZE_STRING);
        $cardholder_name = filter_var($_POST['cardholder_name'] ?? '', FILTER_SANITIZE_STRING);
    } elseif ($payment_method === 'PayPal') {
        $paypal_email = filter_var($_POST['paypal_email'] ?? '', FILTER_SANITIZE_EMAIL);
    } elseif ($payment_method === 'Bank Transfer') {
        $bank_account_info = filter_var($_POST['bank_account_info'] ?? '', FILTER_SANITIZE_STRING);
    }

    // Set default status for a new subscription
    $status = 'active';
    $approved = 'yes';

    // Prepare SQL INSERT statement
    // Note: We're simplifying the 'subscriptions' table structure in the query
    // to only include relevant fields for this simplified form.
    // You might need to adjust your 'subscriptions' table in your database
    // to include columns like 'paypal_email' or 'bank_account_info' if you want to store them.
    // For this example, I'm only storing what's common or directly applicable.
    // If you need to store all details, you'd need more columns and a more complex INSERT.

    // For simplicity, let's insert common fields and a JSON blob for method-specific details
    // This requires a 'payment_details_json' column (TEXT type) in your 'subscriptions' table.
    $payment_details_array = [
        'method' => $payment_method,
        'card_number' => $card_number,
        'card_type' => $card_type,
        'expiry_date' => $expiry_month_year,
        'cvc' => $cvc,
        'cardholder_name' => $cardholder_name,
        'paypal_email' => $paypal_email,
        'bank_account_info' => $bank_account_info
    ];
    $payment_details_json = json_encode($payment_details_array);

    $stmt = $conn->prepare("INSERT INTO subscriptions
        (email, payment_method, payment_details_json, status, approved)
        VALUES (?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("sssss",
            $email,
            $payment_method,
            $payment_details_json,
            $status,
            $approved
        );

        if ($stmt->execute()) {
            $_SESSION['is_premium'] = true;
            $_SESSION['email'] = $email;
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['user_id'] = uniqid('sub_');
            }

            header("Location: index.php?subscription=success");
            exit();
        } else {
            $error_message = "Error processing your subscription: " . $stmt->error;
            error_log("Subscription insert failed: " . $stmt->error);
        }
        $stmt->close();
    } else {
        $error_message = "Failed to prepare subscription statement: " . $conn->error;
        error_log("Failed to prepare subscription statement: " . $conn->error);
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>LuxReads - Premium Subscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0a0a0a, #1a1a1a); /* Dark gradient background */
            font-family: 'Montserrat', sans-serif; /* Modern, clean font */
            overflow-y: auto;
            color: #e0e0e0; /* Light grey text */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1; /* For stacking context with bubbles */
        }

        /* Background Bubbles/Orbs */
        .bubble-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .bubble {
            position: absolute;
            bottom: -150px; /* Start further down */
            width: 60px; /* Larger bubbles */
            height: 60px;
            background: rgba(255, 221, 0, 0.1); /* More subtle gold */
            border-radius: 50%;
            animation: rise 15s infinite ease-in-out; /* Slower, smoother animation */
            box-shadow: 0 0 20px rgba(255, 221, 0, 0.4); /* Glowing effect */
            filter: blur(2px); /* Soften the edges */
        }

        @keyframes rise {
            0% {
                transform: translateY(0) scale(0.8);
                opacity: 0.5;
            }
            50% {
                transform: translateY(-50vh) scale(1.2);
                opacity: 0.3;
            }
            100% {
                transform: translateY(-120vh) scale(0.7);
                opacity: 0;
            }
        }
        /* Staggered animations for bubbles */
        .bubble:nth-child(1) { left: 10%; animation-duration: 18s; animation-delay: 0s; transform: scale(0.7); }
        .bubble:nth-child(2) { left: 20%; animation-duration: 15s; animation-delay: 2s; transform: scale(1.1); }
        .bubble:nth-child(3) { left: 30%; animation-duration: 20s; animation-delay: 4s; transform: scale(0.9); }
        .bubble:nth-child(4) { left: 40%; animation-duration: 16s; animation-delay: 1s; transform: scale(1.3); }
        .bubble:nth-child(5) { left: 50%; animation-duration: 19s; animation-delay: 3s; transform: scale(0.8); }
        .bubble:nth-child(6) { left: 60%; animation-duration: 14s; animation-delay: 0.5s; transform: scale(1.0); }
        .bubble:nth-child(7) { left: 70%; animation-duration: 17s; animation-delay: 2.5s; transform: scale(0.6); }
        .bubble:nth-child(8) { left: 80%; animation-duration: 15.5s; animation-delay: 4.5s; transform: scale(1.2); }
        .bubble:nth-child(9) { left: 90%; animation-duration: 18.5s; animation-delay: 1.5s; transform: scale(0.95); }
        .bubble:nth-child(10) { left: 95%; animation-duration: 16.5s; animation-delay: 3.5s; transform: scale(0.75); }


        /* Form Container */
        .form-container {
            position: relative;
            z-index: 2; /* Above bubbles */
            max-width: 600px; /* Wider form */
            width: 90%; /* Responsive width */
            margin: 60px auto; /* Centered with top/bottom margin */
            background: rgba(26, 26, 26, 0.6); /* Slightly more transparent dark background */
            backdrop-filter: blur(15px) saturate(180%); /* **Glassy Effect** */
            -webkit-backdrop-filter: blur(15px) saturate(180%); /* Safari compatibility */
            border-radius: 20px; /* More rounded corners */
            box-shadow: 0 0 40px rgba(255, 221, 0, 0.4), /* Stronger, golden glow */
                        0 0 0 2px rgba(255, 221, 0, 0.2); /* Subtle golden border */
            padding: 40px; /* More internal padding */
            border: 1px solid rgba(255, 221, 0, 0.1); /* Very subtle border */
            animation: fadeInScale 0.6s ease-out; /* Animation on load */
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        h2 {
            font-family: 'Playfair Display', serif; /* Elegant heading font */
            text-align: center;
            color: #ffdd00; /* Gold color */
            margin-bottom: 35px; /* More space below heading */
            font-size: 2.8em; /* Larger heading */
            text-shadow: 0 0 15px rgba(255, 221, 0, 0.6); /* Glowing text */
            letter-spacing: 1px;
        }

        .error-message, .success-message {
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1em;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .error-message {
            background-color: rgba(255, 0, 0, 0.2);
            color: #ff6b6b;
            border: 1px solid #ff6b6b;
        }

        .success-message {
            background-color: rgba(0, 255, 0, 0.2);
            color: #6bff6b;
            border: 1px solid #6bff6b;
        }

        /* Form groups for spacing */
        .form-group {
            margin-bottom: 20px; /* Space between fields */
        }

        /* Labels */
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ffdd00; /* Gold color for labels */
            font-size: 1.05em;
            text-shadow: 0 0 5px rgba(255, 221, 0, 0.2);
        }

        .section-label {
            font-family: 'Playfair Display', serif;
            font-size: 1.5em;
            color: #fff;
            margin-top: 30px;
            margin-bottom: 20px;
            display: block;
            text-align: center;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        /* Inputs and Selects */
        input[type="email"],
        input[type="text"],
        select {
            width: calc(100% - 24px); /* Account for padding */
            padding: 12px;
            border: none;
            border-radius: 10px; /* More rounded */
            background: rgba(68, 68, 68, 0.4); /* Semi-transparent dark background */
            color: #fff;
            font-size: 1.1em;
            box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.4); /* Inner shadow */
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 221, 0, 0.1); /* Subtle gold border */
        }

        input[type="email"]:focus,
        input[type="text"]:focus,
        select:focus {
            outline: none;
            background: rgba(68, 68, 68, 0.6); /* Slightly darker on focus */
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.6), 0 0 15px rgba(255, 221, 0, 0.6); /* Stronger glow on focus */
            border-color: #ffdd00;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Specific styles for select dropdown arrow */
        select {
            -webkit-appearance: none; /* Remove default arrow */
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23ffdd00%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13.2-6.4H18.6c-5%200-9.3%201.8-13.2%206.4-3.9%204.6-5.8%2010.5-5.8%2017s1.9%2012.4%205.8%2017l124.7%20124.7c3.9%203.9%208.7%205.8%2013.2%205.8s9.3-1.9%2013.2-5.8L287%20103.4c3.9-3.9%205.8-9.8%205.8-17s-1.9-12.4-5.8-17z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
            padding-right: 30px; /* Make space for the custom arrow */
        }

        /* Row for side-by-side fields */
        .form-row {
            display: flex;
            gap: 20px; /* Space between columns */
            margin-bottom: 20px;
        }

        .form-row .form-group {
            flex: 1; /* Each group takes equal space */
            margin-bottom: 0; /* Remove extra margin */
        }

        /* Dynamic payment method sections */
        .payment-method-section {
            display: none; /* Hidden by default */
            border-top: 1px solid rgba(255, 221, 0, 0.1);
            padding-top: 20px;
            margin-top: 20px;
        }
        .payment-method-section.active {
            display: block; /* Show when active */
        }

        /* Subscribe Button */
        .subscribe-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #ffdd00, #e6c800); /* Gold gradient */
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 30px;
            font-size: 1.3em;
            color: #3e2723; /* Dark brown text for contrast */
            box-shadow: 0 8px 20px rgba(255, 221, 0, 0.4); /* Strong glow */
            letter-spacing: 0.5px;
        }

        .subscribe-btn:hover {
            background: linear-gradient(45deg, #e6c800, #ffdd00); /* Reverse gradient on hover */
            transform: translateY(-3px) scale(1.01); /* Lift and slight scale */
            box-shadow: 0 12px 30px rgba(255, 221, 0, 0.6); /* More intense glow */
        }

        .info-text {
            text-align: center;
            color: #ffdd00;
            margin-top: 25px;
            font-style: italic;
            font-size: 0.95em;
            text-shadow: 0 0 5px rgba(255, 221, 0, 0.2);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .form-container {
                padding: 30px 20px;
                margin: 40px auto;
            }
            h2 {
                font-size: 2.2em;
                margin-bottom: 25px;
            }
            .form-row {
                flex-direction: column; /* Stack columns on small screens */
                gap: 0; /* Remove gap when stacked */
            }
            .form-row .form-group {
                margin-bottom: 20px; /* Restore margin between stacked fields */
            }
            .subscribe-btn {
                padding: 12px;
                font-size: 1.1em;
            }
            input[type="email"], input[type="text"], select {
                width: calc(100% - 24px); /* Re-adjust width for stacked */
            }
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 25px 15px;
                margin: 20px auto;
            }
            h2 {
                font-size: 1.8em;
                margin-bottom: 20px;
            }
            label {
                font-size: 0.9em;
            }
            input[type="email"], input[type="text"], select {
                font-size: 1em;
                padding: 10px;
            }
            .subscribe-btn {
                font-size: 1em;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="bubble-wrapper">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <div class="form-container">
        <h2>Premium Subscription</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email"
                       value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>"
                       <?php echo isset($_SESSION['email']) ? 'readonly' : ''; ?> required>
            </div>

            <div class="form-group">
                <label for="payment_method">Choose Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">Select a method</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>

            <div id="creditCardFields" class="payment-method-section">
                <label class="section-label">Credit Card Details</label>
                <div class="form-group">
                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX"
                           pattern="[0-9]{13,19}" title="Card number must be 13 to 19 digits (digits only)">
                </div>

                <div class="form-group">
                    <label for="card_type">Card Type</label>
                    <select id="card_type" name="card_type">
                        <option value="">Select Card Type</option>
                        <option value="Visa">Visa</option>
                        <option value="MasterCard">MasterCard</option>
                        <option value="American Express">American Express</option>
                        <option value="Discover">Discover</option>
                        <option value="JCB">JCB</option>
                        <option value="UnionPay">UnionPay</option>
                        <option value="Diners Club">Diners Club</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="expiry_month_year">Expiry Date (MM/YY)</label>
                        <input type="text" id="expiry_month_year" name="expiry_month_year" placeholder="MM/YY"
                               pattern="(0[1-9]|1[0-2])\/([0-9]{2})" title="Format: MM/YY (e.g., 12/25)">
                    </div>
                    <div class="form-group">
                        <label for="cvc">CVC</label>
                        <input type="text" id="cvc" name="cvc" placeholder="CVC"
                               pattern="[0-9]{3,4}" title="CVC must be 3 or 4 digits">
                    </div>
                </div>

                <div class="form-group">
                    <label for="cardholder_name">Cardholder Name</label>
                    <input type="text" id="cardholder_name" name="cardholder_name" placeholder="Full Name on Card">
                </div>
            </div>

            <div id="paypalFields" class="payment-method-section">
                <label class="section-label">PayPal Details</label>
                <div class="form-group">
                    <p style="text-align: center; color: #e0e0e0; font-style: italic; margin-bottom: 20px;">
                        You will be redirected to PayPal to complete your payment securely.
                    </p>
                    <label for="paypal_email">PayPal Email</label>
                    <input type="email" id="paypal_email" name="paypal_email" placeholder="your.paypal@example.com">
                </div>
            </div>

            <div id="bankTransferFields" class="payment-method-section">
                <label class="section-label">Bank Transfer Details</label>
                <div class="form-group">
                    <p style="text-align: center; color: #e0e0e0; font-style: italic; margin-bottom: 20px;">
                        Please transfer the subscription fee to the following account. Your subscription will be activated upon confirmation.
                    </p>
                    <label for="bank_account_info">Your Bank Account Name/Reference</label>
                    <input type="text" id="bank_account_info" name="bank_account_info" placeholder="Your Name or Reference">
                    <div style="background: rgba(255, 221, 0, 0.1); border: 1px solid rgba(255, 221, 0, 0.3); border-radius: 8px; padding: 15px; margin-top: 20px; color: #ffdd00; font-size: 0.95em; text-align: left;">
                        <strong>Bank Name:</strong> Example Bank<br>
                        <strong>Account No:</strong> 1234567890<br>
                        <strong>SWIFT/BIC:</strong> EXAMPLSWIFT<br>
                        <strong>Reference:</strong> Your Email Address
                    </div>
                </div>
            </div>

            <button class="subscribe-btn" type="submit" name="submit">Subscribe Now</button>
        </form>

        <div class="info-text">Your information is securely processed.</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethodSelect = document.getElementById('payment_method');
            const creditCardFields = document.getElementById('creditCardFields');
            const paypalFields = document.getElementById('paypalFields');
            const bankTransferFields = document.getElementById('bankTransferFields');

            // Function to show/hide relevant fields
            function showPaymentFields() {
                const selectedMethod = paymentMethodSelect.value;

                // Hide all sections first
                creditCardFields.classList.remove('active');
                paypalFields.classList.remove('active');
                bankTransferFields.classList.remove('active');

                // Make all fields within these sections NOT required by default
                // This prevents HTML5 validation errors on hidden fields
                creditCardFields.querySelectorAll('input, select').forEach(field => field.removeAttribute('required'));
                paypalFields.querySelectorAll('input, select').forEach(field => field.removeAttribute('required'));
                bankTransferFields.querySelectorAll('input, select').forEach(field => field.removeAttribute('required'));


                // Show the selected section and make its fields required
                if (selectedMethod === 'Credit Card') {
                    creditCardFields.classList.add('active');
                    // Make credit card fields required
                    document.getElementById('card_number').setAttribute('required', 'required');
                    document.getElementById('card_type').setAttribute('required', 'required');
                    document.getElementById('expiry_month_year').setAttribute('required', 'required');
                    document.getElementById('cvc').setAttribute('required', 'required');
                    document.getElementById('cardholder_name').setAttribute('required', 'required');

                } else if (selectedMethod === 'PayPal') {
                    paypalFields.classList.add('active');
                    // Make PayPal email required
                    document.getElementById('paypal_email').setAttribute('required', 'required');
                } else if (selectedMethod === 'Bank Transfer') {
                    bankTransferFields.classList.add('active');
                    // Make bank account info required
                    document.getElementById('bank_account_info').setAttribute('required', 'required');
                }
            }

            // Add event listener to the payment method dropdown
            paymentMethodSelect.addEventListener('change', showPaymentFields);

            // Call on initial load to show fields if a default option is selected or for refresh
            showPaymentFields();
        });
    </script>

</body>
</html>
