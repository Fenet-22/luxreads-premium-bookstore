<?php
session_start();

// Check if the user is logged in as an admin (or implement your own authorization check)
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "luxreads");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if we have an 'approve' request
if (isset($_GET['approve']) && isset($_GET['subscription_id'])) {
    $subscription_id = $_GET['subscription_id'];

    // Update status to 'approved'
    $stmt = $conn->prepare("UPDATE subscriptions SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $subscription_id);

    if ($stmt->execute()) {
        echo "<h2 style='color:lightgreen;text-align:center;'>Subscription approved successfully!</h2>";
    } else {
        echo "<h2 style='color:red;text-align:center;'>Error: " . $stmt->error . "</h2>";
    }

    $stmt->close();
}

// Retrieve all subscriptions
$sql = "SELECT * FROM subscriptions WHERE status = 'pending' ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Approve Subscriptions</title>
</head>
<body>
    <h2>Pending Subscriptions</h2>
    <table border="1">
        <tr>
            <th>Email</th>
            <th>Cardholder Name</th>
            <th>Country</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['cardholder_name']; ?></td>
                <td><?php echo $row['country']; ?></td>
                <td>
                    <a href="approve_subscription.php?approve=true&subscription_id=<?php echo $row['id']; ?>">Approve</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
