<?php
echo '
<footer class="modern-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>About Hidden Line</h3>
            <p>"Hidden Line" is a website that provides links to access various services on the dark web, which can only be accessed using a special browser like Tor. The site acts as a directory connecting users to hidden content on the internet. While offering anonymity, the dark web also carries significant risks, including illegal and harmful content.</p>
        </div>
        
        <div class="footer-section">
            <h3>Donate to the project</h3>
            <ul>
                <li><i class="fab fa-bitcoin"></i> BTC: bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh</li>
                <li><i class="fab fa-ethereum"></i> ETH: 0x71C7656EC7ab88b098defB751B7401B5f6d8976F</li>
                <li><i class="fas fa-coins"></i> USDT: TQVsMt6HNqm7pQd5SyHBYxpnBhXpAGRBrY</li>
                <li><i class="fas fa-coins"></i> DOGE: D8vFz4p1L37jdg47HXKtSvC8bRBG6JaBfj</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Connect With Us</h3>
            <div class="social-links">
                <a href="https://t.me/+K4H6i81jmAU4NTk1" class="social-icon"><i class="fab fa-telegram"></i></a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; ' . date('Y') . ' Hidden Line. All rights reserved.</p>
        <p>Developed with <i class="fas fa-heart"></i> by <a href="https://github.com/0srD4n/Hidden_line">0srD4n</a></p>
    </div>
</footer>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
.modern-footer {
    background: #1a1a1a;
    color: #fff;
    padding: 2rem 2rem 5rem 2rem;
    font-family: "Segoe UI", Arial, sans-serif;
}

.footer-content {
    padding-top: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.footer-section h3 {
    color: #00ff9d;
    margin-bottom: 1.5rem;
    font-size: 1.2rem;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 0.8rem;
}

.footer-section a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-section a:hover {
    color: #00ff9d;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-icon {
    font-size: 1.5rem;
    transition: transform 0.3s ease;
}

.social-icon:hover {
    transform: translateY(-3px);
}

.footer-bottom {
    text-align: center;
    padding-top: 4rem;
    border-top: 1px solid #333;
}

.footer-bottom p {
    margin: 0.5rem 0;
    color: #888;
}

.footer-bottom i.fa-heart {
    color: #ff4d4d;
}

@media (max-width: 768px) {
    .modern-footer {
        padding: 2rem 1rem;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .social-links {
        justify-content: center;
    }
}
</style>';
?>