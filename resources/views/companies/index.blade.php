@extends('layouts.app')

@section('content')
<div class="card shadow-sm" style="border:none;border-radius:12px;overflow:hidden;">
    <div class="card-header" style="background:linear-gradient(135deg,#1e293b 0%,#334155 100%);padding:18px 24px;border:none;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white" style="font-weight:600;font-size:16px;">
                <i class="fa fa-building mr-2" style="color:#f97316;"></i>Gestión de Empresas
            </h5>
            <div class="d-flex align-items-center" style="gap:12px;">
                <div style="position:relative;">
                    <i class="fa fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                    <input type="text" id="search" placeholder="Buscar empresa..." onkeyup="debounceSearch()" style="width:260px;padding:10px 12px 10px 38px;border:none;border-radius:8px;font-size:14px;background:#f1f5f9;">
                </div>
                <a href="/configuration_admin" class="btn-orange">
                    <i class="fa fa-plus"></i> Nueva Empresa
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0" style="border-collapse:separate;border-spacing:0;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th class="th-style">#</th>
                    <th class="th-style">NIT</th>
                    <th class="th-style">Empresa</th>
                    <th class="th-style">Email</th>
                    <th class="th-style">Ambiente</th>
                    <th class="th-style">Estado</th>
                    <th class="th-style">Docs</th>
                    <th class="th-style">Fecha</th>
                    <th class="th-style" style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="tblBody"></tbody>
        </table>
    </div>
</div>

<!-- MODAL EDICIÓN -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
            <div class="modal-header-custom">
                <span style="color:white;font-size:17px;font-weight:600;"><i class="fa fa-building mr-2"></i>Configuración de Empresa</span>
                <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:1;font-size:28px;font-weight:300;text-shadow:none;">&times;</button>
            </div>
            
            <div class="tabs-container">
                <ul class="nav nav-tabs" id="tabsNav">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#t1"><span class="tab-num">1</span>Empresa</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#t2"><span class="tab-num">2</span>Software</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#t3"><span class="tab-num">3</span>Certificado</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#t4"><span class="tab-num">4</span>Resolución</a></li>
                </ul>
            </div>
            
            <div class="tab-content" style="padding:28px;min-height:500px;background:#fff;">
                <div class="tab-pane fade show active" id="t1"></div>
                <div class="tab-pane fade" id="t2"></div>
                <div class="tab-pane fade" id="t3"></div>
                <div class="tab-pane fade" id="t4"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* General */
.th-style{padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;}
#tblBody tr{transition:all 0.15s;}
#tblBody tr:hover{background:#fff7ed;}
#tblBody td{padding:14px 16px;font-size:13px;vertical-align:middle;border-bottom:1px solid #f1f5f9;}

