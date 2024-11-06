<aside class="main-sidebar sidebar-light-primary elevation-4">
    <div class="dropdown">
        <a href="./" class="brand-link">
            <div class="brand-wrapper">
                <img src="./logo/Evalucator.png" alt="Evalucator Logo" class="brand-image">
                <span class="brand-title"><b>Insightrix</b></span>
            </div>
        </a>
    </div>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard Link -->
                <li class="nav-item dropdown">
                    <a href="./" class="nav-link nav-home">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=department" class="nav-link nav-academic_list">
                        <i class="nav-icon fas fa-calendar"></i>
                        <p>Department</p>
                    </a>
                </li>
                
                <!-- Subjects Link -->
                <li class="nav-item dropdown">
                    <a href="./index.php?page=subject_list" class="nav-link nav-subject_list">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Subjects</p>
                    </a>
                </li>
                
                <!-- Classes Link -->
                <li class="nav-item dropdown">
                    <a href="./index.php?page=class_list" class="nav-link nav-class_list">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Classes</p>
                    </a>
                </li>
                
                <!-- Academic Year Link -->
                <li class="nav-item dropdown">
                    <a href="./index.php?page=academic_list" class="nav-link nav-academic_list">
                        <i class="nav-icon fas fa-calendar"></i>
                        <p>Academic Year</p>
                    </a>
                </li>
                
              

                <!-- Faculties Section -->
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_faculty">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>
                            Faculties
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./index.php?page=new_faculty" class="nav-link nav-new_faculty tree-item">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./index.php?page=faculty_list" class="nav-link nav-faculty_list tree-item">
                                <i class="fas fa-list nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Students Section -->
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_student">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>
                            Students
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./index.php?page=new_student" class="nav-link nav-new_student tree-item">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./index.php?page=student_list" class="nav-link nav-student_list tree-item">
                                <i class="fas fa-list nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>


                
                <!-- Evaluation Report Link 
                <li class="nav-item dropdown">
                    <a href="./index.php?page=report" class="nav-link nav-report">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Evaluation Report</p>
                    </a>
                </li> -->


                <!-- Manage Survey Section -->
<li class="nav-item">
    <a href="#" class="nav-link nav-manage_survey">
        <i class="nav-icon fas fa-chalkboard-teacher"></i>
        <p>
            Manage Survey
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="./index.php?page=questionnaire" class="nav-link nav-questionnaire tree-item">
                <i class="fas fa-file-alt nav-icon"></i>
                <p>Questionnaires</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="./index.php?page=view_surveys" class="nav-link nav-view_surveys tree-item">
                <i class="fas fa-database nav-icon"></i>
                <p>View Surveys Data</p>
            </a>
        </li>
    </ul>
</li>

                
                <!-- Users Section 
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_user">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                            Users
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./index.php?page=new_user" class="nav-link nav-new_user tree-item">
                                <i class="fas fa-user-plus nav-icon"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./index.php?page=user_list" class="nav-link nav-user_list tree-item">
                                <i class="fas fa-list nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li> -->
            </ul> 
        </nav>
    </div>
</aside>



<style>
 /* Main Sidebar Container */
.main-sidebar {
    font-family: 'Montserrat', sans-serif;
    background-color: #FFFFFF;
    
    border: 0px 0px 0px 10px solid #e2e8f0;
    transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    animation: smooth;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Brand Link and Logo Styles */
.brand-link {
    position: relative;
    padding: 0.8rem 1rem;
    border: 1px solid #e2e8f0;
    background-color: #FFFFFF !important;
    border-radius: 0px 0px 0 0;
    transition: all 0.3s ease;
    overflow: visible;
    text-decoration: none;
}

.brand-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
}

.brand-image {
    display: block !important;
    width: 40px;
    height: 40px;
    transition: all 0.3s ease;
    object-fit: contain;
}

.brand-title {
    font-size: 1.5rem;
    font-weight: bold;
    transition: all 0.3s ease;
    color: #191919;
}

/* Sidebar General Styles */
.sidebar {
    margin-left: 5px;
    margin-right: 5px;
    padding-top: 10px;
}

/* Navigation Items Styling */
.nav-sidebar .nav-item {
    margin-bottom: 0.5rem;
}

.nav-sidebar .nav-link {
    color: #637381 !important;
    border-radius: 10px;
    box-shadow: none !important;
    transition: background-color 0.3s ease, color 0.3s ease;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
     opacity: 0.75;
}

.nav-sidebar .nav-link i {
    color: #191919 !important;
    margin-right: 0.5rem;
    font-size: 1.1rem;
    transition: color 0.3s ease;
}

