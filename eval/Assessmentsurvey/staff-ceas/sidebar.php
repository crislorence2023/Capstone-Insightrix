<!-- Sidebar Component -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="./" class="brand-link">
        <div class="brand-wrapper">
            <img src="logo/Evalucator.png" alt="Evalucator Logo" class="brand-image">
            <span class="brand-title"><b>CME</b></span>
        </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                
                <p style="color: #333333; margin-left: 1rem; margin-top: .5rem; font-weight: 500;">General</p>
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="staff_cme_index.php" class="nav-link nav-home">
                        <i class="ri-home-line"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="staff_cme_index.php?page=evaluation-status" class="nav-link nav-evaluation-status">
                        <i class="ri-clipboard-line"></i>
                        <p>Evaluation Status</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="staff_cme_index.php?page=Room" class="nav-link nav-class_room">
                        <i class="ri-group-line"></i>
                        <p>Room</p>
                    </a>
                </li>

                <p style="color: #333333; margin-left: 1rem; margin-top: 1rem; font-weight: 500;">Academic Section</p>
                <!-- Department -->
               
                
                <!-- Academic -->
                <li class="nav-item">
                    <a href="staff_cme_index.php?page=subject_list" class="nav-link nav-subject_list">
                        <i class="ri-book-line"></i>
                        <p>Subjects</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="staff_cme_index.php?page=class_list" class="nav-link nav-class_list">
                        <i class="ri-group-line"></i>
                        <p>Classes</p>
                    </a>
                </li>

                <p style="color: #333333; margin-left: 1rem; margin-top: 1rem; font-weight: 500;">Create And Assign</p>
                <!-- Faculties -->
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_faculty">
                        <i class="ri-user-line"></i>
                        <p>
                            Faculties
                            <i class="right ri-arrow-right-s-line"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="staff_cme_index.php?page=new_faculty" class="nav-link nav-new_faculty tree-item">
                                <i class="ri-add-line"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_cme_index.php?page=faculty_list" class="nav-link nav-faculty_list tree-item">
                                <i class="ri-list-unordered"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Students -->
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_student">
                        <i class="ri-user-line"></i>
                        <p>
                            Students
                            <i class="right ri-arrow-right-s-line"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="staff_cme_index.php?page=new_student" class="nav-link nav-new_student tree-item">
                                <i class="ri-add-line"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_cme_index.php?page=student_list" class="nav-link nav-student_list tree-item">
                                <i class="ri-list-unordered"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="staff_cme_index.php?page=AssignClassSubjects" class="nav-link nav-assign_faculty">
                        <i class="ri-calendar-line"></i>
                        <p>Assign Faculty</p>
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

/* Brand Styles */
.brand-link {
    padding: 0.8rem 1rem;
    border-bottom: 1px solid var(--border-color);
    background-color: var(--sidebar-bg) !important;
    text-decoration: none;
    transition: all 0.3s ease;
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
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--text-color);
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
    
}

.nav-sidebar .nav-link i {
    color: #333333 !important;
    margin-right: 0.5rem;
    font-size: 1.35rem;
}

/* Hover & Active States */
.nav-sidebar .nav-link:hover:not(.menu-open > .nav-link),
.nav-sidebar .nav-link.active:not(.menu-open > .nav-link) {
    background: linear-gradient(135deg, #4A90E2, #50E3C2) !important;
    color: white !important;
    border-radius: 10px;
}

.nav-sidebar .nav-link:hover:not(.menu-open > .nav-link) i,
.nav-sidebar .nav-link.active:not(.menu-open > .nav-link) i {
    color: white !important;
    
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

/* Add these new styles */
.sidebar-collapse .sidebar p {
    display: none;  /* Hide the section titles when collapsed */
}

.sidebar-collapse .nav-sidebar .nav-link i {
    font-size: 1.35rem;
    margin-right: 0;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current page from URL
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'home';
    const section = urlParams.get('s') || '';
    
    // Construct page identifier
    const pageIdentifier = section ? `${page}_${section}` : page;
    
    // Special cases mapping
    const pageMapping = {
        'Room': 'class_room',
        'AssignClassSubjects': 'assign_faculty'
    };
    
    // Use mapped identifier if it exists, otherwise use original
    const mappedIdentifier = pageMapping[pageIdentifier] || pageIdentifier;
    
    // Find and activate current page link
    const currentLink = document.querySelector(`.nav-link.nav-${mappedIdentifier}`);
    
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