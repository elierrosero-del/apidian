@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header" style="background: #343a40; padding: 15px 20px;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white"><i class="fa fa-building mr-2"></i>Gestión de Empresas</h5>
            <div class="d-flex align-items-center">
                <input type="text" id="search" class="form-control form-control-sm mr-2" placeholder="Buscar..." style="width: 220px;" onkeyup="debounceSearch()">
                <a href="/configuration_admin" class="btn btn-orange btn-sm"><i class="fa fa-plus"></i> Nueva</a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="companies-table">
                <thead>
                    <tr style="background: #f8f9fa;">
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

<!-- MODAL EDITAR - FULLSCREEN -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <!-- Header con gradiente naranja -->
            <div class="modal-header-custom">
                <div class="d-flex align-items-center">
                    <i class="fa fa-edit mr-3" style="font-size: 24px;"></i>
                    <div>
                        <h5 class="mb-0">Editar Empresa</h5>
                        <span id="modal-company-name" style="opacity: 0.9; font-size: 14px;"></span>
                    </div>
                </div>
                <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            
            <!-- Tabs -->
            <div class="modal-tabs">
                <ul class="nav nav-tabs" id="companyTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-empresa">
                            <i class="fa fa-building"></i> Empresa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-software">
                            <i class="fa fa-cog"></i> Software
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-certificado">
                            <i class="fa fa-certificate"></i> Certificado
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-resoluciones">
                            <i class="fa fa-file-alt"></i> Resoluciones
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Tab Content -->
            <div class="modal-body-custom">
                <div class="tab-content" id="companyTabsContent">
                    <div class="tab-pane fade show active" id="tab-empresa" role="tabpanel"></div>
                    <div class="tab-pane fade" id="tab-software" role="tabpanel"></div>
                    <div class="tab-pane fade" id="tab-certificado" role="tabpanel"></div>
                    <div class="tab-pane fade" id="tab-resoluciones" role="tabpanel"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ========== MODAL FULLSCREEN ========== */
.modal-fullscreen {
    width: 100vw;
    max-width: 100%;
    height: 100vh;
    margin: 0;
    padding: 0;
}
.modal-fullscreen .modal-content {
    height: 100vh;
    border: 0;
    border-radius: 0;
    display: flex;
    flex-direction: column;
}

/* Header del Modal */
.modal-header-custom {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    color: white;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}
.modal-header-custom h5 {
    font-weight: 600;
    font-size: 20px;
    margin: 0;
}
.close-btn {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.2s;
}
.close-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}

/* Tabs del Modal */
.modal-tabs {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    flex-shrink: 0;
}
.modal-tabs .nav-tabs {
    border: none;
    padding: 0 30px;
}
.modal-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 18px 30px;
    margin-bottom: -1px;
    transition: all 0.2s;
    font-size: 15px;
}
.modal-tabs .nav-link i {
    margin-right: 10px;
    font-size: 16px;
}
.modal-tabs .nav-link:hover {
    color: #f97316;
    background: transparent;
    border-color: transparent;
}
.modal-tabs .nav-link.active {
    color: #f97316;
    background: white;
    border-bottom: 3px solid #f97316;
    font-weight: 600;
}

/* Body del Modal */
.modal-body-custom {
    flex: 1;
    overflow-y: auto;
    padding: 40px 50px;
    background: #fff;
}

/* ========== BOTONES ========== */
.btn-orange {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    border: none;
    color: white;
    font-weight: 600;
    padding: 10px 24px;
    border-radius: 6px;
    transition: all 0.2s;
}
.btn-orange:hover {
    background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(249,115,22,0.4);
}
.btn-orange-outline {
    background: transparent;
    border: 2px solid #f97316;
    color: #f97316;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 6px;
    transition: all 0.2s;
}
.btn-orange-outline:hover {
    background: #f97316;
    color: white;
}

/* ========== TABLA PRINCIPAL ========== */
#companies-table thead th {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 14px 12px;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
}
#companies-table tbody td {
    padding: 12px;
    vertical-align: middle;
    font-size: 13px;
    border-bottom: 1px solid #f1f3f4;
}
#companies-table tbody tr:hover {
    background: #fff8f3;
}

