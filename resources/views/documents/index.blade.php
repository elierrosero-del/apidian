@extends('layouts.app')

@section('content')
<div class="card" style="margin-top: -20px;">
    <div class="card-header" style="padding: 10px 15px;">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <span style="font-weight: 600;">Lista de Documentos</span>
            <button class="btn-filter-toggle" onclick="toggleFilters()">
                <i class="fa fa-filter"></i> Filtros
            </button>
        </div>
        
        <!-- Filtros -->
        <div id="filters-panel" class="filters-panel" style="display: none;">
            <div class="filters-row">
                <div class="filter-group">
                    <label>Tipo Documento</label>
                    <select id="filter-type" onchange="applyFilters()">
                        <option value="">Todos</option>
                        <option value="1">Factura</option>
                        <option value="2">Factura Exportación</option>
                        <option value="3">Contingencia</option>
                        <option value="4">Nota Crédito</option>
                        <option value="5">Nota Débito</option>
                        <option value="6">Nómina</option>
                        <option value="7">Nómina Ajuste</option>
                        <option value="11">Doc. Soporte</option>
                        <option value="12">Nota Ajuste DS</option>
                        <option value="13">NC Doc. Soporte</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Cliente</label>
                    <input type="text" id="filter-client" placeholder="Buscar cliente..." onkeyup="debounceFilter()">
                </div>
                <div class="filter-group">
                    <label>Número</label>
                    <input type="text" id="filter-number" placeholder="Número documento..." onkeyup="debounceFilter()">
                </div>
                <div class="filter-group">
                    <button class="btn-clear" onclick="clearFilters()"><i class="fa fa-times"></i> Limpiar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" style="padding: 10px;">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm" id="documents-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tipo</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Archivos</th>
                        <th>DIAN</th>
                    </tr>
                </thead>
                <tbody id="documents-body">
                    <tr><td colspan="9" class="text-center">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div id="pagination-controls" class="pagination-box" style="display:none;">
            <button class="page-btn" id="btn-prev" onclick="changePage(-1)">
                <i class="fa fa-chevron-left"></i> Anterior
            </button>
            <span class="page-info" id="page-info">Página 1 de 1</span>
            <button class="page-btn" id="btn-next" onclick="changePage(1)">
                Siguiente <i class="fa fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<style>
.card { margin-bottom: 0; }
.table-sm td, .table-sm th { padding: 8px 10px; font-size: 13px; }

