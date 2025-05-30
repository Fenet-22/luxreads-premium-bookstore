<?php
session_start();

// Check if user is logged in
$userLoggedIn = isset($_SESSION['user_id']);

// IMPORTANT: Set $_SESSION['is_premium'] in your login/signup/subscribe logic.
// This example assumes 'is_premium' is a boolean in the session.
$isPremium = isset($_SESSION['is_premium']) && $_SESSION['is_premium'] === true;

// --- FOR TESTING ONLY: Uncomment these lines to simulate different user states ---
// $_SESSION['user_id'] = 1;         // Simulate a logged-in user
// $_SESSION['email'] = 'test@example.com'; // Useful for download.php
// $_SESSION['is_premium'] = true; // Simulate a premium user (set to false for non-premium)
//
// To clear session for testing:
// session_unset();
// session_destroy();
// header("Location: index.php"); exit();
// --------------------------------------------------------------------------------

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>LuxReads - Premium Bookstore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        <h1>LuxReads</h1>
        <nav>
            <a href="index.php">Home</a>
            <?php if (!$userLoggedIn): ?>
                <a href="signup.php">Signup</a>
                <a href="login.php">Login</a>
            <?php else: ?>
                <a href="logout.php">Logout</a>
                <?php if (!$isPremium): ?>
                    <a href="subscribe.php" class="subscribe-link">Subscribe</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    </header>

    <div class="hero">
        <h2>Unlock Your Potential with Premium Books</h2>
        <p>Discover business and mindset books to transform your life and career.</p>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for books, authors, or topics...">
        </div>

        <div class="genre-list">
            <div class="genre" onclick="filterBooks('business')">Business</div>
            <div class="genre" onclick="filterBooks('mindset')">Mindset</div>
            <div class="genre" onclick="filterBooks('finance')">Finance</div>
            <div class="genre" onclick="filterBooks('self_help')">Self-help</div>
            <div class="genre" onclick="displayAllBooks()">All</div>
        </div>
    </div>

    <div class="book-section-wrapper">
        <section class="book-section" id="book-section">
            </section>
    </div>

    <footer>
        <p>&copy; 2025 LuxReads. All rights reserved.</p>
    </footer>

<script>
    // Pass PHP variables to JavaScript as global variables
    let userLoggedIn = <?php echo $userLoggedIn ? 'true' : 'false'; ?>;
    let userIsPremium = <?php echo $isPremium ? 'true' : 'false'; ?>;
</script>
<script src="script.js"></script>

<div id="auth-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn" onclick="closeAuthModal()">&times;</span>
        <h2>Access Restricted</h2>
        <p>You need to be logged in to download books or access premium content.</p>
        <a href="login.php" class="modal-btn">Login</a>
        <a href="signup.php" class="modal-btn">Sign Up</a>
    </div>
</div>

<div id="premium-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn" onclick="closePremiumModal()">&times;</span>
        <h2>Premium Content</h2>
        <p>This is a premium book. Please subscribe to our premium plan to unlock all exclusive content!</p>
        <a href="subscribe.php" class="modal-btn">Subscribe Now</a>
        <button class="modal-btn" onclick="closePremiumModal()">No Thanks</button>
    </div>
</div>

</body>
</html>