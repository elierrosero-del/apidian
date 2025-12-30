@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="font-weight: 600;">
                <i class="fa fa-building mr-2" style="color: #f97316;"></i>Gestión de Empresas
            </h5>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <input type="text" id="search" class="form-control form-control-sm" placeholder="Buscar..." style="width: 220px;" onkeyup="debounceSearch()">
                <a href="/configuration_admin" class="btn btn-sm" style="background:#f97316;color:white;font-weight:600;"><i class="fa fa-plus"></i> Nueva</a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>#</th><th>NIT</th><th>Empresa</th><th>Email</th><th>Ambiente</th><th>Estado</th><th>Docs</th><th>Fecha</th><th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="tblBody"></tbody>
        </table>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog" style="max-width:950px;margin:25px auto;">
        <div class="modal-content" style="border:none;border-radius:6px;overflow:hidden;">
            <div class="modal-header" style="background:#343a40;padding:14px 20px;border:none;">
                <span class="text-white" style="font-size:15px;">Configuracion de Empresa</span>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity:1;">&times;</button>
            </div>
            <div class="modal-body p-0">
                <!-- TABS BOOTSTRAP -->
                <ul class="nav nav-tabs" id="tabsNav" style="background:#f5f5f5;padding:0 15px;border-bottom:1px solid #ddd;">
                    <li class="nav-item">
                        <a class="nav-link active" id="t1-tab" data-toggle="tab" href="#t1" style="border:none;padding:12px 20px;font-size:13px;color:#666;">
                            <span style="display:inline-block;width:20px;height:20px;background:#f97316;color:white;border-radius:50%;text-align:center;line-height:20px;font-size:11px;margin-right:8px;">1</span>Empresa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="t2-tab" data-toggle="tab" href="#t2" style="border:none;padding:12px 20px;font-size:13px;color:#666;">
                            <span style="display:inline-block;width:20px;height:20px;background:#ccc;color:white;border-radius:50%;text-align:center;line-height:20px;font-size:11px;margin-right:8px;">2</span>Software
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="t3-tab" data-toggle="tab" href="#t3" style="border:none;padding:12px 20px;font-size:13px;color:#666;">
                            <span style="display:inline-block;width:20px;height:20px;background:#ccc;color:white;border-radius:50%;text-align:center;line-height:20px;font-size:11px;margin-right:8px;">3</span>Certificado
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="t4-tab" data-toggle="tab" href="#t4" style="border:none;padding:12px 20px;font-size:13px;color:#666;">
                            <span style="display:inline-block;width:20px;height:20px;background:#ccc;color:white;border-radius:50%;text-align:center;line-height:20px;font-size:11px;margin-right:8px;">4</span>Resolucion
                        </a>
                    </li>
                </ul>
                
                <!-- TAB CONTENT -->
                <div class="tab-content" style="padding:25px;">
                    <div class="tab-pane fade show active" id="t1"></div>
                    <div class="tab-pane fade" id="t2"></div>
                    <div class="tab-pane fade" id="t3"></div>
                    <div class="tab-pane fade" id="t4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th{font-size:11px;font-weight:600;color:#666;text-transform:uppercase;padding:10px 12px;}
