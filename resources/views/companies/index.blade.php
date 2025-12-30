@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="font-weight: 600;">Gestión de Empresas</h5>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <input type="text" id="search" class="form-control form-control-sm" placeholder="Buscar..." style="width: 200px;" onkeyup="debounceSearch()">
                <a href="/configuration_admin" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Nueva</a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
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
                    <tr><td colspan="9" class="text-center py-4">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Editar Completo -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-building"></i> Editar Empresa: <span id="modal-company-name"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="companyTabs">
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
                <div class="tab-content p-4" id="companyTabsContent">
                    <!-- Tab Empresa -->
                    <div class="tab-pane fade show active" id="tab-empresa"></div>
                    <!-- Tab Software -->
                    <div class="tab-pane fade" id="tab-software"></div>
                    <!-- Tab Certificado -->
                    <div class="tab-pane fade" id="tab-certificado"></div>
                    <!-- Tab Resoluciones -->
                    <div class="tab-pane fade" id="tab-resoluciones"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table thead th { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; padding: 12px; }
.table tbody td { padding: 10px 12px; vertical-align: middle; font-size: 13px; }
.env-badge, .state-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.env-prod { background: #dcfce7; color: #166534; }
.env-hab { background: #fef3c7; color: #92400e; }
.state-active { background: #dcfce7; color: #166534; }
.state-inactive { background: #fee2e2; color: #991b1b; }
.nav-tabs .nav-link { color: #64748b; font-weight: 500; padding: 12px 20px; }
.nav-tabs .nav-link.active { color: #f97316; border-bottom: 2px solid #f97316; }
.nav-tabs .nav-link i { margin-right: 6px; }
.form-group label { font-weight: 600; font-size: 12px; color: #64748b; margin-bottom: 4px; }
.modal-xl { max-width: 900px; }
.swal2-popup { font-size: 14px; }
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let searchTimeout, tables = {}, currentCompanyId = null;
const csrf = document.querySelector('meta[name="csrf-token"]').content;

document.addEventListener('DOMContentLoaded', function() {
    loadCompanies();
    loadTables();
});

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(loadCompanies, 300);
}

function loadTables() {
    fetch('/companies/tables').then(r => r.json()).then(data => { tables = data; });
}

function loadCompanies() {
    const search = document.getElementById('search').value;
    fetch('/companies/records?search=' + encodeURIComponent(search))
        .then(r => r.json())
        .then(data => renderTable(data.data))
        .catch(e => {
            document.getElementById('companies-body').innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error al cargar</td></tr>';
        });
}

function renderTable(companies) {
    const tbody = document.getElementById('companies-body');
    if (!companies || companies.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4">No hay empresas</td></tr>';
        return;
    }
    let html = '';
    companies.forEach((c, i) => {
        const envClass = c.type_environment_id == 1 ? 'env-prod' : 'env-hab';
        const envText = c.type_environment_id == 1 ? 'Producción' : 'Habilitación';
        const stateClass = c.state ? 'state-active' : 'state-inactive';
        const stateText = c.state ? 'Activa' : 'Inactiva';
        html += `<tr>
            <td>${i + 1}</td>
            <td><strong>${c.identification_number || '-'}</strong></td>
            <td>${c.name}</td>
            <td>${c.email}</td>
            <td><span class="env-badge ${envClass}">${envText}</span></td>
            <td><span class="state-badge ${stateClass}">${stateText}</span></td>
            <td><span class="badge badge-secondary">${c.documents_count}</span></td>
            <td>${c.created_at}</td>
            <td class="text-center">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">Acciones</button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="editCompany(${c.id}, '${c.name}')"><i class="fa fa-edit text-primary"></i> Editar</a>
                        <a class="dropdown-item" href="/documents?company=${c.identification_number}"><i class="fa fa-folder text-warning"></i> Documentos</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="changeEnvironment(${c.id}, ${c.type_environment_id}, '${c.name}')"><i class="fa fa-exchange-alt text-success"></i> Cambiar Ambiente</a>
                        <a class="dropdown-item" href="#" onclick="toggleState(${c.id}, ${c.state}, '${c.name}')"><i class="fa fa-power-off ${c.state ? 'text-danger' : 'text-success'}"></i> ${c.state ? 'Deshabilitar' : 'Habilitar'}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="#" onclick="deleteCompany(${c.id}, '${c.identification_number}', '${c.name}')"><i class="fa fa-trash"></i> Eliminar</a>
                    </div>
                </div>
            </td>
        </tr>`;
    });
    tbody.innerHTML = html;
}
</script>

<script>
function editCompany(id, name) {
    currentCompanyId = id;
    document.getElementById('modal-company-name').textContent = name;
    fetch('/companies/' + id + '/data')
        .then(r => r.json())
        .then(data => {
            renderEmpresaTab(data);
            renderSoftwareTab(data);
            renderCertificadoTab(data);
            renderResolucionesTab(data);
            $('#editModal').modal('show');
        });
}

function renderEmpresaTab(data) {
    const c = data.company;
    let deptOptions = '<option value="">Seleccionar...</option>';
    tables.departments.forEach(d => {
        deptOptions += `<option value="${d.id}" ${c.department_id == d.id ? 'selected' : ''}>${d.name}</option>`;
    });
    let typeDocOptions = tables.type_document_identifications.map(t => 
        `<option value="${t.id}" ${c.type_document_identification_id == t.id ? 'selected' : ''}>${t.name}</option>`
    ).join('');
    let orgOptions = tables.type_organizations.map(t => 
        `<option value="${t.id}" ${c.type_organization_id == t.id ? 'selected' : ''}>${t.name}</option>`
    ).join('');
    let regOptions = tables.type_regimes.map(t => 
        `<option value="${t.id}" ${c.type_regime_id == t.id ? 'selected' : ''}>${t.name}</option>`
    ).join('');
    let liabOptions = tables.type_liabilities.map(t => 
        `<option value="${t.id}" ${c.type_liability_id == t.id ? 'selected' : ''}>${t.name}</option>`
    ).join('');
    
    document.getElementById('tab-empresa').innerHTML = `
        <form id="empresaForm">
            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Identificación</label><input type="text" name="identification_number" class="form-control" value="${c.identification_number || ''}" required></div></div>
                <div class="col-md-1"><div class="form-group"><label>DV</label><input type="text" name="dv" class="form-control" value="${c.dv || ''}" maxlength="1"></div></div>
                <div class="col-md-4"><div class="form-group"><label>Nombre/Razón Social</label><input type="text" name="name" class="form-control" value="${c.name || ''}" required></div></div>
                <div class="col-md-4"><div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="${c.email || ''}" required></div></div>
            </div>
            <div class="row">
                <div class="col-md-4"><div class="form-group"><label>Teléfono</label><input type="text" name="phone" class="form-control" value="${c.phone || ''}"></div></div>
                <div class="col-md-4"><div class="form-group"><label>Dirección</label><input type="text" name="address" class="form-control" value="${c.address || ''}"></div></div>
                <div class="col-md-4"><div class="form-group"><label>Registro Mercantil</label><input type="text" name="merchant_registration" class="form-control" value="${c.merchant_registration || ''}"></div></div>
            </div>
            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Tipo Documento</label><select name="type_document_identification_id" class="form-control">${typeDocOptions}</select></div></div>
                <div class="col-md-3"><div class="form-group"><label>Departamento</label><select name="department_id" class="form-control" onchange="loadMunicipios(this.value, ${c.municipality_id})">${deptOptions}</select></div></div>
                <div class="col-md-3"><div class="form-group"><label>Municipio</label><select name="municipality_id" id="municipio_select" class="form-control"><option value="${c.municipality_id}">Cargando...</option></select></div></div>
                <div class="col-md-3"><div class="form-group"><label>Organización</label><select name="type_organization_id" class="form-control">${orgOptions}</select></div></div>
            </div>
            <div class="row">
                <div class="col-md-4"><div class="form-group"><label>Régimen</label><select name="type_regime_id" class="form-control">${regOptions}</select></div></div>
                <div class="col-md-4"><div class="form-group"><label>Responsabilidad</label><select name="type_liability_id" class="form-control">${liabOptions}</select></div></div>
                <div class="col-md-4"><div class="form-group"><label>Estado</label><select name="state" class="form-control"><option value="1" ${c.state ? 'selected' : ''}>Activa</option><option value="0" ${!c.state ? 'selected' : ''}>Inactiva</option></select></div></div>
            </div>
            <hr>
            <button type="button" class="btn btn-primary" onclick="saveEmpresa()"><i class="fa fa-save"></i> Guardar Empresa</button>
        </form>`;
    if (c.department_id) loadMunicipios(c.department_id, c.municipality_id);
}

function loadMunicipios(deptId, selectedId) {
    const select = document.getElementById('municipio_select');
    const filtered = tables.municipalities.filter(m => m.department_id == deptId);
    select.innerHTML = '<option value="">Seleccionar...</option>' + filtered.map(m => 
        `<option value="${m.id}" ${m.id == selectedId ? 'selected' : ''}>${m.name}</option>`
    ).join('');
}

function saveEmpresa() {
    const form = document.getElementById('empresaForm');
    const data = Object.fromEntries(new FormData(form).entries());
    fetch('/companies/' + currentCompanyId, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(res => {
        if (res.success) {
            Swal.fire('¡Guardado!', res.message, 'success');
            loadCompanies();
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
}
</script>

<script>
function renderSoftwareTab(data) {
    const s = data.software || {};
    document.getElementById('tab-software').innerHTML = `
        <form id="softwareForm">
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> Configure el software DIAN para esta empresa.</div>
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>ID Software (Identifier)</label><input type="text" name="identifier" class="form-control" value="${s.identifier || ''}" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"></div></div>
                <div class="col-md-6"><div class="form-group"><label>PIN</label><input type="text" name="pin" class="form-control" value="${s.pin || ''}" placeholder="12345"></div></div>
            </div>
            <div class="row">
                <div class="col-md-12"><div class="form-group"><label>URL Servicio DIAN</label><input type="text" name="url" class="form-control" value="${s.url || 'https://vpfe.dian.gov.co/WcfDianCustomerServices.svc'}"></div></div>
            </div>
            <hr>
            <button type="button" class="btn btn-primary" onclick="saveSoftware()"><i class="fa fa-save"></i> Guardar Software</button>
        </form>`;
}

function saveSoftware() {
    const form = document.getElementById('softwareForm');
    const data = Object.fromEntries(new FormData(form).entries());
    fetch('/companies/' + currentCompanyId + '/software', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(res => {
        if (res.success) {
            Swal.fire('¡Guardado!', res.message, 'success');
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
}

function renderCertificadoTab(data) {
    const c = data.certificate;
    let certInfo = '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No hay certificado cargado.</div>';
    if (c) {
        const expClass = c.expiration && new Date(c.expiration) < new Date() ? 'text-danger' : 'text-success';
        certInfo = `<div class="alert alert-success">
            <i class="fa fa-check-circle"></i> Certificado: <strong>${c.name}</strong><br>
            <span class="${expClass}">Vence: ${c.expiration || 'No disponible'}</span>
        </div>`;
    }
    document.getElementById('tab-certificado').innerHTML = `
        ${certInfo}
        <form id="certificadoForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Archivo Certificado (.p12 o .pfx)</label><input type="file" name="certificate" class="form-control" accept=".p12,.pfx" required></div></div>
                <div class="col-md-6"><div class="form-group"><label>Contraseña del Certificado</label><input type="password" name="password" class="form-control" required></div></div>
            </div>
            <hr>
            <button type="button" class="btn btn-primary" onclick="uploadCertificate()"><i class="fa fa-upload"></i> Cargar Certificado</button>
        </form>`;
}

function uploadCertificate() {
    const form = document.getElementById('certificadoForm');
    const formData = new FormData(form);
    Swal.fire({ title: 'Cargando certificado...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    fetch('/companies/' + currentCompanyId + '/certificate', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf },
        body: formData
    }).then(r => r.json()).then(res => {
        Swal.close();
        if (res.success) {
            Swal.fire('¡Cargado!', res.message + (res.expiration ? '<br>Vence: ' + res.expiration : ''), 'success');
            editCompany(currentCompanyId, document.getElementById('modal-company-name').textContent);
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
}
</script>

<script>
function renderResolucionesTab(data) {
    let rows = '';
    if (data.resolutions && data.resolutions.length > 0) {
        data.resolutions.forEach(r => {
            rows += `<tr id="res-${r.id}">
                <td>${r.type_document_id}</td>
                <td><strong>${r.prefix}</strong></td>
                <td>${r.resolution}</td>
                <td>${r.from}</td>
                <td>${r.to}</td>
                <td><input type="number" class="form-control form-control-sm" style="width:80px" value="${r.next_consecutive}" id="next-${r.id}"></td>
                <td><small>${r.date_from} - ${r.date_to}</small></td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="updateResolution(${r.id})"><i class="fa fa-save"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deleteResolution(${r.id})"><i class="fa fa-trash"></i></button>
                </td>
            </tr>`;
        });
    }
    document.getElementById('tab-resoluciones').innerHTML = `
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="thead-light">
                    <tr><th>Tipo</th><th>Prefijo</th><th>Resolución</th><th>Desde</th><th>Hasta</th><th>Actual</th><th>Vigencia</th><th>Acciones</th></tr>
                </thead>
                <tbody>${rows || '<tr><td colspan="8" class="text-center">No hay resoluciones</td></tr>'}</tbody>
            </table>
        </div>`;
}

function updateResolution(id) {
    const next = document.getElementById('next-' + id).value;
    fetch('/companies/resolution/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ next_consecutive: next })
    }).then(r => r.json()).then(res => {
        if (res.success) Swal.fire('¡Guardado!', 'Resolución actualizada', 'success');
        else Swal.fire('Error', res.message, 'error');
    });
}

function deleteResolution(id) {
    Swal.fire({
        title: '¿Eliminar resolución?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/companies/resolution/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf }
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    document.getElementById('res-' + id).remove();
                    Swal.fire('Eliminada', 'Resolución eliminada', 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>

<script>
function changeEnvironment(id, currentEnv, name) {
    const newEnv = currentEnv == 1 ? 2 : 1;
    const envName = newEnv == 1 ? 'Producción' : 'Habilitación';
    Swal.fire({
        title: 'Cambiar Ambiente',
        html: `¿Cambiar <strong>${name}</strong> a <strong>${envName}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/companies/' + id + '/environment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ type_environment_id: newEnv })
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    loadCompanies();
                    Swal.fire('¡Cambiado!', `Ambiente cambiado a ${envName}`, 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}

function toggleState(id, currentState, name) {
    const action = currentState ? 'deshabilitar' : 'habilitar';
    Swal.fire({
        title: `¿${currentState ? 'Deshabilitar' : 'Habilitar'} empresa?`,
        html: `¿Desea ${action} <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: currentState ? '#d33' : '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${action}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/companies/' + id + '/toggle-state', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf }
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    loadCompanies();
                    Swal.fire('¡Listo!', res.message, 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}

function deleteCompany(id, nit, name) {
    Swal.fire({
        title: '⚠️ Eliminar Empresa',
        html: `<p>¿Está seguro de eliminar <strong>${name}</strong> (${nit})?</p>
               <p class="text-danger"><small>Se eliminarán: Usuario, Software, Certificado, Resoluciones y todos los datos asociados.</small></p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar todo',
        cancelButtonText: 'Cancelar',
        input: 'text',
        inputPlaceholder: `Escriba "${nit}" para confirmar`,
        inputValidator: (value) => {
            if (value !== nit) return 'Debe escribir el NIT correctamente para confirmar';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Eliminando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch('/companies/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf }
            }).then(r => r.json()).then(res => {
                Swal.close();
                if (res.success) {
                    loadCompanies();
                    Swal.fire('Eliminada', res.message, 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>
@endsection
