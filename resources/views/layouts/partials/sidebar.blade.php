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
                
                <!-- Sección Principal -->
                <div class="nav-section">
                    <span class="nav-section-title">PRINCIPAL</span>
                </div>
                
                <ul class="nav nav-main">
                    @if(isset(Auth::user()->email))
                        <li class="{{ ($path[0] === 'documents' || $path[0] === 'dashboard')?'nav-active':'' }}">
                            <a class="nav-link" href="{{route('documents_index')}}">
                                <div class="nav-icon nav-icon-documents">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <span>Documentos</span>
                                <span class="nav-badge">DIAN</span>
                            </a>
                        </li>
                        
                        <li class="{{ ($path[0] === 'taxes')?'nav-active':'' }}">
                            <a class="nav-link" href="{{route('tax_index')}}">
                                <div class="nav-icon nav-icon-taxes">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <span>Impuestos</span>
                            </a>
                        </li>
                        
                        <!-- Sección Configuración -->
                        <div class="nav-section">
                            <span class="nav-section-title">CONFIGURACIÓN</span>
                        </div>
                        
                        <li class="nav-parent {{ in_array($path[0], ['configuration'])?'nav-active nav-expanded':'' }}">
                            <a class="nav-link" href="#">
                                <div class="nav-icon nav-icon-company">
                                    <i class="fas fa-building"></i>
                                </div>
                                <span>Empresas</span>
                                <i class="fas fa-chevron-down nav-arrow"></i>
                            </a>
                            <ul class="nav nav-children">
                                <li class="{{ (request()->routeIs('configuration_index'))?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('configuration_index')}}">
                                        <i class="fas fa-th-list"></i>
                                        <span>Ver Empresas</span>
                                    </a>
                                </li>
                                <li class="{{ (request()->routeIs('configuration_admin'))?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('configuration_admin')}}">
                                        <i class="fas fa-plus"></i>
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
                                    <div class="nav-icon nav-icon-inbox">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <span>Documentos Recibidos</span>
                                </a>
                                <input type="submit" id="action-button" style="display: none;" >
                            </form>
                        </li>
                        <li>
                            <form action="{{ url('/customer-password/'.$comp_id.'/'.$cust_id) }}" method="GET">
                                @csrf
                                <a class="nav-link" href="javascript:;" onclick="$('#action-button2').click();">
                                    <div class="nav-icon nav-icon-key">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <span>Cambiar Password</span>
                                </a>
                                <input type="submit" id="action-button2" style="display: none;" >
                            </form>
                        </li>
                    @endif
                </ul>
            </nav>
            
            <!-- Footer del Sidebar -->
            <div class="sidebar-footer">
                <div class="sidebar-footer-content">
                    <div class="api-status">
                        <span class="status-dot"></span>
                        <span class="status-text">API Activa</span>
                    </div>
                    <div class="version-info">v2.1</div>
                </div>
            </div>
        </div>
        <script>
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
/* ============================================
   SIDEBAR PROFESIONAL - APIDIAN
   ============================================ */

/* Logo */
.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 5px 0;
}

.logo-icon {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, #f97316, #ea580c);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4);
    transition: transform 0.3s ease;
}

.logo-icon:hover {
    transform: scale(1.05);
}

.logo-icon svg {
    width: 24px;
    height: 24px;
    color: white;
}

.logo-text {
    font-size: 1.4rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: 1px;
}

/* Secciones del Nav */
.nav-section {
    padding: 20px 20px 8px 20px;
}

.nav-section-title {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

/* Nav Icons con colores únicos */
.nav-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.nav-icon i {
    font-size: 16px;
    color: white;
}

/* Colores únicos para cada sección */
.nav-icon-documents {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.nav-icon-taxes {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.nav-icon-company {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.nav-icon-inbox {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.nav-icon-key {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* Nav Links */
ul.nav-main > li > a {
    display: flex !important;
    align-items: center !important;
    padding: 10px 16px !important;
    margin: 4px 12px !important;
    border-radius: 12px !important;
    transition: all 0.3s ease !important;
    color: #cbd5e1 !important;
    text-decoration: none !important;
}

ul.nav-main > li > a:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
}

ul.nav-main > li > a:hover .nav-icon {
    transform: scale(1.1);
}

ul.nav-main > li.nav-active > a {
    background: rgba(249, 115, 22, 0.15) !important;
    color: #f97316 !important;
    border-left: 3px solid #f97316;
    margin-left: 9px !important;
}

ul.nav-main > li > a span {
    font-weight: 500;
    font-size: 14px;
}

/* Badge */
.nav-badge {
    margin-left: auto;
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
    font-size: 9px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    letter-spacing: 0.5px;
}

/* Arrow */
.nav-arrow {
    margin-left: auto;
    font-size: 10px;
    transition: transform 0.3s ease;
    color: #64748b;
}

.nav-parent.nav-expanded > a .nav-arrow {
    transform: rotate(180deg);
}

/* Children */
ul.nav-main ul.nav-children {
    background: rgba(0, 0, 0, 0.2) !important;
    border-radius: 12px !important;
    margin: 8px 16px !important;
    padding: 8px !important;
    border-left: 2px solid #334155;
}

ul.nav-main ul.nav-children > li > a {
    padding: 10px 16px !important;
    margin: 2px 0 !important;
    border-radius: 8px !important;
    color: #94a3b8 !important;
    font-size: 13px !important;
}

ul.nav-main ul.nav-children > li > a i {
    width: 20px;
    margin-right: 10px;
    font-size: 12px;
    color: #64748b;
}

ul.nav-main ul.nav-children > li > a:hover {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #f97316 !important;
}

ul.nav-main ul.nav-children > li > a:hover i {
    color: #f97316;
}

ul.nav-main ul.nav-children > li.nav-active > a {
    background: rgba(249, 115, 22, 0.1) !important;
    color: #f97316 !important;
}

ul.nav-main ul.nav-children > li.nav-active > a i {
    color: #f97316;
}

/* Sidebar Footer */
.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 16px 20px;
    background: linear-gradient(to top, #1e293b, transparent);
}

.sidebar-footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.api-status {
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #22c55e;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.status-text {
    font-size: 12px;
    color: #94a3b8;
    font-weight: 500;
}

.version-info {
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.1);
    padding: 4px 10px;
    border-radius: 20px;
}

/* Nano Content padding for footer */
.nano-content {
    padding-bottom: 80px !important;
}
</style>