/* Badges */
.badge-prod { background: #28a745; color: white; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-hab { background: #ffc107; color: #212529; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-active { background: #d4edda; color: #155724; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-inactive { background: #f8d7da; color: #721c24; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }

/* Dropdown */
.dropdown-menu { border: none; box-shadow: 0 5px 25px rgba(0,0,0,0.15); border-radius: 8px; padding: 8px 0; }
.dropdown-item { padding: 10px 20px; font-size: 13px; }
.dropdown-item:hover { background: #fff3e6; }
.dropdown-item i { width: 20px; }

/* ========== FORMULARIOS EN TABS ========== */
.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #343a40;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}
.section-title i {
    margin-right: 12px;
    color: #f97316;
}
.section-desc {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 30px;
}
.form-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
}
.form-group label {
    font-weight: 600;
    font-size: 13px;
    color: #495057;
    margin-bottom: 8px;
}
.form-control {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.2s;
}
.form-control:focus {
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
}
.form-control-sm {
    padding: 8px 12px;
    font-size: 13px;
}

/* Alert boxes */
.alert-success-custom {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-left: 4px solid #28a745;
    color: #155724;
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
}
.alert-warning-custom {
    background: #fff3cd;
    border: 1px solid #ffeeba;
    border-left: 4px solid #ffc107;
    color: #856404;
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
}

/* Tabla resoluciones */
.table-resoluciones {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.table-resoluciones thead th {
    background: #f8f9fa;
    font-size: 12px;
    font-weight: 600;
    color: #495057;
    padding: 14px 16px;
    border-bottom: 2px solid #dee2e6;
}
.table-resoluciones tbody td {
    padding: 14px 16px;
    font-size: 13px;
    vertical-align: middle;
}
.badge-consecutivo {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    font-family: monospace;
}

/* New resolution form */
.new-resolution-box {
    background: #fff8f3;
    border: 2px dashed #f97316;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let companies = [];
let tables = {};
let currentCompanyId = null;
let currentCompanyData = null;
let searchTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    loadTables();
    loadCompanies();
});

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadCompanies(), 300);
}

function loadTables() {
    fetch('/companies/tables')
        .then(r => r.json())
        .then(data => { tables = data; })
        .catch(e => console.error('Error:', e));
}

function loadCompanies() {
    const search = document.getElementById('search').value;
    fetch('/companies/records?search=' + encodeURIComponent(search))
        .then(r => r.json())
        .then(data => {
            companies = data.data;
            renderTable();
        })
        .catch(e => {
            document.getElementById('companies-body').innerHTML = 
                '<tr><td colspan="9" class="text-center text-danger py-4">Error al cargar</td></tr>';
        });
}

function renderTable() {
    const tbody = document.getElementById('companies-body');
    if (!companies.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">No hay empresas</td></tr>';
        return;
    }
    tbody.innerHTML = companies.map((c, i) => `
        <tr>
            <td>${i + 1}</td>
            <td><strong>${c.identification_number}</strong>-${c.dv || '0'}</td>
            <td>${c.name}</td>
            <td><small class="text-muted">${c.email}</small></td>
            <td><span class="badge-${c.type_environment_id == 1 ? 'prod' : 'hab'}">${c.type_environment_id == 1 ? 'Producción' : 'Habilitación'}</span></td>
            <td><span class="badge-${c.state ? 'active' : 'inactive'}">${c.state ? 'Activa' : 'Inactiva'}</span></td>
            <td><span class="badge badge-secondary">${c.documents_count}</span></td>
            <td><small>${c.created_at}</small></td>
            <td class="text-center">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">Acciones</button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="editCompany(${c.id})"><i class="fa fa-edit text-primary"></i> Editar</a>
                        <a class="dropdown-item" href="#" onclick="changeEnvironment(${c.id}, ${c.type_environment_id})"><i class="fa fa-exchange-alt text-info"></i> Cambiar Ambiente</a>
                        <a class="dropdown-item" href="#" onclick="toggleState(${c.id}, ${c.state})"><i class="fa fa-${c.state ? 'ban text-warning' : 'check text-success'}"></i> ${c.state ? 'Deshabilitar' : 'Habilitar'}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="#" onclick="deleteCompany(${c.id}, '${c.identification_number}')"><i class="fa fa-trash"></i> Eliminar</a>
                    </div>
                </div>
            </td>
        </tr>
    `).join('');
}

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
        .catch(e => Swal.fire('Error', 'No se pudo cargar', 'error'));
}

