<!-- Left Sidebar Start -->
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

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

            <ul id="side-menu">

                <li class="menu-title">Menu</li>

                <li>
                    <a href="{{ route('any', 'index') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                <li>
                    <a href="#sidebarProduct" data-bs-toggle="collapse">
                        <i data-feather="box"></i> <!-- أيقونة تناسب المنتجات -->
                        <span> Product </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarProduct">
                        <ul class="nav-second-level">
                            <li><a href="{{ route('second', ['product', 'categories']) }}"
                                    class="tp-link">Categories</a></li>
                            <li><a href="{{ route('second', ['product', '']) }}" class="tp-link">Products</a></li>
                            <li><a href="{{ route('second', ['product', 'units']) }}" class="tp-link">Units</a></li>
                            <li><a href="{{ route('second', ['product', 'brands']) }}" class="tp-link">Brands</a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="{{ route('second', ['purchases', '']) }}" class="tp-link">
                        <i data-feather="shopping-cart"></i>
                        <span> Purchases </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('second', ['sales', '']) }}" class="tp-link">
                        <i data-feather="dollar-sign"></i>
                        <span> Sales </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('second', ['suppliers', '']) }}" class="tp-link">
                        <i data-feather="truck"></i>
                        <span> Suppliers </span>
                    </a>
                </li>


                <li class="menu-title">Pages</li>


            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
<!-- Left Sidebar End -->
