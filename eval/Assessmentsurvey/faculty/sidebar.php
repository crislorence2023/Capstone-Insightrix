<aside class="main-sidebar sidebar-light-primary elevation-4">
    <div class="dropdown">
        <a href="./" class="brand-link">
            <div class="brand-wrapper">
                <img src="./logo/Evalucator.png" alt="Evalucator Logo" class="brand-image">
                <span class="brand-title"><b>Instructor</b></span>
            </div>
        </a>
    </div>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item dropdown">
                    <a href="./" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=result" class="nav-link nav-result">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>Evaluation Result</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
/* Main Sidebar Container */
.main-sidebar {
    font-family: 'Montserrat', sans-serif;
    background-color: #FFFFFF;
    border-right: 1px solid #e2e8f0;
    transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-bottom-right-radius: 15px;
    border-top-right-radius: 20px;
}

/* Brand Link and Logo Styles */
.brand-link {
    position: relative;
    padding: 0.8rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    background-color: #FFFFFF !important;
    transition: all 0.3s ease;
    text-decoration: none;
    border-top-right-radius: 20px;
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
    padding-top: 10px;
}

/* Navigation Items Styling */
.nav-sidebar .nav-item {
    margin-bottom: 0.5rem;
}

.nav-sidebar .nav-link {
    color: #191919 !important;
    border-radius: 10px;
    box-shadow: none !important;
    transition: background-color 0.3s ease, color 0.3s ease;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    margin-left: 5px;
    margin-right: 5px;
}

.nav-sidebar .nav-link i {
    color: #191919 !important;
    margin-right: 0.5rem;
    font-size: 1.1rem;
    transition: color 0.3s ease;
}

/* Hover and Active States */
.nav-sidebar .nav-link:hover,
.nav-sidebar .nav-link.active {
    background: #f9f9f9 !important;
    border: 1px solid #d3d3d3 !important;
    color: #333 !important;
    border-radius: 10px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.nav-sidebar .nav-link:hover i,
.nav-sidebar .nav-link.active i {
    color: teal !important;
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

/* Enhanced Hover Effects */
.nav-link:hover {
    transform: translateX(2px);
}

/* Responsive Styles */
@media (max-width: 768px) {
    .main-sidebar {
        margin-left: -250px;
    }

    .sidebar-open .main-sidebar {
        margin-left: 0;
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
</style>

<script>
$(document).ready(function(){
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
    if(s!='')
        page = page+'_'+s;
    if($('.nav-link.nav-'+page).length > 0){
        $('.nav-link.nav-'+page).addClass('active')
        if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
            $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
        }
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
            $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }
    }

    function toggleSidebar() {
        $('body').toggleClass('sidebar-collapse');
        $('body').toggleClass('sidebar-open');
        
        // Force a re-flow to ensure proper rendering
        $('.main-sidebar').hide().show(0);
    }

    // Hamburger menu click event
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        toggleSidebar();
    });

    // Check and set initial state
    function checkSidebarState() {
        if ($(window).width() <= 768) {
            $('body').addClass('sidebar-collapse');
        } else {
            $('body').removeClass('sidebar-collapse');
        }
    }

    // Run on page load
    checkSidebarState();

    // Run on window resize
    $(window).resize(function() {
        checkSidebarState();
    });
});
</script>