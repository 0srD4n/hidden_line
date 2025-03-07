<?php

session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// function database connection
define("DB_HOST", "localhost");
define("DB_USER", "aj1q13thf9uLS17qf7yXMqcPUFGHd0bX");
define("DB_PASS", "Sahabat123!!");
define("DB_NAME", "IcX5MPRXxzX35Js4WrpLsN3fhHHaxNQ5");
// define("DB_HOST", "localhost");
// define("DB_USER", "root");
// define("DB_PASS", "180406");
// define("DB_NAME", "link");
try {
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// table function
function createTable() {
    global $db;
    $admin = "CREATE TABLE IF NOT EXISTS Admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $sql = "CREATE TABLE IF NOT EXISTS User (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        jumlahpost INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $post = "CREATE TABLE IF NOT EXISTS Post (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        user_id INT NOT NULL,
        link VARCHAR(255) NOT NULL,
        category ENUM('Chat','Forum','Search','Mail','Other'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE
    )";
    $captcha = "CREATE TABLE IF NOT EXISTS Captcha (
        id VARCHAR(255) PRIMARY KEY,
        time INT NOT NULL,
        code VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    try {
        $db->exec($sql);
        $db->exec($post);
        $db->exec($admin);
        $db->exec($captcha);
    } catch(PDOException $e) {
        die("Error creating tables: " . $e->getMessage());
    }
}

// route
function route() {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method == 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] == 'login' && !empty($_POST['username']) && !empty($_POST['password'])) {
            validate_login($_POST['username'], $_POST['password']);
        } else if ($_POST['action'] == 'formlogin') {
            $_SESSION['whare'] = "/login";
            showlogin();
        } else if ($_POST['action'] == 'logout') {
            logout();
        } else if ($_POST['action'] == 'formregister') {
            $_SESSION['whare'] = "/register";
            showregister();
        } else if ($_POST['action'] == 'validate_register') {
            validate_register($_POST['username'], $_POST['email'], $_POST['password']);
        } else if ($_POST['action'] == 'formdashboard') {
            $_SESSION['whare'] = "/dashboard";
            showdashboard();
        } else if ($_POST['action'] == 'formcreatepost') {
            // Add CSRF validation
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['notification'] = [
                    'message' => 'Invalid CSRF token',
                    'type' => 'error'
                ];
                showlogin();
                exit();
            }

            if (!empty($_POST['title']) && !empty($_POST['description']) && !empty($_POST['link']) && !empty($_POST['category'])) {
                createPost(
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['link'],
                    $_POST['category'],
                    $_SESSION['user_id']
                );
            } else {
                $_SESSION['notification'] = [
                    'message' => 'Please fill in all required fields',
                    'type' => 'error'
                ];
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            }

        } else if ($_POST['action'] == 'deletepost') {
            $_SESSION['whare'] = "/dashboard";
            deletepost($_POST['post_id']);
        } else if ($_POST['action'] == 'formhome') {
            $_SESSION['whare'] = "/home";
            showhome();
        }
    } else if (isset($_POST['delete_user'])) {
        $_SESSION['whare'] = "/dashboard";
        deleteUser($_POST['user_id']);
    } else if (isset($_POST['delete_post'])) {
        $_SESSION['whare'] = "/dashboard";
        deletepostUser($_POST['post_id']);
    } else if ($method == 'POST') {
        notfound();
    } else {
        showhome();
    }
}
// function delete post user 
function deletepostUser($post_id) {
    global $db;
    
    // Check if post exists before deletion
    $check_stmt = $db->prepare("SELECT id FROM Post WHERE id = ?");
    $check_stmt->execute([$post_id]);
    
    if ($check_stmt->rowCount() > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM Post WHERE id = ?");
            $stmt->execute([$post_id]);
            $_SESSION['notification'] = [
                'message' => 'Post deleted successfully',
                'type' => 'success'
            ];
            // Redirect to prevent form resubmission
            dashadmin();
            exit();
        } catch (PDOException $e) {
            $_SESSION['notification'] = [
                'message' => 'Error deleting post: ' . $e->getMessage(),
                'type' => 'error'
            ];
            dashadmin();
            exit();
        }
    } else {
        $_SESSION['notification'] = [
            'message' => 'Post not found',
            'type' => 'error'
        ];
        dashadmin();
        exit();
    }
}