/* Botones */
.btn-orange{background:linear-gradient(135deg,#f97316 0%,#ea580c 100%);color:white!important;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(249,115,22,0.3);border:none;cursor:pointer;transition:all 0.2s;}
.btn-orange:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(249,115,22,0.4);color:white!important;text-decoration:none;}
.btn-secondary{background:#f1f5f9;color:#475569;border:none;padding:12px 24px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;transition:all 0.2s;}
.btn-secondary:hover{background:#e2e8f0;}

/* Badges */
.badge-prod{background:#dcfce7;color:#166534;padding:6px 14px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-hab{background:#fef3c7;color:#92400e;padding:6px 14px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-on{background:#dcfce7;color:#166534;padding:6px 14px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-off{background:#fee2e2;color:#991b1b;padding:6px 14px;border-radius:20px;font-size:11px;font-weight:600;}

/* Dropdown */
.dropdown-menu{border:none;box-shadow:0 10px 40px rgba(0,0,0,0.12);border-radius:12px;padding:8px;}
.dropdown-item{font-size:13px;padding:10px 16px;border-radius:8px;margin:2px 0;}
.dropdown-item:hover{background:#fff7ed;}
.dropdown-item i{width:20px;}

/* Modal */
.modal-header-custom{background:linear-gradient(135deg,#1e293b 0%,#334155 100%);padding:20px 28px;display:flex;justify-content:space-between;align-items:center;}
.tabs-container{background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:0 20px;}
#tabsNav{border:none;gap:8px;margin:0;}
#tabsNav .nav-link{border:none!important;padding:16px 24px;font-size:14px;font-weight:500;color:#64748b;background:transparent!important;display:flex;align-items:center;gap:10px;border-bottom:3px solid transparent!important;margin-bottom:-1px;transition:all 0.2s;}
#tabsNav .nav-link:hover{color:#f97316;}
#tabsNav .nav-link.active{color:#f97316!important;border-bottom-color:#f97316!important;font-weight:600;}
.tab-num{width:24px;height:24px;border-radius:50%;background:#cbd5e1;color:white;font-size:12px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;}
#tabsNav .nav-link.active .tab-num{background:#f97316;}

/* Form Card */
.form-card{background:#f8fafc;border-radius:16px;padding:24px;margin-bottom:20px;}
.form-card-title{font-size:15px;font-weight:700;color:#1e293b;margin-bottom:6px;display:flex;align-items:center;gap:10px;}
.form-card-title i{color:#f97316;}
.form-card-desc{font-size:13px;color:#64748b;margin-bottom:20px;}
.form-grid{display:grid;gap:16px;margin-bottom:16px;}
.form-grid-2{grid-template-columns:repeat(2,1fr);}
.form-grid-3{grid-template-columns:repeat(3,1fr);}
.form-grid-4{grid-template-columns:repeat(4,1fr);}
.form-group{margin-bottom:0;}
.form-label{display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.3px;}
.form-control-custom{width:100%;padding:12px 16px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;color:#1e293b;transition:all 0.2s;background:white;}
.form-control-custom:focus{outline:none;border-color:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,0.1);}
select.form-control-custom{appearance:none;background:white url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") right 12px center/16px no-repeat;padding-right:40px;}

/* Alerts */
.alert-success-box{background:linear-gradient(135deg,#ecfdf5 0%,#d1fae5 100%);border:2px solid #a7f3d0;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px;}
.alert-success-box i{color:#22c55e;font-size:20px;}
.alert-success-box span{font-size:14px;color:#166534;}
.alert-warning-box{background:linear-gradient(135deg,#fffbeb 0%,#fef3c7 100%);border:2px solid #fde68a;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px;}
.alert-warning-box i{color:#f59e0b;font-size:20px;}
.alert-warning-box span{font-size:14px;color:#92400e;}

/* Resolution Table */
.res-table{width:100%;border-collapse:collapse;background:white;border-radius:12px;overflow:hidden;border:2px solid #e2e8f0;}
.res-table th{background:#f8fafc;padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;text-align:left;border-bottom:2px solid #e2e8f0;}
.res-table td{padding:14px 16px;font-size:13px;border-bottom:1px solid #f1f5f9;vertical-align:middle;}
.res-table tr:last-child td{border-bottom:none;}
.res-table tr:hover{background:#fefce8;}
.badge-consec{background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%);color:white;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;font-family:'Courier New',monospace;}
.btn-icon{width:34px;height:34px;border-radius:8px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all 0.2s;margin:0 2px;}
.btn-icon-edit{background:#dbeafe;color:#2563eb;}
.btn-icon-edit:hover{background:#bfdbfe;}
.btn-icon-del{background:#fee2e2;color:#dc2626;}
.btn-icon-del:hover{background:#fecaca;}

/* New Resolution */
.new-res-box{background:linear-gradient(135deg,#fff7ed 0%,#ffedd5 100%);border:2px dashed #fdba74;border-radius:12px;padding:20px;margin-bottom:20px;display:none;}
.new-res-box.show{display:block;}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.3/sweetalert2.all.min.js"></script>
<script>
var data=[],tbl={},cid=null,cdata=null,stimer=null;
document.addEventListener('DOMContentLoaded',function(){loadTbl();loadData();});
function debounceSearch(){clearTimeout(stimer);stimer=setTimeout(loadData,300);}
function loadTbl(){fetch('/companies/tables').then(r=>r.json()).then(d=>{tbl=d;});}
function loadData(){
    fetch('/companies/records?search='+encodeURIComponent(document.getElementById('search').value))
    .then(r=>r.json()).then(d=>{data=d.data;render();});
}
function render(){
    var h='';
    if(!data.length){h='<tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">No hay empresas registradas</td></tr>';}
    else{
        data.forEach((c,i)=>{
            h+='<tr>';
            h+='<td style="font-weight:600;color:#64748b;">'+(i+1)+'</td>';
            h+='<td><span style="font-weight:700;color:#1e293b;">'+c.identification_number+'</span><span style="color:#94a3b8;">-'+(c.dv||0)+'</span></td>';
            h+='<td style="font-weight:500;color:#1e293b;">'+c.name+'</td>';
            h+='<td style="color:#64748b;font-size:12px;">'+c.email+'</td>';
            h+='<td><span class="badge-'+(c.type_environment_id==1?'prod':'hab')+'">'+(c.type_environment_id==1?'Producción':'Habilitación')+'</span></td>';
            h+='<td><span class="badge-'+(c.state?'on':'off')+'">'+(c.state?'Activa':'Inactiva')+'</span></td>';
            h+='<td><span style="background:#e0f2fe;color:#0369a1;padding:4px 12px;border-radius:8px;font-size:12px;font-weight:600;">'+c.documents_count+'</span></td>';
            h+='<td style="color:#64748b;font-size:12px;">'+c.created_at+'</td>';
            h+='<td style="text-align:center;">';
            h+='<div class="dropdown"><button class="btn btn-sm" style="background:#f1f5f9;border:none;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;color:#475569;" data-toggle="dropdown">Acciones <i class="fa fa-chevron-down ml-1" style="font-size:10px;"></i></button>';
            h+='<div class="dropdown-menu dropdown-menu-right">';
            h+='<a class="dropdown-item" href="#" onclick="edit('+c.id+')"><i class="fa fa-edit text-primary"></i> Editar</a>';
            h+='<a class="dropdown-item" href="#" onclick="viewDocs('+c.id+',\''+c.identification_number+'\')"><i class="fa fa-file-alt text-success"></i> Ver Documentos</a>';
            h+='<a class="dropdown-item" href="#" onclick="chgEnv('+c.id+','+c.type_environment_id+')"><i class="fa fa-exchange-alt text-info"></i> Cambiar Ambiente</a>';
            h+='<a class="dropdown-item" href="#" onclick="chgState('+c.id+','+(c.state?1:0)+')"><i class="fa fa-'+(c.state?'ban text-warning':'check text-success')+'"></i> '+(c.state?'Deshabilitar':'Habilitar')+'</a>';
            h+='<div class="dropdown-divider"></div>';
            h+='<a class="dropdown-item text-danger" href="#" onclick="del('+c.id+',\''+c.identification_number+'\')"><i class="fa fa-trash"></i> Eliminar</a>';
            h+='</div></div></td></tr>';
        });
    }
    document.getElementById('tblBody').innerHTML=h;
}
function csrf(){return document.querySelector('meta[name="csrf-token"]').content;}
function opts(arr,sel){if(!arr)return'';return arr.map(a=>'<option value="'+a.id+'"'+(a.id==sel?' selected':'')+'>'+a.name+'</option>').join('');}
function optsMun(sel,dept){if(!tbl.municipalities)return'<option value="">Seleccionar...</option>';var h='<option value="">Seleccionar...</option>';tbl.municipalities.forEach(m=>{if(m.department_id==dept)h+='<option value="'+m.id+'"'+(m.id==sel?' selected':'')+'>'+m.name+'</option>';});return h;}

function edit(id){
    cid=id;
    fetch('/companies/'+id+'/data').then(r=>r.json()).then(d=>{
        cdata=d;
        fillT1();fillT2();fillT3();fillT4();
        $('#tabsNav a:first').tab('show');
        $('#modalEdit').modal('show');
    });
}
</script>

<script>
function fillT1(){
    var c=cdata.company;
    var h='<div class="form-card">';
    h+='<div class="form-card-title"><i class="fa fa-building"></i> Datos Generales de la Empresa</div>';
    h+='<div class="form-card-desc">Complete la información básica de la empresa para facturación electrónica DIAN.</div>';
    
    h+='<div class="form-grid form-grid-3">';
    h+='<div class="form-group"><label class="form-label">Identificación (NIT)</label><input class="form-control-custom" id="f1" value="'+c.identification_number+'" placeholder="900123456"></div>';
    h+='<div class="form-group"><label class="form-label">DV</label><input class="form-control-custom" id="f2" value="'+(c.dv||'')+'" placeholder="0" maxlength="1"></div>';
    h+='<div class="form-group"><label class="form-label">Razón Social / Nombre</label><input class="form-control-custom" id="f3" value="'+c.name+'" placeholder="Nombre de la empresa"></div>';
    h+='</div>';
    
    h+='<div class="form-grid form-grid-3">';
    h+='<div class="form-group"><label class="form-label">Registro Mercantil</label><input class="form-control-custom" id="f4" value="'+(c.merchant_registration||'')+'" placeholder="0000000-00"></div>';
    h+='<div class="form-group"><label class="form-label">Dirección</label><input class="form-control-custom" id="f5" value="'+(c.address||'')+'" placeholder="Calle 123 # 45-67"></div>';
    h+='<div class="form-group"><label class="form-label">Teléfono</label><input class="form-control-custom" id="f6" value="'+(c.phone||'')+'" placeholder="3001234567"></div>';
    h+='</div>';
    
    h+='<div class="form-grid form-grid-3">';
    h+='<div class="form-group"><label class="form-label">Correo Electrónico</label><input class="form-control-custom" id="f7" value="'+c.email+'" placeholder="empresa@email.com" type="email"></div>';
    h+='<div class="form-group"><label class="form-label">Tipo de Documento</label><select class="form-control-custom" id="f8">'+opts(tbl.type_document_identifications,c.type_document_identification_id)+'</select></div>';
    h+='<div class="form-group"><label class="form-label">Departamento</label><select class="form-control-custom" id="f9" onchange="chgDept()"><option value="">Seleccionar...</option>'+opts(tbl.departments,c.department_id)+'</select></div>';
    h+='</div>';
    
    h+='<div class="form-grid form-grid-4">';
    h+='<div class="form-group"><label class="form-label">Municipio</label><select class="form-control-custom" id="f10">'+optsMun(c.municipality_id,c.department_id)+'</select></div>';
    h+='<div class="form-group"><label class="form-label">Tipo Organización</label><select class="form-control-custom" id="f11">'+opts(tbl.type_organizations,c.type_organization_id)+'</select></div>';
    h+='<div class="form-group"><label class="form-label">Régimen Tributario</label><select class="form-control-custom" id="f12">'+opts(tbl.type_regimes,c.type_regime_id)+'</select></div>';
    h+='<div class="form-group"><label class="form-label">Responsabilidad Fiscal</label><select class="form-control-custom" id="f13">'+opts(tbl.type_liabilities,c.type_liability_id)+'</select></div>';
    h+='</div>';
    
    h+='</div>';
    h+='<button class="btn-orange" onclick="saveT1()"><i class="fa fa-save"></i> Guardar Cambios</button>';
    document.getElementById('t1').innerHTML=h;
}

function fillT2(){
    var s=cdata.software||{};
    var h='<div class="form-card">';
    h+='<div class="form-card-title"><i class="fa fa-cog"></i> Configuración del Software DIAN</div>';
    h+='<div class="form-card-desc">Ingrese los datos del software registrado en la DIAN para esta empresa.</div>';
    
    h+='<div class="form-grid" style="grid-template-columns:1fr;">';
    h+='<div class="form-group"><label class="form-label">Identificador del Software (UUID)</label><input class="form-control-custom" id="s1" value="'+(s.identifier||'')+'" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" style="font-family:monospace;letter-spacing:0.5px;"></div>';
    h+='</div>';
    
    h+='<div class="form-grid form-grid-2">';
    h+='<div class="form-group"><label class="form-label">PIN del Software</label><input class="form-control-custom" id="s2" value="'+(s.pin||'')+'" placeholder="12345"></div>';
    h+='<div class="form-group"><label class="form-label">URL del Servicio Web (Opcional)</label><input class="form-control-custom" id="s3" value="'+(s.url||'')+'" placeholder="https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc"></div>';
    h+='</div>';
    
    h+='</div>';
    h+='<button class="btn-orange" onclick="saveT2()"><i class="fa fa-save"></i> Guardar Software</button>';
    document.getElementById('t2').innerHTML=h;
}

function fillT3(){
    var c=cdata.certificate||{};
    var h='<div class="form-card">';
    h+='<div class="form-card-title"><i class="fa fa-shield-alt"></i> Certificado Digital</div>';
    h+='<div class="form-card-desc">Cargue el certificado digital (.p12 o .pfx) para firmar los documentos electrónicos.</div>';
    
    if(c.name){
        h+='<div class="alert-success-box"><i class="fa fa-check-circle"></i><span><strong>Certificado cargado:</strong> '+c.name+(c.expiration?' &nbsp;|&nbsp; <strong>Vence:</strong> '+c.expiration:'')+'</span></div>';
    }else{
        h+='<div class="alert-warning-box"><i class="fa fa-exclamation-triangle"></i><span>No hay certificado digital cargado para esta empresa.</span></div>';
    }
    
    h+='<div class="form-grid form-grid-2">';
    h+='<div class="form-group"><label class="form-label">Archivo del Certificado (.p12 / .pfx)</label><input type="file" class="form-control-custom" id="c1" accept=".p12,.pfx" style="padding:10px;"></div>';
    h+='<div class="form-group"><label class="form-label">Contraseña del Certificado</label><input type="password" class="form-control-custom" id="c2" placeholder="••••••••••"></div>';
    h+='</div>';
    
    h+='</div>';
    h+='<button class="btn-orange" onclick="saveT3()"><i class="fa fa-upload"></i> Subir Certificado</button>';
    document.getElementById('t3').innerHTML=h;
}

function fillT4(){
    var r=cdata.resolutions||[];
    var tp=[{id:1,n:'Factura'},{id:4,n:'Nota Crédito'},{id:5,n:'Nota Débito'},{id:11,n:'Doc. Soporte'},{id:13,n:'Nota Ajuste DS'}];
    
    var h='<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">';
    h+='<div><div class="form-card-title" style="margin:0;"><i class="fa fa-file-invoice"></i> Resoluciones de Facturación</div>';
    h+='<div style="font-size:13px;color:#64748b;margin-top:4px;">Administre las resoluciones DIAN de esta empresa</div></div>';
    h+='<button class="btn-orange" onclick="toggleNR()"><i class="fa fa-plus"></i> Nueva Resolución</button></div>';
    
    // Form nueva resolución
    h+='<div class="new-res-box" id="nrBox">';
    h+='<div style="font-weight:600;color:#1e293b;margin-bottom:16px;"><i class="fa fa-plus-circle mr-2" style="color:#f97316;"></i>Agregar Nueva Resolución</div>';
    h+='<div class="form-grid form-grid-3">';
    h+='<div class="form-group"><label class="form-label">Tipo de Documento</label><select class="form-control-custom" id="nr1">'+tp.map(t=>'<option value="'+t.id+'">'+t.n+'</option>').join('')+'</select></div>';
    h+='<div class="form-group"><label class="form-label">Prefijo</label><input class="form-control-custom" id="nr2" placeholder="SETP"></div>';
    h+='<div class="form-group"><label class="form-label">Número de Resolución</label><input class="form-control-custom" id="nr3" placeholder="18760000001"></div>';
    h+='</div>';
    h+='<div class="form-grid form-grid-4">';
    h+='<div class="form-group"><label class="form-label">Desde</label><input type="number" class="form-control-custom" id="nr4" placeholder="1"></div>';
    h+='<div class="form-group"><label class="form-label">Hasta</label><input type="number" class="form-control-custom" id="nr5" placeholder="5000"></div>';
    h+='<div class="form-group"><label class="form-label">Vigencia Desde</label><input type="date" class="form-control-custom" id="nr6"></div>';
    h+='<div class="form-group"><label class="form-label">Vigencia Hasta</label><input type="date" class="form-control-custom" id="nr7"></div>';
    h+='</div>';
    h+='<div style="margin-top:12px;"><button class="btn-orange" onclick="addRes()"><i class="fa fa-check"></i> Guardar Resolución</button> <button class="btn-secondary" onclick="toggleNR()">Cancelar</button></div>';
    h+='</div>';
    
    // Tabla resoluciones
    h+='<table class="res-table"><thead><tr>';
    h+='<th>Tipo</th><th>Prefijo</th><th>Resolución</th><th>Rango</th><th>Actual</th><th>Vigencia</th><th style="text-align:center;">Acciones</th>';
    h+='</tr></thead><tbody>';
    if(r.length){
        r.forEach(x=>{
            var tn=tp.find(t=>t.id==x.type_document_id);
            h+='<tr>';
            h+='<td style="font-weight:500;">'+(tn?tn.n:'Tipo '+x.type_document_id)+'</td>';
            h+='<td><span style="background:#f1f5f9;padding:4px 10px;border-radius:6px;font-weight:700;color:#1e293b;">'+x.prefix+'</span></td>';
            h+='<td style="font-family:monospace;color:#475569;">'+(x.resolution||'-')+'</td>';
            h+='<td style="color:#64748b;font-size:12px;">'+(x.from||0).toLocaleString()+' - '+(x.to||0).toLocaleString()+'</td>';
            h+='<td><span class="badge-consec">'+x.prefix+(x.next_consecutive||x.from||1)+'</span></td>';
            h+='<td style="color:#64748b;font-size:12px;">'+(x.date_from||'-')+'<br>al '+(x.date_to||'-')+'</td>';
            h+='<td style="text-align:center;"><button class="btn-icon btn-icon-edit" onclick="editRes('+x.id+')" title="Editar"><i class="fa fa-edit"></i></button>';
            h+='<button class="btn-icon btn-icon-del" onclick="delRes('+x.id+')" title="Eliminar"><i class="fa fa-trash"></i></button></td>';
            h+='</tr>';
        });
    }else{
        h+='<tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">No hay resoluciones configuradas</td></tr>';
    }
    h+='</tbody></table>';
    
    document.getElementById('t4').innerHTML=h;
}

function toggleNR(){document.getElementById('nrBox').classList.toggle('show');}
function chgDept(){document.getElementById('f10').innerHTML=optsMun(null,document.getElementById('f9').value);}
</script>

<script>
// Guardar datos
function saveT1(){
    var data={
        identification_number:document.getElementById('f1').value,
        dv:document.getElementById('f2').value,
        name:document.getElementById('f3').value,
        merchant_registration:document.getElementById('f4').value,
        address:document.getElementById('f5').value,
        phone:document.getElementById('f6').value,
        email:document.getElementById('f7').value,
        type_document_identification_id:document.getElementById('f8').value,
        municipality_id:document.getElementById('f10').value,
        type_organization_id:document.getElementById('f11').value,
        type_regime_id:document.getElementById('f12').value,
        type_liability_id:document.getElementById('f13').value
    };
    fetch('/companies/'+cid,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify(data)})
    .then(r=>r.json()).then(x=>{
        if(x.success){Swal.fire({icon:'success',title:'¡Guardado!',text:'Los datos se actualizaron correctamente',confirmButtonColor:'#f97316'});loadData();}
        else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});
    });
}

function saveT2(){
    fetch('/companies/'+cid+'/software',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({identifier:document.getElementById('s1').value,pin:document.getElementById('s2').value,url:document.getElementById('s3').value})})
    .then(r=>r.json()).then(x=>{
        if(x.success)Swal.fire({icon:'success',title:'¡Guardado!',text:'Software actualizado',confirmButtonColor:'#f97316'});
        else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});
    });
}

function saveT3(){
    var f=document.getElementById('c1').files[0];
    if(!f&&!cdata.certificate){Swal.fire({icon:'error',title:'Error',text:'Seleccione un archivo',confirmButtonColor:'#f97316'});return;}
    if(!f)return;
    var fd=new FormData();fd.append('certificate',f);fd.append('password',document.getElementById('c2').value);
    Swal.fire({title:'Subiendo...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/companies/'+cid+'/certificate',{method:'POST',headers:{'X-CSRF-TOKEN':csrf()},body:fd})
    .then(r=>r.json()).then(x=>{
        if(x.success){Swal.fire({icon:'success',title:'¡Cargado!',confirmButtonColor:'#f97316'});fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT3();});}
        else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});
    });
}

function addRes(){
    var data={
        type_document_id:document.getElementById('nr1').value,
        prefix:document.getElementById('nr2').value,
        resolution:document.getElementById('nr3').value,
        from:document.getElementById('nr4').value||1,
        to:document.getElementById('nr5').value||5000,
        date_from:document.getElementById('nr6').value||new Date().toISOString().split('T')[0],
        date_to:document.getElementById('nr7').value||'2030-12-31',
        resolution_date:new Date().toISOString().split('T')[0]
    };
    fetch('/companies/'+cid+'/resolution',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify(data)})
    .then(r=>r.json()).then(x=>{
        if(x.success){Swal.fire({icon:'success',title:'¡Creada!',confirmButtonColor:'#f97316'});fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT4();});}
        else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});
    });
}

function delRes(id){
    Swal.fire({title:'¿Eliminar resolución?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/resolution/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf()}}).then(()=>{fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT4();});});
    });
}

function editRes(id){
    var res=cdata.resolutions.find(r=>r.id==id);if(!res)return;
    Swal.fire({
        title:'<span style="font-size:18px;font-weight:600;">Editar Resolución</span>',
        html:`<div style="text-align:left;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;">Prefijo</label>
                <input id="ed_prefix" value="${res.prefix}" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;"></div>
                <div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;">Resolución</label>
                <input id="ed_res" value="${res.resolution||''}" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-top:14px;">
                <div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;">Desde</label>
                <input id="ed_from" type="number" value="${res.from||1}" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;"></div>
                <div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;">Hasta</label>
                <input id="ed_to" type="number" value="${res.to||5000}" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;"></div>
                <div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;">Actual</label>
                <input id="ed_next" type="number" value="${res.next_consecutive||res.from||1}" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;background:#f0fdf4;"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px;">
                <div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;">Vigencia Desde</label>
                <input id="ed_dfrom" type="date" value="${res.date_from||''}" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;"></div>
                <div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;">Vigencia Hasta</label>
                <input id="ed_dto" type="date" value="${res.date_to||''}" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;"></div>
            </div>
            <div style="margin-top:14px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;">Clave Técnica</label>
                <input id="ed_key" value="${res.technical_key||''}" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
            </div>
        </div>`,
        width:550,
        showCancelButton:true,
        confirmButtonText:'<i class="fa fa-save"></i> Guardar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#f97316',
        cancelButtonColor:'#64748b',
        preConfirm:()=>({prefix:document.getElementById('ed_prefix').value,resolution:document.getElementById('ed_res').value,from:document.getElementById('ed_from').value,to:document.getElementById('ed_to').value,next_consecutive:document.getElementById('ed_next').value,technical_key:document.getElementById('ed_key').value,date_from:document.getElementById('ed_dfrom').value,date_to:document.getElementById('ed_dto').value})
    }).then(result=>{
        if(result.isConfirmed){
            fetch('/companies/resolution/'+id,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify(result.value)})
            .then(r=>r.json()).then(x=>{
                if(x.success){Swal.fire({icon:'success',title:'¡Actualizada!',confirmButtonColor:'#f97316'});fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT4();});}
                else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});
            });
        }
    });
}

// Acciones de tabla
function chgEnv(id,c){
    Swal.fire({title:'Cambiar Ambiente',text:'¿Cambiar a '+(c==1?'Habilitación':'Producción')+'?',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316',cancelButtonColor:'#64748b',confirmButtonText:'Sí, cambiar'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/'+id+'/environment',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({type_environment_id:c==1?2:1})}).then(()=>loadData());
    });
}

function chgState(id,c){
    Swal.fire({title:(c?'Deshabilitar':'Habilitar')+' Empresa',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316',cancelButtonColor:'#64748b',confirmButtonText:'Sí, confirmar'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/'+id+'/toggle-state',{method:'POST',headers:{'X-CSRF-TOKEN':csrf()}}).then(()=>loadData());
    });
}

function del(id,nit){
    Swal.fire({title:'Eliminar Empresa',html:'¿Eliminar <strong>'+nit+'</strong>?<br><small style="color:#dc2626;">Se eliminarán todos los datos</small>',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Sí, eliminar'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf()}}).then(()=>{Swal.fire({icon:'success',title:'Eliminada',confirmButtonColor:'#f97316'});loadData();});
    });
}

function viewDocs(id,nit){
    Swal.fire({title:'Cargando...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/companies/'+id+'/documents').then(r=>r.json()).then(d=>{
        Swal.close();
        var docs=d.data||[];
        var tipos={'1':'Factura','4':'NC','5':'ND','11':'DS','13':'NA DS'};
        var h='<div style="max-height:450px;overflow-y:auto;">';
        h+='<table style="width:100%;border-collapse:collapse;font-size:13px;">';
        h+='<thead><tr style="background:#f8fafc;position:sticky;top:0;">';
        h+='<th style="padding:12px;text-align:left;border-bottom:2px solid #e2e8f0;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Tipo</th>';
        h+='<th style="padding:12px;text-align:left;border-bottom:2px solid #e2e8f0;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Número</th>';
        h+='<th style="padding:12px;text-align:left;border-bottom:2px solid #e2e8f0;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Fecha</th>';
        h+='<th style="padding:12px;text-align:right;border-bottom:2px solid #e2e8f0;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Total</th>';
        h+='<th style="padding:12px;text-align:center;border-bottom:2px solid #e2e8f0;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Estado</th>';
        h+='</tr></thead><tbody>';
        if(docs.length){
            docs.forEach(doc=>{
                var tipo=tipos[doc.type_document_id]||'Doc '+doc.type_document_id;
                var estado=doc.state_document_id==1?'<span style="background:#dcfce7;color:#166534;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;">Aprobado</span>':'<span style="background:#fee2e2;color:#991b1b;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;">Pendiente</span>';
                h+='<tr style="border-bottom:1px solid #f1f5f9;">';
                h+='<td style="padding:12px;">'+tipo+'</td>';
                h+='<td style="padding:12px;font-weight:600;">'+(doc.prefix||'')+doc.number+'</td>';
                h+='<td style="padding:12px;color:#64748b;">'+doc.created_at+'</td>';
                h+='<td style="padding:12px;text-align:right;font-family:monospace;">$'+Number(doc.total||0).toLocaleString()+'</td>';
                h+='<td style="padding:12px;text-align:center;">'+estado+'</td>';
                h+='</tr>';
            });
        }else{
            h+='<tr><td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;">No hay documentos emitidos</td></tr>';
        }
        h+='</tbody></table></div>';
        Swal.fire({
            title:'<span style="font-size:17px;"><i class="fa fa-file-alt mr-2" style="color:#f97316;"></i>Documentos de '+nit+'</span>',
            html:h,
            width:750,
            showCloseButton:true,
            showConfirmButton:false,
        });
    }).catch(()=>{Swal.fire({icon:'error',title:'Error',text:'No se pudieron cargar los documentos',confirmButtonColor:'#f97316'});});
}
</script>
@endsection
