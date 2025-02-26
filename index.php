<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); 
define('DB_PASS', '180406');
define('DB_NAME', 'link');

// Connect to database
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
// create table link if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    short_url VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
// create table users if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)");


// Route handling
$route = $_SERVER['REQUEST_URI'];
$routes = [
    '/' => 'welcome_page',
    '/register' => 'register_page',
    '/login' => 'login_page',
    '/dashboard' => 'dashboard_page',
    '/profile' => 'profile_page',
    '/logout' => 'logout_user'
];

if (array_key_exists($route, $routes)) {
    call_user_func($routes[$route]);
    exit();
} else {
    header("HTTP/1.0 404 Not Found");
    error_page();
    exit();
}

// Authentication functions
function register_user($username, $password, $email) {
    global $pdo;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $hashed_password, $email]);
}

function login_user($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

function logout_user() {
    session_destroy();
    header('Location: /');
    exit();
}

// Page handlers
function print_header($title, $class) {
    // Validate and sanitize inputs
    $safe_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safe_class = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');
    
    // Security headers
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // CSRF token generation
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>' . $safe_title . '</title>
        <link rel="stylesheet" href="/style/' . $safe_class . '">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="stylesheet" href="/style/main.css">
        <script src="/js/main.js" defer></script>
    </head>
    <body>
    <nav class="main-nav">
        <div class="nav-brand">Hidden Line</div>
        <div class="nav-links">
            ' . (isset($_SESSION['user_id']) ? 
            'Welcome, ' . htmlspecialchars($_SESSION['username']) . 
            ' | <a href="/dashboard">Dashboard</a>
            | <a href="/profile">Profile</a>
            | <a href="/logout">Logout</a>' 
            : '<a href="/login">Login</a> | <a href="/register">Register</a>') . '
        </div>
    </nav>';
}

function welcome_page() {
    print_header('Welcome', 'welcome.css');
    echo '<div class="container">
        <h1>Welcome to Hidden Line</h1>
        <p>This is a feature-rich web application with user authentication and more.</p>
    </div>';
    include 'templates/footer.php';
}

function register_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (register_user($_POST['username'], $_POST['password'], $_POST['email'])) {
            header('Location: /login');
            exit();
        }
    }
    print_header('Register', 'auth.css');
    include 'templates/register_form.php';
    include 'templates/footer.php';
}

function login_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (login_user($_POST['username'], $_POST['password'])) {
            header('Location: /dashboard');
            exit();
        }
    }
    print_header('Login', 'auth.css');
    include 'templates/login_form.php';
    include 'templates/footer.php';
}

function dashboard_page() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit();
    }
    print_header('Dashboard', 'dashboard.css');
    include 'templates/dashboard.php';
    include 'templates/footer.php';
}

function profile_page() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit();
    }
    print_header('Profile', 'profile.css');
    include 'templates/profile.php';
    include 'templates/footer.php';
}

function error_page() {
    print_header('Error 404', 'error.css');
    echo '<div class="container">
        <h1>404 - Page Not Found</h1>
        <p>The requested page could not be found. Try reporting this to the admin.</p>
        
    </div>';
    include 'templates/footer.php';
}
?>