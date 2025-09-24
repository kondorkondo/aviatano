<?php
session_start();

// Database configuration (same as Laravel's .env file)
define('DB_HOST', 'localhost');
define('DB_NAME', 'egnohetd_bet');
define('DB_USER', 'egnohetd_bet'); // Change if needed
define('DB_PASS', 'egnohetd_bet'); // Change if needed

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo "<div class='message error'>User with this email already exists.</div>";
        include 'auth.html';
        exit();
    }
    
    // Generate a unique user ID (similar to Laravel's format)
    $lastUser = $pdo->query("SELECT id FROM users ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $newUserId = $lastUser ? $lastUser['id'] + 1 : 50000;
    
    // Get initial bonus from settings
    $bonusSetting = $pdo->query("SELECT value FROM settings WHERE category = 'initial_bonus'")->fetch(PDO::FETCH_ASSOC);
    $initialBonus = $bonusSetting ? $bonusSetting['value'] : 50;
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (id, name, mobile, email, gender, country, currency, password, status, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, 'Tz', 'TZS', ?, '1', NOW(), NOW())");
    
    if ($stmt->execute([$newUserId, $name, $mobile, $email, $gender, $password])) {
        // Create wallet for the user
        $walletStmt = $pdo->prepare("INSERT INTO wallets (userid, amount, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $walletStmt->execute([$newUserId, $initialBonus]);
        
        // Set session variables
        $_SESSION['user_id'] = $newUserId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        
        // Redirect to welcome page
        header("Location: crash");
        exit();
    } else {
        echo "<div class='message error'>Registration failed. Please try again.</div>";
        include 'auth.html';
        exit();
    }
}
?>