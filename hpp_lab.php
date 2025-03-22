<?php
// Vulnerable PHP script to demonstrate HTTP Parameter Pollution (HPP)

// Function to check if an admin is logged in
function isAdmin($role) {
    return ($role === "admin") ? "You have admin access!" : "Access denied.";
}

// Vulnerable authentication bypass
if (isset($_GET['user']) && isset($_GET['role'])) {
    echo "<h3>Login Attempt:</h3>";
    echo "User: " . htmlspecialchars($_GET['user']) . "<br>";
    echo "Role: " . htmlspecialchars($_GET['role']) . "<br>";
    echo isAdmin($_GET['role']);
}

// Demonstrating server-side HPP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Processing Transaction:</h3>";
    echo "Amount: " . htmlspecialchars($_POST['amount']) . "<br>";
    echo "Currency: " . htmlspecialchars($_POST['currency']) . "<br>";
    echo "Transaction completed!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>HPP Vulnerable Lab</title>
</head>
<body>
    <h2>HPP Demonstration</h2>
    <h3>Client-Side HPP:</h3>
    <p>Try accessing:</p>
    <code>http://localhost/hpp_lab.php?user=attacker&role=user&role=admin</code>
    
    <h3>Server-Side HPP:</h3>
    <form method="POST">
        Amount: <input type="text" name="amount" value="1000"><br>
        Currency: <input type="text" name="currency" value="USD"><br>
        <input type="submit" value="Submit">
    </form>
    <p>Use Burp Suite/ZAP to modify request:</p>
    <code>amount=1000&currency=USD&currency=INR</code>
</body>
</html>
