@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">
                <i class="fa fa-building mr-2" style="color: #f97316;"></i>Gestión de Empresas
            </h5>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                    <input type="text" id="search" class="form-control" placeholder="Buscar empresa..." onkeyup="debounceSearch()">
                </div>
                <a href="/configuration_admin" class="btn btn-sm btn-orange"><i class="fa fa-plus"></i> Nueva</a>
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
                    <tr><td colspan="9" class="text-center py-4 text-muted">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL EDITAR - Estilo Wizard -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 1100px;">
        <div class="modal-content" style="border: none; border-radius: 8px; overflow: hidden;">
            <!-- Header Gris Oscuro -->
            <div class="modal-header" style="background: #343a40; border: none; padding: 15px 25px;">
                <h5 class="modal-title text-white" style="font-weight: 500; font-size: 16px;">
                    Configuracion de Empresa
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 1;">
                    <span>&times;</span>
                </button>
            </div>
            
            <!-- Wizard Steps -->
            <div class="wizard-steps">
                <div class="wizard-step active" data-step="1" onclick="goToStep(1)">
                    <div class="step-number">1</div>
                    <div class="step-title">Empresa</div>
                </div>
                <div class="wizard-line"></div>
                <div class="wizard-step" data-step="2" onclick="goToStep(2)">
                    <div class="step-number">2</div>
                    <div class="step-title">Software</div>
                </div>
                <div class="wizard-line"></div>
                <div class="wizard-step" data-step="3" onclick="goToStep(3)">
                    <div class="step-number">3</div>
                    <div class="step-title">Certificado</div>
                </div>
                <div class="wizard-line"></div>
                <div class="wizard-step" data-step="4" onclick="goToStep(4)">
                    <div class="step-number">4</div>
                    <div class="step-title">Resolucion</div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="modal-body" style="padding: 30px 40px; min-height: 400px;">
                <div id="step-content"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* Botón naranja */
.btn-orange {
    background: #f97316;
    border: none;
    color: white;
    font-weight: 600;
}
.btn-orange:hover {
    background: #ea580c;
    color: white;
}

/* Tabla */
.table thead th {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    padding: 12px;
    border-bottom: 2px solid #e2e8f0;
}
.table tbody td {
    padding: 12px;
    vertical-align: middle;
    font-size: 13px;
}
.table tbody tr:hover { background: #fff7ed; }

/* Badges */
.badge-prod { background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 11px; }
.badge-hab { background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 11px; }
.badge-active { background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 11px; }
.badge-inactive { background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 11px; }

/* Dropdown */
.dropdown-menu { border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.15); border-radius: 6px; }
.dropdown-item { padding: 8px 16px; font-size: 13px; }
.dropdown-item:hover { background: #fff7ed; }
.dropdown-item i { width: 20px; }

/* ========== WIZARD STEPS ========== */
.wizard-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 50px;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
}
.wizard-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    min-width: 100px;
}
.step-number {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid #d1d5db;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    color: #9ca3af;
    background: white;
    margin-bottom: 8px;
    transition: all 0.3s;
}
.step-title {
    font-size: 13px;
    color: #9ca3af;
    font-weight: 500;
    transition: all 0.3s;
}
.wizard-step.active .step-number {
    border-color: #f97316;
    background: #f97316;
    color: white;
}
.wizard-step.active .step-title {
    color: #1f2937;
    font-weight: 600;
}
.wizard-step.completed .step-number {
    border-color: #22c55e;
    background: #22c55e;
    color: white;
}
.wizard-line {
    flex: 1;
    height: 2px;
    background: #e5e7eb;
    margin: 0 10px;
    margin-bottom: 28px;
}

/* ========== FORMULARIOS ========== */
.section-title {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e7eb;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 6px;
    font-weight: 500;
}
.form-control {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 10px 14px;
    font-size: 14px;
    color: #374151;
    transition: all 0.2s;
}
.form-control:focus {
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
}
.form-control::placeholder {
    color: #9ca3af;
}

/* Alert boxes */
.alert-success-box {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
}
.alert-success-box i {
    color: #22c55e;
    font-size: 20px;
    margin-right: 12px;
}
.alert-warning-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 25px;
}

/* Tabla resoluciones */
.res-table {
    width: 100%;
    border-collapse: collapse;
}
.res-table th {
    background: #f9fafb;
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}
.res-table td {
    padding: 14px 16px;
    font-size: 13px;
    border-bottom: 1px solid #f3f4f6;
}
.badge-consec {
    background: #0ea5e9;
    color: white;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-family: monospace;
}

