<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./Assessmentsurvey/logo/Evalucator-nobg2.png" type="image/x-icon">
    <title>Insightrix - Teacher Assessment System</title>
    <link href="https://fonts.googleapis.com/css2?family=Istok+Web:ital,wght@0,400;0,700;1,400;1,700&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
    --primary: #0D9488;
    --primary-dark: #0F766E;
    --primary-light: #14B8A6;
    --background: #F9FAFB;
    --white: #FFFFFF;
    --text: #1F2937;
    --border: #E5E7EB;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Montserrat", sans-serif;
    background: #fdfdfd;
    color: var(--text);
    line-height: 1.5;
}

.navbar {
    background: none;
    padding: .9rem 2rem;
    border-radius: 20px;
    position: sticky;
    top: 0;
    z-index: 100;
}

.nav-container {
    max-width: auto;
    margin: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 1rem;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: 2rem;
    height: 2rem;
    margin-left: 1rem;
}

.btn {
            padding: 0.625rem 1.75rem;
            border-radius: 1rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            background: var(--text);
            color: var(--white);
            border: none;
            margin-top: 10px;
        }


.btn-primary:hover {
    text-decoration: underline;
    transform: translateY(-2px);
}

.hero {
    position: relative;
    padding: 2rem;
    background-color: white;
}

.image-grid-container {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    grid-template-rows: repeat(2, 300px);
    gap: 1rem;
    max-width: 1000px;
    margin: 0 auto;
    padding: 1rem;
}

.image-container {
    position: relative;
    border-radius: 1.5rem;
    overflow: hidden;
    border: 1px solid #EFEFEF;
    height: 100%;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    cursor: pointer;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.image-container .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: teal;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-container:hover .overlay {
    opacity: .8;
}

.image-container:hover img {
    transform: scale(1.1);
}

.overlay h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

/* Grid item sizes */
.large {
    grid-column: span 3;
    grid-row: span 2;
}

.medium {
    grid-column: span 3;
    grid-row: span 1;
}

.small {
    grid-column: span 2;
    grid-row: span 1;
}

.medium-small {
    grid-column: span 1;
    grid-row: span 1;
}

.container {
    display: flex;
    gap: 10px;
    margin: .1rem 1rem 1rem 1rem;
    padding: 20px;
}

.main-container {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    flex: 1;
    background-color: #f0f0f0;
    border-radius: 30px;
    max-height: 67vh;
    flex-shrink: 0;
    overflow: hidden;
    position: relative;
}
.main-container .play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    padding: 0%;
    transform: translate(-50%, -50%);
    background-color: rgba(255, 255, 255, 0.8);
    color: rgb(53, 56, 56);
    
    border-radius: 0.5rem;
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    opacity: 0;
    pointer-events: none;
}

.main-container:hover .play-button,
.main-container:focus-within .play-button {
    opacity: 1;
    pointer-events: auto;
}

.main-container .play-button:hover {
    background-color: rgba(13, 148, 136, 0.9);
    color: white;
}


        .main-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .main-container .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
           
            color: rgb(74, 74, 74);
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            font-size: 1.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .main-container .play-button:hover {
            background-color: rgba(255, 255, 255, 0.8);
            color: rgb(42, 43, 44);
        }



        .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
    
}

.modal-content {
    background-color: #fefefe;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    height: 80%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 20px;
}

.modal-content video {
    max-width: 100%;
    max-height: 100%;
}

.close {
    color: #aaa;
    align-self: flex-end;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
}


      

.sidebar {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 200px;
}

.small-container {
    border: 1px solid rgb(107, 213, 213);
    background-color: #f0f0f0;
    border-radius: 50px;
    height: 29vh;
    overflow: hidden;
    position: relative;
}

img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.text-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    padding: 20px;
    background: linear-gradient(to bottom, rgba(18, 61, 63, 0.7) 0%, rgba(0,0,0,0) 100%);
    z-index: 5;
}

.main-title {
    padding: 50px 50px 0px 100px;
    font-size: 28px;
}

.main-title h2 {
    max-width: 450px;
    font-size: 32px;
    color: white;
    font-weight: bold;
}

.main-title p {
    margin-top: 1rem;
    max-width: 300px;
    font-size: 18px;
    color: white;
}

.hero-btn {
    padding: 0.4rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 20px;
    margin-top: 3rem;
}

.small-title {
    color: white;
    text-align: center;
    padding: 50px 50px;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
}

.hero-top-t {
    margin-left: 3rem;
    margin-right: 3rem;
    display: flex;
    flex-direction: row;
}

