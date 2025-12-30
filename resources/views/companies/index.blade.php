@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-dark text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-building mr-2"></i>Gestión de Empresas</h5>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <input type="text" id="search" class="form-control form-control-sm" placeholder="Buscar..." style="width: 200px; border-radius: 4px;" onkeyup="debounceSearch()">
                <a href="/configuration_admin" class="btn btn-sm btn-orange"><i class="fa fa-plus"></i> Nueva</a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>NIT</th>
                        <th>Empresa</th>
                        <th>Email</th>
                        <th>Ambiente</th>
                        <th>Estado</th>
                        <th>Docs</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="companies-body">
                    <tr><td colspan="9" class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Editar Empresa - Grande y Profesional -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl-custom" role="document">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <h5 class="modal-title text-white">
                    <i class="fa fa-edit mr-2"></i>Editar Empresa: <span id="modal-company-name" class="font-weight-bold"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <!-- Tabs -->
                <ul class="nav nav-tabs nav-tabs-custom" id="companyTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="empresa-tab" data-toggle="tab" href="#tab-empresa" role="tab">
                            <i class="fa fa-building mr-1"></i> Empresa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="software-tab" data-toggle="tab" href="#tab-software" role="tab">
                            <i class="fa fa-cog mr-1"></i> Software
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="certificado-tab" data-toggle="tab" href="#tab-certificado" role="tab">
                            <i class="fa fa-certificate mr-1"></i> Certificado
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="resoluciones-tab" data-toggle="tab" href="#tab-resoluciones" role="tab">
                            <i class="fa fa-file-alt mr-1"></i> Resoluciones
                        </a>
                    </li>
                </ul>
                <!-- Tab Content -->
                <div class="tab-content" id="companyTabsContent">
                    <div class="tab-pane fade show active p-4" id="tab-empresa" role="tabpanel"></div>
                    <div class="tab-pane fade p-4" id="tab-software" role="tabpanel"></div>
                    <div class="tab-pane fade p-4" id="tab-certificado" role="tabpanel"></div>
                    <div class="tab-pane fade p-4" id="tab-resoluciones" role="tabpanel"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Extra Grande */
.modal-xl-custom {
    max-width: 1100px;
    width: 95%;
    margin: 20px auto;
}
.modal-content {
    border: none;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

/* Header Naranja */
.bg-orange {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
}
.modal-header.bg-orange {
    border-bottom: none;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
}
.modal-header .close {
    opacity: 0.9;
    text-shadow: none;
    font-size: 28px;
}
.modal-header .close:hover {
    opacity: 1;
}

/* Tabs Personalizados */
.nav-tabs-custom {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    padding: 0 15px;
}
.nav-tabs-custom .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 12px 20px;
    margin-bottom: -2px;
    transition: all 0.2s;
}
.nav-tabs-custom .nav-link:hover {
    color: #f97316;
    border-color: transparent;
    background: transparent;
}
.nav-tabs-custom .nav-link.active {
    color: #f97316;
    background: #fff;
    border-bottom: 3px solid #f97316;
    font-weight: 600;
}

/* Botón Naranja */
.btn-orange {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 4px;
    transition: all 0.2s;
}
.btn-orange:hover {
    background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(249,115,22,0.4);
}

/* Tabla */
.table thead th {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 10px;
    border-bottom: 2px solid #dee2e6;
}
.table tbody td {
    padding: 10px;
    vertical-align: middle;
    font-size: 13px;
}

/* Badges */
.badge-env-prod {
    background: #28a745;
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
}
.badge-env-hab {
    background: #ffc107;
    color: #212529;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
}
.badge-active {
    background: #28a745;
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
}
.badge-inactive {
    background: #dc3545;
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
}

/* Dropdown Acciones */
.dropdown-menu {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border-radius: 6px;
}
.dropdown-item {
    padding: 8px 16px;
    font-size: 13px;
}
.dropdown-item:hover {
    background: #fff3e0;
}
.dropdown-item i {
    width: 20px;
    margin-right: 8px;
}

