<footer>
    <div class="footer-main">
        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Contact Info</h4>
            <p><i class="fas fa-map-marker-alt"></i> 123 Company Address</p>
            <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
            <p><i class="fas fa-envelope"></i> info@company.com</p>
        </div>
        
        <div class="footer-section">
            <h4>Follow Us</h4>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="footer-column footer-column_left">
            <div class="footer__copyright d-flex align-items-start">
                <div class="copyright-icon">
                    <img src="images/copyright.png" alt="Copyright">
                </div>
                <p class="ftr-txt">Copyright © 2022-2025. All rights reserved.</p>
            </div>
        </div>
        
        <div class="footer-bottom-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Cookie Policy</a>
        </div>
    </div>
</footer>

<style>
footer {
    background-color: #2c3e50;
    color: #ecf0f1;
    padding: 40px 0 15px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.footer-main {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto 30px;
    padding: 0 20px;
}

.footer-section {
    flex: 1;
    min-width: 250px;
    margin-bottom: 25px;
    padding: 0 15px;
}

.footer-section h4 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #3498db;
    position: relative;
    padding-bottom: 8px;
}

.footer-section h4:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 2px;
    background: #3498db;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 10px;
}

.footer-section ul li a {
    color: #bdc3c7;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-section ul li a:hover {
    color: #3498db;
}

.footer-section p {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
}

.footer-section i {
    margin-right: 10px;
    color: #3498db;
    width: 20px;
}

.social-icons {
    display: flex;
    gap: 15px;
}

.social-icons a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: #fff;
    transition: all 0.3s;
}

.social-icons a:hover {
    background: #3498db;
    transform: translateY(-3px);
}

.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px 20px 10px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    flex-wrap: wrap;
}

.footer__copyright {
    display: flex;
    align-items: center;
    gap: 10px;
}

.copyright-icon img {
    width: 20px;
    height: 20px;
    filter: invert(1);
}

.ftr-txt {
    margin: 0;
    color: #bdc3c7;
    font-size: 14px;
}

.footer-bottom-links {
    display: flex;
    gap: 20px;
}

.footer-bottom-links a {
    color: #bdc3c7;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.footer-bottom-links a:hover {
    color: #3498db;
}

@media (max-width: 768px) {
    .footer-main {
        flex-direction: column;
    }
    
    .footer-bottom {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .footer__copyright {
        justify-content: center;
    }
    
    .footer-bottom-links {
        justify-content: center;
    }
}
</style>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">