/* Hover and Active States */
.nav-sidebar .nav-link:hover:not(.menu-open > .nav-link) {
    background: linear-gradient(135deg, #4A90E2, #50E3C2) !important;
    color: white !important;
    border-radius: 10px;
}

.nav-sidebar .nav-link:hover:not(.menu-open > .nav-link) i,
.nav-sidebar .nav-link.active:not(.menu-open > .nav-link) i {
    color: white !important;
}

.nav-sidebar .nav-link.active:not(.menu-open > .nav-link) {
    background: linear-gradient(135deg, #4A90E2, #50E3C2) !important;
    color: white !important;
    border-radius: 10px;
}

/* Dropdown/Treeview Styles */
.nav-treeview {
    padding-top: 8px;
    padding-bottom: 8px;
    margin-left: 1rem;
}

.nav-treeview .nav-item {
    margin-bottom: 8px;
}

.nav-sidebar .menu-open {
    margin-bottom: 16px;
}

.nav-sidebar .nav-treeview .nav-link {
    padding-left: 2.5rem;
}

.nav-treeview .nav-link.active {
    background: linear-gradient(135deg, #4A90E2, #50E3C2) !important;
    color: white !important;
}

.nav-treeview .nav-link.active i {
    color: white !important;
}

/* Collapsed Sidebar States */
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

.sidebar-collapse .brand-link {
    width: 4.6rem;
}

.sidebar-collapse .nav-sidebar .nav-link i {
    margin-right: 0;
}

/* Hover Effects for Collapsed Sidebar */
.sidebar-collapse .brand-link:hover .brand-wrapper {
    position: relative;
}

.sidebar-collapse .brand-link:hover .brand-title {
    display: block;
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background-color: white;
    padding: 0.5rem 1rem;
    border-radius: 0 4px 4px 0;
    box-shadow: 4px 0 8px rgba(0, 0, 0, 0.1);
    white-space: nowrap;
    z-index: 1000;
}

/* Menu Open States */
.nav-sidebar .menu-open > .nav-link {
    background: transparent !important;
    color: #191919 !important;
}

.nav-sidebar .menu-open > .nav-link i {
    color: #191919 !important;
}

/* Dropdown Arrow Animation */
.nav-sidebar .nav-link .right {
    transition: transform 0.3s ease;
    margin-left: auto;
}

.nav-sidebar .menu-open > .nav-link .right {
    transform: rotate(90deg);
}

/* Enhanced Hover Effects */
.nav-link:hover {
    transform: translateX(2px);
}

.nav-treeview .nav-link:hover {
    transform: translateX(4px);
}

/* Additional Spacing and Layout */
.nav-sidebar {
    margin-top: 1rem;
}

.nav-sidebar > .nav-item {
    margin-bottom: 0.5rem;
}

.nav-link p {
    margin-bottom: 0;
    margin-left: 0.5rem;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .main-sidebar {
        margin-left: 0;
    }

    .sidebar {
        margin-left: 10px;
    }

    .brand-wrapper {
        justify-content: flex-start;
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

/* Dark Mode Support (Optional) */
@media (prefers-color-scheme: dark) {
    .main-sidebar {
        background-color: #1a1a1a;
    }

    .brand-link {
        background-color: #1a1a1a !important;
        border-color: #333;
    }

    .nav-sidebar .nav-link,
    .nav-sidebar .nav-link i,
    .brand-title {
        color: #ffffff !important;
    }

    .sidebar-collapse .brand-link:hover .brand-title {
        background-color: #1a1a1a;
        color: #ffffff;
    }
}

/* Accessibility Improvements */
.nav-link:focus {
    outline: 2px solid #4A90E2;
    outline-offset: 2px;
}

.nav-link:focus:not(:focus-visible) {
    outline: none;
}

/* Smooth Scrolling for Sidebar */
.sidebar {
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

/* Custom Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.3);
}
/* Responsive styles */
@media (max-width: 768px) {
    .main-sidebar {
        margin-left: 10px;
    }

    .sidebar {
        margin-left: 10px;
    }
}

/* Additional fix for dropdown arrow rotation */
.nav-sidebar .nav-link .right {
    transition: transform 0.3s ease;
}

.nav-sidebar .menu-open > .nav-link .right {
    transform: rotate(90deg);
}
</style>


<script>
   $(document).ready(function(){
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
    if(s!='')
        page = page+'_'+s;
    if($('.nav-link.nav-'+page).length > 0){
        $('.nav-link.nav-'+page).addClass('active');
        if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active');
            $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open');
        }
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
            $('.nav-link.nav-'+page).parent().addClass('menu-open');
        }
    }

    // Add functionality for the hamburger menu
    $('[data-widget="pushmenu"]').on('click', function() {
        $('body').toggleClass('sidebar-collapse');
    });
});

</script>