/* Botón siguiente centrado */
.btn-next-wrapper {
    text-align: center;
    margin-top: 30px;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.3/sweetalert2.all.min.js"></script>


<script>
var companies = [], tables = {}, currentId = null, currentData = null, currentStep = 1, searchTimer = null;

document.addEventListener('DOMContentLoaded', function() {
    loadTables();
    loadCompanies();
});

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadCompanies, 300);
}

function loadTables() {
    fetch('/companies/tables').then(function(r) { return r.json(); }).then(function(d) { tables = d; });
}

function loadCompanies() {
    var search = document.getElementById('search').value;
    fetch('/companies/records?search=' + encodeURIComponent(search))
        .then(function(r) { return r.json(); })
        .then(function(d) { companies = d.data; renderTable(); });
}

function renderTable() {
    var tbody = document.getElementById('companies-body');
    if (!companies.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">No hay empresas</td></tr>';
        return;
    }
    var html = '';
    for (var i = 0; i < companies.length; i++) {
        var c = companies[i];
        html += '<tr>';
        html += '<td>' + (i+1) + '</td>';
        html += '<td><strong>' + c.identification_number + '</strong>-' + (c.dv||'0') + '</td>';
        html += '<td>' + c.name + '</td>';
        html += '<td><small class="text-muted">' + c.email + '</small></td>';
        html += '<td><span class="badge-' + (c.type_environment_id==1?'prod':'hab') + '">' + (c.type_environment_id==1?'Producción':'Habilitación') + '</span></td>';
        html += '<td><span class="badge-' + (c.state?'active':'inactive') + '">' + (c.state?'Activa':'Inactiva') + '</span></td>';
        html += '<td><span class="badge badge-secondary">' + c.documents_count + '</span></td>';
        html += '<td><small>' + c.created_at + '</small></td>';
        html += '<td class="text-center"><div class="dropdown"><button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">Acciones</button>';
        html += '<div class="dropdown-menu dropdown-menu-right">';
        html += '<a class="dropdown-item" href="#" onclick="editCompany('+c.id+')"><i class="fa fa-edit text-primary"></i> Editar</a>';
        html += '<a class="dropdown-item" href="#" onclick="changeEnv('+c.id+','+c.type_environment_id+')"><i class="fa fa-exchange-alt text-info"></i> Cambiar Ambiente</a>';
        html += '<a class="dropdown-item" href="#" onclick="toggleState('+c.id+','+(c.state?1:0)+')"><i class="fa fa-'+(c.state?'ban text-warning':'check text-success')+'"></i> '+(c.state?'Deshabilitar':'Habilitar')+'</a>';
        html += '<div class="dropdown-divider"></div>';
        html += '<a class="dropdown-item text-danger" href="#" onclick="deleteCompany('+c.id+',\''+c.identification_number+'\')"><i class="fa fa-trash"></i> Eliminar</a>';
        html += '</div></div></td></tr>';
    }
    tbody.innerHTML = html;
}

function editCompany(id) {
    currentId = id;
    currentStep = 1;
    fetch('/companies/' + id + '/data')
        .then(function(r) { return r.json(); })
        .then(function(d) {
            currentData = d;
            updateWizardSteps();
            renderStep();
            $('#editModal').modal('show');
        });
}

function goToStep(step) {
    currentStep = step;
    updateWizardSteps();
    renderStep();
}

function updateWizardSteps() {
    var steps = document.querySelectorAll('.wizard-step');
    steps.forEach(function(s) {
        var stepNum = parseInt(s.getAttribute('data-step'));
        s.classList.remove('active', 'completed');
        if (stepNum === currentStep) s.classList.add('active');
        else if (stepNum < currentStep) s.classList.add('completed');
    });
}

function renderStep() {
    var content = document.getElementById('step-content');
    if (currentStep === 1) renderEmpresaStep(content);
    else if (currentStep === 2) renderSoftwareStep(content);
    else if (currentStep === 3) renderCertificadoStep(content);
    else if (currentStep === 4) renderResolucionStep(content);
}

