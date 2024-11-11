<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insightrix - Teacher Assessment System</title>
    <link rel="icon" href="./Assessmentsurvey/logo/evalucator-nobg2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-light: #14B8A6;
            --secondary: #4B5563;
            --background: #F9FAFB;
            --white: #FFFFFF;
            --text: #1F2937;
            --text-light: #6B7280;
            --border: #E5E7EB;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--background);
            color: var(--text);
            line-height: 1.6;
        }

        .navbar {
            background: var(--white);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            font-size: 1.5rem;
        }

        .btn {
            padding: 0.625rem 1.75rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
            border: 2px solid var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(13, 148, 136, 0.2);
        }

        .hero {
            text-align: center;
            padding: 6rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            margin-bottom: 4rem;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto 2rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .section {
            background: var(--white);
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-bottom: 3rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .section:hover {
            transform: translateY(-5px);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            margin: 3rem 0;
        }

        .feature-box {
            padding: 2.5rem;
            background: var(--white);
            border-radius: 1rem;
            border: 2px solid var(--border);
            transition: all 0.3s ease;
        }

        .feature-box:hover {
            border-color: var(--primary-light);
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(13, 148, 136, 0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--primary-dark);
        }

        .how-it-works {
            display: flex;
            justify-content: space-between;
            gap: 3rem;
            margin: 3rem 0;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
            padding: 2rem;
            background: var(--background);
            border-radius: 1rem;
            border: 2px solid var(--border);
        }

        .step:hover {
            border-color: var(--primary-light);
            transform: translateY(-5px);
        }

        .step:not(:last-child)::after {
            content: '→';
            position: absolute;
            right: -2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 2rem;
            font-weight: bold;
        }

        .step-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .testimonials {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
        }

        .testimonial {
            padding: 2rem;
            background: var(--background);
            border-radius: 1rem;
            border: 2px solid var(--border);
            transition: all 0.3s ease;
        }

        .testimonial:hover {
            border-color: var(--primary-light);
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(13, 148, 136, 0.1);
        }

        .quote {
            font-style: italic;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .author {
            font-weight: 600;
            color: var(--primary);
        }

        .section-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: var(--primary-dark);
            text-align: center;
        }

        .privacy-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .privacy-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: var(--background);
            border-radius: 1rem;
            border: 2px solid var(--border);
            transition: all 0.3s ease;
        }

        .privacy-item:hover {
            border-color: var(--primary-light);
            transform: translateY(-5px);
        }

        .privacy-item i {
            font-size: 1.5rem;
            color: var(--primary);
        }

        footer {
            background: var(--white);
            padding: 3rem 2rem;
            text-align: center;
            border-top: 1px solid var(--border);
        }

        footer p {
            margin: 0.5rem 0;
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.25rem;
            }

            .how-it-works {
                flex-direction: column;
            }

            .step:not(:last-child)::after {
                content: '↓';
                right: 50%;
                top: auto;
                bottom: -2rem;
                transform: translateX(50%);
            }

            .section {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="logo">
                <i class=""></i>
                Insightrix(BETA)
            </a>
            <a href="Assessmentsurvey/login.php" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </a>
        </div>
    </nav>

    <div class="hero">
        <h1>Empowering Education Through Feedback</h1>
        <p>A streamlined platform for students to provide constructive Feedback, helping educators create more effective learning environments.</p>
        <a href="Assessmentsurvey/login.php" class="btn btn-primary">
            <i class="fas fa-rocket"></i>
            Get Started
        </a>
    </div>

    <main class="container">
        <section class="section">
            <h2 class="section-title">Why Choose Insightrix?</h2>
            <div class="features-grid">
                <div class="feature-box">
                    <i class="fas fa-comments feature-icon"></i>
                    <h3 class="feature-title">For Students</h3>
                    <p>Share your valuable feedback anonymously, helping shape your learning experience and improve teaching methods.</p>
                </div>
                <div class="feature-box">
                    <i class="fas fa-chalkboard-teacher feature-icon"></i>
                    <h3 class="feature-title">For Teachers</h3>
                    <p>Gain valuable insights into your teaching effectiveness and identify areas for professional growth.</p>
                </div>
                <div class="feature-box">
                    <i class="fas fa-school feature-icon"></i>
                    <h3 class="feature-title">For Education</h3>
                    <p>Foster a collaborative learning environment that promotes continuous improvement and excellence.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">How It Works</h2>
            <div class="how-it-works">
                <div class="step">
                    <i class="fas fa-user-graduate step-icon"></i>
                    <h3>Student Feedback</h3>
                    <p>Students complete anonymous assessments</p>
                </div>
                <div class="step">
                    <i class="fas fa-chart-bar step-icon"></i>
                    <h3>Analysis</h3>
                    <p>Feedback is analyzed and organized</p>
                </div>
                <div class="step">
                    <i class="fas fa-lightbulb step-icon"></i>
                    <h3>Improvement</h3>
                    <p>Teachers implement positive changes</p>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Privacy & Security</h2>
            <p>Your privacy and security are our top priorities. We ensure:</p>
            <div class="privacy-features">
                <div class="privacy-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Anonymous Feedback</span>
                </div>
                <div class="privacy-item">
                    <i class="fas fa-lock"></i>
                    <span>Secure Data Storage</span>
                </div>
                <div class="privacy-item">
                    <i class="fas fa-user-shield"></i>
                    <span>Protected Identity</span>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">What Users Say</h2>
            <div class="testimonials">
                <div class="testimonial">
                    <p class="quote">"The feedback system helped me understand my teaching strengths and areas for improvement."</p>
                    <p class="author">- Professor Johnson</p>
                </div>
                <div class="testimonial">
                    <p class="quote">"I feel heard and valued knowing my feedback contributes to better learning experiences."</p>
                    <p class="author">- Sarah, Student</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Insightrix. All rights reserved.</p>
        <p>Need help? Contact support@insightrix.com</p>
    </footer>
</body>
</html>