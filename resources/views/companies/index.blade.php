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
                <a href="/configuration_admin" style="background:linear-gradient(135deg,#f97316 0%,#ea580c 100%);color:white;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(249,115,22,0.3);">
                    <i class="fa fa-plus"></i> Nueva Empresa
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0" style="border-collapse:separate;border-spacing:0;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;">#</th>
                    <th style="padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;">NIT</th>
                    <th style="padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;">Empresa</th>
                    <th style="padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;">Email</th>
                    <th style="padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;">Ambiente</th>
                    <th style="padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;">Estado</th>
                    <th style="padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;">Docs</th>
                    <th style="padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;">Fecha</th>
                    <th style="padding:14px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e2e8f0;text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="tblBody"></tbody>
        </table>
    </div>
</div>

<!-- MODAL PROFESIONAL -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog" style="max-width:1000px;margin:30px auto;">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#1e293b 0%,#334155 100%);padding:20px 28px;display:flex;justify-content:space-between;align-items:center;">
                <span style="color:white;font-size:17px;font-weight:600;">Configuración de Empresa</span>
                <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:1;font-size:28px;font-weight:300;text-shadow:none;">&times;</button>
            </div>
            
            <!-- Tabs -->
            <div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:0 20px;">
                <ul class="nav nav-tabs" id="tabsNav" style="border:none;gap:8px;">
                    <li class="nav-item"><a class="nav-link active" id="t1-tab" data-toggle="tab" href="#t1"><span class="tab-num">1</span>Empresa</a></li>
                    <li class="nav-item"><a class="nav-link" id="t2-tab" data-toggle="tab" href="#t2"><span class="tab-num">2</span>Software</a></li>
                    <li class="nav-item"><a class="nav-link" id="t3-tab" data-toggle="tab" href="#t3"><span class="tab-num">3</span>Certificado</a></li>
                    <li class="nav-item"><a class="nav-link" id="t4-tab" data-toggle="tab" href="#t4"><span class="tab-num">4</span>Resolución</a></li>
                </ul>
            </div>
            
            <!-- Content -->
            <div class="tab-content" style="padding:32px;min-height:480px;background:white;">
                <div class="tab-pane fade show active" id="t1"></div>
                <div class="tab-pane fade" id="t2"></div>
                <div class="tab-pane fade" id="t3"></div>
                <div class="tab-pane fade" id="t4"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* Tabla */
#tblBody tr{transition:all 0.15s;}
#tblBody tr:hover{background:#fff7ed;}
#tblBody td{padding:14px 16px;font-size:13px;vertical-align:middle;border-bottom:1px solid #f1f5f9;}

