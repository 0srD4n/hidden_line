<?php
echo '<nav class="navbar navbar-expand-lg navbar-dark  shadow">
  <div class="container">
    <a class="navbar-brand fw-bold text-uppercase tracking-wider" href="/">HIDDEN LINE</a>
    <div class="d-flex gap-2 align-items-center" id="mobile-nav">';
        if(isset($_SESSION['username']) && isset($_SESSION['user_id'])){
          echo '<form method="POST" action="/">
                  <input type="hidden" name="action" value="logout">
                  <button class="btn btn-outline-danger btn-sm px-3" type="submit">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                  </button>
                </form>';
        } else {
          echo '<form action="/" method="POST">
                  <input type="hidden" name="action" value="formlogin">
                  <button class="btn ' . (isset($_SESSION['whare']) && $_SESSION['whare'] == '/login' ? 'btn-light' : 'btn-outline-light') . ' btn-sm px-3" type="submit">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                  </button>
                </form>
                <form action="/" method="POST">
                  <input type="hidden" name="action" value="formregister">
                  <button class="btn ' . (isset($_SESSION['whare']) && $_SESSION['whare'] == '/register' ? 'btn-light' : 'btn-outline-light') . ' btn-sm px-3" type="submit">
                    <i class="bi bi-person-plus me-1"></i>Register
                  </button>
                </form>';
        }
        echo '</div>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto align-items-center">
        <li class="nav-item">';
        if(isset($_SESSION['username']) && isset($_SESSION['user_id'])){
          echo '<div class="d-flex gap-2">
                  <form method="POST" action="/">
                    <input type="hidden" name="action" value="formdashboard">
                    <button style="color: #fff;" class="btn  no-border' . (isset($_SESSION['whare']) && $_SESSION['whare'] == '/dashboard' ? 'btn-light' : 'btn-outline-light') . ' btn-sm px-3" type="submit">
                      <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </button>
                  </form>
                  <form method="POST" action="/">
                    <input type="hidden" name="action" value="formhome">
                      <button style="color: #fff;" class="btn  no-border' . (isset($_SESSION['whare']) && $_SESSION['whare'] == '/' ? 'btn-light' : 'btn-outline-light') . ' btn-sm px-3" type="submit">
                      <i class="bi bi-house-door me-1"></i>Home
                    </button>
                  </form>
                </div>';
        }else{
          echo '<form method="POST" action="/">
                  <input type="hidden" name="action" value="formhome">
                  <button style="color: #fff; border: none;" class="btn ' . (isset($_SESSION['whare']) && $_SESSION['whare'] == '/' ? 'btn-light' : 'btn-outline-light') . ' btn-sm px-3" type="submit">
                    <i class="bi bi-house-door me-1"></i>Home
                  </button>
                </form>';
        echo '</li>';
        }
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.onion') !== false) {
    echo '<li class="nav-item">
            <a class="nav-link px-3 d-flex align-items-center" href="https://dans.prtcl.icu/index.php" target="_blank" rel="noopener noreferrer">
              <i class="bi bi-globe me-1"></i>
              Clearnet Site
            </a>
          </li>';
} else {
    echo '<li class="nav-item">
            <a class="nav-link px-3 d-flex align-items-center" href="http://hidlisnonhc6ogbdlx3f4jpln43hyzvn6tbzvfqgv727v3kar3so3dad.onion/" target="_blank" rel="noopener noreferrer">
              <i class="bi bi-shield-lock me-1"></i>
              Onion Site
            </a>
          </li>';
}
echo '</ul>
      <div class="d-flex gap-2 align-items-center">';
        if(isset($_SESSION['username']) && isset($_SESSION['user_id'])){
          echo '<form method="POST" action="/">
                  <input type="hidden" name="action" value="logout">
                  <button class="btn btn-outline-danger btn-sm px-3" type="submit">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                  </button>
                </form>';
        } else {
          echo '<form action="/" method="POST">
                  <input type="hidden" name="action" value="formlogin">
                  <button class="btn ' . ($_SESSION['whare'] == '/login' ? 'btn-light' : 'btn-outline-light') . ' btn-sm px-3" type="submit">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                  </button>
                </form>
                <form action="/" method="POST">
                  <input type="hidden" name="action" value="formregister">
                  <button class="btn ' . ($_SESSION['whare'] == '/register' ? 'btn-light' : 'btn-outline-light') . ' btn-sm px-3" type="submit">
                    <i class="bi bi-person-plus me-1"></i>Register
                  </button>
                </form>';
        }
        echo '</div>
    </div>
  </div>
</nav>';
echo '<style>
@media(min-width: 992px) {
  #mobile-nav {
    display: none !important;
  }
}
  nav{
      background: #1a1a1a;
      }
@media (max-width: 992px) {
  .navbar-brand {
    font-size: 1.2rem;
  }
  #navbarNav {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.1);
  }
  .navbar-nav {
    margin-bottom: 1rem;
  }
}
</style>';