function renderEmpresaStep(el) {
    var c = currentData.company;
    var html = '<div class="section-title">Datos Generales 2</div>';
    html += '<div class="row">';
    html += '<div class="col-md-4"><div class="form-group"><label>Identificacion</label><input type="text" class="form-control" id="f_nit" value="'+c.identification_number+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Dv</label><input type="text" class="form-control" id="f_dv" value="'+(c.dv||'')+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Empresa</label><input type="text" class="form-control" id="f_name" value="'+c.name+'"></div></div>';
    html += '</div>';
    html += '<div class="row">';
    html += '<div class="col-md-4"><div class="form-group"><label>Registro Mercantil</label><input type="text" class="form-control" id="f_merchant" value="'+(c.merchant_registration||'')+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Direccion</label><input type="text" class="form-control" id="f_address" value="'+(c.address||'')+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Telefono</label><input type="text" class="form-control" id="f_phone" value="'+(c.phone||'')+'"></div></div>';
    html += '</div>';
    html += '<div class="row">';
    html += '<div class="col-md-4"><div class="form-group"><label>Correo Electronico</label><input type="email" class="form-control" id="f_email" value="'+c.email+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Tipo Documentacion</label><select class="form-control" id="f_typedoc">'+optHtml(tables.type_document_identifications,c.type_document_identification_id)+'</select></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Departamento</label><select class="form-control" id="f_dept" onchange="loadMun()"><option value="">Seleccionar</option>'+optHtml(tables.departments,c.department_id)+'</select></div></div>';
    html += '</div>';
    html += '<div class="row">';
    html += '<div class="col-md-4"><div class="form-group"><label>Municipio</label><select class="form-control" id="f_mun">'+optHtmlFilter(tables.municipalities,c.municipality_id,c.department_id)+'</select></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Organizacion</label><select class="form-control" id="f_org">'+optHtml(tables.type_organizations,c.type_organization_id)+'</select></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Regimen</label><select class="form-control" id="f_regime">'+optHtml(tables.type_regimes,c.type_regime_id)+'</select></div></div>';
    html += '</div>';
    html += '<div class="btn-next-wrapper"><button class="btn btn-orange" onclick="saveEmpresa()">Siguiente</button></div>';
    el.innerHTML = html;
}

function renderSoftwareStep(el) {
    var s = currentData.software || {};
    var html = '<div class="section-title">Configuración del Software DIAN</div>';
    html += '<div class="row">';
    html += '<div class="col-md-6"><div class="form-group"><label>Identificador del Software</label><input type="text" class="form-control" id="s_id" value="'+(s.identifier||'')+'" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"></div></div>';
    html += '<div class="col-md-6"><div class="form-group"><label>PIN del Software</label><input type="text" class="form-control" id="s_pin" value="'+(s.pin||'')+'" placeholder="12345"></div></div>';
    html += '</div>';
    html += '<div class="row">';
    html += '<div class="col-md-12"><div class="form-group"><label>URL del Servicio (opcional)</label><input type="text" class="form-control" id="s_url" value="'+(s.url||'')+'" placeholder="https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc"></div></div>';
    html += '</div>';
    html += '<div class="btn-next-wrapper"><button class="btn btn-orange" onclick="saveSoftware()">Siguiente</button></div>';
    el.innerHTML = html;
}

function renderCertificadoStep(el) {
    var cert = currentData.certificate || {};
    var html = '<div class="section-title">Certificado Digital</div>';
    if (cert.name) {
        html += '<div class="alert-success-box"><i class="fa fa-check-circle"></i><div><strong>Certificado actual:</strong> '+cert.name+(cert.expiration?' - Vence: '+cert.expiration:'')+'</div></div>';
    } else {
        html += '<div class="alert-warning-box"><i class="fa fa-exclamation-triangle mr-2" style="color:#f59e0b;"></i>No hay certificado cargado.</div>';
    }
    html += '<div class="row">';
    html += '<div class="col-md-6"><div class="form-group"><label>Archivo del Certificado (.p12 / .pfx)</label><input type="file" class="form-control" id="c_file" accept=".p12,.pfx"></div></div>';
    html += '<div class="col-md-6"><div class="form-group"><label>Contraseña del Certificado</label><input type="password" class="form-control" id="c_pass" placeholder="••••••••"></div></div>';
    html += '</div>';
    html += '<div class="btn-next-wrapper"><button class="btn btn-orange" onclick="uploadCert()">Siguiente</button></div>';
    el.innerHTML = html;
}