/* Badges */
.bdg{display:inline-flex;align-items:center;padding:6px 14px;border-radius:20px;font-size:11px;font-weight:600;}
.bdg-prod{background:#dcfce7;color:#166534;}
.bdg-hab{background:#fef3c7;color:#92400e;}
.bdg-on{background:#dcfce7;color:#166534;}
.bdg-off{background:#fee2e2;color:#991b1b;}

/* Dropdown */
.dropdown-menu{border:none;box-shadow:0 10px 40px rgba(0,0,0,0.12);border-radius:12px;padding:8px;}
.dropdown-item{font-size:13px;padding:10px 16px;border-radius:8px;margin:2px 0;}
.dropdown-item:hover{background:#fff7ed;}
.dropdown-item i{width:20px;}

/* Tabs */
#tabsNav{margin:0;}
#tabsNav .nav-link{border:none;padding:16px 24px;font-size:14px;font-weight:500;color:#64748b;background:transparent;display:flex;align-items:center;gap:10px;border-bottom:3px solid transparent;margin-bottom:-1px;transition:all 0.2s;}
#tabsNav .nav-link:hover{color:#f97316;}
#tabsNav .nav-link.active{color:#f97316;border-bottom-color:#f97316;font-weight:600;}
.tab-num{width:24px;height:24px;border-radius:50%;background:#cbd5e1;color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;}
#tabsNav .nav-link.active .tab-num{background:#f97316;}

/* Form Elements */
.form-card{background:#f8fafc;border-radius:16px;padding:28px;margin-bottom:24px;}
.form-card-title{font-size:15px;font-weight:700;color:#1e293b;margin-bottom:6px;display:flex;align-items:center;gap:10px;}
.form-card-title i{color:#f97316;}
.form-card-desc{font-size:13px;color:#64748b;margin-bottom:24px;}
.form-row{display:flex;gap:20px;margin-bottom:20px;}
.form-col{flex:1;}
.form-label{display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.3px;}
.form-input{width:100%;padding:14px 18px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;color:#1e293b;transition:all 0.2s;background:white;}
.form-input:focus{outline:none;border-color:#f97316;box-shadow:0 0 0 4px rgba(249,115,22,0.1);}
.form-input::placeholder{color:#94a3b8;}
.form-select{width:100%;padding:14px 18px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;color:#1e293b;background:white url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") right 12px center/20px no-repeat;appearance:none;cursor:pointer;}
.form-select:focus{outline:none;border-color:#f97316;box-shadow:0 0 0 4px rgba(249,115,22,0.1);}

/* Buttons */
.btn-primary-custom{background:linear-gradient(135deg,#f97316 0%,#ea580c 100%);color:white;border:none;padding:14px 28px;border-radius:12px;font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:10px;box-shadow:0 4px 14px rgba(249,115,22,0.3);transition:all 0.2s;}
.btn-primary-custom:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(249,115,22,0.4);}
.btn-secondary-custom{background:#f1f5f9;color:#475569;border:none;padding:14px 28px;border-radius:12px;font-size:14px;font-weight:600;cursor:pointer;transition:all 0.2s;}
.btn-secondary-custom:hover{background:#e2e8f0;}

/* Alert boxes */
.alert-success-custom{background:linear-gradient(135deg,#ecfdf5 0%,#d1fae5 100%);border:2px solid #a7f3d0;border-radius:12px;padding:18px 22px;margin-bottom:24px;display:flex;align-items:center;gap:14px;}
.alert-success-custom i{color:#22c55e;font-size:22px;}
.alert-success-custom span{font-size:14px;color:#166534;}
.alert-warning-custom{background:linear-gradient(135deg,#fffbeb 0%,#fef3c7 100%);border:2px solid #fde68a;border-radius:12px;padding:18px 22px;margin-bottom:24px;display:flex;align-items:center;gap:14px;}
.alert-warning-custom i{color:#f59e0b;font-size:22px;}
.alert-warning-custom span{font-size:14px;color:#92400e;}

/* Resolution Table */
.res-table-wrapper{background:white;border-radius:12px;border:2px solid #e2e8f0;overflow:hidden;}
.res-table{width:100%;border-collapse:collapse;}
.res-table th{background:#f8fafc;padding:14px 18px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;text-align:left;border-bottom:2px solid #e2e8f0;}
.res-table td{padding:16px 18px;font-size:13px;border-bottom:1px solid #f1f5f9;vertical-align:middle;}
.res-table tr:last-child td{border-bottom:none;}
.res-table tr:hover{background:#fefce8;}
.badge-consec{background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%);color:white;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;font-family:'Courier New',monospace;}
.btn-action{width:36px;height:36px;border-radius:10px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all 0.2s;}
.btn-action-edit{background:#dbeafe;color:#2563eb;}
.btn-action-edit:hover{background:#bfdbfe;transform:scale(1.05);}
.btn-action-del{background:#fee2e2;color:#dc2626;}
.btn-action-del:hover{background:#fecaca;transform:scale(1.05);}

/* New Resolution Form */
.new-res-form{background:linear-gradient(135deg,#fff7ed 0%,#ffedd5 100%);border:2px dashed #fdba74;border-radius:16px;padding:24px;margin-bottom:24px;display:none;}
.new-res-form.show{display:block;}
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
            h+='<tr><td style="font-weight:600;color:#64748b;">'+(i+1)+'</td>';
            h+='<td><span style="font-weight:700;color:#1e293b;">'+c.identification_number+'</span><span style="color:#94a3b8;">-'+(c.dv||0)+'</span></td>';
            h+='<td style="font-weight:500;color:#1e293b;">'+c.name+'</td>';
            h+='<td style="color:#64748b;font-size:12px;">'+c.email+'</td>';
            h+='<td><span class="bdg bdg-'+(c.type_environment_id==1?'prod':'hab')+'">'+(c.type_environment_id==1?'Producción':'Habilitación')+'</span></td>';
            h+='<td><span class="bdg bdg-'+(c.state?'on':'off')+'">'+(c.state?'Activa':'Inactiva')+'</span></td>';
            h+='<td><span style="background:#f1f5f9;padding:4px 12px;border-radius:8px;font-size:12px;font-weight:600;color:#475569;">'+c.documents_count+'</span></td>';
            h+='<td style="color:#64748b;font-size:12px;">'+c.created_at+'</td>';
            h+='<td style="text-align:center;"><div class="dropdown"><button class="btn btn-sm" style="background:#f1f5f9;border:none;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;color:#475569;" data-toggle="dropdown">Acciones <i class="fa fa-chevron-down ml-1" style="font-size:10px;"></i></button>';
            h+='<div class="dropdown-menu dropdown-menu-right">';
            h+='<a class="dropdown-item" href="#" onclick="edit('+c.id+')"><i class="fa fa-edit text-primary"></i> Editar</a>';
            h+='<a class="dropdown-item" href="#" onclick="chgEnv('+c.id+','+c.type_environment_id+')"><i class="fa fa-exchange-alt text-info"></i> Cambiar Ambiente</a>';
            h+='<a class="dropdown-item" href="#" onclick="chgState('+c.id+','+(c.state?1:0)+')"><i class="fa fa-'+(c.state?'ban text-warning':'check text-success')+'"></i> '+(c.state?'Deshabilitar':'Habilitar')+'</a>';
            h+='<div class="dropdown-divider"></div>';
            h+='<a class="dropdown-item text-danger" href="#" onclick="del('+c.id+',\''+c.identification_number+'\')"><i class="fa fa-trash"></i> Eliminar</a>';
            h+='</div></div></td></tr>';
        });
    }
    document.getElementById('tblBody').innerHTML=h;
}
function edit(id){
    cid=id;
    fetch('/companies/'+id+'/data').then(r=>r.json()).then(d=>{
        cdata=d;
        fillT1();fillT2();fillT3();fillT4();
        $('#t1-tab').tab('show');
        $('#modalEdit').modal('show');
    });
}
function fillT1(){
    var c=cdata.company;
    var h='<div class="form-card">';
    h+='<div class="form-card-title"><i class="fa fa-building"></i> Datos Generales de la Empresa</div>';
    h+='<div class="form-card-desc">Complete la información básica de la empresa para facturación electrónica DIAN.</div>';
    h+='<div class="form-row"><div class="form-col" style="flex:2;"><label class="form-label">Identificación (NIT)</label><input class="form-input" id="f1" value="'+c.identification_number+'" placeholder="900123456"></div>';
    h+='<div class="form-col" style="flex:0.5;"><label class="form-label">DV</label><input class="form-input" id="f2" value="'+(c.dv||'')+'" placeholder="0" maxlength="1"></div>';
    h+='<div class="form-col" style="flex:3;"><label class="form-label">Razón Social / Nombre</label><input class="form-input" id="f3" value="'+c.name+'" placeholder="Nombre de la empresa"></div></div>';
    h+='<div class="form-row"><div class="form-col"><label class="form-label">Registro Mercantil</label><input class="form-input" id="f4" value="'+(c.merchant_registration||'')+'" placeholder="0000000-00"></div>';
    h+='<div class="form-col"><label class="form-label">Dirección</label><input class="form-input" id="f5" value="'+(c.address||'')+'" placeholder="Calle 123 # 45-67"></div>';
    h+='<div class="form-col"><label class="form-label">Teléfono</label><input class="form-input" id="f6" value="'+(c.phone||'')+'" placeholder="3001234567"></div></div>';
    h+='<div class="form-row"><div class="form-col"><label class="form-label">Correo Electrónico</label><input class="form-input" id="f7" value="'+c.email+'" placeholder="empresa@email.com" type="email"></div>';
    h+='<div class="form-col"><label class="form-label">Tipo de Documento</label><select class="form-select" id="f8">'+opts(tbl.type_document_identifications,c.type_document_identification_id)+'</select></div>';
    h+='<div class="form-col"><label class="form-label">Departamento</label><select class="form-select" id="f9" onchange="chgDept()"><option value="">Seleccionar...</option>'+opts(tbl.departments,c.department_id)+'</select></div></div>';
    h+='<div class="form-row"><div class="form-col"><label class="form-label">Municipio</label><select class="form-select" id="f10">'+optsMun(c.municipality_id,c.department_id)+'</select></div>';
    h+='<div class="form-col"><label class="form-label">Tipo de Organización</label><select class="form-select" id="f11">'+opts(tbl.type_organizations,c.type_organization_id)+'</select></div>';
    h+='<div class="form-col"><label class="form-label">Régimen Tributario</label><select class="form-select" id="f12">'+opts(tbl.type_regimes,c.type_regime_id)+'</select></div></div>';
    h+='</div>';
    h+='<button class="btn-primary-custom" onclick="saveT1()"><i class="fa fa-save"></i> Guardar Cambios</button>';
    document.getElementById('t1').innerHTML=h;
}
function fillT2(){
    var s=cdata.software||{};
    var h='<div class="form-card">';
    h+='<div class="form-card-title"><i class="fa fa-cog"></i> Configuración del Software DIAN</div>';
    h+='<div class="form-card-desc">Ingrese los datos del software registrado en la DIAN para esta empresa.</div>';
    h+='<div class="form-row"><div class="form-col"><label class="form-label">Identificador del Software (UUID)</label><input class="form-input" id="s1" value="'+(s.identifier||'')+'" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" style="font-family:\'Courier New\',monospace;letter-spacing:0.5px;"></div></div>';
    h+='<div class="form-row"><div class="form-col" style="flex:1;"><label class="form-label">PIN del Software</label><input class="form-input" id="s2" value="'+(s.pin||'')+'" placeholder="12345"></div>';
    h+='<div class="form-col" style="flex:2;"><label class="form-label">URL del Servicio Web (Opcional)</label><input class="form-input" id="s3" value="'+(s.url||'')+'" placeholder="https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc"></div></div>';
    h+='</div>';
    h+='<button class="btn-primary-custom" onclick="saveT2()"><i class="fa fa-save"></i> Guardar Software</button>';
    document.getElementById('t2').innerHTML=h;
}
function fillT3(){
    var c=cdata.certificate||{};
    var h='<div class="form-card">';
    h+='<div class="form-card-title"><i class="fa fa-shield-alt"></i> Certificado Digital</div>';
    h+='<div class="form-card-desc">Cargue el certificado digital (.p12 o .pfx) para firmar los documentos electrónicos.</div>';
    if(c.name){
        h+='<div class="alert-success-custom"><i class="fa fa-check-circle"></i><span><strong>Certificado cargado:</strong> '+c.name+(c.expiration?' &nbsp;|&nbsp; <strong>Vence:</strong> '+c.expiration:'')+'</span></div>';
    }else{
        h+='<div class="alert-warning-custom"><i class="fa fa-exclamation-triangle"></i><span>No hay certificado digital cargado para esta empresa.</span></div>';
    }
    h+='<div class="form-row"><div class="form-col"><label class="form-label">Archivo del Certificado (.p12 / .pfx)</label><input type="file" class="form-input" id="c1" accept=".p12,.pfx" style="padding:12px;"></div>';
    h+='<div class="form-col"><label class="form-label">Contraseña del Certificado</label><input type="password" class="form-input" id="c2" placeholder="••••••••••"></div></div>';
    h+='</div>';
    h+='<button class="btn-primary-custom" onclick="saveT3()"><i class="fa fa-upload"></i> Subir Certificado</button>';
    document.getElementById('t3').innerHTML=h;
}
</script>

<script>
function fillT4(){
    var r=cdata.resolutions||[];
    var tp=[{id:1,n:'Factura'},{id:4,n:'Nota Crédito'},{id:5,n:'Nota Débito'},{id:11,n:'Doc. Soporte'},{id:12,n:'Nota Ajuste'}];
    var h='<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">';
    h+='<div><div class="form-card-title" style="margin:0;"><i class="fa fa-file-invoice"></i> Resoluciones de Facturación</div>';
    h+='<div style="font-size:13px;color:#64748b;margin-top:4px;">Administre las resoluciones DIAN de esta empresa</div></div>';
    h+='<button class="btn-primary-custom" style="padding:12px 20px;" onclick="toggleNR()"><i class="fa fa-plus"></i> Nueva Resolución</button></div>';
    
    h+='<div class="new-res-form" id="nrBox">';
    h+='<div style="font-weight:600;color:#1e293b;margin-bottom:16px;"><i class="fa fa-plus-circle mr-2" style="color:#f97316;"></i>Agregar Nueva Resolución</div>';
    h+='<div class="form-row"><div class="form-col"><label class="form-label">Tipo de Documento</label><select class="form-select" id="nr1">'+tp.map(t=>'<option value="'+t.id+'">'+t.n+'</option>').join('')+'</select></div>';
    h+='<div class="form-col"><label class="form-label">Prefijo</label><input class="form-input" id="nr2" placeholder="SETP"></div>';
    h+='<div class="form-col"><label class="form-label">Número de Resolución</label><input class="form-input" id="nr3" placeholder="18760000001"></div></div>';
    h+='<div class="form-row"><div class="form-col"><label class="form-label">Consecutivo Desde</label><input type="number" class="form-input" id="nr4" placeholder="1"></div>';
    h+='<div class="form-col"><label class="form-label">Consecutivo Hasta</label><input type="number" class="form-input" id="nr5" placeholder="5000"></div>';
    h+='<div class="form-col"><label class="form-label">&nbsp;</label><button class="btn-primary-custom" style="width:100%;justify-content:center;" onclick="addRes()"><i class="fa fa-check"></i> Guardar</button></div></div>';
    h+='</div>';
    
    h+='<div class="res-table-wrapper"><table class="res-table"><thead><tr>';
    h+='<th>Tipo</th><th>Prefijo</th><th>Resolución</th><th>Rango</th><th>Consecutivo Actual</th><th>Vigencia</th><th style="text-align:center;">Acciones</th>';
    h+='</tr></thead><tbody>';
    if(r.length){
        r.forEach(x=>{
            var tn=tp.find(t=>t.id==x.type_document_id);
            h+='<tr>';
            h+='<td style="font-weight:500;">'+(tn?tn.n:x.type_document_id)+'</td>';
            h+='<td><span style="background:#f1f5f9;padding:4px 10px;border-radius:6px;font-weight:700;color:#1e293b;">'+x.prefix+'</span></td>';
            h+='<td style="font-family:\'Courier New\',monospace;color:#475569;">'+x.resolution+'</td>';
            h+='<td style="color:#64748b;">'+x.from.toLocaleString()+' - '+x.to.toLocaleString()+'</td>';
            h+='<td><span class="badge-consec">'+x.prefix+(x.next_consecutive||x.from)+'</span></td>';
            h+='<td style="color:#64748b;font-size:12px;">'+(x.date_from||'')+'<br>al '+(x.date_to||'')+'</td>';
            h+='<td style="text-align:center;"><button class="btn-action btn-action-edit" onclick="editRes('+x.id+')" title="Editar"><i class="fa fa-edit"></i></button>';
            h+='<button class="btn-action btn-action-del" onclick="delRes('+x.id+')" title="Eliminar"><i class="fa fa-trash"></i></button></td>';
            h+='</tr>';
        });
    }else{
        h+='<tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">No hay resoluciones configuradas</td></tr>';
    }
    h+='</tbody></table></div>';
    h+='<div style="margin-top:24px;"><button class="btn-secondary-custom" data-dismiss="modal"><i class="fa fa-times mr-2"></i>Cerrar</button></div>';
    document.getElementById('t4').innerHTML=h;
}
function toggleNR(){var b=document.getElementById('nrBox');b.classList.toggle('show');}
function opts(arr,sel){if(!arr)return'';return arr.map(a=>'<option value="'+a.id+'"'+(a.id==sel?' selected':'')+'>'+a.name+'</option>').join('');}
function optsMun(sel,dept){if(!tbl.municipalities)return'<option>Seleccionar...</option>';var h='<option value="">Seleccionar...</option>';tbl.municipalities.forEach(m=>{if(m.department_id==dept)h+='<option value="'+m.id+'"'+(m.id==sel?' selected':'')+'>'+m.name+'</option>';});return h;}
function chgDept(){document.getElementById('f10').innerHTML=optsMun(null,document.getElementById('f9').value);}
function csrf(){return document.querySelector('meta[name="csrf-token"]').content;}
function saveT1(){
    fetch('/companies/'+cid,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({identification_number:document.getElementById('f1').value,dv:document.getElementById('f2').value,name:document.getElementById('f3').value,merchant_registration:document.getElementById('f4').value,address:document.getElementById('f5').value,phone:document.getElementById('f6').value,email:document.getElementById('f7').value,type_document_identification_id:document.getElementById('f8').value,municipality_id:document.getElementById('f10').value,type_organization_id:document.getElementById('f11').value,type_regime_id:document.getElementById('f12').value})})
    .then(r=>r.json()).then(x=>{if(x.success){Swal.fire({icon:'success',title:'¡Guardado!',text:'Los datos de la empresa se actualizaron correctamente',confirmButtonColor:'#f97316'});loadData();}else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});});
}
function saveT2(){
    fetch('/companies/'+cid+'/software',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({identifier:document.getElementById('s1').value,pin:document.getElementById('s2').value,url:document.getElementById('s3').value})})
    .then(r=>r.json()).then(x=>{if(x.success)Swal.fire({icon:'success',title:'¡Guardado!',text:'La configuración del software se actualizó',confirmButtonColor:'#f97316'});else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});});
}
function saveT3(){
    var f=document.getElementById('c1').files[0];if(!f){if(cdata.certificate)return;Swal.fire({icon:'error',title:'Error',text:'Seleccione un archivo de certificado',confirmButtonColor:'#f97316'});return;}
    var fd=new FormData();fd.append('certificate',f);fd.append('password',document.getElementById('c2').value);
    Swal.fire({title:'Subiendo certificado...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/companies/'+cid+'/certificate',{method:'POST',headers:{'X-CSRF-TOKEN':csrf()},body:fd})
    .then(r=>r.json()).then(x=>{if(x.success){Swal.fire({icon:'success',title:'¡Cargado!',text:'El certificado se subió correctamente',confirmButtonColor:'#f97316'});fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT3();});}else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});});
}
function addRes(){
    fetch('/companies/'+cid+'/resolution',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({type_document_id:document.getElementById('nr1').value,prefix:document.getElementById('nr2').value,resolution:document.getElementById('nr3').value,from:document.getElementById('nr4').value,to:document.getElementById('nr5').value,resolution_date:new Date().toISOString().split('T')[0],date_from:new Date().toISOString().split('T')[0],date_to:'2030-12-31'})})
    .then(r=>r.json()).then(x=>{if(x.success){Swal.fire({icon:'success',title:'¡Creada!',text:'La resolución se agregó correctamente',confirmButtonColor:'#f97316'});fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT4();});}else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});});
}
function delRes(id){
    Swal.fire({title:'¿Eliminar resolución?',text:'Esta acción no se puede deshacer',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/resolution/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf()}}).then(r=>r.json()).then(()=>{fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT4();});});
    });
}
function editRes(id){
    var res=cdata.resolutions.find(r=>r.id==id);if(!res)return;
    Swal.fire({
        title:'<span style="font-size:20px;font-weight:600;">Editar Resolución</span>',
        html:`<div style="text-align:left;padding:10px 0;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;">Prefijo</label>
                <input id="ed_prefix" value="${res.prefix}" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;"></div>
                <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;">Resolución</label>
                <input id="ed_res" value="${res.resolution}" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;">Desde</label>
                <input id="ed_from" type="number" value="${res.from}" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;"></div>
                <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;">Hasta</label>
                <input id="ed_to" type="number" value="${res.to}" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;">Consecutivo Actual</label>
                <input id="ed_next" type="number" value="${res.next_consecutive||res.from}" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;background:#f0fdf4;"></div>
                <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;">Clave Técnica</label>
                <input id="ed_key" value="${res.technical_key||''}" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;">Fecha Desde</label>
                <input id="ed_dfrom" type="date" value="${res.date_from||''}" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;"></div>
                <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;text-transform:uppercase;">Fecha Hasta</label>
                <input id="ed_dto" type="date" value="${res.date_to||''}" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;"></div>
            </div>
        </div>`,
        width:600,
        showCancelButton:true,
        confirmButtonText:'<i class="fa fa-save"></i> Guardar Cambios',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#f97316',
        cancelButtonColor:'#64748b',
        preConfirm:()=>({prefix:document.getElementById('ed_prefix').value,resolution:document.getElementById('ed_res').value,from:document.getElementById('ed_from').value,to:document.getElementById('ed_to').value,next_consecutive:document.getElementById('ed_next').value,technical_key:document.getElementById('ed_key').value,date_from:document.getElementById('ed_dfrom').value,date_to:document.getElementById('ed_dto').value})
    }).then(result=>{
        if(result.isConfirmed){
            fetch('/companies/resolution/'+id,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify(result.value)})
            .then(r=>r.json()).then(x=>{if(x.success){Swal.fire({icon:'success',title:'¡Actualizada!',confirmButtonColor:'#f97316'});fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT4();});}else Swal.fire({icon:'error',title:'Error',text:x.message,confirmButtonColor:'#f97316'});});
        }
    });
}
function chgEnv(id,c){
    Swal.fire({title:'Cambiar Ambiente',text:'¿Cambiar a '+(c==1?'Habilitación':'Producción')+'?',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316',cancelButtonColor:'#64748b',confirmButtonText:'Sí, cambiar'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/'+id+'/environment',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({type_environment_id:c==1?2:1})}).then(()=>loadData());
    });
}
function chgState(id,c){
    Swal.fire({title:(c?'Deshabilitar':'Habilitar')+' Empresa',text:'¿Está seguro?',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316',cancelButtonColor:'#64748b',confirmButtonText:'Sí, confirmar'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/'+id+'/toggle-state',{method:'POST',headers:{'X-CSRF-TOKEN':csrf()}}).then(()=>loadData());
    });
}
function del(id,nit){
    Swal.fire({title:'Eliminar Empresa',html:'¿Eliminar <strong>'+nit+'</strong>?<br><small style="color:#dc2626;">Se eliminarán todos los datos asociados</small>',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Sí, eliminar'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf()}}).then(()=>{Swal.fire({icon:'success',title:'Eliminada',confirmButtonColor:'#f97316'});loadData();});
    });
}
</script>
@endsection
