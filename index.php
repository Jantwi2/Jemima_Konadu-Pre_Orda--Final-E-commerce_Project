<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PreOrda - Streamline Your Pre-Order Business in Ghana</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            overflow-x: hidden;
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        nav.scrolled {
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.6rem;
            font-weight: 700;
            color: #2563eb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #2563eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #4b5563;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #2563eb;
        }

        .cta-button {
            background: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
        }

        .cta-button:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }

        /* Hero Section */
        .hero {
            margin-top: 80px;
            min-height: 90vh;
            display: flex;
            align-items: center;
            background: #f8fafc;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: rgba(37, 99, 235, 0.03);
            border-radius: 50%;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: #0f172a;
        }

        .hero-content .highlight {
            color: #2563eb;
        }

        .hero-content p {
            font-size: 1.25rem;
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .primary-btn {
            background: #2563eb;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .primary-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .secondary-btn {
            background: white;
            color: #2563eb;
            padding: 1rem 2rem;
            border: 2px solid #2563eb;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .secondary-btn:hover {
            background: #2563eb;
            color: white;
            transform: translateY(-2px);
        }

        .hero-image {
            position: relative;
        }

        .phone-mockup {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            padding: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .phone-screen {
            background: #f1f5f9;
            border-radius: 20px;
            padding: 2rem 1.5rem;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .screen-header {
            text-align: center;
            font-weight: 700;
            color: #2563eb;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .order-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .order-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: #dcfce7;
            color: #15803d;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Stats Section */
        .stats {
            background: #2563eb;
            color: white;
            padding: 4rem 2rem;
        }

        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Features Section */
        .features {
            padding: 6rem 2rem;
            background: white;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        .section-header p {
            font-size: 1.2rem;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
        }

        .feature-card {
            background: #f8fafc;
            padding: 2.5rem;
            border-radius: 16px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-color: #2563eb;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: #2563eb;
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.7;
        }

        /* How It Works */
        .how-it-works {
            padding: 6rem 2rem;
            background: #f8fafc;
        }

        .steps-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .step {
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
            opacity: 0;
            transform: translateX(-50px);
            transition: all 0.6s ease;
        }

        .step.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: #2563eb;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            flex-shrink: 0;
        }

        .step-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .step-content p {
            color: #64748b;
            line-height: 1.7;
            font-size: 1.1rem;
        }

        /* CTA Section */
        .cta-section {
            padding: 6rem 2rem;
            background: #0f172a;
            color: white;
            text-align: center;
        }

        .cta-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-container h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }

        .cta-container p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        /* Footer */
        footer {
            background: #1e293b;
            color: white;
            padding: 3rem 2rem 2rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-section h4 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #2563eb;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #334155;
            color: #94a3b8;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #4b5563;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-buttons {
                justify-content: center;
            }

            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .phone-mockup {
                max-width: 350px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .stat-item h3 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav id="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="images/logo_c.png" alt="PreOrda Logo" style="height: 40px; width: auto;">
            </div>
            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="#" class="cta-button">Get Started</a></li>
            </ul>
            <button class="mobile-menu-btn">☰</button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Streamline Your <span class="highlight">Pre-Order Business</span> in Ghana</h1>
                <p>The digital platform designed for micro-shippers. Automate order tracking, payments, and shipping with real-time updates and local payment integration.</p>
                <div class="hero-buttons">
                    <a href="view/register.php" class="primary-btn">Start Free Trial</a>
                    <a href="#how-it-works" class="secondary-btn">See How It Works</a>
                </div>
            </div>
            <div class="hero-image">
                <div class="phone-mockup">
                    <div class="phone-screen">
                        <div class="screen-header">Active Orders</div>
                        <div class="order-card">
                            <div class="order-status">In Transit</div>
                            <h4 style="margin: 0.5rem 0; color: #0f172a;">iPhone 15 Pro Max</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">Est. Arrival: Oct 15, 2025</p>
                        </div>
                        <div class="order-card">
                            <div class="order-status" style="background: #fef3c7; color: #92400e;">Processing</div>
                            <h4 style="margin: 0.5rem 0; color: #0f172a;">Designer Sneakers</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">Payment Confirmed</p>
                        </div>
                        <div class="order-card">
                            <div class="order-status" style="background: #dbeafe; color: #1e40af;">New Order</div>
                            <h4 style="margin: 0.5rem 0; color: #0f172a;">Gaming Console</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">Awaiting Payment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-container">
            <div class="stat-item">
                <h3>$1.01B</h3>
                <p>Ghana E-commerce Market by 2025</p>
            </div>
            <div class="stat-item">
                <h3>100%</h3>
                <p>Order Tracking Accuracy</p>
            </div>
            <div class="stat-item">
                <h3>50%</h3>
                <p>Time Saved on Admin Tasks</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-container">
            <div class="section-header">
                <h2>Everything You Need to Succeed</h2>
                <p>Built specifically for Ghanaian micro-shippers with the tools that matter most</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📦</div>
                    <h3>Automated Order Tracking</h3>
                    <p>Track every order from placement to delivery with real-time updates. No more manual spreadsheets or missed orders.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💳</div>
                    <h3>Local Payment Integration</h3>
                    <p>Accept payments through Mobile Money, bank transfers, and other local payment methods your customers already use.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <h3>Shipping Management</h3>
                    <p>Coordinate shipments, manage delivery schedules, and keep customers informed every step of the way.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Mobile & Web Access</h3>
                    <p>Manage your business from anywhere with seamless mobile and web applications designed for on-the-go entrepreneurs.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Business Analytics</h3>
                    <p>Get insights into your sales, popular products, payment trends, and customer behavior to grow your business.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Customer Management</h3>
                    <p>Build lasting relationships with organized customer profiles, order history, and automated communication tools.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="features-container">
            <div class="section-header">
                <h2>How PreOrda Works</h2>
                <p>Get started in minutes and transform your pre-order business</p>
            </div>
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Create Your Account</h3>
                        <p>Sign up in minutes and set up your business profile. Add your product catalog and payment preferences to get started immediately.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Receive & Manage Orders</h3>
                        <p>Customers place pre-orders through your personalized link. All orders are automatically organized and tracked in your dashboard.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Track Payments & Shipping</h3>
                        <p>Monitor payment status in real-time and update shipping information. Customers receive automatic notifications at every stage.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Deliver & Grow</h3>
                        <p>Complete deliveries efficiently with built-in logistics support. Use analytics to understand your business and scale faster.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contact">
        <div class="cta-container">
            <h2>Ready to Transform Your Business?</h2>
            <p>Join the future of pre-order management in Ghana. Start your free trial today and see the difference.</p>
            <div class="hero-buttons" style="justify-content: center;">
                <a href="view/register.php" class="primary-btn">Get Started Free</a>
                <a href="#" class="secondary-btn" style="border-color: white; color: white;">Schedule a Demo</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <div class="logo" style="margin-bottom: 1rem;">
                    <img src="images/logo.png" alt="PreOrda Logo" style="height: 40px; width: auto;">
                </div>
                <p style="color: #94a3b8;">Empowering Ghanaian micro-shippers with smart technology for pre-order management.</p>
            </div>
            <div class="footer-section">
                <h4>Product</h4>
                <ul>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Case Studies</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Security</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 PreOrda. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for step animations
        const steps = document.querySelectorAll('.step');
        const observerOptions = {
            threshold: 0.2,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, index * 150);
                }
            });
        }, observerOptions);

        steps.forEach(step => observer.observe(step));

        // Feature cards hover effect
        const featureCards = document.querySelectorAll('.feature-card');
        featureCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.background = 'white';
            });
            card.addEventListener('mouseleave', function() {
                this.style.background = '#f8fafc';
            });
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero-image');
            if (hero) {
                hero.style.transform = `translateY(${scrolled * 0.3}px)`;
            }
        });
    </script>
</body>
</html>