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

<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Empresa</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="editModalBody">
                Cargando...
            </div>
        </div>
    </div>
</div>

<!-- Modal Resoluciones -->
<div class="modal fade" id="resolutionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resoluciones</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="resolutionsModalBody">
                Cargando...
            </div>
        </div>
    </div>
</div>

<style>
.table thead th {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    border-bottom: 2px solid #e2e8f0;
    padding: 12px;
}
.table tbody td {
    padding: 10px 12px;
    vertical-align: middle;
    font-size: 13px;
}
.env-badge, .state-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}
.env-prod { background: #dcfce7; color: #166534; }
.env-hab { background: #fef3c7; color: #92400e; }
.state-active { background: #dcfce7; color: #166534; }
.state-inactive { background: #fee2e2; color: #991b1b; }
.btn-action {
    padding: 4px 8px;
    font-size: 11px;
    border-radius: 4px;
    margin: 1px;
}
.dropdown-menu { font-size: 13px; }
.dropdown-item { padding: 8px 15px; }
.dropdown-item i { width: 20px; }
</style>

<script>
let searchTimeout;
let tables = {};

document.addEventListener('DOMContentLoaded', function() {
    loadCompanies();
    loadTables();
});

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(loadCompanies, 300);
}

function loadTables() {
    fetch('/companies/tables')
        .then(r => r.json())
        .then(data => { tables = data; });
}

function loadCompanies() {
    const search = document.getElementById('search').value;
    fetch('/companies/records?search=' + encodeURIComponent(search))
        .then(r => r.json())
        .then(data => renderTable(data.data))
        .catch(e => {
            document.getElementById('companies-body').innerHTML = 
                '<tr><td colspan="9" class="text-center text-danger">Error al cargar</td></tr>';
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
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                        Acciones
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="editCompany(${c.id})">
                            <i class="fa fa-edit text-primary"></i> Editar
                        </a>
                        <a class="dropdown-item" href="#" onclick="showResolutions(${c.id})">
                            <i class="fa fa-file-alt text-info"></i> Resoluciones
                        </a>
                        <a class="dropdown-item" href="/documents?company=${c.identification_number}">
                            <i class="fa fa-folder text-warning"></i> Documentos
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="changeEnvironment(${c.id}, ${c.type_environment_id})">
                            <i class="fa fa-exchange-alt text-success"></i> Cambiar Ambiente
                        </a>
                        <a class="dropdown-item" href="#" onclick="toggleState(${c.id})">
                            <i class="fa fa-power-off ${c.state ? 'text-danger' : 'text-success'}"></i> 
                            ${c.state ? 'Deshabilitar' : 'Habilitar'}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="#" onclick="deleteCompany(${c.id}, '${c.identification_number}')">
                            <i class="fa fa-trash"></i> Eliminar
                        </a>
                    </div>
                </div>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
}

function editCompany(id) {
    fetch('/companies/' + id + '/edit-form')
        .then(r => r.text())
        .then(html => {
            document.getElementById('editModalBody').innerHTML = html;
            $('#editModal').modal('show');
        });
}

function saveCompany(id) {
    const form = document.getElementById('editForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/companies/' + id, {
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
            $('#editModal').modal('hide');
            loadCompanies();
            alert('✅ ' + res.message);
        } else {
            alert('❌ ' + res.message);
        }
    });
}

function showResolutions(id) {
    fetch('/companies/' + id + '/resolutions-list')
        .then(r => r.text())
        .then(html => {
            document.getElementById('resolutionsModalBody').innerHTML = html;
            $('#resolutionsModal').modal('show');
        });
}

function changeEnvironment(id, currentEnv) {
    const newEnv = currentEnv == 1 ? 2 : 1;
    const envName = newEnv == 1 ? 'Producción' : 'Habilitación';
    
    if (!confirm(`¿Cambiar ambiente a ${envName}?`)) return;
    
    fetch('/companies/' + id + '/environment', {
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
            loadCompanies();
            alert('✅ ' + res.message);
        } else {
            alert('❌ ' + res.message);
        }
    });
}

function toggleState(id) {
    if (!confirm('¿Cambiar estado de la empresa?')) return;
    
    fetch('/companies/' + id + '/toggle-state', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            loadCompanies();
            alert('✅ ' + res.message);
        } else {
            alert('❌ ' + res.message);
        }
    });
}

function deleteCompany(id, nit) {
    if (!confirm(`¿Eliminar empresa ${nit}? Esta acción no se puede deshacer.`)) return;
    if (!confirm('¿Está seguro? Se eliminarán todos los datos asociados.')) return;
    
    fetch('/companies/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            loadCompanies();
            alert('✅ ' + res.message);
        } else {
            alert('❌ ' + res.message);
        }
    });
}
</script>
@endsection
