<!-- Left Sidebar Start -->
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">

            <!-- Logo Section -->
            <div class="logo-box">
                <a href="{{ route('any', 'index') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-light.png" alt="" height="24">
                    </span>
                </a>
                <a href="{{ route('any', 'index') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-dark.png" alt="" height="24">
                    </span>
                </a>
            </div>

            <!-- Main Navigation -->
            <ul id="side-menu">
                <li class="menu-title">Menu</li>

                <li>
                    <a href="{{ route('any', 'index') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                <!-- Product Section -->
                <li>
                    <a href="#sidebarProduct" data-bs-toggle="collapse">
                        <i data-feather="box"></i>
                        <span> Product </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarProduct">
                        <ul class="nav-second-level">
                            <li><a href="{{ route('second', ['products', 'categories']) }}"
                                    class="tp-link">Categories</a></li>
                            <li><a href="{{ route('second', ['products', '']) }}" class="tp-link">Products</a></li>
                            <li><a href="{{ route('second', ['products', 'units']) }}" class="tp-link">Units</a></li>
                            <li><a href="{{ route('second', ['products', 'brands']) }}" class="tp-link">Brands</a></li>
                        </ul>
                    </div>
                </li>

                <!-- Core Operations -->
                <li class="menu-title">Operations</li>

                <li>
                    <a href="{{ route('second', ['purchases', '']) }}" class="tp-link">
                        <i data-feather="shopping-cart"></i>
                        <span> Purchases </span>
                    </a>
                </li>
                <li>
                    <a href="#sidebarSales" data-bs-toggle="collapse">
                        <i data-feather="box"></i>
                        <span> Sales </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarSales">
                        <ul class="nav-second-level">
                            <li><a href="{{ route('second', ['sales', '']) }}" class="tp-link">Sales</a></li>

                            <li><a href="{{ route('sale-returns.index') }}" class="tp-link">Return Sales</a>
                            </li>

                        </ul>
                    </div>
                </li>



                <li>
                    <a href="{{ route('second', ['inventory', 'transactions']) }}" class="tp-link">
                        <i data-feather="database"></i>
                        <span> Inventory Transactions </span>
                    </a>
                </li>

                <!-- Management -->
                <li class="menu-title">Management</li>

                <li>
                    <a href="{{ route('second', ['customers', '']) }}" class="tp-link">
                        <i data-feather="users"></i>
                        <span> Customers </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('second', ['suppliers', '']) }}" class="tp-link">
                        <i data-feather="truck"></i>
                        <span> Suppliers </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('second', ['invoices', '']) }}" class="tp-link">
                        <i data-feather="file-text"></i>
                        <span> Invoices </span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="menu-title">Settings</li>
                <li>
                    <a href="#sidebarSettings" data-bs-toggle="collapse">
                        <i data-feather="box"></i>
                        <span> Settings </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarSettings">
                        <ul class="nav-second-level">
                            <li><a href="{{ route('settings.general') }}" class="tp-link">General Settings</a></li>
                            <li><a href="{{ route('settings.currencies') }}" class="tp-link"> Currencies</a></li>

                        </ul>
                    </div>
                </li>


                <!-- Reporting -->
                <li class="menu-title">Reports & Extras</li>

                <li>
                    <a href="{{ route('second', ['reports', '']) }}" class="tp-link">
                        <i data-feather="bar-chart-2"></i>
                        <span> Reports </span>
                    </a>
                </li>


            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- Left Sidebar End -->
