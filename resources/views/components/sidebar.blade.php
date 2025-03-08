<div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
            <img src="{{ asset('assets/images/logos/favicon.svg') }}" width="60" alt="" />
        </a>
        <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
        </div>
    </div>
    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
        <ul id="sidebarnav">
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"
                    aria-expanded="false">
                    <span>
                        <i class="ti ti-layout-dashboard"></i>
                    </span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Management</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::is('students') ? 'active' : '' }}" href="{{ route('students') }}"
                    aria-expanded="false">
                    <span>
                        <i class="ti ti-user"></i>
                    </span>
                    <span class="hide-menu">Students</span>
                </a>
            </li>
            @hasrole('Super_Admin')
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::is('cashier') ? 'active' : '' }}" href="{{ route('cashier') }}"
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="hide-menu">Cashiers</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::is('reports') ? 'active' : '' }}" href="{{ route('reports') }}"
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="hide-menu">Reports</span>
                    </a>
                </li>
            @endhasrole
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Library Management</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::is('books') ? 'active' : '' }}" href="{{ route('books') }}"
                    aria-expanded="false">
                    <span>
                        <i class="ti ti-book"></i>
                    </span>
                    <span class="hide-menu">Books</span>
                </a>
            </li>
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Payments</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::is('add.payment') ? 'active' : '' }}"
                    href="{{ route('add.payment') }}" aria-expanded="false">
                    <span>
                        <i class="ti ti-cards"></i>
                    </span>
                    <span class="hide-menu">Add Payments</span>
                </a>
            </li>
        </ul>

    </nav>
    <!-- End Sidebar navigation -->
</div>