function renderEmpresaTab(data) {
    const c = data.company;
    document.getElementById('tab-empresa').innerHTML = `
        <div class="section-title"><i class="fa fa-building"></i> Información de la Empresa</div>
        <p class="section-desc">Configure los datos básicos de la empresa para facturación electrónica.</p>
        
        <form id="form-empresa">
            <div class="form-section">
                <div class="row">
                    <div class="col-lg-2 col-md-3">
                        <div class="form-group">
                            <label>Identificación</label>
                            <input type="text" class="form-control" name="identification_number" value="${c.identification_number}" required>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-2">
                        <div class="form-group">
                            <label>DV</label>
                            <input type="text" class="form-control" name="dv" value="${c.dv || ''}" maxlength="1">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-7">
                        <div class="form-group">
                            <label>Nombre/Razón Social</label>
                            <input type="text" class="form-control" name="name" value="${c.name}" required>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="${c.email}" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-2 col-md-4">
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" class="form-control" name="phone" value="${c.phone || ''}">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-8">
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" class="form-control" name="address" value="${c.address || ''}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="form-group">
                            <label>Registro Mercantil</label>
                            <input type="text" class="form-control" name="merchant_registration" value="${c.merchant_registration || ''}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Tipo Documento</label>
                            <select class="form-control" name="type_document_identification_id">
                                ${(tables.type_document_identifications || []).map(t => 
                                    `<option value="${t.id}" ${t.id == c.type_document_identification_id ? 'selected' : ''}>${t.name}</option>`
                                ).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
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
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Municipio</label>
                            <select class="form-control" name="municipality_id" id="select-municipio">
                                ${(tables.municipalities || []).filter(m => m.department_id == c.department_id).map(m => 
                                    `<option value="${m.id}" ${m.id == c.municipality_id ? 'selected' : ''}>${m.name}</option>`
                                ).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
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
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Régimen</label>
                            <select class="form-control" name="type_regime_id">
                                ${(tables.type_regimes || []).map(t => 
                                    `<option value="${t.id}" ${t.id == c.type_regime_id ? 'selected' : ''}>${t.name}</option>`
                                ).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Responsabilidad</label>
                            <select class="form-control" name="type_liability_id">
                                ${(tables.type_liabilities || []).map(t => 
                                    `<option value="${t.id}" ${t.id == c.type_liability_id ? 'selected' : ''}>${t.name}</option>`
                                ).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Estado</label>
                            <select class="form-control" name="state">
                                <option value="1" ${c.state ? 'selected' : ''}>Activa</option>
                                <option value="0" ${!c.state ? 'selected' : ''}>Inactiva</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-orange btn-lg">
                <i class="fa fa-save mr-2"></i> Guardar Empresa
            </button>
        </form>
    `;
    document.getElementById('form-empresa').addEventListener('submit', saveEmpresa);
}

function loadMunicipios(deptId) {
    const sel = document.getElementById('select-municipio');
    sel.innerHTML = (tables.municipalities || []).filter(m => m.department_id == deptId)
        .map(m => `<option value="${m.id}">${m.name}</option>`).join('');
}