// delete user function
function deleteUser($user_id) {
    global $db;
    
    // Check if user exists before deletion
    $check_stmt = $db->prepare("SELECT id FROM User WHERE id = ?");
    $check_stmt->execute([$user_id]);
    
    if ($check_stmt->rowCount() > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM User WHERE id = ?");
            $stmt->execute([$user_id]);
            $_SESSION['notification'] = [
                'message' => 'User deleted successfully',
                'type' => 'success'
            ];
            // Redirect to prevent form resubmission
            dashadmin();
            exit();
        } catch (PDOException $e) {
            $_SESSION['notification'] = [
                'message' => 'Error deleting user: ' . $e->getMessage(),
                'type' => 'error'
            ];
            dashadmin();
            exit();
        }
    } 
}

// start
createTable();
route();

function logout() {
    session_unset();
    session_destroy();
 showlogin();
    exit;
}

function sessionlife() {
    $session_lifetime = 3600; 
    if(!isset($_SESSION['last_activity'])){
        $_SESSION['last_activity'] = time();
    }
    
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        
        if ($inactive_time > $session_lifetime) {
            // Session expired, destroy it and redirect to login
            session_unset();
            session_destroy();
            showlogin();
            exit();
        } else {
            // Still active, update last activity timestamp
            $_SESSION['last_activity'] = time();
        }
    } else {
        // First activity, set initial timestamp
        $_SESSION['last_activity'] = time();
    }
}

// header function
function print_header($title) {
    // Validate and sanitize inputs
    $safe_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    
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
        <meta name="description" content="Link is a website that provides a collection of links to various websites.">
        <meta property="og:title" content="Hidden Line" />
        <title>' . $safe_title . '</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="icon" type="image/x-icon" href="./favicon.svg">
    </head>';

}

// function validate username and password user
$error = "";
$message = "";
$message_type = "";

// Handle login form submission
function validate_login($username, $password) {
    global $error, $message, $message_type, $db;

    try {
        // Validate inputs
        if (empty($username) || empty($password)) {
            $error = "Username and password are required";
            return false;
        }

        // Validate captcha
        if (!isset($_POST['challenge']) || !isset($_POST['captcha']) || 
            !checkCaptcha($_POST['challenge'], $_POST['captcha'])) {
            $error = "Invalid captcha";
            $message_type = "error";
            showlogin();
            return false;
        }

        // Check if admin login
        if ($username === 'admin') {
            $sql = "SELECT * FROM Admin WHERE username = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                dashadmin();
                exit;
            }
        }

        // Regular user login
        $sql = "SELECT * FROM User WHERE username = :username";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['last_activity'] = time();
            
            $message = "Login successful";
            $message_type = "success";
            
            showdashboard();
            exit();
        } else {
            $error = "Invalid username or password";
            $message_type = "error";
            showlogin();
        }
    } catch (PDOException $e) {
        error_log("Database error during login: " . $e->getMessage());
        $error = "Login error occurred. Please try again.";
        $message_type = "error";
        return false;
    }
}