/* Filtros */
.btn-filter-toggle {
    background: #1e293b;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
}
.btn-filter-toggle:hover { background: #334155; }
.filters-panel {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e2e8f0;
}
.filters-row {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: flex-end;
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.filter-group label {
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
}
.filter-group select, .filter-group input {
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 13px;
    min-width: 150px;
}
.filter-group select:focus, .filter-group input:focus {
    outline: none;
    border-color: #f97316;
}
.btn-clear {
    background: #ef4444;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
}
.btn-clear:hover { background: #dc2626; }

/* Paginación */
.pagination-box {
    margin-top: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
}
.page-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #f97316;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
}
.page-btn:hover:not(:disabled) { background: #ea580c; }
.page-btn:disabled { background: #cbd5e1; cursor: not-allowed; }
.page-info { color: #64748b; font-size: 13px; }

/* Estados y botones */
.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}
.status-success { background: #dcfce7; color: #166534; }
.status-warning { background: #fef3c7; color: #92400e; }
.status-info { background: #dbeafe; color: #1e40af; }
.file-btn {
    display: inline-block;
    padding: 3px 6px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
    text-decoration: none;
    margin: 1px;
}
.file-btn.xml { background: #dbeafe; color: #1e40af; }
.file-btn.pdf { background: #fee2e2; color: #991b1b; }
.file-btn:hover { opacity: 0.8; text-decoration: none; }
.btn-dian {
    background: #f97316;
    color: white;
    border: none;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
}
.btn-dian:hover { background: #ea580c; color: white; text-decoration: none; }
.btn-resend {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 10px;
    cursor: pointer;
    margin-left: 5px;
}
.btn-resend:hover { background: #2563eb; }
.env-badge {
    font-size: 9px;
    padding: 2px 4px;
    border-radius: 3px;
    margin-left: 4px;
}
.env-hab { background: #fef3c7; color: #92400e; }
.env-prod { background: #dcfce7; color: #166534; }
</style>

<script>
let currentPage = 1;
let totalPages = 1;
let totalRecords = 0;
const perPage = 15;
let filterTimeout = null;

const DIAN_URLS = {
    1: 'https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey=',
    2: 'https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey='
};

document.addEventListener('DOMContentLoaded', function() {
    loadDocuments();
});

function toggleFilters() {
    const panel = document.getElementById('filters-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function debounceFilter() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(applyFilters, 500);
}

function applyFilters() {
    currentPage = 1;
    loadDocuments();
}

function clearFilters() {
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-client').value = '';
    document.getElementById('filter-number').value = '';
    currentPage = 1;
    loadDocuments();
}

function loadDocuments() {
    let url = '/documents/records?page=' + currentPage + '&per_page=' + perPage;
    
    // Filtro de empresa desde URL
    const urlParams = new URLSearchParams(window.location.search);
    const company = urlParams.get('company');
    if (company) url += '&company=' + company;
    
    // Filtros del panel
    const filterType = document.getElementById('filter-type').value;
    const filterClient = document.getElementById('filter-client').value;
    const filterNumber = document.getElementById('filter-number').value;
    
    if (filterType) url += '&type=' + filterType;
    if (filterClient) url += '&client=' + encodeURIComponent(filterClient);
    if (filterNumber) url += '&number=' + encodeURIComponent(filterNumber);
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            renderTable(data.data);
            totalRecords = data.total || 0;
            totalPages = Math.ceil(totalRecords / perPage);
            updatePagination();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('documents-body').innerHTML = 
                '<tr><td colspan="9" class="text-center text-danger">Error al cargar</td></tr>';
        });
}

function renderTable(documents) {
    const tbody = document.getElementById('documents-body');
    
    if (!documents || documents.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No hay documentos</td></tr>';
        return;
    }
    
    let html = '';
    documents.forEach((doc, index) => {
        const rowNum = (currentPage - 1) * perPage + index + 1;
        
        let statusHtml = '<span class="status-badge status-' + doc.state_class + '">' + doc.state_name + '</span>';
        if (doc.can_resend) {
            statusHtml += '<button class="btn-resend" onclick="resendDocument(' + doc.id + ')" title="Reenviar"><i class="fa fa-redo"></i></button>';
        }
        
        let filesHtml = '';
        if (doc.xml && doc.xml !== 'INITIAL_NUMBER.XML') {
            filesHtml += '<a href="/documents/downloadxml/' + doc.xml + '" class="file-btn xml">XML</a>';
        }
        if (doc.pdf && doc.pdf !== 'INITIAL_NUMBER.PDF') {
            filesHtml += '<a href="/documents/downloadpdf/' + doc.pdf + '" class="file-btn pdf">PDF</a>';
        }
        if (!filesHtml) filesHtml = '-';
        
        let dianHtml = '-';
        if (doc.cufe && doc.cufe.length > 10) {
            const dianUrl = DIAN_URLS[doc.environment] || DIAN_URLS[2];
            const envText = doc.environment == 1 ? 'PROD' : 'HAB';
            const envClass = doc.environment == 1 ? 'env-prod' : 'env-hab';
            dianHtml = '<a href="' + dianUrl + doc.cufe + '" target="_blank" class="btn-dian">Ver</a> <span class="env-badge ' + envClass + '">' + envText + '</span>';
        }
        
        let total = doc.total ? '$' + Number(doc.total).toLocaleString('es-CO') : '$0';
        let fecha = doc.date ? doc.date.split(' ')[0] : '-';
        
        html += '<tr>';
        html += '<td>' + rowNum + '</td>';
        html += '<td>' + (doc.type_document_name || 'Doc') + '</td>';
        html += '<td><strong>' + (doc.prefix || '') + (doc.number || '') + '</strong></td>';
        html += '<td>' + (doc.client || 'N/A') + '</td>';
        html += '<td>' + fecha + '</td>';
        html += '<td class="text-right">' + total + '</td>';
        html += '<td>' + statusHtml + '</td>';
        html += '<td class="text-center">' + filesHtml + '</td>';
        html += '<td class="text-center" style="white-space:nowrap;">' + dianHtml + '</td>';
        html += '</tr>';
    });
    
    tbody.innerHTML = html;
}

function updatePagination() {
    const controls = document.getElementById('pagination-controls');
    const pageInfo = document.getElementById('page-info');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    
    if (totalRecords > perPage) {
        controls.style.display = 'flex';
        pageInfo.textContent = 'Página ' + currentPage + ' de ' + totalPages + ' (' + totalRecords + ' docs)';
        btnPrev.disabled = currentPage <= 1;
        btnNext.disabled = currentPage >= totalPages;
    } else {
        controls.style.display = totalRecords > 0 ? 'flex' : 'none';
        if (totalRecords > 0) {
            pageInfo.textContent = totalRecords + ' documento(s)';
            btnPrev.style.display = 'none';
            btnNext.style.display = 'none';
        }
    }
}

function changePage(direction) {
    const newPage = currentPage + direction;
    if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        loadDocuments();
        window.scrollTo(0, 0);
    }
}

function resendDocument(docId) {
    if (!confirm('¿Reenviar documento a la DIAN?')) return;
    alert('Función de reenvío pendiente de implementar.');
}
</script>
@endsection