function saveEmpresa(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target).entries());
    fetch(`/companies/${currentCompanyId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(res => {
        res.success ? Swal.fire('¡Guardado!', res.message, 'success') && loadCompanies() : Swal.fire('Error', res.message, 'error');
    }).catch(() => Swal.fire('Error', 'No se pudo guardar', 'error'));
}
</script>

<script>
function renderSoftwareTab(data) {
    const s = data.software || {};
    document.getElementById('tab-software').innerHTML = `
        <div class="section-title"><i class="fa fa-cog"></i> Información del Software DIAN</div>
        <p class="section-desc">Configure el identificador y PIN del software registrado en la DIAN para esta empresa.</p>
        
        <form id="form-software">
            <div class="form-section">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Identificador del Software</label>
                            <input type="text" class="form-control" name="identifier" value="${s.identifier || ''}" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>PIN del Software</label>
                            <input type="text" class="form-control" name="pin" value="${s.pin || ''}" placeholder="12345">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>URL del Servicio (opcional)</label>
                            <input type="text" class="form-control" name="url" value="${s.url || ''}" placeholder="https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc">
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-orange btn-lg"><i class="fa fa-save mr-2"></i> Guardar Software</button>
        </form>
    `;
    document.getElementById('form-software').addEventListener('submit', saveSoftware);
}

function saveSoftware(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target).entries());
    fetch(`/companies/${currentCompanyId}/software`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(res => {
        res.success ? Swal.fire('¡Guardado!', res.message, 'success') : Swal.fire('Error', res.message, 'error');
    }).catch(() => Swal.fire('Error', 'No se pudo guardar', 'error'));
}

function renderCertificadoTab(data) {
    const cert = data.certificate || {};
    document.getElementById('tab-certificado').innerHTML = `
        <div class="section-title"><i class="fa fa-shield-alt"></i> Certificado Digital</div>
        <p class="section-desc">Suba el certificado digital (.p12 o .pfx) para firmar los documentos electrónicos.</p>
        
        ${cert.name ? `
            <div class="alert-success-custom">
                <i class="fa fa-check-circle mr-2"></i>
                <strong>Certificado actual:</strong> ${cert.name}
                ${cert.expiration ? ` - Vence: ${cert.expiration}` : ''}
            </div>
        ` : `
            <div class="alert-warning-custom">
                <i class="fa fa-exclamation-triangle mr-2"></i>
                No hay certificado cargado para esta empresa.
            </div>
        `}
        
        <form id="form-certificado" enctype="multipart/form-data">
            <div class="form-section">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Archivo del Certificado (.p12 / .pfx)</label>
                            <input type="file" class="form-control-file" name="certificate" accept=".p12,.pfx" required style="padding: 10px 0;">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Contraseña del Certificado</label>
                            <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-orange btn-lg"><i class="fa fa-upload mr-2"></i> Subir Certificado</button>
        </form>
    `;
    document.getElementById('form-certificado').addEventListener('submit', uploadCertificate);
}

function uploadCertificate(e) {
    e.preventDefault();
    Swal.fire({ title: 'Subiendo...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    fetch(`/companies/${currentCompanyId}/certificate`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: new FormData(e.target)
    }).then(r => r.json()).then(res => {
        if (res.success) {
            Swal.fire('¡Cargado!', res.message, 'success');
            fetch(`/companies/${currentCompanyId}/data`).then(r => r.json()).then(d => renderCertificadoTab(d));
        } else Swal.fire('Error', res.message, 'error');
    }).catch(() => Swal.fire('Error', 'No se pudo subir', 'error'));
}

function renderResolucionesTab(data) {
    const res = data.resolutions || [];
    const types = [{id:1,name:'Factura'},{id:2,name:'Factura Exp.'},{id:3,name:'Contingencia'},{id:4,name:'Nota Crédito'},{id:5,name:'Nota Débito'},{id:11,name:'Doc. Soporte'},{id:12,name:'Nota Ajuste DS'}];
    
    document.getElementById('tab-resoluciones').innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="section-title mb-0"><i class="fa fa-list"></i> Resoluciones Configuradas</div>
            </div>
            <button class="btn btn-orange" onclick="showNewResForm()"><i class="fa fa-plus mr-2"></i> Nueva Resolución</button>
        </div>
        
        <div id="new-res-form" style="display:none;" class="new-resolution-box">
            <h6 class="mb-3"><i class="fa fa-plus-circle mr-2 text-orange"></i>Nueva Resolución</h6>
            <form id="form-new-res">
                <div class="row">
                    <div class="col-md-2"><div class="form-group"><label>Tipo</label>
                        <select class="form-control form-control-sm" name="type_document_id">${types.map(t=>`<option value="${t.id}">${t.name}</option>`).join('')}</select>
                    </div></div>
                    <div class="col-md-2"><div class="form-group"><label>Prefijo</label><input type="text" class="form-control form-control-sm" name="prefix" required></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Resolución</label><input type="text" class="form-control form-control-sm" name="resolution" required></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Fecha Res.</label><input type="date" class="form-control form-control-sm" name="resolution_date" required></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Clave Técnica</label><input type="text" class="form-control form-control-sm" name="technical_key"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-2"><div class="form-group"><label>Desde</label><input type="number" class="form-control form-control-sm" name="from" required></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Hasta</label><input type="number" class="form-control form-control-sm" name="to" required></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Fecha Desde</label><input type="date" class="form-control form-control-sm" name="date_from" required></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Fecha Hasta</label><input type="date" class="form-control form-control-sm" name="date_to" required></div></div>
                    <div class="col-md-4 d-flex align-items-end pb-3">
                        <button type="submit" class="btn btn-orange btn-sm mr-2"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="hideNewResForm()">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
        
        <table class="table table-resoluciones">
            <thead><tr><th>Tipo</th><th>Prefijo</th><th>Resolución</th><th>Rango</th><th>Consecutivo</th><th>Vigencia</th><th>Acciones</th></tr></thead>
            <tbody>
                ${res.length ? res.map(r => `
                    <tr>
                        <td>${types.find(t=>t.id==r.type_document_id)?.name || r.type_document_id}</td>
                        <td><strong>${r.prefix}</strong></td>
                        <td>${r.resolution}</td>
                        <td>${r.from} - ${r.to}</td>
                        <td><span class="badge-consecutivo">${r.prefix}${r.next_consecutive || r.from}</span></td>
                        <td><small>${r.date_from || ''} al ${r.date_to || ''}</small></td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteRes(${r.id})"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('') : '<tr><td colspan="7" class="text-center text-muted py-4">No hay resoluciones</td></tr>'}
            </tbody>
        </table>
    `;
    const f = document.getElementById('form-new-res');
    if(f) f.addEventListener('submit', createRes);
}

function showNewResForm() { document.getElementById('new-res-form').style.display = 'block'; }
function hideNewResForm() { document.getElementById('new-res-form').style.display = 'none'; }

function createRes(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target).entries());
    fetch(`/companies/${currentCompanyId}/resolution`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(res => {
        if (res.success) {
            Swal.fire('¡Creada!', res.message, 'success');
            fetch(`/companies/${currentCompanyId}/data`).then(r => r.json()).then(d => { currentCompanyData = d; renderResolucionesTab(d); });
        } else Swal.fire('Error', res.message, 'error');
    });
}

function deleteRes(id) {
    Swal.fire({
        title: '¿Eliminar?', icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#f97316', confirmButtonText: 'Sí', cancelButtonText: 'No'
    }).then(r => {
        if (r.isConfirmed) {
            fetch(`/companies/resolution/${id}`, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    Swal.fire('Eliminada', '', 'success');
                    fetch(`/companies/${currentCompanyId}/data`).then(r => r.json()).then(d => { currentCompanyData = d; renderResolucionesTab(d); });
                }
            });
        }
    });
}

function changeEnvironment(id, curr) {
    const newEnv = curr == 1 ? 2 : 1;
    Swal.fire({
        title: '¿Cambiar ambiente?', text: `Pasará a ${newEnv == 1 ? 'Producción' : 'Habilitación'}`,
        icon: 'question', showCancelButton: true, confirmButtonColor: '#f97316', confirmButtonText: 'Sí'
    }).then(r => {
        if (r.isConfirmed) {
            fetch(`/companies/${id}/environment`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ type_environment_id: newEnv })
            }).then(r => r.json()).then(res => { res.success && Swal.fire('¡Listo!', '', 'success') && loadCompanies(); });
        }
    });
}

function toggleState(id, curr) {
    Swal.fire({
        title: `¿${curr ? 'Deshabilitar' : 'Habilitar'}?`, icon: 'question',
        showCancelButton: true, confirmButtonColor: '#f97316', confirmButtonText: 'Sí'
    }).then(r => {
        if (r.isConfirmed) {
            fetch(`/companies/${id}/toggle-state`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(r => r.json()).then(res => { res.success && Swal.fire('¡Listo!', '', 'success') && loadCompanies(); });
        }
    });
}

function deleteCompany(id, nit) {
    Swal.fire({
        title: '¿Eliminar empresa?',
        html: `Se eliminará <strong>${nit}</strong> y todos sus datos.<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Eliminar'
    }).then(r => {
        if (r.isConfirmed) {
            fetch(`/companies/${id}`, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(r => r.json()).then(res => { res.success && Swal.fire('Eliminada', '', 'success') && loadCompanies(); });
        }
    });
}
</script>
@endsection
