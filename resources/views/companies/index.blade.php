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

<!-- MODAL CONFIGURACIÓN -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog" style="max-width: 1000px; margin: 30px auto;">
        <div class="modal-content" style="border: none; border-radius: 8px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <!-- Header -->
            <div class="modal-header" style="background: #343a40; border: none; padding: 16px 24px;">
                <h6 class="modal-title text-white mb-0" style="font-weight: 500;">Configuracion de Empresa</h6>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 1; font-size: 24px;">&times;</button>
            </div>
            
            <!-- Body con sidebar -->
            <div class="modal-body p-0" style="display: flex; min-height: 500px;">
                <!-- Sidebar izquierdo -->
                <div class="config-sidebar">
                    <div class="sidebar-item active" data-tab="empresa" onclick="switchTab('empresa')">
                        <span class="sidebar-num">1</span>
                        <span class="sidebar-text">Empresa</span>
                    </div>
                    <div class="sidebar-item" data-tab="software" onclick="switchTab('software')">
                        <span class="sidebar-num">2</span>
                        <span class="sidebar-text">Software</span>
                    </div>
                    <div class="sidebar-item" data-tab="certificado" onclick="switchTab('certificado')">
                        <span class="sidebar-num">3</span>
                        <span class="sidebar-text">Certificado</span>
                    </div>
                    <div class="sidebar-item" data-tab="resolucion" onclick="switchTab('resolucion')">
                        <span class="sidebar-num">4</span>
                        <span class="sidebar-text">Resolucion</span>
                    </div>
                </div>
                
                <!-- Contenido derecho -->
                <div class="config-content">
                    <div id="tab-empresa" class="tab-panel active"></div>
                    <div id="tab-software" class="tab-panel"></div>
                    <div id="tab-certificado" class="tab-panel"></div>
                    <div id="tab-resolucion" class="tab-panel"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-orange { background: #f97316; border: none; color: white; font-weight: 600; }
.btn-orange:hover { background: #ea580c; color: white; }

.table thead th { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; padding: 12px; border-bottom: 2px solid #e2e8f0; }
.table tbody td { padding: 12px; vertical-align: middle; font-size: 13px; }
.table tbody tr:hover { background: #fff7ed; }

.badge-prod { background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 11px; }
.badge-hab { background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 11px; }
.badge-active { background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 11px; }
.badge-inactive { background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 11px; }

.dropdown-menu { border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.15); border-radius: 6px; }
.dropdown-item { padding: 8px 16px; font-size: 13px; }
.dropdown-item:hover { background: #fff7ed; }
.dropdown-item i { width: 20px; }

/* ===== MODAL SIDEBAR ===== */
.config-sidebar {
    width: 180px;
    background: #f8f9fa;
    border-right: 1px solid #e5e7eb;
    padding: 20px 0;
    flex-shrink: 0;
}
.sidebar-item {
    display: flex;
    align-items: center;
    padding: 14px 20px;
    cursor: pointer;
    border-left: 3px solid transparent;
    transition: all 0.2s;
}
.sidebar-item:hover {
    background: #f1f5f9;
}
.sidebar-item.active {
    background: white;
    border-left-color: #f97316;
}
.sidebar-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}
.sidebar-item.active .sidebar-num {
    background: #f97316;
    color: white;
}
.sidebar-text {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}
.sidebar-item.active .sidebar-text {
    color: #1f2937;
    font-weight: 600;
}

/* ===== CONTENIDO ===== */
.config-content {
    flex: 1;
    padding: 30px;
    overflow-y: auto;
}
.tab-panel { display: none; }
.tab-panel.active { display: block; }

.section-title {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e5e7eb;
}
.form-group { margin-bottom: 18px; }
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
    width: 100%;
}
.form-control:focus {
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    outline: none;
}

.cert-status {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: #ecfdf5;
    border-radius: 8px;
    margin-bottom: 20px;
}
.cert-status i { color: #22c55e; margin-right: 10px; font-size: 18px; }
.cert-status.warning { background: #fffbeb; }
.cert-status.warning i { color: #f59e0b; }

.res-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.res-table { width: 100%; border-collapse: collapse; }
.res-table th { background: #f9fafb; padding: 12px; font-size: 12px; font-weight: 600; color: #6b7280; text-align: left; border-bottom: 1px solid #e5e7eb; }
.res-table td { padding: 12px; font-size: 13px; border-bottom: 1px solid #f3f4f6; }
.res-table .btn-del { background: #fee2e2; color: #dc2626; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; }
.res-table .btn-del:hover { background: #fecaca; }

.new-res-form { background: #f9fafb; border-radius: 8px; padding: 20px; margin-bottom: 20px; display: none; }

.btn-save { background: #f97316; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 10px; }
.btn-save:hover { background: #ea580c; }
.btn-close-modal { background: #6b7280; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 10px; margin-left: 10px; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.3/sweetalert2.all.min.js"></script>


<script>
var companies = [], tables = {}, currentId = null, currentData = null, searchTimer = null;

document.addEventListener('DOMContentLoaded', function() {
    loadTables();
    loadCompanies();
});

function debounceSearch() { clearTimeout(searchTimer); searchTimer = setTimeout(loadCompanies, 300); }

function loadTables() { fetch('/companies/tables').then(function(r){return r.json();}).then(function(d){tables=d;}); }

function loadCompanies() {
    var search = document.getElementById('search').value;
    fetch('/companies/records?search=' + encodeURIComponent(search))
        .then(function(r){return r.json();})
        .then(function(d){ companies = d.data; renderTable(); });
}

function renderTable() {
    var tbody = document.getElementById('companies-body');
    if (!companies.length) { tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">No hay empresas</td></tr>'; return; }
    var html = '';
    for (var i = 0; i < companies.length; i++) {
        var c = companies[i];
        html += '<tr><td>'+(i+1)+'</td>';
        html += '<td><strong>'+c.identification_number+'</strong>-'+(c.dv||'0')+'</td>';
        html += '<td>'+c.name+'</td>';
        html += '<td><small class="text-muted">'+c.email+'</small></td>';
        html += '<td><span class="badge-'+(c.type_environment_id==1?'prod':'hab')+'">'+(c.type_environment_id==1?'Producción':'Habilitación')+'</span></td>';
        html += '<td><span class="badge-'+(c.state?'active':'inactive')+'">'+(c.state?'Activa':'Inactiva')+'</span></td>';
        html += '<td><span class="badge badge-secondary">'+c.documents_count+'</span></td>';
        html += '<td><small>'+c.created_at+'</small></td>';
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
    fetch('/companies/' + id + '/data').then(function(r){return r.json();}).then(function(d){
        currentData = d;
        renderAllTabs();
        switchTab('empresa');
        $('#editModal').modal('show');
    });
}

function switchTab(tab) {
    document.querySelectorAll('.sidebar-item').forEach(function(el){ el.classList.remove('active'); });
    document.querySelectorAll('.tab-panel').forEach(function(el){ el.classList.remove('active'); });
    document.querySelector('.sidebar-item[data-tab="'+tab+'"]').classList.add('active');
    document.getElementById('tab-'+tab).classList.add('active');
}

function renderAllTabs() {
    renderEmpresaTab();
    renderSoftwareTab();
    renderCertificadoTab();
    renderResolucionTab();
}

function renderEmpresaTab() {
    var c = currentData.company;
    var html = '<div class="section-title">Datos Generales</div>';
    html += '<div class="row"><div class="col-md-4"><div class="form-group"><label>Identificacion</label><input type="text" class="form-control" id="f_nit" value="'+c.identification_number+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Dv</label><input type="text" class="form-control" id="f_dv" value="'+(c.dv||'')+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Empresa</label><input type="text" class="form-control" id="f_name" value="'+c.name+'"></div></div></div>';
    
    html += '<div class="row"><div class="col-md-4"><div class="form-group"><label>Registro Mercantil</label><input type="text" class="form-control" id="f_merchant" value="'+(c.merchant_registration||'')+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Direccion</label><input type="text" class="form-control" id="f_address" value="'+(c.address||'')+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Telefono</label><input type="text" class="form-control" id="f_phone" value="'+(c.phone||'')+'"></div></div></div>';
    
    html += '<div class="row"><div class="col-md-4"><div class="form-group"><label>Correo Electronico</label><input type="email" class="form-control" id="f_email" value="'+c.email+'"></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Tipo Documentacion</label><select class="form-control" id="f_typedoc">'+optHtml(tables.type_document_identifications,c.type_document_identification_id)+'</select></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Departamento</label><select class="form-control" id="f_dept" onchange="loadMun()"><option value="">Seleccionar</option>'+optHtml(tables.departments,c.department_id)+'</select></div></div></div>';
    
    html += '<div class="row"><div class="col-md-4"><div class="form-group"><label>Municipio</label><select class="form-control" id="f_mun">'+optHtmlFilter(tables.municipalities,c.municipality_id,c.department_id)+'</select></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Organizacion</label><select class="form-control" id="f_org">'+optHtml(tables.type_organizations,c.type_organization_id)+'</select></div></div>';
    html += '<div class="col-md-4"><div class="form-group"><label>Regimen</label><select class="form-control" id="f_regime">'+optHtml(tables.type_regimes,c.type_regime_id)+'</select></div></div></div>';
    
    html += '<button class="btn-save" onclick="saveEmpresa()"><i class="fa fa-save mr-2"></i>Guardar Empresa</button>';
    document.getElementById('tab-empresa').innerHTML = html;
}

function renderSoftwareTab() {
    var s = currentData.software || {};
    var html = '<div class="section-title">Configuración del Software DIAN</div>';
    html += '<div class="row"><div class="col-md-6"><div class="form-group"><label>Identificador del Software</label><input type="text" class="form-control" id="s_id" value="'+(s.identifier||'')+'" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"></div></div>';
    html += '<div class="col-md-6"><div class="form-group"><label>PIN del Software</label><input type="text" class="form-control" id="s_pin" value="'+(s.pin||'')+'" placeholder="12345"></div></div></div>';
    html += '<div class="row"><div class="col-md-12"><div class="form-group"><label>URL del Servicio (opcional)</label><input type="text" class="form-control" id="s_url" value="'+(s.url||'')+'" placeholder="https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc"></div></div></div>';
    html += '<button class="btn-save" onclick="saveSoftware()"><i class="fa fa-save mr-2"></i>Guardar Software</button>';
    document.getElementById('tab-software').innerHTML = html;
}

function renderCertificadoTab() {
    var cert = currentData.certificate || {};
    var html = '<div class="section-title">Certificado Digital</div>';
    if (cert.name) {
        html += '<div class="cert-status"><i class="fa fa-check-circle"></i><div><strong>Certificado actual:</strong> '+cert.name+(cert.expiration?' - Vence: '+cert.expiration:'')+'</div></div>';
    } else {
        html += '<div class="cert-status warning"><i class="fa fa-exclamation-triangle"></i><div>No hay certificado cargado para esta empresa.</div></div>';
    }
    html += '<div class="row"><div class="col-md-6"><div class="form-group"><label>Archivo del Certificado (.p12 / .pfx)</label><input type="file" class="form-control" id="c_file" accept=".p12,.pfx" style="padding: 8px;"></div></div>';
    html += '<div class="col-md-6"><div class="form-group"><label>Contraseña del Certificado</label><input type="password" class="form-control" id="c_pass" placeholder="••••••••"></div></div></div>';
    html += '<button class="btn-save" onclick="uploadCert()"><i class="fa fa-upload mr-2"></i>Subir Certificado</button>';
    document.getElementById('tab-certificado').innerHTML = html;
}

function renderResolucionTab() {
    var res = currentData.resolutions || [];
    var types = [{id:1,n:'Factura'},{id:4,n:'Nota Crédito'},{id:5,n:'Nota Débito'},{id:11,n:'Doc. Soporte'},{id:12,n:'Nota Ajuste'}];
    var html = '<div class="res-header"><div class="section-title mb-0" style="border:none;padding:0;">Resoluciones Configuradas</div>';
    html += '<button class="btn btn-sm btn-orange" onclick="toggleNewRes()"><i class="fa fa-plus mr-1"></i>Nueva Resolución</button></div>';
    
    html += '<div class="new-res-form" id="newResForm"><div class="row">';
    html += '<div class="col-md-2"><div class="form-group"><label>Tipo</label><select class="form-control" id="nr_type">'+types.map(function(t){return '<option value="'+t.id+'">'+t.n+'</option>';}).join('')+'</select></div></div>';
    html += '<div class="col-md-2"><div class="form-group"><label>Prefijo</label><input type="text" class="form-control" id="nr_prefix"></div></div>';
    html += '<div class="col-md-3"><div class="form-group"><label>Resolución</label><input type="text" class="form-control" id="nr_res"></div></div>';
    html += '<div class="col-md-2"><div class="form-group"><label>Desde</label><input type="number" class="form-control" id="nr_from"></div></div>';
    html += '<div class="col-md-2"><div class="form-group"><label>Hasta</label><input type="number" class="form-control" id="nr_to"></div></div>';
    html += '<div class="col-md-1"><div class="form-group"><label>&nbsp;</label><button class="btn-save" style="padding:10px 12px;margin-top:0;" onclick="createRes()"><i class="fa fa-check"></i></button></div></div>';
    html += '</div></div>';
    
    html += '<table class="res-table"><thead><tr><th>Tipo</th><th>Prefijo</th><th>Resolución</th><th>Rango</th><th>Consecutivo</th><th>Vigencia</th><th></th></tr></thead><tbody>';
    if (res.length) {
        res.forEach(function(r) {
            var tn = types.find(function(t){return t.id==r.type_document_id;});
            html += '<tr><td>'+(tn?tn.n:r.type_document_id)+'</td><td><strong>'+r.prefix+'</strong></td><td>'+r.resolution+'</td>';
            html += '<td>'+r.from+' - '+r.to+'</td><td>'+r.prefix+(r.next_consecutive||r.from)+'</td>';
            html += '<td><small>'+(r.date_from||'')+' al '+(r.date_to||'')+'</small></td>';
            html += '<td><button class="btn-del" onclick="deleteRes('+r.id+')"><i class="fa fa-trash"></i></button></td></tr>';
        });
    } else {
        html += '<tr><td colspan="7" class="text-center text-muted py-3">No hay resoluciones configuradas</td></tr>';
    }
    html += '</tbody></table>';
    html += '<button class="btn-close-modal" data-dismiss="modal">Cerrar</button>';
    document.getElementById('tab-resolucion').innerHTML = html;
}

function toggleNewRes() { var f = document.getElementById('newResForm'); f.style.display = f.style.display === 'none' ? 'block' : 'none'; }

function optHtml(arr, sel) { if (!arr) return ''; return arr.map(function(a){return '<option value="'+a.id+'"'+(a.id==sel?' selected':'')+'>'+a.name+'</option>';}).join(''); }
function optHtmlFilter(arr, sel, dept) { if (!arr) return '<option value="">Seleccionar</option>'; var h = '<option value="">Seleccionar</option>'; arr.forEach(function(a){if(a.department_id==dept) h += '<option value="'+a.id+'"'+(a.id==sel?' selected':'')+'>'+a.name+'</option>';}); return h; }
function loadMun() { document.getElementById('f_mun').innerHTML = optHtmlFilter(tables.municipalities, null, document.getElementById('f_dept').value); }

function saveEmpresa() {
    var data = { identification_number: document.getElementById('f_nit').value, dv: document.getElementById('f_dv').value, name: document.getElementById('f_name').value, email: document.getElementById('f_email').value, phone: document.getElementById('f_phone').value, address: document.getElementById('f_address').value, merchant_registration: document.getElementById('f_merchant').value, type_document_identification_id: document.getElementById('f_typedoc').value, municipality_id: document.getElementById('f_mun').value, type_organization_id: document.getElementById('f_org').value, type_regime_id: document.getElementById('f_regime').value };
    fetch('/companies/'+currentId, { method: 'PUT', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}, body: JSON.stringify(data) })
    .then(function(r){return r.json();}).then(function(res){ if(res.success) { Swal.fire('Guardado','','success'); loadCompanies(); } else Swal.fire('Error',res.message,'error'); });
}

function saveSoftware() {
    var data = { identifier: document.getElementById('s_id').value, pin: document.getElementById('s_pin').value, url: document.getElementById('s_url').value };
    fetch('/companies/'+currentId+'/software', { method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}, body: JSON.stringify(data) })
    .then(function(r){return r.json();}).then(function(res){ if(res.success) Swal.fire('Guardado','','success'); else Swal.fire('Error',res.message,'error'); });
}

function uploadCert() {
    var file = document.getElementById('c_file').files[0];
    var pass = document.getElementById('c_pass').value;
    if (!file) { if(currentData.certificate) return; Swal.fire('Error','Seleccione un archivo','error'); return; }
    var fd = new FormData(); fd.append('certificate', file); fd.append('password', pass);
    Swal.fire({title:'Subiendo...',allowOutsideClick:false,didOpen:function(){Swal.showLoading();}});
    fetch('/companies/'+currentId+'/certificate', { method: 'POST', headers: {'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}, body: fd })
    .then(function(r){return r.json();}).then(function(res){ if(res.success) { Swal.fire('Cargado','','success'); fetch('/companies/'+currentId+'/data').then(function(r){return r.json();}).then(function(d){ currentData=d; renderCertificadoTab(); }); } else Swal.fire('Error',res.message,'error'); });
}

function createRes() {
    var data = { type_document_id: document.getElementById('nr_type').value, prefix: document.getElementById('nr_prefix').value, resolution: document.getElementById('nr_res').value, from: document.getElementById('nr_from').value, to: document.getElementById('nr_to').value, resolution_date: new Date().toISOString().split('T')[0], date_from: new Date().toISOString().split('T')[0], date_to: '2030-12-31' };
    fetch('/companies/'+currentId+'/resolution', { method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}, body: JSON.stringify(data) })
    .then(function(r){return r.json();}).then(function(res){ if(res.success) { Swal.fire('Creada','','success'); fetch('/companies/'+currentId+'/data').then(function(r){return r.json();}).then(function(d){ currentData=d; renderResolucionTab(); }); } else Swal.fire('Error',res.message,'error'); });
}

function deleteRes(id) {
    Swal.fire({title:'¿Eliminar?',icon:'warning',showCancelButton:true,confirmButtonColor:'#f97316',confirmButtonText:'Sí'}).then(function(r){
        if(r.isConfirmed) { fetch('/companies/resolution/'+id, {method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}}).then(function(r){return r.json();}).then(function(res){ if(res.success) { fetch('/companies/'+currentId+'/data').then(function(r){return r.json();}).then(function(d){ currentData=d; renderResolucionTab(); }); }}); }
    });
}

function changeEnv(id, curr) {
    var newEnv = curr==1?2:1;
    Swal.fire({title:'¿Cambiar a '+(newEnv==1?'Producción':'Habilitación')+'?',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316'}).then(function(r){
        if(r.isConfirmed) { fetch('/companies/'+id+'/environment', {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({type_environment_id:newEnv})}).then(function(r){return r.json();}).then(function(res){ if(res.success) loadCompanies(); }); }
    });
}

function toggleState(id, curr) {
    Swal.fire({title:'¿'+(curr?'Deshabilitar':'Habilitar')+'?',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316'}).then(function(r){
        if(r.isConfirmed) { fetch('/companies/'+id+'/toggle-state', {method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}}).then(function(r){return r.json();}).then(function(res){ if(res.success) loadCompanies(); }); }
    });
}

function deleteCompany(id, nit) {
    Swal.fire({title:'¿Eliminar '+nit+'?',text:'Se eliminarán todos los datos',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545',confirmButtonText:'Eliminar'}).then(function(r){
        if(r.isConfirmed) { fetch('/companies/'+id, {method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}}).then(function(r){return r.json();}).then(function(res){ if(res.success) { Swal.fire('Eliminada','','success'); loadCompanies(); }}); }
    });
}
</script>
@endsection