.center-website{
    margin-left: 2rem;
    margin-right: 3rem;
    margin-top: 2rem;
    display: flex;
    flex-direction: row;
    font-size: 2.5rem;
    font-weight: bold;
    justify-content: center;
    margin-bottom: 2rem;
    color: teal;
}

.main-hero-text {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text);
    
}

.hero-top-t .sub-hero-text {
    font-size: 15px;
    text-align: right;
    padding-top: 3rem;
    max-width: 16rem;
    font-weight: 500;
    color: rgb(26, 47, 47);
    text-decoration: underline;
}

.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 3rem auto;
    padding: 0 2rem;
}

.image-box {
    position: relative;
    height: 300px;
    border-radius: 1rem;
    overflow: hidden;
    cursor: pointer;
}

.image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(13, 148, 136, 0.9);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    color: var(--white);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-box:hover .image-overlay {
    opacity: 1;
}

.image-box:hover img {
    transform: scale(1.1);
}

.section {
    padding: 5rem 2rem;
}

.section-title {
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 3rem;
    color: var(--primary-dark);
}

.process-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.process-item {
    text-align: center;
    padding: 2rem;
    background: var(--white);
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease;
}

.process-item:hover {
    transform: translateY(-5px);
}

.process-icon {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

footer {
    background: var(--white);
    padding: 3rem 2rem;
    text-align: center;
    margin-top: 4rem;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin: 1rem 0;
}

.social-links a {
    color: var(--primary);
    font-size: 1.5rem;
    transition: color 0.3s ease;
}

.social-links a:hover {
    color: var(--primary-dark);
}



@media (max-width: 1500px) {
    
}

@media (max-width: 992px) {
    .image-grid-container {
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: auto;
        gap: 1rem;
    }

    .large, .medium, .small, .medium-small {
        grid-column: span 2;
        grid-row: span 1;
    }
}

@media (max-width: 768px) {
    .hero h1 {
        font-size: 2rem;
    }
    
    .image-grid {
        grid-template-columns: 1fr;
    }
    
    .process-grid {
        grid-template-columns: 1fr;
    }
    
    .container {
        flex-direction: column;
    }
    
    .sidebar {
        width: 100%;
        flex-direction: row;
    }
    
    .small-container {
        flex: 1;
    }
    
    .main-title {
        padding: 170px 50px 0px 100px;
        font-size: 24px;
        font-weight: bold;
    }

    .small-title {
        padding: 90px 65px;
        font-size: 16px;
        font-weight: bold;
    }
}

@media (max-width: 576px) {

    .hero-top-t {
    margin-left: 2rem;
    margin-right: 2rem;
    display: flex;
    align-self: center;
    justify-self: center;
    align-self: center;
    text-align: center;
}
    .main-hero-text {
    justify-self: center;
    align-self: center;
    text-align: center;
    
}
    .container {
    display: flex;
    flex-direction: column;
    height: 35rem;
    margin: 0rem .1rem .1rem .1rem;
   
}
.main-hero-text {
    
    align-self: center;
    
}
   

.center-website{
    margin-left: 2rem;
    margin-right: 2rem;
    
    display: flex;
    flex-direction: row;
    font-size: 2.5rem;
    font-weight: bold;
    justify-content: center;
    margin-bottom: 2rem;
    color: teal;
}


    .main-container {
   
    height: 200%;
   
    
}
.main-container video {
            width: 100%;
            height: 40rem;
            object-fit: cover;
        }
    .image-grid-container {
        grid-template-columns: 1fr;
    }

    .large, .medium, .small, .medium-small {
        grid-column: span 1;
    }
}
        
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="logo">
                
                <img src="./Assessmentsurvey/images/Evalucator.png" alt="logo">
            </a>
            <a href="Assessmentsurvey/login.php" class="btn btn-primary">
                
                Login
            </a>
        </div>
    </nav>
    <div class="hero-top-t">
        <p class="main-hero-text">Insightrix (BETA)</p>
       
    </div>
    <div class="container">
        <div class="main-container">
            <video autoplay loop muted>
                <source src="./Assessmentsurvey/images/shortvideo-insightrix4.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="play-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-player-play" width="50" height="50" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M7 4v16l13 -8z" />
                </svg>
            </div>
        </div>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <video id="modalVideo" controls style="width: 100%; height: 100%;">
                    <source src="./Assessmentsurvey/images/Insightrix video.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
        <!--
        <div class="sidebar">
            <div class="small-container">
                <div class="text-overlay small-title"></div>
                <img src="./Assessmentsurvey/images/img7-student.png" alt="Sidebar image placeholder 1">
            </div>
            <div class="small-container">
                <div class="text-overlay small-title"></div>
                <img src="./Assessmentsurvey/images/img9-instructor.png" alt="Sidebar image placeholder 2">
            </div>
        </div>
    </div>-->
    </div>
    
    </div>
    <section class="hero">
        <p class="center-website">The System</p>
        <div class="image-grid-container">
            <div class="image-container large">
                <img src="./Assessmentsurvey/images/hero-insightrix-name1.svg" alt="Education" />
                
            </div>
            <div class="image-container medium">
                <img src="./Assessmentsurvey/images/img9-instructor.png" alt="Students" />
                <div class="overlay">
                    <h3>Evaluation</h3>
                </div>
            </div>
            <div class="image-container small">
                <img src="./Assessmentsurvey/images/img10login.png" alt="Teachers" />
                <div class="overlay">
                    <h3>Login</h3>
                </div>
            </div>
            <div class="image-container medium-small">
                <img src="./Assessmentsurvey/images/hero-insightrix-name3.svg" alt="Collaboration" />
               
            </div>
           
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Why Choose Insightrix?</h2>
        <div class="image-grid">
            <div class="image-box">
                <img src="./Assessmentsurvey/images/img7-student.png" alt="For Students">
                <div class="image-overlay">
                    <i class="fas fa-user-graduate mb-4 text-4xl"></i>
                    <h3 class="text-xl font-bold mb-2">For Students</h3>
                    <p>Share your valuable feedback anonymously, helping shape your learning experience.</p>
                </div>
            </div>
            <div class="image-box">
                <img src="./Assessmentsurvey/images/img9-instructor.png" alt="For Teachers">
                <div class="image-overlay">
                    <i class="fas fa-chalkboard-teacher mb-4 text-4xl"></i>
                    <h3 class="text-xl font-bold mb-2">For Teachers</h3>
                    <p>Gain valuable insights into your teaching effectiveness and areas for growth.</p>
                </div>
            </div>
            <div class="image-box">
                <img src="./Assessmentsurvey/images/img8-student-evalaute.png" alt="For Education">
                <div class="image-overlay">
                    <i class="fas fa-school mb-4 text-4xl"></i>
                    <h3 class="text-xl font-bold mb-2">For Education</h3>
                    <p>Foster a collaborative learning environment that promotes excellence.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section bg-white">
        <h2 class="section-title">How It Works</h2>
        <div class="process-grid">
            <div class="process-item">
                <i class="fas fa-comments process-icon"></i>
                <h3>Student Feedback</h3>
                <p>Students complete anonymous assessments</p>
            </div>
            <div class="process-item">
                <i class="fas fa-chart-bar process-icon"></i>
                <h3>Analysis</h3>
                <p>Feedback is analyzed and organized</p>
            </div>
            <div class="process-item">
                <i class="fas fa-lightbulb process-icon"></i>
                <h3>Improvement</h3>
                <p>Teachers implement positive changes</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="social-links">
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
        <p>&copy; <?php echo date("Y"); ?> Insightrix. All rights reserved.</p>
        <p>Need help? Contact support@insightrix.com</p>
    </footer>

   
    <script>

const mainContainer = document.querySelector('.main-container');
const playButton = document.querySelector('.main-container .play-button');

mainContainer.addEventListener('mouseenter', () => {
    playButton.style.opacity = '1';
    playButton.style.pointerEvents = 'auto';
});

mainContainer.addEventListener('mouseleave', () => {
    playButton.style.opacity = '0';
    playButton.style.pointerEvents = 'none';
});

mainContainer.addEventListener('focus-within', () => {
    playButton.style.opacity = '1';
    playButton.style.pointerEvents = 'auto';
});

mainContainer.addEventListener('blur', () => {
    playButton.style.opacity = '0';
    playButton.style.pointerEvents = 'none';
});
        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add intersection observer for fade-in animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        // Observe all section titles and image boxes
        document.querySelectorAll('.section-title, .image-box, .process-item, .center-website, .image-grid-container').forEach((el) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });

        // Add modal functionality
        const modal = document.getElementById("myModal");
const btn = document.querySelector(".play-button");
const span = document.getElementsByClassName("close")[0];

btn.addEventListener("click", () => {
    modal.style.display = "flex";
    const modalVideo = document.getElementById("modalVideo");
    modalVideo.play();
});

span.addEventListener("click", () => {
    modal.style.display = "none";
    const modalVideo = document.getElementById("modalVideo");
    modalVideo.pause();
});

window.addEventListener("click", (event) => {
    if (event.target == modal) {
        modal.style.display = "none";
        const modalVideo = document.getElementById("modalVideo");
        modalVideo.pause();
    }
});
    </script>
</body>
</html>