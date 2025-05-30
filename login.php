<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $conn = new mysqli("sql105.infinityfree.com", "if0_39050947", "awK48vQuF51H", "if0_39050947_luxreads");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_premium'] = $user['is_premium'];

            // Redirect to previous page or homepage
            $redirectTo = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirectTo");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with this email.";
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
  <title>Login - LuxBook</title>
  <style>
    body {
      margin: 0; padding: 0;
      background: linear-gradient(120deg, #1f1f1f, #2c2c2c);
      font-family: 'Segoe UI', sans-serif;
      display: flex; justify-content: center; align-items: center;
      height: 100vh; overflow: hidden;
      position: relative;
    }

    .bubble {
      position: absolute;
      width: 150px; height: 150px;
      background: rgba(255, 215, 0, 0.1);
      border-radius: 50%;
      animation: bounce 6s infinite ease-in-out alternate;
      filter: blur(60px);
    }

    @keyframes bounce {
      0% { top: 10%; left: 10%; transform: scale(1); }
      50% { top: 50%; left: 80%; transform: scale(1.4); }
      100% { top: 80%; left: 20%; transform: scale(1.1); }
    }

    .form-box {
      background: #2a2a2a;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 0 40px rgba(255, 215, 0, 0.2);
      width: 90%;
      max-width: 400px;
      z-index: 1;
      text-align: center;
    }

    .form-box h2 {
      color: gold;
      margin-bottom: 20px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 10px;
      background: #3a3a3a;
      color: #fff;
    }

    input:focus {
      outline: 2px solid gold;
    }

    button {
      width: 100%;
      background: gold;
      border: none;
      padding: 12px;
      margin-top: 10px;
      font-weight: bold;
      border-radius: 10px;
      cursor: pointer;
      transition: 0.3s;
      color: #1f1f1f;
    }

    button:hover {
      background: #ffcc00;
    }

    .link {
      margin-top: 15px;
      color: #aaa;
      font-size: 14px;
    }

    .link a {
      color: gold;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="bubble"></div>

  <div class="form-box">
    <h2>Welcome Back</h2>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">Login</button>
      <div class="link">Don't have an account? <a href="signup.php">Sign up</a></div>
    </form>
  </div>

</body>
</html>