.table td{padding:10px 12px;font-size:13px;vertical-align:middle;}
.table tr:hover{background:#fff8f3;}
.bdg-prod{background:#d1fae5;color:#065f46;padding:3px 10px;border-radius:12px;font-size:11px;}
.bdg-hab{background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:12px;font-size:11px;}
.bdg-on{background:#d1fae5;color:#065f46;padding:3px 10px;border-radius:12px;font-size:11px;}
.bdg-off{background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:12px;font-size:11px;}
.dropdown-menu{border:none;box-shadow:0 3px 12px rgba(0,0,0,.15);border-radius:6px;}
.dropdown-item{font-size:13px;padding:8px 15px;}
.dropdown-item:hover{background:#fff7ed;}
#tabsNav .nav-link.active{background:white;border-bottom:2px solid #f97316;color:#333;font-weight:600;}
#tabsNav .nav-link.active span{background:#f97316!important;}
.frm-label{font-size:12px;color:#666;margin-bottom:4px;display:block;}
.frm-input{width:100%;border:1px solid #ddd;border-radius:4px;padding:8px 12px;font-size:14px;}
.frm-input:focus{border-color:#f97316;outline:none;box-shadow:0 0 0 2px rgba(249,115,22,.1);}
.frm-select{width:100%;border:1px solid #ddd;border-radius:4px;padding:8px 12px;font-size:14px;background:white;}
.btn-save{background:#f97316;color:white;border:none;padding:8px 20px;border-radius:4px;font-size:13px;font-weight:600;cursor:pointer;}
.btn-save:hover{background:#ea580c;}
.section-title{font-size:14px;font-weight:600;color:#333;margin-bottom:20px;padding-bottom:10px;border-bottom:1px solid #eee;}
.cert-ok{background:#d1fae5;border-radius:6px;padding:12px 15px;margin-bottom:15px;display:flex;align-items:center;}
.cert-ok i{color:#059669;margin-right:10px;}
.cert-no{background:#fef3c7;border-radius:6px;padding:12px 15px;margin-bottom:15px;}
.res-tbl{width:100%;border-collapse:collapse;}
.res-tbl th{background:#f9fafb;padding:10px;font-size:11px;font-weight:600;color:#666;text-align:left;border-bottom:1px solid #eee;}
.res-tbl td{padding:10px;font-size:13px;border-bottom:1px solid #f3f4f6;}
.btn-del{background:#fee2e2;color:#dc2626;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;}
.btn-del:hover{background:#fecaca;}
.new-res{background:#f9fafb;border-radius:6px;padding:15px;margin-bottom:15px;display:none;}
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
    if(!data.length){h='<tr><td colspan="9" class="text-center py-4 text-muted">No hay empresas</td></tr>';}
    else{
        data.forEach((c,i)=>{
            h+='<tr><td>'+(i+1)+'</td><td><b>'+c.identification_number+'</b>-'+(c.dv||0)+'</td><td>'+c.name+'</td>';
            h+='<td><small class="text-muted">'+c.email+'</small></td>';
            h+='<td><span class="bdg-'+(c.type_environment_id==1?'prod':'hab')+'">'+(c.type_environment_id==1?'Producción':'Habilitación')+'</span></td>';
            h+='<td><span class="bdg-'+(c.state?'on':'off')+'">'+(c.state?'Activa':'Inactiva')+'</span></td>';
            h+='<td><span class="badge badge-secondary">'+c.documents_count+'</span></td>';
            h+='<td><small>'+c.created_at+'</small></td>';
            h+='<td class="text-center"><div class="dropdown"><button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">Acciones</button>';
            h+='<div class="dropdown-menu dropdown-menu-right">';
            h+='<a class="dropdown-item" href="#" onclick="edit('+c.id+')"><i class="fa fa-edit text-primary mr-2"></i>Editar</a>';
            h+='<a class="dropdown-item" href="#" onclick="chgEnv('+c.id+','+c.type_environment_id+')"><i class="fa fa-exchange-alt text-info mr-2"></i>Cambiar Ambiente</a>';
            h+='<a class="dropdown-item" href="#" onclick="chgState('+c.id+','+(c.state?1:0)+')"><i class="fa fa-'+(c.state?'ban text-warning':'check text-success')+' mr-2"></i>'+(c.state?'Deshabilitar':'Habilitar')+'</a>';
            h+='<div class="dropdown-divider"></div>';
            h+='<a class="dropdown-item text-danger" href="#" onclick="del('+c.id+',\''+c.identification_number+'\')"><i class="fa fa-trash mr-2"></i>Eliminar</a>';
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
    var h='<div class="section-title">Datos Generales</div>';
    h+='<div class="row mb-3"><div class="col-md-4"><label class="frm-label">Identificacion</label><input class="frm-input" id="f1" value="'+c.identification_number+'"></div>';
    h+='<div class="col-md-4"><label class="frm-label">Dv</label><input class="frm-input" id="f2" value="'+(c.dv||'')+'"></div>';
    h+='<div class="col-md-4"><label class="frm-label">Empresa</label><input class="frm-input" id="f3" value="'+c.name+'"></div></div>';
    h+='<div class="row mb-3"><div class="col-md-4"><label class="frm-label">Registro Mercantil</label><input class="frm-input" id="f4" value="'+(c.merchant_registration||'')+'"></div>';
    h+='<div class="col-md-4"><label class="frm-label">Direccion</label><input class="frm-input" id="f5" value="'+(c.address||'')+'"></div>';
    h+='<div class="col-md-4"><label class="frm-label">Telefono</label><input class="frm-input" id="f6" value="'+(c.phone||'')+'"></div></div>';
    h+='<div class="row mb-3"><div class="col-md-4"><label class="frm-label">Correo Electronico</label><input class="frm-input" id="f7" value="'+c.email+'"></div>';
    h+='<div class="col-md-4"><label class="frm-label">Tipo Documentacion</label><select class="frm-select" id="f8">'+opts(tbl.type_document_identifications,c.type_document_identification_id)+'</select></div>';
    h+='<div class="col-md-4"><label class="frm-label">Departamento</label><select class="frm-select" id="f9" onchange="chgDept()"><option value="">Seleccionar</option>'+opts(tbl.departments,c.department_id)+'</select></div></div>';
    h+='<div class="row mb-3"><div class="col-md-4"><label class="frm-label">Municipio</label><select class="frm-select" id="f10">'+optsMun(c.municipality_id,c.department_id)+'</select></div>';
    h+='<div class="col-md-4"><label class="frm-label">Organizacion</label><select class="frm-select" id="f11">'+opts(tbl.type_organizations,c.type_organization_id)+'</select></div>';
    h+='<div class="col-md-4"><label class="frm-label">Regimen</label><select class="frm-select" id="f12">'+opts(tbl.type_regimes,c.type_regime_id)+'</select></div></div>';
    h+='<button class="btn-save" onclick="saveT1()"><i class="fa fa-save mr-1"></i> Guardar Empresa</button>';
    document.getElementById('t1').innerHTML=h;
}
function fillT2(){
    var s=cdata.software||{};
    var h='<div class="section-title">Configuración del Software DIAN</div>';
    h+='<div class="row mb-3"><div class="col-md-6"><label class="frm-label">Identificador del Software</label><input class="frm-input" id="s1" value="'+(s.identifier||'')+'" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"></div>';
    h+='<div class="col-md-6"><label class="frm-label">PIN del Software</label><input class="frm-input" id="s2" value="'+(s.pin||'')+'" placeholder="12345"></div></div>';
    h+='<div class="row mb-3"><div class="col-md-12"><label class="frm-label">URL del Servicio (opcional)</label><input class="frm-input" id="s3" value="'+(s.url||'')+'" placeholder="https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc"></div></div>';
    h+='<button class="btn-save" onclick="saveT2()"><i class="fa fa-save mr-1"></i> Guardar Software</button>';
    document.getElementById('t2').innerHTML=h;
}
function fillT3(){
    var c=cdata.certificate||{};
    var h='<div class="section-title">Certificado Digital</div>';
    if(c.name){h+='<div class="cert-ok"><i class="fa fa-check-circle"></i><span><b>Certificado actual:</b> '+c.name+(c.expiration?' - Vence: '+c.expiration:'')+'</span></div>';}
    else{h+='<div class="cert-no"><i class="fa fa-exclamation-triangle mr-2" style="color:#f59e0b;"></i>No hay certificado cargado.</div>';}
    h+='<div class="row mb-3"><div class="col-md-6"><label class="frm-label">Archivo del Certificado (.p12 / .pfx)</label><input type="file" class="frm-input" id="c1" accept=".p12,.pfx" style="padding:6px;"></div>';
    h+='<div class="col-md-6"><label class="frm-label">Contraseña del Certificado</label><input type="password" class="frm-input" id="c2" placeholder="••••••••"></div></div>';
    h+='<button class="btn-save" onclick="saveT3()"><i class="fa fa-upload mr-1"></i> Subir Certificado</button>';
    document.getElementById('t3').innerHTML=h;
}
function fillT4(){
    var r=cdata.resolutions||[];
    var tp=[{id:1,n:'Factura'},{id:4,n:'Nota Crédito'},{id:5,n:'Nota Débito'},{id:11,n:'Doc. Soporte'},{id:12,n:'Nota Ajuste'}];
    var h='<div class="section-title" style="display:flex;justify-content:space-between;align-items:center;border:none;padding:0;margin-bottom:15px;">Resoluciones Configuradas<button class="btn-save" style="padding:6px 12px;" onclick="toggleNR()"><i class="fa fa-plus mr-1"></i> Nueva Resolución</button></div>';
    h+='<div class="new-res" id="nrBox"><div class="row"><div class="col-md-2"><label class="frm-label">Tipo</label><select class="frm-select" id="nr1">'+tp.map(t=>'<option value="'+t.id+'">'+t.n+'</option>').join('')+'</select></div>';
    h+='<div class="col-md-2"><label class="frm-label">Prefijo</label><input class="frm-input" id="nr2"></div>';
    h+='<div class="col-md-2"><label class="frm-label">Resolución</label><input class="frm-input" id="nr3"></div>';
    h+='<div class="col-md-2"><label class="frm-label">Desde</label><input type="number" class="frm-input" id="nr4"></div>';
    h+='<div class="col-md-2"><label class="frm-label">Hasta</label><input type="number" class="frm-input" id="nr5"></div>';
    h+='<div class="col-md-2"><label class="frm-label">&nbsp;</label><button class="btn-save" style="width:100%;" onclick="addRes()"><i class="fa fa-check"></i></button></div></div></div>';
    h+='<table class="res-tbl"><thead><tr><th>Tipo</th><th>Prefijo</th><th>Resolución</th><th>Rango</th><th>Consecutivo</th><th>Vigencia</th><th></th></tr></thead><tbody>';
    if(r.length){r.forEach(x=>{var tn=tp.find(t=>t.id==x.type_document_id);h+='<tr><td>'+(tn?tn.n:x.type_document_id)+'</td><td><b>'+x.prefix+'</b></td><td>'+x.resolution+'</td><td>'+x.from+' - '+x.to+'</td><td>'+x.prefix+(x.next_consecutive||x.from)+'</td><td><small>'+(x.date_from||'')+' al '+(x.date_to||'')+'</small></td><td><button class="btn-del" onclick="delRes('+x.id+')"><i class="fa fa-trash"></i></button></td></tr>';});}
    else{h+='<tr><td colspan="7" class="text-center text-muted py-3">No hay resoluciones</td></tr>';}
    h+='</tbody></table>';
    h+='<button class="btn btn-secondary mt-3" data-dismiss="modal">Cerrar</button>';
    document.getElementById('t4').innerHTML=h;
}
function toggleNR(){var b=document.getElementById('nrBox');b.style.display=b.style.display=='none'?'block':'none';}
function opts(arr,sel){if(!arr)return'';return arr.map(a=>'<option value="'+a.id+'"'+(a.id==sel?' selected':'')+'>'+a.name+'</option>').join('');}
function optsMun(sel,dept){if(!tbl.municipalities)return'<option>Seleccionar</option>';var h='<option value="">Seleccionar</option>';tbl.municipalities.forEach(m=>{if(m.department_id==dept)h+='<option value="'+m.id+'"'+(m.id==sel?' selected':'')+'>'+m.name+'</option>';});return h;}
function chgDept(){document.getElementById('f10').innerHTML=optsMun(null,document.getElementById('f9').value);}
function csrf(){return document.querySelector('meta[name="csrf-token"]').content;}
function saveT1(){
    fetch('/companies/'+cid,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({identification_number:document.getElementById('f1').value,dv:document.getElementById('f2').value,name:document.getElementById('f3').value,merchant_registration:document.getElementById('f4').value,address:document.getElementById('f5').value,phone:document.getElementById('f6').value,email:document.getElementById('f7').value,type_document_identification_id:document.getElementById('f8').value,municipality_id:document.getElementById('f10').value,type_organization_id:document.getElementById('f11').value,type_regime_id:document.getElementById('f12').value})})
    .then(r=>r.json()).then(x=>{if(x.success){Swal.fire('Guardado','','success');loadData();}else Swal.fire('Error',x.message,'error');});
}
function saveT2(){
    fetch('/companies/'+cid+'/software',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({identifier:document.getElementById('s1').value,pin:document.getElementById('s2').value,url:document.getElementById('s3').value})})
    .then(r=>r.json()).then(x=>{if(x.success)Swal.fire('Guardado','','success');else Swal.fire('Error',x.message,'error');});
}
function saveT3(){
    var f=document.getElementById('c1').files[0];if(!f){if(cdata.certificate)return;Swal.fire('Error','Seleccione archivo','error');return;}
    var fd=new FormData();fd.append('certificate',f);fd.append('password',document.getElementById('c2').value);
    Swal.fire({title:'Subiendo...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    fetch('/companies/'+cid+'/certificate',{method:'POST',headers:{'X-CSRF-TOKEN':csrf()},body:fd})
    .then(r=>r.json()).then(x=>{if(x.success){Swal.fire('Cargado','','success');fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT3();});}else Swal.fire('Error',x.message,'error');});
}
function addRes(){
    fetch('/companies/'+cid+'/resolution',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({type_document_id:document.getElementById('nr1').value,prefix:document.getElementById('nr2').value,resolution:document.getElementById('nr3').value,from:document.getElementById('nr4').value,to:document.getElementById('nr5').value,resolution_date:new Date().toISOString().split('T')[0],date_from:new Date().toISOString().split('T')[0],date_to:'2030-12-31'})})
    .then(r=>r.json()).then(x=>{if(x.success){Swal.fire('Creada','','success');fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT4();});}else Swal.fire('Error',x.message,'error');});
}
function delRes(id){
    Swal.fire({title:'¿Eliminar?',icon:'warning',showCancelButton:true,confirmButtonColor:'#f97316'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/resolution/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf()}}).then(r=>r.json()).then(()=>{fetch('/companies/'+cid+'/data').then(r=>r.json()).then(d=>{cdata=d;fillT4();});});
    });
}
function chgEnv(id,c){
    Swal.fire({title:'¿Cambiar a '+(c==1?'Habilitación':'Producción')+'?',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/'+id+'/environment',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({type_environment_id:c==1?2:1})}).then(()=>loadData());
    });
}
function chgState(id,c){
    Swal.fire({title:'¿'+(c?'Deshabilitar':'Habilitar')+'?',icon:'question',showCancelButton:true,confirmButtonColor:'#f97316'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/'+id+'/toggle-state',{method:'POST',headers:{'X-CSRF-TOKEN':csrf()}}).then(()=>loadData());
    });
}
function del(id,nit){
    Swal.fire({title:'¿Eliminar '+nit+'?',text:'Se eliminarán todos los datos',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545'}).then(r=>{
        if(r.isConfirmed)fetch('/companies/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf()}}).then(()=>{Swal.fire('Eliminada','','success');loadData();});
    });
}
</script>
@endsection