function renderResolucionStep(el) {
    var res = currentData.resolutions || [];
    var types = [{id:1,n:'Factura'},{id:4,n:'Nota Crédito'},{id:5,n:'Nota Débito'},{id:11,n:'Doc. Soporte'},{id:12,n:'Nota Ajuste'}];
    var html = '<div class="d-flex justify-content-between align-items-center mb-3">';
    html += '<div class="section-title mb-0" style="border:none;padding:0;">Resoluciones Configuradas</div>';
    html += '<button class="btn btn-sm btn-orange" onclick="toggleNewRes()"><i class="fa fa-plus mr-1"></i>Nueva Resolución</button>';
    html += '</div>';
    
    html += '<div id="newResBox" style="display:none;background:#f9fafb;border-radius:8px;padding:20px;margin-bottom:20px;">';
    html += '<div class="row">';
    html += '<div class="col-md-2"><div class="form-group"><label>Tipo</label><select class="form-control form-control-sm" id="nr_type">'+types.map(function(t){return '<option value="'+t.id+'">'+t.n+'</option>';}).join('')+'</select></div></div>';
    html += '<div class="col-md-2"><div class="form-group"><label>Prefijo</label><input type="text" class="form-control form-control-sm" id="nr_prefix"></div></div>';
    html += '<div class="col-md-2"><div class="form-group"><label>Resolución</label><input type="text" class="form-control form-control-sm" id="nr_res"></div></div>';
    html += '<div class="col-md-2"><div class="form-group"><label>Desde</label><input type="number" class="form-control form-control-sm" id="nr_from"></div></div>';
    html += '<div class="col-md-2"><div class="form-group"><label>Hasta</label><input type="number" class="form-control form-control-sm" id="nr_to"></div></div>';
    html += '<div class="col-md-2"><div class="form-group"><label>&nbsp;</label><button class="btn btn-sm btn-orange btn-block" onclick="createRes()">Guardar</button></div></div>';
    html += '</div></div>';
    
    html += '<table class="res-table"><thead><tr><th>Tipo</th><th>Prefijo</th><th>Resolución</th><th>Rango</th><th>Consecutivo</th><th>Vigencia</th><th></th></tr></thead><tbody>';
    if (res.length) {
        res.forEach(function(r) {
            var tn = types.find(function(t){return t.id==r.type_document_id;});
            html += '<tr>';
            html += '<td>'+(tn?tn.n:r.type_document_id)+'</td>';
            html += '<td><strong>'+r.prefix+'</strong></td>';
            html += '<td>'+r.resolution+'</td>';
            html += '<td>'+r.from+' - '+r.to+'</td>';
            html += '<td><span class="badge-consec">'+r.prefix+(r.next_consecutive||r.from)+'</span></td>';
            html += '<td><small>'+(r.date_from||'')+' al '+(r.date_to||'')+'</small></td>';
            html += '<td><button class="btn btn-sm btn-outline-danger" onclick="deleteRes('+r.id+')"><i class="fa fa-trash"></i></button></td>';
            html += '</tr>';
        });
    } else {
        html += '<tr><td colspan="7" class="text-center text-muted py-3">No hay resoluciones</td></tr>';
    }
    html += '</tbody></table>';
    html += '<div class="btn-next-wrapper"><button class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div>';
    el.innerHTML = html;
}

