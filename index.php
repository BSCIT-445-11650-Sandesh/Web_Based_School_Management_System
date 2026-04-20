<?php
session_start();
include 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $redirect = ['admin' => 'admin_dashboard.php', 'teacher' => 'teacher_dashboard.php', 'student' => 'student_dashboard.php'];
    header("Location: " . ($redirect[$_SESSION['role']] ?? 'login.php'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance & Performance Tracker - Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Homepage Specific Styles */
        .homepage-container {
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-text .subtitle {
            font-size: 1.25rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 1rem 2rem;
            font-size: 1rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-hero-primary {
            background: white;
            color: var(--primary);
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .btn-hero-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-hero-secondary:hover {
            background: white;
            color: var(--primary);
        }

        .hero-image {
            position: relative;
        }

        .hero-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.3s;
        }

        .hero-card:hover {
            transform: perspective(1000px) rotateY(0deg);
        }

        .hero-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .hero-card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .demo-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .demo-stat {
            text-align: center;
            padding: 1rem;
            background: var(--background);
            border-radius: 8px;
        }

        .demo-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .demo-stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        /* Features Section */
        .features-section {
            padding: 5rem 2rem;
            background: var(--background);
        }

        .section-header {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.125rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .feature-description {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            position: relative;
        }

        .stats-container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-text {
            font-size: 1.125rem;
            opacity: 0.9;
        }

        /* Benefits Section */
        .benefits-section {
            padding: 5rem 2rem;
            background: white;
        }

        .benefits-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .benefits-text h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .benefits-list {
            list-style: none;
            margin-top: 2rem;
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--background);
            border-radius: 12px;
            transition: all 0.3s;
        }

        .benefit-item:hover {
            transform: translateX(5px);
            background: linear-gradient(90deg, rgba(79, 70, 229, 0.1) 0%, transparent 100%);
        }

        .benefit-icon {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .benefit-content h4 {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .benefit-content p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .benefits-image {
            position: relative;
        }

        .benefits-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
        }

        /* CTA Section */
        .cta-section {
            padding: 5rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            text-align: center;
            position: relative;
        }

        .cta-content {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            font-size: 1.125rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 2rem;
        }

        .login-preview {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 400px;
            margin: 2rem auto 0;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .login-preview h3 {
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .quick-login-btn {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .quick-login-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .demo-credentials {
            background: var(--background);
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-align: left;
        }

        .demo-credentials strong {
            color: var(--text-primary);
        }

        /* Footer */
        .homepage-footer {
            background: var(--text-primary);
            color: white;
            padding: 3rem 2rem 1rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            color: white;
        }

        .footer-section p, .footer-section ul {
            color: rgba(255,255,255,0.8);
            line-height: 1.6;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.6);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .benefits-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .demo-stats {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <div class="homepage-container">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-bg-pattern"></div>
            <div class="hero-content animate-fade-in">
                <div class="hero-text">
                    <h1>Transform Your Educational Experience</h1>
                    <p class="subtitle">
                        Comprehensive Student Attendance & Performance Tracking System designed for modern educational institutions. Streamline administration, enhance engagement, and drive academic excellence.
                    </p>
                    <div class="hero-buttons">
                        <a href="login.php" class="btn-hero btn-hero-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Get Started Now
                        </a>
                        <a href="#features" class="btn-hero btn-hero-secondary">
                            <i class="fas fa-play-circle"></i>
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="hero-image floating">
                    <div class="hero-card">
                        <div class="hero-card-header">
                            <div class="hero-card-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h3>Live Dashboard</h3>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">Real-time insights at your fingertips</p>
                            </div>
                        </div>
                        <div class="demo-stats">
                            <div class="demo-stat">
                                <div class="demo-stat-value">98%</div>
                                <div class="demo-stat-label">Attendance Rate</div>
                            </div>
                            <div class="demo-stat">
                                <div class="demo-stat-value">245</div>
                                <div class="demo-stat-label">Active Students</div>
                            </div>
                            <div class="demo-stat">
                                <div class="demo-stat-value">12</div>
                                <div class="demo-stat-label">Teachers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features-section">
            <div class="section-header animate-fade-in">
                <h2 class="section-title">Powerful Features for Modern Education</h2>
                <p class="section-subtitle">
                    Everything you need to manage student attendance, track performance, and foster academic success
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="feature-title">Smart Attendance</h3>
                    <p class="feature-description">
                        Automated attendance tracking with detailed analytics. Generate reports, identify patterns, and ensure student engagement.
                    </p>
                </div>
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="feature-title">Performance Tracking</h3>
                    <p class="feature-description">
                        Comprehensive grade management system with detailed performance analytics and progress tracking for each student.
                    </p>
                </div>
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Role-Based Access</h3>
                    <p class="feature-description">
                        Secure multi-role system with tailored interfaces for administrators, teachers, and students.
                    </p>
                </div>
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="feature-title">Advanced Analytics</h3>
                    <p class="feature-description">
                        Powerful reporting tools with visual analytics to make data-driven decisions and improve educational outcomes.
                    </p>
                </div>
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Mobile Responsive</h3>
                    <p class="feature-description">
                        Access the system from any device. Our responsive design ensures seamless experience on desktop, tablet, and mobile.
                    </p>
                </div>
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Secure & Reliable</h3>
                    <p class="feature-description">
                        Enterprise-grade security with data encryption and regular backups to protect sensitive educational information.
                    </p>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-text">Students Managed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-text">Educators</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-text">Institutions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-text">Uptime</div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="benefits-section">
            <div class="benefits-container">
                <div class="benefits-text">
                    <h2>Why Choose Our System?</h2>
                    <p style="color: var(--text-secondary); font-size: 1.125rem; margin-bottom: 2rem;">
                        Experience the difference with our comprehensive educational management solution designed to save time and improve outcomes.
                    </p>
                    <ul class="benefits-list">
                        <li class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="benefit-content">
                                <h4>Save Time</h4>
                                <p>Automate routine tasks and reduce administrative workload by up to 70%</p>
                            </div>
                        </li>
                        <li class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="benefit-content">
                                <h4>Improve Performance</h4>
                                <p>Track student progress and identify areas needing attention early</p>
                            </div>
                        </li>
                        <li class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="benefit-content">
                                <h4>Enhance Communication</h4>
                                <p>Keep parents, teachers, and students connected with real-time updates</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="benefits-image">
                    <div class="benefits-card">
                        <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">
                            <i class="fas fa-star" style="color: var(--warning);"></i>
                            Trusted by Leading Institutions
                        </h3>
                        <div style="display: grid; gap: 1rem;">
                            <div style="padding: 1rem; background: var(--background); border-radius: 8px;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                    <strong>Easy Implementation</strong>
                                </div>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">Get started in minutes with our intuitive setup process</p>
                            </div>
                            <div style="padding: 1rem; background: var(--background); border-radius: 8px;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                    <strong>24/7 Support</strong>
                                </div>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">Round-the-clock assistance to ensure smooth operation</p>
                            </div>
                            <div style="padding: 1rem; background: var(--background); border-radius: 8px;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                    <strong>Regular Updates</strong>
                                </div>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">Continuous improvements based on user feedback</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-subtitle">
                    Join thousands of educators who are already transforming their institutions with our powerful system
                </p>
                <div class="login-preview">
                    <h3>Quick Login Demo</h3>
                    <a href="login.php" class="quick-login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Login to Dashboard
                    </a>
                    <div class="demo-credentials">
                        <strong>Demo Credentials:</strong><br>
                        Admin: admin@email.com / 1234<br>
                        Teacher: teacher@email.com / 1234<br>
                        Student: adnan@email.com / 1234
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="homepage-footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-graduation-cap"></i> Student Tracker</h3>
                    <p>
                        Empowering educational institutions with modern technology for better student management and academic excellence.
                    </p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="#features"><i class="fas fa-star"></i> Features</a></li>
                        <li><a href="#"><i class="fas fa-phone"></i> Contact</a></li>
                        <li><a href="#"><i class="fas fa-question-circle"></i> Support</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>System Features</h3>
                    <ul>
                        <li><i class="fas fa-check"></i> Attendance Management</li>
                        <li><i class="fas fa-check"></i> Performance Tracking</li>
                        <li><i class="fas fa-check"></i> Grade Management</li>
                        <li><i class="fas fa-check"></i> Analytics & Reports</li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p>
                        <i class="fas fa-envelope"></i> info@studenttracker.com<br>
                        <i class="fas fa-phone"></i> +1 (555) 123-4567<br>
                        <i class="fas fa-map-marker-alt"></i> 123 Education Street<br>
                        Learning City, LC 12345
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Student Attendance & Performance Tracker. All rights reserved. | Designed with <i class="fas fa-heart" style="color: var(--danger);"></i> for educators</p>
            </div>
        </footer>
    </div>

    <script>
        // Smooth scrolling for anchor links
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

        // Add scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe all feature cards and benefit items
        document.querySelectorAll('.feature-card, .benefit-item, .stat-item').forEach(el => {
            observer.observe(el);
        });

        // Add parallax effect to hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroBg = document.querySelector('.hero-bg-pattern');
            if (heroBg) {
                heroBg.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    </script>
</body>
</html>
