@php
$path = explode('/', request()->path());
$path[1] = (array_key_exists(1, $path)> 0)?$path[1]:'';
$path[2] = (array_key_exists(2, $path)> 0)?$path[2]:'';
$path[0] = ($path[0] === '')?'documents':$path[0];
$comp_id = $path[1];
$cust_id = $path[2];
@endphp

<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <div class="sidebar-title">
            <div class="sidebar-logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="logo-text">APIDIAN</span>
            </div>
        </div>
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    @if(isset(Auth::user()->email))
                        <li class="{{ ($path[0] === 'documents' || $path[0] === 'dashboard')?'nav-active':'' }}">
                            <a class="nav-link" href="{{route('documents_index')}}">
                                <i class="fas fa-file-invoice" aria-hidden="true"></i>
                                <span>Documentos</span>
                            </a>
                        </li>
                        <li class="{{ ($path[0] === 'taxes')?'nav-active':'' }}">
                            <a class="nav-link" href="{{route('tax_index')}}">
                                <i class="fas fa-percentage" aria-hidden="true"></i>
                                <span>Impuestos</span>
                            </a>
                        </li>
                        <li class="nav-parent {{ in_array($path[0], ['configuration'])?'nav-active nav-expanded':'' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-building" aria-hidden="true"></i>
                                <span>Empresas</span>
                            </a>
                            <ul class="nav nav-children">
                                <li class="{{ (request()->routeIs('configuration_index'))?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('configuration_index')}}">
                                        <i class="fas fa-list"></i>
                                        <span>Lista de Empresas</span>
                                    </a>
                                </li>
                                <li class="{{ (request()->routeIs('configuration_admin'))?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('configuration_admin')}}">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>Nueva Empresa</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="{{ ($path[0] === 'dashboard')?'nav-active':'nav-active' }}">
                            <form action="{{ url('/okcustomerlogin/'.$comp_id.'/'.$cust_id) }}" method="POST">
                                @csrf
                                <a class="nav-link" href="javascript:;" onclick="$('#action-button').click();">
                                    <input type="hidden" name="verificar" value="FALSE"/>
                                    <i class="fas fa-inbox" aria-hidden="true"></i>
                                    <span>Documentos Recibidos</span>
                                </a>
                                <input type="submit" id="action-button" style="display: none;" >
                            </form>
                        </li>
                        <li class="{{ ($path[0] === 'dashboard')?'nav-active':'nav-active' }}">
                            <form action="{{ url('/customer-password/'.$comp_id.'/'.$cust_id) }}" method="GET">
                                @csrf
                                <a class="nav-link" href="javascript:;" onclick="$('#action-button2').click();">
                                    <i class="fas fa-key" aria-hidden="true"></i>
                                    <span>Cambiar Password</span>
                                </a>
                                <input type="submit" id="action-button2" style="display: none;" >
                            </form>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        <script>
            // Maintain Scroll Position
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');
                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>
    </div>
</aside>

<style>
/* Sidebar Logo */
.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 12px;
}

.logo-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #f97316, #ea580c);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4);
}

.logo-icon svg {
    width: 24px;
    height: 24px;
    color: white;
}

.logo-text {
    font-size: 1.25rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.5px;
}

/* Nav Children Icons */
ul.nav-main ul.nav-children > li > a i {
    margin-right: 10px;
    width: 16px;
    font-size: 12px;
}
</style>