function toggleNewRes() {
    var box = document.getElementById('newResBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

function optHtml(arr, sel) {
    if (!arr) return '';
    return arr.map(function(a) { return '<option value="'+a.id+'"'+(a.id==sel?' selected':'')+'>'+a.name+'</option>'; }).join('');
}

function optHtmlFilter(arr, sel, dept) {
    if (!arr) return '<option value="">Seleccionar</option>';
    var html = '<option value="">Seleccionar</option>';
    arr.forEach(function(a) { if (a.department_id == dept) html += '<option value="'+a.id+'"'+(a.id==sel?' selected':'')+'>'+a.name+'</option>'; });
    return html;
}

function loadMun() {
    var dept = document.getElementById('f_dept').value;
    document.getElementById('f_mun').innerHTML = optHtmlFilter(tables.municipalities, null, dept);
}

function saveEmpresa() {
    var data = {
        identification_number: document.getElementById('f_nit').value,
        dv: document.getElementById('f_dv').value,
        name: document.getElementById('f_name').value,
        email: document.getElementById('f_email').value,
        phone: document.getElementById('f_phone').value,
        address: document.getElementById('f_address').value,
        merchant_registration: document.getElementById('f_merchant').value,
        type_document_identification_id: document.getElementById('f_typedoc').value,
        municipality_id: document.getElementById('f_mun').value,
        type_organization_id: document.getElementById('f_org').value,
        type_regime_id: document.getElementById('f_regime').value
    };
    fetch('/companies/'+currentId, {
        method: 'PUT',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify(data)
    }).then(function(r){return r.json();}).then(function(res){
        if(res.success) { Swal.fire('Guardado','','success'); goToStep(2); loadCompanies(); }
        else Swal.fire('Error',res.message,'error');
    });
}

function saveSoftware() {
    var data = { identifier: document.getElementById('s_id').value, pin: document.getElementById('s_pin').value, url: document.getElementById('s_url').value };
    fetch('/companies/'+currentId+'/software', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify(data)
    }).then(function(r){return r.json();}).then(function(res){
        if(res.success) { Swal.fire('Guardado','','success'); goToStep(3); }
        else Swal.fire('Error',res.message,'error');
    });
}

function uploadCert() {
    var file = document.getElementById('c_file').files[0];
    var pass = document.getElementById('c_pass').value;
    if (!file && !currentData.certificate) { Swal.fire('Error','Seleccione un archivo','error'); return; }
    if (!file) { goToStep(4); return; }
    var fd = new FormData();
    fd.append('certificate', file);
    fd.append('password', pass);
    Swal.fire({title:'Subiendo...',allowOutsideClick:false,didOpen:function(){Swal.showLoading();}});
    fetch('/companies/'+currentId+'/certificate', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body: fd
    }).then(function(r){return r.json();}).then(function(res){
        if(res.success) {
            Swal.fire('Cargado','','success');
            fetch('/companies/'+currentId+'/data').then(function(r){return r.json();}).then(function(d){ currentData=d; goToStep(4); });
        } else Swal.fire('Error',res.message,'error');
    });
}

function createRes() {
    var data = {
        type_document_id: document.getElementById('nr_type').value,
        prefix: document.getElementById('nr_prefix').value,
        resolution: document.getElementById('nr_res').value,
        from: document.getElementById('nr_from').value,
        to: document.getElementById('nr_to').value,
        resolution_date: new Date().toISOString().split('T')[0],
        date_from: new Date().toISOString().split('T')[0],
        date_to: '2030-12-31'
    };
    fetch('/companies/'+currentId+'/resolution', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify(data)
    }).then(function(r){return r.json();}).then(function(res){
        if(res.success) {
            Swal.fire('Creada','','success');
            fetch('/companies/'+currentId+'/data').then(function(r){return r.json();}).then(function(d){ currentData=d; renderResolucionStep(document.getElementById('step-content')); });
        } else Swal.fire('Error',res.message,'error');
    });
}

function deleteRes(id) {
    Swal.fire({title:'¿Eliminar?',icon:'warning',showCancelButton:true,confirmButtonColor:'#f97316',confirmButtonText:'Sí'}).then(function(r){
        if(r.isConfirmed) {
            fetch('/companies/resolution/'+id, {method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
            .then(function(r){return r.json();}).then(function(res){
                if(res.success) {
                    fetch('/companies/'+currentId+'/data').then(function(r){return r.json();}).then(function(d){ currentData=d; renderResolucionStep(document.getElementById('step-content')); });
                }
            });
        }
    });
}

function changeEnv(id, curr) {
    var newEnv = curr==1?2:1;
    Swal.fire({title:'¿Cambiar a '+(newEnv==1?'Producción':'Habilitación')+'?',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316'}).then(function(r){
        if(r.isConfirmed) {
            fetch('/companies/'+id+'/environment', {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({type_environment_id:newEnv})})
            .then(function(r){return r.json();}).then(function(res){ if(res.success) loadCompanies(); });
        }
    });
}

function toggleState(id, curr) {
    Swal.fire({title:'¿'+(curr?'Deshabilitar':'Habilitar')+'?',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316'}).then(function(r){
        if(r.isConfirmed) {
            fetch('/companies/'+id+'/toggle-state', {method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
            .then(function(r){return r.json();}).then(function(res){ if(res.success) loadCompanies(); });
        }
    });
}

function deleteCompany(id, nit) {
    Swal.fire({title:'¿Eliminar '+nit+'?',text:'Se eliminarán todos los datos',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545',confirmButtonText:'Eliminar'}).then(function(r){
        if(r.isConfirmed) {
            fetch('/companies/'+id, {method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
            .then(function(r){return r.json();}).then(function(res){ if(res.success) { Swal.fire('Eliminada','','success'); loadCompanies(); }});
        }
    });
}
</script>
@endsection