/* Formularios en Modal */
.tab-content {
    min-height: 350px;
}
.form-group label {
    font-weight: 600;
    font-size: 12px;
    color: #495057;
    margin-bottom: 5px;
}
.form-control {
    border-radius: 4px;
    border: 1px solid #ced4da;
    padding: 8px 12px;
    font-size: 14px;
}
.form-control:focus {
    border-color: #f97316;
    box-shadow: 0 0 0 0.2rem rgba(249,115,22,0.15);
}

/* Card en tabs */
.info-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 15px;
}
.info-card-title {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    margin-bottom: 10px;
}

/* Tabla resoluciones */
.table-resoluciones th {
    font-size: 11px;
    background: #f8f9fa;
}
.table-resoluciones td {
    font-size: 12px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let companies = [];
let tables = {};
let currentCompanyId = null;
let currentCompanyData = null;
let searchTimeout = null;

// Cargar al iniciar
document.addEventListener('DOMContentLoaded', function() {
    loadTables();
    loadCompanies();
});

// Debounce para búsqueda
function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadCompanies(), 300);
}

// Cargar tablas de referencia
function loadTables() {
    fetch('/companies/tables')
        .then(r => r.json())
        .then(data => { tables = data; })
        .catch(e => console.error('Error cargando tablas:', e));
}

// Cargar empresas
function loadCompanies() {
    const search = document.getElementById('search').value;
    fetch('/companies/records?search=' + encodeURIComponent(search))
        .then(r => r.json())
        .then(data => {
            companies = data.data;
            renderTable();
        })
        .catch(e => {
            console.error('Error:', e);
            document.getElementById('companies-body').innerHTML = 
                '<tr><td colspan="9" class="text-center text-danger py-4">Error al cargar datos</td></tr>';
        });
}

// Renderizar tabla
function renderTable() {
    const tbody = document.getElementById('companies-body');
    if (!companies.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4">No hay empresas registradas</td></tr>';
        return;
    }
    
    tbody.innerHTML = companies.map((c, i) => `
        <tr>
            <td>${i + 1}</td>
            <td><strong>${c.identification_number}</strong>-${c.dv || '0'}</td>
            <td>${c.name}</td>
            <td><small>${c.email}</small></td>
            <td>
                <span class="badge-env-${c.type_environment_id == 1 ? 'prod' : 'hab'}">
                    ${c.type_environment_id == 1 ? 'Producción' : 'Habilitación'}
                </span>
            </td>
            <td>
                <span class="badge-${c.state ? 'active' : 'inactive'}">
                    ${c.state ? 'Activa' : 'Inactiva'}
                </span>
            </td>
            <td><span class="badge badge-secondary">${c.documents_count}</span></td>
            <td><small>${c.created_at}</small></td>
            <td class="text-center">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                        Acciones
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="editCompany(${c.id})">
                            <i class="fa fa-edit text-primary"></i> Editar
                        </a>
                        <a class="dropdown-item" href="#" onclick="changeEnvironment(${c.id}, ${c.type_environment_id})">
                            <i class="fa fa-exchange-alt text-info"></i> Cambiar Ambiente
                        </a>
                        <a class="dropdown-item" href="#" onclick="toggleState(${c.id}, ${c.state})">
                            <i class="fa fa-${c.state ? 'ban text-warning' : 'check text-success'}"></i> 
                            ${c.state ? 'Deshabilitar' : 'Habilitar'}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="#" onclick="deleteCompany(${c.id}, '${c.identification_number}')">
                            <i class="fa fa-trash"></i> Eliminar
                        </a>
                    </div>
                </div>
            </td>
        </tr>
    `).join('');
}

// Editar empresa
function editCompany(id) {
    currentCompanyId = id;
    fetch(`/companies/${id}/data`)
        .then(r => r.json())
        .then(data => {
            currentCompanyData = data;
            document.getElementById('modal-company-name').textContent = data.company.name;
            renderEmpresaTab(data);
            renderSoftwareTab(data);
            renderCertificadoTab(data);
            renderResolucionesTab(data);
            $('#editModal').modal('show');
        })
        .catch(e => {
            Swal.fire('Error', 'No se pudo cargar la empresa', 'error');
        });
}

