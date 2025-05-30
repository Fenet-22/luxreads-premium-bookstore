<?php


// Backend PHP code for handling the form
session_start();  // Start session to manage user login state
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $servername = "sql105.infinityfree.com";
    $username = "if0_39050947";
    $password = "awK48vQuF51H"; // Change if you have a DB password
    $dbname = "if0_39050947_luxreads"; // Change if your DB has a different name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("<h2 style='color:red;text-align:center;'>Connection failed: " . $conn->connect_error . "</h2>");
    }

    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $isPremium = 0; // define it first!

$stmt = $conn->prepare("INSERT INTO users (name, email, password, is_premium) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $name, $email, $hashedPassword, $isPremium);
 // default is_premium set to 0 (false)

    if ($stmt->execute()) {
        echo "<h2 style='color:lightgreen;text-align:center;'>Signup successful!</h2>";

        // After successful signup, create a session for the user
        $_SESSION['user_email'] = $email;  // Store email to identify the user
        $_SESSION['is_premium'] = false;  // Not premium by default
        header('Location: index.php'); // Redirect to the homepage
        exit();
    } else {
        echo "<h2 style='color:red;text-align:center;'>Error: " . $stmt->error . "</h2>";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #1c1c1c;
      font-family: 'Segoe UI', sans-serif;
      overflow: hidden;
      color: #fff;
    }

    .bubble-wrapper {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
    }

    .bubble {
      position: absolute;
      bottom: -100px;
      width: 40px;
      height: 40px;
      background: rgba(255, 221, 0, 0.2);
      border-radius: 50%;
      animation: rise 8s infinite ease-in;
    }

    @keyframes rise {
      0% {
        transform: translateY(0) scale(1);
        opacity: 0.7;
      }
      50% {
        transform: translateY(-60vh) scale(1.4);
        opacity: 0.4;
      }
      100% {
        transform: translateY(-120vh) scale(1);
        opacity: 0;
      }
    }

    .bubble:nth-child(1) { left: 10%; animation-duration: 10s; }
    .bubble:nth-child(2) { left: 20%; animation-duration: 12s; }
    .bubble:nth-child(3) { left: 30%; animation-duration: 9s; }
    .bubble:nth-child(4) { left: 40%; animation-duration: 11s; }
    .bubble:nth-child(5) { left: 50%; animation-duration: 10s; }
    .bubble:nth-child(6) { left: 60%; animation-duration: 13s; }
    .bubble:nth-child(7) { left: 70%; animation-duration: 9s; }
    .bubble:nth-child(8) { left: 80%; animation-duration: 12s; }
    .bubble:nth-child(9) { left: 90%; animation-duration: 11s; }
    .bubble:nth-child(10) { left: 95%; animation-duration: 14s; }

    .form-container {
      position: relative;
      z-index: 2;
      max-width: 400px;
      margin: 100px auto;
      background: #2a2a2a;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(255, 221, 0, 0.3);
    }

    h2 {
      text-align: center;
      color: #ffdd00;
      margin-bottom: 20px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      background: #444;
      color: #fff;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #ffdd00;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #e6c800;
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
    <h2>Create Account</h2>
    <form method="POST" action="">
      <input type="text" name="fullname" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="submit">Sign Up</button>
    </form>
  </div>

</body>
</html>
