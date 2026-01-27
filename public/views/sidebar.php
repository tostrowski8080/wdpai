<nav class="sidebar-navigation">
    <div class="logo">Activity Helper</div>
    
    <ul class="nav-list">
        <li class="nav-item">
            <a href="/dashboard" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <span class="material-icons-round">grid_view</span>
                <span class="link-text">Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="/calendar" class="<?= $currentPage === 'calendar' ? 'active' : '' ?>">
                <span class="material-icons-round">calendar_today</span>
                <span class="link-text">Calendar</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="/add-activity" class="<?= $currentPage === 'add-activity' ? 'active' : '' ?>">
                <span class="material-icons-round">add_circle</span>
                <span class="link-text">Activity</span> 
                </a>
        </li>

        <li class="nav-item">
            <a href="/add-category" class="<?= $currentPage === 'add-category' ? 'active' : '' ?>">
                <span class="material-icons-round">category</span>
                <span class="link-text">Category</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="/profile" class="<?= $currentPage === 'profile' ? 'active' : '' ?>">
                <span class="material-icons-round">person</span>
                <span class="link-text">Profile</span>
            </a>
        </li>
    </ul>

    <a href="/logout" class="logout-btn">
        <span class="material-icons-round">logout</span>
        Logout
    </a>
</nav>