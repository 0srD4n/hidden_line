<?php


// Route handling
$route = $_SERVER['REQUEST_URI'];
$routes = [
    '/' => 'welcome_page'
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
$search_engines = [];
$search_json = file_get_contents('./link/search.json');
if ($search_json !== false) {
    $search_engines = json_decode($search_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Failed to decode search.json: ' . json_last_error_msg());
        $search_engines = [];
    }
} else {
    error_log('Failed to read search.json file');
}
// Page handlers
function print_header($title, $class) {
    global $nav_class;
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
        <link rel="stylesheet" href="/style/' . $safe_class .'">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="stylesheet" href="/style/main.css">
    </head>';

    include 'templates/nav.php';

}


function welcome_page() {
    print_header('Hidden Line', 'welcome.css');
   
    echo '
        <div class="container">
            <h1>Welcome to Hidden Line</h1>
            <p>Hidden Line is a website that shares a list of links connected to Tor and clearnet. If you want your link to be featured here, you can join the Telegram group below or contact us at <a style="color: #00ff9d;" href="mailto:XplDan@proton.me">XplDan@proton.me</a>. </p>
            <p> if you found the dead link, please contact us at <a style="color: #00ff9d;" href="mailto:XplDan@proton.me">XplDan@proton.me</a>. </p>
        </div>';
        echo '<ul class="nav-links">
        <li class="nav-item"><a class="nav-link" href="#forums">Forums</a></li>
        <li class="nav-item"><a class="nav-link" href="#chat">Chat</a></li>
        <li class="nav-item"><a class="nav-link" href="#mail">Mail</a></li>
        <li class="nav-item"><a class="nav-link" href="#pastebin">Pastebin</a></li>
        <li class="nav-item"><a class="nav-link" href="#search">Search</a></li>
        </ul>';
        print_mainpage();

    include 'templates/footer.php';
}
function print_mainpage(){
    $search_engines = json_decode(file_get_contents('link/search.json'), true)['search'];
    $chat_rooms = json_decode(file_get_contents('link/chat_room.json'), true)['chat'];
    $mail_services = json_decode(file_get_contents('link/mail.json'), true)['mail'];
    $pastebin_files = json_decode(file_get_contents('link/pastebin_files.json'), true)['pastebin'];
    $forums = json_decode(file_get_contents('link/forums.json'), true)['forums'];
    echo '<div class="main-content">';

    // Search Engines Section
    echo '<div class="category-section" id="search">
            <h2>Search Engine</h2>';
    if (!empty($search_engines)) {
        echo '<table class="dark-links">
                <tr>
                    <th>Service</th>
                    <th>Description</th>
                    <th>onion Link</th>
                </tr>';
        
        foreach($search_engines as $engine) {
            $title = !empty($engine['title']) ? htmlspecialchars($engine['title'], ENT_QUOTES, 'UTF-8') : '';
            $description = !empty($engine['description']) ? htmlspecialchars($engine['description'], ENT_QUOTES, 'UTF-8') : '';
            $url = !empty($engine['url']) ? htmlspecialchars($engine['url'], ENT_QUOTES, 'UTF-8') : '';

            if ($title && $description && $url) {
                echo '<tr>
                        <td>' . $title . '</td>
                        <td>' . $description . '</td>
                        <td><a href="' . $url . '" rel="noopener noreferrer" target="_blank"><code>' . $title . '</code></a></td>
                      </tr>';
            }
        }
        echo '</table>';
    } else {
        echo '<p class="error">No search engines available at the moment.</p>';
    }
    echo '</div>';

    // Chat Rooms Section
    echo '<div class="category-section" id="chat">
            <h2>Chat Room</h2>';
    if (!empty($chat_rooms)) {
        echo '<table class="dark-links">
                <tr>
                    <th>Service</th>
                    <th>Description</th>
                    <th>onion Link</th>
                </tr>';
        
        foreach($chat_rooms as $chat) {
            $title = !empty($chat['title']) ? htmlspecialchars($chat['title'], ENT_QUOTES, 'UTF-8') : '';
            $description = !empty($chat['description']) ? htmlspecialchars($chat['description'], ENT_QUOTES, 'UTF-8') : '';
            $url = !empty($chat['url']) ? htmlspecialchars($chat['url'], ENT_QUOTES, 'UTF-8') : '';

            if ($title && $description && $url) {
                echo '<tr>
                        <td>' . $title . '</td>
                        <td>' . $description . '</td>
                        <td><a href="' . $url . '" rel="noopener noreferrer" target="_blank"><code>' . $title . '</code></a></td>
                      </tr>';
            }
        }
        echo '</table>';
    } else {
        echo '<p class="error">No chat rooms available at the moment.</p>';
    }
    echo '</div>';

    // Pastebin & File Sharing Section
    echo '<div class="category-section" id="pastebin">
            <h2>Mail Services anonymous</h2>';
    if (!empty($mail_services)) {
        echo '<table class="dark-links">
                <tr>
                    <th>Service</th>
                    <th>onion Link</th>
                </tr>';
        
        foreach($pastebin_files as $pastebin) {
            $title = !empty($pastebin['title']) ? htmlspecialchars($pastebin['title'], ENT_QUOTES, 'UTF-8') : '';
            $url = !empty($pastebin['url']) ? htmlspecialchars($pastebin['url'], ENT_QUOTES, 'UTF-8') : '';

            if ($title && $url) {
                echo '<tr>
                        <td>' . $title . '</td>
                        <td><a href="' . $url . '" rel="noopener noreferrer" target="_blank"><code>' . $title . '</code></a></td>
                      </tr>';
            }
        }
        echo '</table>';
    } else {
        echo '<p class="error">No pastebin & file sharing available at the moment.</p>';
    }
    echo '</div>';

    echo '<div class="category-section" id="mail">
    <h2>Mail Services anonymous</h2>';
if (!empty($mail_services)) {
echo '<table class="dark-links">
        <tr>
            <th>Service</th>
            <th>Description</th>
            <th>onion Link</th>
        </tr>';

foreach($mail_services as $mail) {
    $title = !empty($mail['title']) ? htmlspecialchars($mail['title'], ENT_QUOTES, 'UTF-8') : '';
    $url = !empty($mail['url']) ? htmlspecialchars($mail['url'], ENT_QUOTES, 'UTF-8') : '';
    $description = !empty($mail['description']) ? htmlspecialchars($mail['description'], ENT_QUOTES, 'UTF-8') : '';
    if ($title && $url) {
        echo '<tr>
                <td>' . $title . '</td>
                <td>' . $description . '</td>   
                <td><a href="' . $url . '" rel="noopener noreferrer" target="_blank"><code>' . $title . '</code></a></td>
              </tr>';
    }
}
echo '</table>';
} else {
echo '<p class="error">No pastebin & file sharing available at the moment.</p>';
}
echo '</div>';


    
echo '<div class="category-section" id="forums">
<h2>Forums Hacking</h2>';
if (!empty($forums)) {
echo '<table class="dark-links">
    <tr>
        <th>Service</th>
        <th>Description</th>
        <th>onion Link</th>
    </tr>';

foreach($forums as $forum) {
$title = !empty($forum['title']) ? htmlspecialchars($forum['title'], ENT_QUOTES, 'UTF-8') : '';
$url = !empty($forum['url']) ? htmlspecialchars($forum['url'], ENT_QUOTES, 'UTF-8') : '';
$description = !empty($forum['description']) ? htmlspecialchars($forum['description'], ENT_QUOTES, 'UTF-8') : '';
if ($title && $url) {
    echo '<tr>
            <td>' . $title . '</td>
            <td>' . $description . '</td>   
            <td><a href="' . $url . '" rel="noopener noreferrer" target="_blank"><code>' . $title . '</code></a></td>
          </tr>';
}
}
echo '</table>';
} else {
echo '<p class="error">No pastebin & file sharing available at the moment.</p>';
}
echo '</div>';
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