// Tab Empresa
function renderEmpresaTab(data) {
    const c = data.company;
    document.getElementById('tab-empresa').innerHTML = `
        <form id="form-empresa">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Identificación</label>
                        <input type="text" class="form-control" name="identification_number" value="${c.identification_number}" required>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>DV</label>
                        <input type="text" class="form-control" name="dv" value="${c.dv || ''}" maxlength="1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nombre/Razón Social</label>
                        <input type="text" class="form-control" name="name" value="${c.name}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="${c.email}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" class="form-control" name="phone" value="${c.phone || ''}">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" class="form-control" name="address" value="${c.address || ''}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Registro Mercantil</label>
                        <input type="text" class="form-control" name="merchant_registration" value="${c.merchant_registration || ''}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo Documento</label>
                        <select class="form-control" name="type_document_identification_id">
                            ${(tables.type_document_identifications || []).map(t => 
                                `<option value="${t.id}" ${t.id == c.type_document_identification_id ? 'selected' : ''}>${t.name}</option>`
                            ).join('')}
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Departamento</label>
                        <select class="form-control" name="department_id" onchange="loadMunicipios(this.value)">
                            <option value="">Seleccione...</option>
                            ${(tables.departments || []).map(d => 
                                `<option value="${d.id}" ${d.id == c.department_id ? 'selected' : ''}>${d.name}</option>`
                            ).join('')}
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Municipio</label>
                        <select class="form-control" name="municipality_id" id="select-municipio">
                            ${(tables.municipalities || []).filter(m => m.department_id == c.department_id).map(m => 
                                `<option value="${m.id}" ${m.id == c.municipality_id ? 'selected' : ''}>${m.name}</option>`
                            ).join('')}
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Organización</label>
                        <select class="form-control" name="type_organization_id">
                            ${(tables.type_organizations || []).map(t => 
                                `<option value="${t.id}" ${t.id == c.type_organization_id ? 'selected' : ''}>${t.name}</option>`
                            ).join('')}
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Régimen</label>
                        <select class="form-control" name="type_regime_id">
                            ${(tables.type_regimes || []).map(t => 
                                `<option value="${t.id}" ${t.id == c.type_regime_id ? 'selected' : ''}>${t.name}</option>`
                            ).join('')}
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Responsabilidad</label>
                        <select class="form-control" name="type_liability_id">
                            ${(tables.type_liabilities || []).map(t => 
                                `<option value="${t.id}" ${t.id == c.type_liability_id ? 'selected' : ''}>${t.name}</option>`
                            ).join('')}
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" name="state">
                            <option value="1" ${c.state ? 'selected' : ''}>Activa</option>
                            <option value="0" ${!c.state ? 'selected' : ''}>Inactiva</option>
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-orange">
                <i class="fa fa-save mr-1"></i> Guardar Empresa
            </button>
        </form>
    `;
    
    document.getElementById('form-empresa').addEventListener('submit', saveEmpresa);
}

// Cargar municipios por departamento
function loadMunicipios(departmentId) {
    const select = document.getElementById('select-municipio');
    const municipios = (tables.municipalities || []).filter(m => m.department_id == departmentId);
    select.innerHTML = municipios.map(m => `<option value="${m.id}">${m.name}</option>`).join('');
}

