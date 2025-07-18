<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ Route('home') }}">PLN APPS</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ Route('home') }}">APPS</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Menu</li>

            <li class="nav-item dropdown">
                <a href="#"
                    class="nav-link has-dropdown"><i class="far fa-file-alt"></i><span>Master Data</span></a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="nav-link"
                            href="{{ url('users') }}">Data User</a>
                    </li>
                    <li>
                        <a class="nav-link"
                            href="{{ url('pelanggan') }}">Data Pelanggan</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ url('searchdatapelanggan') }}"
                    class="nav-link"><i class="fas fa-plug"></i><span>Search Data Pelanggan</span></a>
            </li>


        </ul>
    </aside>
</div>