function notification($message, $type) {
    // Use passed in type parameter instead of global
    $alertClass = ($type == 'error') ? 'alert-danger' : 'alert-success';
    $iconClass = ($type == 'error') ? 'fa-circle-exclamation icon-danger' : 'fa-circle-check icon-success';
    $bgColor = ($type == 'error') ? '#dc3545' : '#198754';

    // Combine error and message handling into one 
    $displayMessage = !empty($message) ? $message : '';
    
    if (!empty($displayMessage)) {
        echo '<style>
                .popup-notification {
                    position: fixed;
                    top: 80px;
                    right: 20px;
                    z-index: 9999;
                    animation: slideIn 0.5s ease-out;
                }
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                .notification-content {
                    background: ' . $bgColor . ';
                    color: white;
                    padding: 15px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    min-width: 300px;
                }
              </style>';
        echo '<div class="popup-notification">
                <div class="notification-content">
                    <div class="d-flex gap-4">
                        <span><i class="fa-solid ' . $iconClass . '"></i></span>
                        <div class="d-flex flex-column gap-2">
                            <div>' . htmlspecialchars($displayMessage) . '</div>
                        </div>
                    </div>
                </div>
            </div>';
    }
}
function notfound() {
    header('HTTP/1.0 404 Not Found');
    print_header('404 Not Found');
    include 'template/navbar.php';
    echo '<body style="background: #1a1a1a;">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
            <div class="text-center">
                <h1 class="display-1 fw-bold text-danger">404</h1>
                <h2 class="display-6 mb-4 text-light">Page Not Found</h2>
                <p class="lead mb-5 text-light">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="formhome">
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </button>
                </form>
            </div>
        </div>
    </body>';
    include 'template/footer.php';
}
// superadmin
superadmin();
function superadmin(){
    global $db;
    // Add admin account if it doesn't exist
$admin_username = "admin";
$admin_password = "aGlkZGVubGluZWRhbnRjYSEhCg==";
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

$check_admin = $db->prepare("SELECT id FROM Admin WHERE username = ?");
$check_admin->bindParam(1, $admin_username);
$check_admin->execute();
$result = $check_admin->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    $add_admin = $db->prepare("INSERT INTO Admin (username, password) VALUES (?, ?)");
    $add_admin->bindParam(1, $admin_username);
    $add_admin->bindParam(2, $hashed_password);
    $add_admin->execute();
}
}
function showlogin() {
    global $error, $message_type;
    require_once 'template/navbar.php';

    if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
        showdashboard();
        exit;
    }

    // Get error message from session if set
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        $message_type = 'error';
        unset($_SESSION['error']); // Clear the error after displaying
    }
    print_header('Login');

    notification($error, $message_type);
    
    echo '<body style="background-color: #1a1a1a; color: #fff;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-lg bg-dark text-white">
                        <div class="card-header bg-dark border-secondary text-white text-center py-4">
                            <h3 class="mb-0">Login</h3>
                        </div>
                        <div class="card-body p-4">
                            <form action="" method="POST">
                                <input type="hidden" name="action" value="login">
                                
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control bg-dark text-white border-secondary" name="username" id="username" placeholder="Username" required>
                                    <label for="username" class="text-black-100">Username</label>
                                    <div class="invalid-feedback text-warning">
                                        Please enter your username
                                    </div>
                                </div>

                                <div class="form-floating mb-4">
                                    <input type="password" class="form-control bg-dark text-white border-secondary" name="password" id="password" placeholder="Password" required>
                                    <label for="password" class="text-black-100">Password</label>
                                    <div class="invalid-feedback text-warning">
                                        Please enter your password
                                    </div>
                                </div>';
                                
                                generateCaptcha();
                                
                                echo '<div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg text-uppercase fw-bold">
                                        Sign In
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>';
}
// Register form
function showregister() {
    global $db;
    require_once __DIR__.'/template/navbar.php';
    $message = '';
    $message_type = '';
    $error = '';


    if(isset($_SESSION['success_message'])) {
        $message = $_SESSION['success_message'];
        $message_type = 'success';
        unset($_SESSION['success_message']);
    }

    print_header('Register');
    
    if(!empty($message)) {
        notification($message, $message_type);
    }

    echo '<body style="background-color: #1a1a1a; color: #fff;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-lg bg-dark text-white">
                        <div class="card-header bg-dark border-secondary text-white text-center py-4">
                            <h3 class="mb-0">Create Account</h3>
                        </div>
                        <div class="card-body p-4">
                            <form action="/" method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="action" value="validate_register">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control bg-dark text-white border-secondary" name="username" id="username" placeholder="Username" required>
                                    <label for="username" class="text-black-100">Username</label>
                                    <div class="invalid-feedback text-warning">
                                        Please choose a username
                                    </div>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control bg-dark text-white border-secondary" name="email" id="email" placeholder="name@example.com" required>
                                    <label for="email" class="text-black-100">Email address</label>
                                    <div class="invalid-feedback text-warning">
                                        Please enter a valid email address
                                    </div>
                                </div>

                                <div class="form-floating mb-4">
                                    <input type="password" class="form-control bg-dark text-white border-secondary" name="password" id="password" placeholder="Password" required>
                                    <label for="password" class="text-black-100">Password</label>
                                    <div class="invalid-feedback text-warning">
                                        Please enter a password
                                    </div>
                                </div>';
                                generateCaptcha();
                                echo '<div class="d-grid">
                                    <button type="submit" name="register" class="btn btn-primary btn-lg text-uppercase fw-bold">
                                        Register
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center py-3 bg-dark border-secondary">
                            <div class="small text-white-50">Already have an account? <a href="/login" class="text-primary">Sign in</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>';
}
// validate register
function validate_register($username, $email, $password) {
    global $db;
    $errors = [];
    $message = '';
    $message_type = '';
    if (!isset($_POST['challenge']) || !isset($_POST['captcha']) || 
    !checkCaptcha($_POST['challenge'], $_POST['captcha'])) {
    $error = "Invalid captcha";
    $message_type = "error";
    notification($error, $message_type);
    showregister();
    exit();
}

    // Check if username already exists
    $check_username = $db->prepare("SELECT username FROM User WHERE username = :username");
    $check_username->bindParam(':username', $username);
    $check_username->execute();
    
    // Check if email already exists
    $check_email = $db->prepare("SELECT email FROM User WHERE email = :email"); 
    $check_email->bindParam(':email', $email);
    $check_email->execute();

    if($check_username->rowCount() > 0) {
        $errors[] = 'Username already exists. Please choose another username.';
        $message = 'Username already exists. Please choose another username.';
        $message_type = 'error';
        notification($message, $message_type);
        showregister();
        exit();
    }
    
    if($check_email->rowCount() > 0) {
        $errors[] = 'Email already registered. Please use another email address.';
        $message = 'Email already registered. Please use another email address.';
        $message_type = 'error';
        notification($message, $message_type);
        showregister();
        exit();
    }

    if(empty($username)) {
        $errors[] = 'Username is required';
        $message = 'Username is required';
        $message_type = 'error';
        notification($message, $message_type);
        showregister();
        exit();
    }

    if(empty($email)) {
        $errors[] = 'Email is required';
        $message = 'Email is required';
        $message_type = 'error';
        notification($message, $message_type);
        showregister();
        exit();
    } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
        $message = 'Please enter a valid email address';
        $message_type = 'error';
        notification($message, $message_type);
        showregister();
        exit();
    }

    if(empty($password)) {
        $errors[] = 'Password is required';
        $message = 'Password is required';
        $message_type = 'error';
        notification($message, $message_type);
        showregister();
        exit();
    }
    if(strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
        $message = 'Password must be at least 8 characters long';
        $message_type = 'error';
        notification($message, $message_type);
        showregister();
        exit();
    }

    // If no errors, proceed with registration
    if(empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO User (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->execute();
            
            $message = 'Registration successful! Please login.';
            $message_type = 'success';
            notification($message, $message_type);
            showlogin();
            exit();
        } catch(PDOException $e) {
            $message = 'Registration failed: ' . $e->getMessage();
            $message_type = 'error';
            notification($message, $message_type);
            showregister();
            exit();
        }
    }
    
    return $errors;
}