// Guardar empresa
function saveEmpresa(e) {
    e.preventDefault();
    const form = new FormData(e.target);
    const data = Object.fromEntries(form.entries());
    
    fetch(`/companies/${currentCompanyId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            Swal.fire('¡Guardado!', res.message, 'success');
            loadCompanies();
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    })
    .catch(e => Swal.fire('Error', 'No se pudo guardar', 'error'));
}
</script>

<script>
// Tab Software
function renderSoftwareTab(data) {
    const s = data.software || {};
    document.getElementById('tab-software').innerHTML = `
        <div class="info-card">
            <div class="info-card-title"><i class="fa fa-info-circle mr-1"></i> Información del Software DIAN</div>
            <p class="text-muted small mb-0">Configure el identificador y PIN del software registrado en la DIAN para esta empresa.</p>
        </div>
        <form id="form-software">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Identificador del Software</label>
                        <input type="text" class="form-control" name="identifier" value="${s.identifier || ''}" 
                            placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>PIN del Software</label>
                        <input type="text" class="form-control" name="pin" value="${s.pin || ''}" 
                            placeholder="12345" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>URL del Servicio (opcional)</label>
                        <input type="text" class="form-control" name="url" value="${s.url || ''}" 
                            placeholder="https://...">
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-orange">
                <i class="fa fa-save mr-1"></i> Guardar Software
            </button>
        </form>
    `;
    
    document.getElementById('form-software').addEventListener('submit', saveSoftware);
}

// Guardar software
function saveSoftware(e) {
    e.preventDefault();
    const form = new FormData(e.target);
    const data = Object.fromEntries(form.entries());
    
    fetch(`/companies/${currentCompanyId}/software`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            Swal.fire('¡Guardado!', res.message, 'success');
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    })
    .catch(e => Swal.fire('Error', 'No se pudo guardar', 'error'));
}

// Tab Certificado
function renderCertificadoTab(data) {
    const cert = data.certificate || {};
    document.getElementById('tab-certificado').innerHTML = `
        <div class="info-card">
            <div class="info-card-title"><i class="fa fa-shield-alt mr-1"></i> Certificado Digital</div>
            <p class="text-muted small mb-0">Suba el certificado digital (.p12 o .pfx) para firmar los documentos electrónicos.</p>
        </div>
        ${cert.name ? `
            <div class="alert alert-success">
                <i class="fa fa-check-circle mr-2"></i>
                <strong>Certificado actual:</strong> ${cert.name}
                ${cert.expiration ? `<br><small>Vence: ${cert.expiration}</small>` : ''}
            </div>
        ` : `
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>
                No hay certificado cargado para esta empresa.
            </div>
        `}
        <form id="form-certificado" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Archivo del Certificado (.p12 / .pfx)</label>
                        <input type="file" class="form-control-file" name="certificate" accept=".p12,.pfx" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Contraseña del Certificado</label>
                        <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-orange">
                <i class="fa fa-upload mr-1"></i> Subir Certificado
            </button>
        </form>
    `;
    
    document.getElementById('form-certificado').addEventListener('submit', uploadCertificate);
}

// Subir certificado
function uploadCertificate(e) {
    e.preventDefault();
    const form = new FormData(e.target);
    
    Swal.fire({
        title: 'Subiendo certificado...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch(`/companies/${currentCompanyId}/certificate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: form
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            Swal.fire('¡Cargado!', res.message, 'success');
            // Actualizar vista
            fetch(`/companies/${currentCompanyId}/data`)
                .then(r => r.json())
                .then(data => renderCertificadoTab(data));
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    })
    .catch(e => Swal.fire('Error', 'No se pudo subir el certificado', 'error'));
}

