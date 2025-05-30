<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start(); // Start output buffering immediately

session_start();

// Removed the problematic ob_end_clean() block here, as ob_start() is at the top.
// if (ob_get_level() > 0) {
//     ob_end_clean();
// }

$conn = new mysqli("sql105.infinityfree.com", "if0_39050947", "awK48vQuF51H", "if0_39050947_luxreads");
if ($conn->connect_error) {
    echo "DEBUG: Database connection failed: " . $conn->connect_error; // DEBUG OUTPUT
    error_log("Database connection failed: " . $conn->connect_error);
    header("Location: index.php?error=db_connection");
    exit();
}

$book = basename($_GET['file'] ?? '');

if (empty($book)) {
    echo "DEBUG: No file specified in URL.<br>"; // DEBUG OUTPUT
    header("Location: index.php?error=no_file_specified");
    exit();
}

$premiumBooksList = [
    "Atomic habits.pdf",
    "021.pdf", // Zero to One
    "built_to_last.pdf", // Jim Collins - Good to Great
    
    "the magic of thinking big.pdf", // David J. Schwartz - The Magic of Thinking Big
    "the-intelligent-investor.pdf",
    
    "The Power Of Now.pdf", // Eckhart Tolle - The Power of Now
    "how-to-win-friends-and-influence-people.pdf",
    "you-are-a-badass.pdf",
    "Rich Dad Poor Dad.pdf", // Robert T. Kiyosaki - Rich Dad Poor Dad
    "The Psychology of Persuasion.pdf",
];

// Check if the requested book is in the premium list (case-insensitive comparison for robust matching)
$isPremiumBook = in_array(strtolower($book), array_map('strtolower', $premiumBooksList));
echo "DEBUG: Requested book: " . htmlspecialchars($book) . "<br>"; // DEBUG OUTPUT
echo "DEBUG: Is premium book according to list: " . ($isPremiumBook ? 'Yes' : 'No') . "<br>"; // DEBUG OUTPUT


// Get user authentication and premium status from session
$userLoggedIn = isset($_SESSION['user_id']);
$userIsPremium = false; // Default to false

echo "DEBUG: User logged in (session user_id exists): " . ($userLoggedIn ? 'Yes' : 'No') . "<br>"; // DEBUG OUTPUT
if ($userLoggedIn) {
    $email = $_SESSION['email'] ?? '';
    echo "DEBUG: User email from session: " . htmlspecialchars($email) . "<br>"; // DEBUG OUTPUT

    // If user is logged in, check their premium subscription status from the database
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT status FROM subscriptions WHERE email = ? ORDER BY id DESC LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $userIsPremium = ($row && strtolower($row['status']) === 'active');
            $stmt->close();
            echo "DEBUG: User is premium (from DB): " . ($userIsPremium ? 'Yes' : 'No') . "<br>"; // DEBUG OUTPUT
        } else {
            echo "DEBUG: Failed to prepare subscription status statement: " . $conn->error . "<br>"; // DEBUG OUTPUT
            error_log("Failed to prepare subscription status statement: " . $conn->error);
            header("Location: index.php?error=db_query_failed");
            exit();
        }
    } else {
        echo "DEBUG: User email not found in session despite user_id.<br>"; // DEBUG OUTPUT
    }
}
$conn->close(); // Close DB connection as it's no longer needed

$file_path = __DIR__ . "/pdf/" . $book; // Full path to the PDF file
echo "DEBUG: Full file path attempting to open: " . htmlspecialchars($file_path) . "<br>"; // DEBUG OUTPUT

// --- Authorization Logic ---
// 1. Check if the file actually exists
if (!file_exists($file_path)) {
    echo "DEBUG: File NOT FOUND at: " . htmlspecialchars($file_path) . "<br>"; // DEBUG OUTPUT
    echo "DEBUG: Current working directory: " . getcwd() . "<br>"; // DEBUG OUTPUT
    if (is_dir(__DIR__ . "/pdf/")) {
        echo "DEBUG: Listing contents of " . __DIR__ . "/pdf/ :<br><pre>";
        print_r(scandir(__DIR__ . "/pdf/"));
        echo "</pre><br>";
    } else {
        echo "DEBUG: PDF directory " . __DIR__ . "/pdf/ does not exist or is not a directory.<br>"; // DEBUG OUTPUT
    }
    error_log("File not found for download attempt: " . $file_path);
    header("Location: index.php?error=file_not_found");
    exit();
}
echo "DEBUG: File EXISTS! Proceeding with checks.<br>"; // DEBUG OUTPUT

// 2. If it's a premium book AND the user is NOT premium OR NOT logged in
if ($isPremiumBook && (!$userLoggedIn || !$userIsPremium)) {
    echo "DEBUG: Premium book requested, but user not logged in or not premium.<br>"; // DEBUG OUTPUT
    header("Location: index.php?access=premium_required");
    exit();
}

// 3. If it's a free book AND the user is NOT logged in (if you require login for ALL downloads)
// If you want free books to be downloadable without login, comment out this block.
if (!$isPremiumBook && !$userLoggedIn) {
    echo "DEBUG: Free book requested, but user not logged in.<br>"; // DEBUG OUTPUT
    header("Location: index.php?access=login_required");
    exit();
}

echo "DEBUG: All checks passed. Preparing to send file.<br>"; // DEBUG OUTPUT

// If all checks pass, proceed with file download
header('Content-Description: File Transfer');
header('Content-Type: application/pdf'); // Mime type for PDF
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// Ensure output buffers are clean and flushed before streaming the file
ob_clean(); // This clears the buffer started by ob_start() and any debug echoes
flush();

readfile($file_path); // Stream the file to the browser
exit();
?>