// dashboard
function showdashboard() {
    // Check if user is logged in first
    if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
        showlogin();
        exit;
    }

    // Clear any previous POST data on refresh
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        unset($_POST);
    }

    require_once 'template/navbar.php';
    print_header('Dashboard');

    // Fetch user's posts
    $user_id = $_SESSION['user_id'];
    $posts = [];
    try {
        global $db;
        $stmt = $db->prepare("SELECT * FROM Post WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        notification("Error fetching posts: " . $e->getMessage(), 'error');
    }

    // Start output
    echo '<body style="background: #1a1a1a;">
    <div class="container mt-4 text-light">
        <div class="row">
            <div class="col-12">
                <h1>Welcome to dashboard, ' . htmlspecialchars($_SESSION['username']) . '</h1>
                <p>You have shared ' . count($posts) . ' links so far.</p>
            </div>
        </div>';

    // Show notifications from session
    if (isset($_SESSION['notification'])) {
        $notification = $_SESSION['notification'];
        notification($notification['message'], $notification['type']);
        unset($_SESSION['notification']);
    }

    echo '<div class="row mt-4">
            <div class="col-md-6">
                <div class="card bg-dark text-light border-secondary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Add New Link</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" >
                            <input type="hidden" name="action" value="formcreatepost">
                            <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title (4-25 characters)</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary" id="title" name="title" required minlength="4" maxlength="25">
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description (6-50 characters)</label>
                                <textarea class="form-control bg-dark text-light border-secondary" id="description" name="description" rows="3" required minlength="6" maxlength="50"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="link" class="form-label">Link URL</label>
                                <input type="url" class="form-control bg-dark text-light border-secondary" id="link" name="link" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select bg-dark text-light border-secondary" id="category" name="category" required>
                                    <option value="">Select category</option>
                                    <option value="Chat">Chat</option>
                                    <option value="Forum">Forum</option>
                                    <option value="Search">Search</option>
                                    <option value="Mail">Mail</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>

              
    
            </div>
            
            <div class="col-md-6">
                <div class="card bg-dark text-light border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Profile Overview</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Username:</strong> ' . htmlspecialchars($_SESSION['username']) . '</p>';
                        
    // Get user stats
    try {
        $stmt = $db->prepare("SELECT email, jumlahpost, created_at FROM User WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo '<p><strong>Email:</strong> ' . htmlspecialchars($user['email']) . '</p>
                  <p><strong>Total Posts:</strong> ' . htmlspecialchars($user['jumlahpost']) . '</p>
                  <p><strong>Member Since:</strong> ' . htmlspecialchars(date('F j, Y', strtotime($user['created_at']))) . '</p>';
        }
    } catch (PDOException $e) {
        notification("Error fetching user data", 'error');
    }

    echo '</div>
                </div>
                
                <div class="card bg-dark text-light border-secondary mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Link Validation Rules</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush bg-dark">
                            <li class="list-group-item bg-dark text-light border-secondary">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Title must be between 4-25 characters
                            </li>
                            <li class="list-group-item bg-dark text-light border-secondary">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Description must be between 6-50 characters
                            </li>
                            <li class="list-group-item bg-dark text-light border-secondary">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                All fields (title, description, link, category) are required
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-dark text-light border-secondary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Your Links</h5>
                    </div>
                    <div class="card-body">';

    if (empty($posts)) {
        echo '<p class="text-center">You haven\'t shared any links yet.</p>';
    } else {
        echo '<div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($posts as $post) {
            echo '<tr>
                    <td>
                        <a href="' . htmlspecialchars($post['link']) . '" target="_blank" class="text-info">' . 
                            htmlspecialchars($post['title']) . 
                        '</a>
                    </td>
                    <td>' . nl2br(htmlspecialchars($post['description'])) . '</td>
                    <td><span class="badge bg-secondary">' . htmlspecialchars($post['category']) . '</span></td>
                    <td>' . htmlspecialchars(date('M j, Y', strtotime($post['created_at']))) . '</td>
                    <td>
                        <form method="POST" action="/" class="d-inline">
                            <input type="hidden" name="action" value="deletepost">
                            <input type="hidden" name="post_id" value="' . $post['id'] . '">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</button>
                        </form>
                    </td>
                </tr>';
        }
        
        echo '</tbody>
            </table>
        </div>';
    }
    echo '</div>
                </div>
            </div>
        </div>
    </div>
    </body>';
    include 'template/footer.php';
}
// function create post with duplicate link prevention and refresh protection
function createPost($title, $description, $link, $category, $user_id) {
    global $db;
    
    // Validate inputs
    $errors = [];
    if (empty($title)) $errors[] = "Title is required";
    if (strlen($title) < 4) $errors[] = "Title must be at least 4 characters";
    if (strlen($title) > 25) $errors[] = "Title cannot be longer than 25 characters";
    if (empty($description)) $errors[] = "Description is required"; 
    if (strlen($description) < 6) $errors[] = "Description must be at least 6 characters";
    if (strlen($description) > 50) $errors[] = "Description cannot be longer than 50 characters";
    if (empty($link)) $errors[] = "Link is required";
    if (!filter_var($link, FILTER_VALIDATE_URL)) $errors[] = "Invalid URL format";
    if (empty($category)) $errors[] = "Category is required";
    
    // Validate category
    $valid_categories = ['Chat', 'Forum', 'Search', 'Mail', 'Other'];
    if (!in_array($category, $valid_categories)) {
        $errors[] = "Invalid category";
        notification("error", "Invalid category");
        showdashboard();
        exit();
    }
    
    if (!empty($errors)) {
        // Show all validation errors
        foreach($errors as $error) {
            notification("error", $error);
        }
        showdashboard();
        exit();
    }

    try {
        // Check for duplicate link in the same category
        $checkStmt = $db->prepare("SELECT id FROM Post WHERE link = ? AND category = ?");
        $checkStmt->execute([$link, $category]);
        $existingPost = $checkStmt->fetch();
        
        if ($existingPost) {
            notification("error", "This link already exists in the same category");
            showdashboard();
            unset($_POST);
            exit();
        }
        
        // Insert new post
        $stmt = $db->prepare("INSERT INTO Post (title, description, user_id, link, category) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$title, $description, $user_id, $link, $category]);
        
        if ($result) {
            // Update user's post count
            $updateStmt = $db->prepare("UPDATE User SET jumlahpost = jumlahpost + 1 WHERE id = ?");
            $updateStmt->execute([$user_id]);
            
            notification("success", "Post created successfully!");
            showdashboard();
            unset($_POST);      
        } else {
            notification("error", "Failed to create post");
        }
        
    } catch (PDOException $e) {
        notification("error", "Database error: " . $e->getMessage());
    }
    
    showdashboard();
    exit();
}
function deletepost($post_id) {
    global $db;
    $user_id = $_SESSION['user_id'];
    
    try {
        // First check if post belongs to user
        $checkStmt = $db->prepare("SELECT id FROM Post WHERE id = ? AND user_id = ?");
        $checkStmt->execute([$post_id, $user_id]);
        
        if ($checkStmt->rowCount() > 0) {
            $deleteStmt = $db->prepare("DELETE FROM Post WHERE id = ?");
            $result = $deleteStmt->execute([$post_id]);
            
            if ($result) {
                // Update user's post count
                $updateStmt = $db->prepare("UPDATE User SET jumlahpost = jumlahpost - 1 WHERE id = ?");
                $updateStmt->execute([$user_id]);
                
                $_SESSION['success'] = "Post deleted successfully!";
            } else {
                $_SESSION['errors'] = ["Failed to delete post"];
            }
        } else {
            $_SESSION['errors'] = ["You don't have permission to delete this post"];
        }
    } catch (PDOException $e) {
        $_SESSION['errors'] = ["Database error: " . $e->getMessage()];
    }
    
    showdashboard();
    exit();
}
// home function 
function showhome() {
require_once 'home.php';
}
function dashadmin() {
    global $db;
    
    // Check if user is logged in as admin
    if (!isset($_SESSION['admin_id'])) {
        showlogin();
        exit();
    }

    print_header('Admin Dashboard');
    echo '<body style="background-color: #212529; color: #fff;">
        <div class="container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Admin Dashboard</h2>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="logout">
                    <button class="btn btn-primary" type="submit">Logout</button>
                </form>
            </div>';

    // Show notifications
    if (isset($_SESSION['notification'])) {
        $notification = $_SESSION['notification'];
        notification($notification['message'], $notification['type']);
        unset($_SESSION['notification']); // Clear notification after showing
    }

    echo '<div class="row">
            <div class="col-md-6">
                <div class="card mb-4 border-0 shadow" style="background-color: #343a40; color: #fff;">
                    <div class="card-header bg-dark border-bottom border-secondary">
                        <h4 class="mb-0">Users Management</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead class="bg-dark">
                                    <tr>
                                        <th class="border-0">Username</th>
                                        <th class="border-0">Email</th>
                                        <th class="border-0">Posts Count</th>
                                        <th class="border-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody>';
    try {
        $stmt = $db->query("SELECT * FROM User");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td class='align-middle'>" . htmlspecialchars($row['username']) . "</td>
                    <td class='align-middle'>" . htmlspecialchars($row['email']) . "</td>
                    <td class='align-middle'>" . $row['jumlahpost'] . "</td>
                    <td class='align-middle'>
                        <form method='POST' action='' onsubmit='return confirm(\"Are you sure you want to delete this user?\")'>
                            <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                            <button type='submit' name='delete_user' class='btn btn-danger btn-sm'>
                                <i class='fas fa-trash-alt'></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>";
        }
    } catch (PDOException $e) {
        echo "<tr><td colspan='4' class='text-danger'>Error fetching users: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
    echo '</tbody></table></div></div></div></div>';

    echo '<div class="col-md-6">
            <div class="card border-0 shadow" style="background-color: #343a40; color: #fff;">
                <div class="card-header bg-dark border-bottom border-secondary">
                    <h4 class="mb-0">Posts Management</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead class="bg-dark">
                                <tr>
                                    <th class="border-0">Title</th>
                                    <th class="border-0">Category</th>
                                    <th class="border-0">User</th>
                                    <th class="border-0">Link</th>
                                    <th class="border-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>';
    try {
        $stmt = $db->query("SELECT Post.*, User.username 
                          FROM Post 
                          JOIN User ON Post.user_id = User.id");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td class='align-middle'>" . htmlspecialchars($row['title']) . "</td>
                    <td class='align-middle'>" . htmlspecialchars($row['category']) . "</td>
                    <td class='align-middle'>" . htmlspecialchars($row['username']) . "</td>
                    <td class='align-middle'>
                        <a href='" . htmlspecialchars($row['link']) . "' class='btn btn-sm btn-outline-light' target='_blank'>
                            <i class='fas fa-external-link-alt'></i> View
                        </a>
                    </td>
                    <td class='align-middle'>
                        <form method='POST' action='' onsubmit='return confirm(\"Are you sure you want to delete this post?\")'>
                            <input type='hidden' name='post_id' value='" . $row['id'] . "'>
                            <button type='submit' name='delete_post' class='btn btn-danger btn-sm'>
                                <i class='fas fa-trash-alt'></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>";
        }
    } catch (PDOException $e) {
        echo "<tr><td colspan='5' class='text-danger'>Error fetching posts: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
    echo '</tbody></table></div></div></div></div></div>
    </body></html>';
}

    function generateCaptcha() {
        global $db;
        $captchaChars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $length = strlen($captchaChars) - 1;
        $code = '';
        for ($i = 0; $i < 5; ++$i) {
            $code .= $captchaChars[mt_rand(0, $length)];
        }
        $randId = mt_rand();
        $time = time();
        
        $stmt = $db->prepare('INSERT INTO Captcha (id, time, code) VALUES (?, ?, ?);');
        $stmt->execute([$randId, $time, $code]);

        $im = imagecreatetruecolor(55, 24);
        $bg = imagecolorallocate($im, 0, 0, 0);
        $fg = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $bg);
        imagestring($im, 5, 5, 5, $code, $fg);
        
        ob_start();
        imagepng($im);
        $imageData = ob_get_clean();
        imagedestroy($im);
        
        echo '<div class="mb-4">
                  <label for="captcha" class="form-label text-white">Enter Captcha</label>
                  <img src="data:image/png;base64,' . base64_encode($imageData) . '" alt="CAPTCHA">
                  <input type="hidden" name="challenge" value="' . $randId . '">
                  <input type="text" class="form-control bg-dark text-light border-secondary" name="captcha" placeholder="Enter Captcha" autocomplete="off" required>
              </div>';
    }
    function checkCaptcha($challenge, $captcha) {
        global $db;
        
        // Clean old captchas (older than 10 minutes)
        $stmt = $db->prepare('DELETE FROM Captcha WHERE time < ?');
        $stmt->execute([time() - 600]);
        
        // Check if captcha exists and matches
        $stmt = $db->prepare('SELECT code FROM Captcha WHERE id = ?');
        $stmt->execute([$challenge]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return false; // Challenge not found
        }
        
        // Delete used captcha
        $stmt = $db->prepare('DELETE FROM Captcha WHERE id = ?');
        $stmt->execute([$challenge]);
        
        // Case-sensitive comparison
        return $row['code'] === $captcha;
    }
     