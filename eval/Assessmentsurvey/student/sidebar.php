<!-- Sidebar Component -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="./" class="brand-link">
        <div class="brand-wrapper">
            <img src="logo/Evalucator.png" alt="Evalucator Logo" class="brand-image">
            <span class="brand-title"><b>Student</b></span>
        </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">


                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="./" class="nav-link nav-home">
                        <i class="ri-home-line"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="./index.php?page=evaluate" class="nav-link nav-evaluation-status">
                        <i class="ri-file-text-line"></i>
                        <p>Evaluate</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./index.php?page=Completed Eval" class="nav-link nav-evaluation-completed">
                        <i class="ri-checkbox-circle-line"></i>
                        <p>Completed Surveys</p>
                    </a>
                </li>

             

            </ul>
        </nav>
    </div>
</aside>

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #4A90E2, #50E3C2);
    --sidebar-bg: #FFFFFF;
    --text-color: #191919;
    --border-color: #e2e8f0;
    --hover-transition: 0.3s ease;
}

.main-sidebar {
    font-family: 'Montserrat', sans-serif;
    background-color: var(--sidebar-bg);
    border-right: 1px solid var(--border-color);
    transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

b, strong {
    font-weight: bold !important;
}

/* Brand Styles */
.brand-link {
    font-weight: 500;
    padding: 0.8rem 1rem;
    border-bottom: 1px solid var(--border-color);
    background-color: var(--sidebar-bg) !important;
    text-decoration: none;
    color: #333333;
    
}

.brand-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
}

.brand-image {
    width: 40px;
    height: 40px;
    object-fit: contain;
}

.brand-title {
    font-size: 1.3rem;
    font-weight: 500;
    color: var(--text-color);
    letter-spacing: 0.05rem;
}

.brand-title b {
    font-weight: 600 !important;
    font-size: 1.3rem;
  
    color: var(--text-color);
    letter-spacing: 0.05rem;
}


/* Sidebar Navigation */
.sidebar {
    margin: 5px;
    padding-top: 10px;
}

.nav-sidebar .nav-item {
    margin-bottom: 0.5rem;
}

.nav-sidebar .nav-link {
    color: #333333 !important;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    padding-left: 1rem;
    gap: 12px;
    border: 1px solid transparent;
    height: 3rem;
    
}

.nav-sidebar .nav-link .right {
    margin-left: auto;
    font-size: 0.8rem;
    display: block !important;
}

.nav-sidebar .nav-link i {
    font-size: 1.5rem;
    flex-shrink: 0;
    color: #333333;
    font-weight: 300;
    opacity: 0.85;
    transform: none;
}

/* Hover & Active States */
.nav-sidebar .nav-link:hover:not(.menu-open > .nav-link),
.nav-sidebar .nav-link.active:not(.menu-open > .nav-link),
.nav-sidebar .nav-link.nav-classroom_view.active,
.nav-sidebar .nav-link.nav-live_classroom_view.active,
.nav-sidebar .nav-link.nav-sidebar_room.active,
.nav-sidebar .nav-link.nav-evaluation-status.active,
.nav-sidebar .nav-link.nav-new-nav-link.active {
    background: #f9f9f9 !important;
    border: 1px solid #b8c6d1 !important;
    color: #333333 !important;
    border-radius: 8px;
    box-shadow: none !important;
}

/* Icon active states */
.nav-sidebar .nav-link:hover:not(.menu-open > .nav-link) i,
.nav-sidebar .nav-link.active:not(.menu-open > .nav-link) i,
.nav-sidebar .nav-link.nav-classroom_view.active i,
.nav-sidebar .nav-link.nav-live_classroom_view.active i,
.nav-sidebar .nav-link.nav-sidebar_room.active i,
.nav-sidebar .nav-link.nav-evaluation-status.active i,
.nav-sidebar .nav-link.nav-new-nav-link.active i {
    color: #333333 !important;
    opacity: 1;
    font-size: 1.5rem;
    transform: none;
}

/* Treeview Styles */
.nav-treeview {
    padding: 8px 0;
    margin-left: 1rem;
}

.nav-treeview .nav-item {
    margin-bottom: 8px;
}

.nav-treeview .nav-link {
    padding-left: 2.5rem;
}

/* Collapsed State */
.sidebar-collapse .main-sidebar {
    margin-left: -250px;
}

.sidebar-collapse .brand-wrapper {
    justify-content: center;
}

.sidebar-collapse .brand-image {
    width: 30px;
    height: 30px;
}

.sidebar-collapse .brand-title {
    display: none;
}

/* Add this new rule to hide section labels when collapsed */
.sidebar-collapse .sidebar p[style*="color: #333333"] {
    display: none;
}

/* Accessibility */
.nav-link:focus {
    outline: 2px solid #4A90E2;
    outline-offset: 2px;
}

.nav-link:focus:not(:focus-visible) {
    outline: none;
}

/* Custom Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-sidebar,
    .sidebar {
        margin-left: 10px;
    }
    
    .brand-image {
        width: 30px;
        height: 30px;
    }
    
    .brand-title {
        font-size: 1.2rem;
    }
    
    .nav-sidebar .nav-link {
        padding: 0.5rem 0.75rem;
    }
    
    .nav-treeview {
        margin-left: 0.5rem;
    }
}

/* Dark Mode */
@media (prefers-color-scheme: dark) {
    :root {
        --sidebar-bg: #1a1a1a;
        --text-color: #ffffff;
        --border-color: #333;
    }
}


.sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
    /* Even lighter grey version of the sidebar active state */
background-color: #dee2e6; /* Very light grey shade */
color: #fff; /* Keep text color white */
border-radius: 10px;
}


</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent default scroll behavior on nav links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Only prevent default if it's not an external link
            if (this.getAttribute('href').startsWith('./')) {
                e.preventDefault();
                const href = this.getAttribute('href');
                window.location.replace(href); // Use replace instead of href to prevent scroll
            }
        });
    });

    // Get current page from URL
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'home';
    
    // Map of special page cases
    const pageMapping = {
        'Classroom': 'classroom_view',
        'LiveClassroom': 'live_classroom_view',
        'Room': 'sidebar_room',
        'evaluate': 'evaluation-status',
        'Completed Eval': 'evaluation-completed'
    };
    
    // Get the correct identifier based on the mapping or use the original page
    const pageIdentifier = pageMapping[page] || page;
    
    // Find and activate current page link
    const currentLink = document.querySelector(`.nav-link.nav-${pageIdentifier}`);
    
    if (currentLink) {
        currentLink.classList.add('active');
        
        // Handle tree view items
        if (currentLink.classList.contains('tree-item')) {
            const parentTreeview = currentLink.closest('.nav-treeview');
            if (parentTreeview) {
                const parentLink = parentTreeview.previousElementSibling;
                const parentItem = parentTreeview.parentElement;
                
                parentLink.classList.add('active');
                parentItem.classList.add('menu-open');
            }
        }
        
        // Handle menu items with tree view
        if (currentLink.classList.contains('nav-is-tree')) {
            currentLink.parentElement.classList.add('menu-open');
        }
    }
    
    // Hamburger menu functionality
    const menuToggle = document.querySelector('[data-widget="pushmenu"]');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapse');
        });
    }
});
</script>

<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