// Tab Resoluciones
function renderResolucionesTab(data) {
    const resolutions = data.resolutions || [];
    const typeDocuments = [
        {id: 1, name: 'Factura'},
        {id: 2, name: 'Factura Exportación'},
        {id: 3, name: 'Factura Contingencia'},
        {id: 4, name: 'Nota Crédito'},
        {id: 5, name: 'Nota Débito'},
        {id: 11, name: 'Doc. Soporte'},
        {id: 12, name: 'Nota Ajuste DS'}
    ];
    
    document.getElementById('tab-resoluciones').innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0"><i class="fa fa-list mr-1"></i> Resoluciones Configuradas</h6>
            <button class="btn btn-sm btn-orange" onclick="showNewResolutionForm()">
                <i class="fa fa-plus mr-1"></i> Nueva Resolución
            </button>
        </div>
        
        <div id="new-resolution-form" style="display:none;" class="info-card mb-3">
            <h6 class="mb-3">Nueva Resolución</h6>
            <form id="form-new-resolution">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipo Documento</label>
                            <select class="form-control form-control-sm" name="type_document_id" required>
                                ${typeDocuments.map(t => `<option value="${t.id}">${t.name}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Prefijo</label>
                            <input type="text" class="form-control form-control-sm" name="prefix" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Resolución</label>
                            <input type="text" class="form-control form-control-sm" name="resolution" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fecha Resolución</label>
                            <input type="date" class="form-control form-control-sm" name="resolution_date" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Clave Técnica</label>
                            <input type="text" class="form-control form-control-sm" name="technical_key">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Desde</label>
                            <input type="number" class="form-control form-control-sm" name="from" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Hasta</label>
                            <input type="number" class="form-control form-control-sm" name="to" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fecha Desde</label>
                            <input type="date" class="form-control form-control-sm" name="date_from" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fecha Hasta</label>
                            <input type="date" class="form-control form-control-sm" name="date_to" required>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-orange mr-2">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="hideNewResolutionForm()">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-resoluciones">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Prefijo</th>
                        <th>Resolución</th>
                        <th>Rango</th>
                        <th>Consecutivo</th>
                        <th>Vigencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${resolutions.length ? resolutions.map(r => `
                        <tr>
                            <td>${typeDocuments.find(t => t.id == r.type_document_id)?.name || r.type_document_id}</td>
                            <td><strong>${r.prefix}</strong></td>
                            <td>${r.resolution}</td>
                            <td>${r.from} - ${r.to}</td>
                            <td><span class="badge badge-info">${r.next_consecutive || r.from}</span></td>
                            <td><small>${r.date_from || ''} al ${r.date_to || ''}</small></td>
                            <td>
                                <button class="btn btn-xs btn-outline-primary" onclick="editResolution(${r.id})" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-xs btn-outline-danger" onclick="deleteResolution(${r.id})" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('') : '<tr><td colspan="7" class="text-center text-muted">No hay resoluciones configuradas</td></tr>'}
                </tbody>
            </table>
        </div>
    `;
    
    const formNew = document.getElementById('form-new-resolution');
    if (formNew) {
        formNew.addEventListener('submit', createResolution);
    }
}

function showNewResolutionForm() {
    document.getElementById('new-resolution-form').style.display = 'block';
}

function hideNewResolutionForm() {
    document.getElementById('new-resolution-form').style.display = 'none';
}

// Crear resolución
function createResolution(e) {
    e.preventDefault();
    const form = new FormData(e.target);
    const data = Object.fromEntries(form.entries());
    
    fetch(`/companies/${currentCompanyId}/resolution`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            Swal.fire('¡Creada!', res.message, 'success');
            // Recargar datos
            fetch(`/companies/${currentCompanyId}/data`)
                .then(r => r.json())
                .then(data => {
                    currentCompanyData = data;
                    renderResolucionesTab(data);
                });
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    })
    .catch(e => Swal.fire('Error', 'No se pudo crear la resolución', 'error'));
}

// Eliminar resolución
function deleteResolution(id) {
    Swal.fire({
        title: '¿Eliminar resolución?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/companies/resolution/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Eliminada', res.message, 'success');
                    fetch(`/companies/${currentCompanyId}/data`)
                        .then(r => r.json())
                        .then(data => {
                            currentCompanyData = data;
                            renderResolucionesTab(data);
                        });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}

// Cambiar ambiente
function changeEnvironment(id, currentEnv) {
    const newEnv = currentEnv == 1 ? 2 : 1;
    const envName = newEnv == 1 ? 'Producción' : 'Habilitación';
    
    Swal.fire({
        title: '¿Cambiar ambiente?',
        text: `La empresa pasará a ambiente de ${envName}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/companies/${id}/environment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type_environment_id: newEnv })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('¡Cambiado!', res.message, 'success');
                    loadCompanies();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}

// Habilitar/Deshabilitar
function toggleState(id, currentState) {
    const action = currentState ? 'deshabilitar' : 'habilitar';
    
    Swal.fire({
        title: `¿${currentState ? 'Deshabilitar' : 'Habilitar'} empresa?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/companies/${id}/toggle-state`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('¡Listo!', res.message, 'success');
                    loadCompanies();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}

// Eliminar empresa
function deleteCompany(id, nit) {
    Swal.fire({
        title: '¿Eliminar empresa?',
        html: `<p>Se eliminará la empresa <strong>${nit}</strong> y todos sus datos asociados:</p>
               <ul class="text-left"><li>Resoluciones</li><li>Software</li><li>Certificado</li><li>Usuario</li></ul>
               <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar todo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/companies/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Eliminada', res.message, 'success');
                    loadCompanies();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>
@endsection
