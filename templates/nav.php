<?php
echo '<nav class="main-nav">
        <div class="nav-brand">
            <svg width="40px" height="40px" viewBox="0 0 64.00 64.00" xmlns="http://www.w3.org/2000/svg" fill="#ffffff" transform="rotate(0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <defs> <style>.cls-1,.cls-2{fill:none;stroke:#ffffff;stroke-linejoin:round;stroke-width:2px;}.cls-1{stroke-linecap:round;}</style> </defs> <g id="cyber-monday"> <circle class="cls-1" cx="49" cy="9" r="2"></circle> <circle cx="27.0992" cy="9.5232" r="1.0691"></circle> <line class="cls-1" x1="10.5509" x2="13.5509" y1="6.0547" y2="9.0547"></line> <line class="cls-1" x1="13.5509" x2="10.5509" y1="6.0547" y2="9.0547"></line> <rect class="cls-1" height="39" rx="4.6321" width="54" x="8" y="16"></rect> <path d="M36.9281,51.5291A1.5292,1.5292,0,1,1,35.399,50,1.5292,1.5292,0,0,1,36.9281,51.5291Z"></path> <line class="cls-2" x1="28" x2="30" y1="62" y2="55"></line> <line class="cls-2" x1="41" x2="43" y1="55" y2="62"></line> <line class="cls-1" x1="54" x2="17" y1="62" y2="62"></line> <polyline class="cls-1" points="45 41 46 35 48 35 49 41"></polyline> <line class="cls-1" x1="46" x2="48" y1="39" y2="39"></line> <line class="cls-1" x1="25" x2="25" y1="27" y2="30"></line> <path class="cls-1" d="M30,24h2.7164A1.2836,1.2836,0,0,1,34,25.2836v.4328A1.2836,1.2836,0,0,1,32.7164,27H30a0,0,0,0,1,0,0V24a0,0,0,0,1,0,0Z"></path> <path class="cls-1" d="M30,27h2.7164A1.2836,1.2836,0,0,1,34,28.2836v.4328A1.2836,1.2836,0,0,1,32.7164,30H30a0,0,0,0,1,0,0V27A0,0,0,0,1,30,27Z"></path> <polyline class="cls-1" points="17 41 17 35 19 37 21 35 21 41"></polyline> <polyline class="cls-1" points="31 41 31 35 35 41 35 35"></polyline> <rect class="cls-1" height="6" rx="1.2508" width="4" x="24" y="35"></rect> <path class="cls-1" d="M38,35h2.7492A1.2508,1.2508,0,0,1,42,36.2508v3.4985A1.2508,1.2508,0,0,1,40.7492,41H38a0,0,0,0,1,0,0V35A0,0,0,0,1,38,35Z"></path> <polyline class="cls-1" points="20 24 17 24 17 30 20 30"></polyline> <polyline class="cls-1" points="23 24 25 27 27 24"></polyline> <line class="cls-1" x1="53" x2="53" y1="38" y2="41"></line> <polyline class="cls-1" points="51 35 53 38 55 35"></polyline> <path class="cls-1" d="M43,30V24h2.6439A1.3562,1.3562,0,0,1,47,25.3561v.2878A1.3562,1.3562,0,0,1,45.6439,27H44l3,3"></path> <polyline class="cls-1" points="40 24 37 24 37 30 40 30"></polyline> <line class="cls-1" x1="40" x2="37" y1="27" y2="27"></line> <line class="cls-2" x1="8" x2="62" y1="48" y2="48"></line> </g> </g></svg>
        </div>';

if (strpos($_SERVER['HTTP_HOST'], '.onion') !== false) {
    echo '<div class="nav-switch">
            <a href="https://dans.prtcl.icu/index.php" class="switch-btn clearnet">
                Switch to Clearnet <span>🌐</span>
            </a>
          </div>';
} else {
    echo '<div class="nav-switch">
            <a href="http://dansxr7vbtnaijc37kmhdwtqjplcpj6ojnbw2vt6nkcysrvxvftubtad.onion/" class="switch-btn tor">
                Switch to Tor <span>🔒</span>
            </a>
          </div>';
}
echo '<link rel="stylesheet" href="/style/nav.css">';
echo '</nav>';
?>