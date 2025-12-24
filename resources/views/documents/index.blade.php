@extends('layouts.app')

@section('content')
<div class="card" style="margin-top: -20px;">
    <div class="card-header doc-header">
        <div class="header-top">
            <span class="header-title">Lista de Documentos</span>
        </div>
        
        <!-- Filtros en línea -->
        <div class="filters-inline">
            <div class="filter-item">
                <select id="filter-type" onchange="applyFilters()">
                    <option value="">Todos los tipos</option>
                    <option value="1">Factura</option>
                    <option value="4">Nota Crédito</option>
                    <option value="5">Nota Débito</option>
                    <option value="11">Doc. Soporte</option>
                    <option value="6">Nómina</option>
                </select>
            </div>
            <div class="filter-item search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="filter-client" placeholder="Buscar cliente..." onkeyup="debounceFilter()">
            </div>
            <div class="filter-item search-box">
                <i class="fa fa-hashtag"></i>
                <input type="text" id="filter-number" placeholder="Número..." onkeyup="debounceFilter()">
            </div>
            <button class="btn-clear" onclick="clearFilters()" title="Limpiar filtros">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table doc-table" id="documents-table">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th width="90">Tipo</th>
                        <th width="130">Número</th>
                        <th>Cliente</th>
                        <th width="90">Fecha</th>
                        <th width="100" class="text-right">Total</th>
                        <th width="90">Estado</th>
                        <th width="80" class="text-center">Archivos</th>
                        <th width="90" class="text-center">DIAN</th>
                    </tr>
                </thead>
                <tbody id="documents-body">
                    <tr><td colspan="9" class="text-center py-4">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div id="pagination-controls" class="pagination-bar" style="display:none;">
            <span class="page-info" id="page-info"></span>
            <div class="page-buttons">
                <button class="page-btn" id="btn-prev" onclick="changePage(-1)">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <span class="page-current" id="page-current">1</span>
                <button class="page-btn" id="btn-next" onclick="changePage(1)">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Header */
.doc-header {
    background: #1e293b;
    padding: 12px 16px !important;
    border: none;
}
.header-top {
    margin-bottom: 12px;
}
.header-title {
    color: white;
    font-weight: 600;
    font-size: 15px;
}

/* Filtros inline */
.filters-inline {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}
.filter-item select {
    background: #334155;
    border: 1px solid #475569;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 13px;
    min-width: 140px;
    cursor: pointer;
}
.filter-item select:focus {
    outline: none;
    border-color: #f97316;
}
.filter-item select option {
    background: #1e293b;
}
.search-box {
    position: relative;
    display: flex;
    align-items: center;
}
.search-box i {
    position: absolute;
    left: 10px;
    color: #94a3b8;
    font-size: 12px;
}
.search-box input {
    background: #334155;
    border: 1px solid #475569;
    color: white;
    padding: 8px 12px 8px 32px;
    border-radius: 6px;
    font-size: 13px;
    width: 160px;
}
.search-box input::placeholder {
    color: #94a3b8;
}
.search-box input:focus {
    outline: none;
    border-color: #f97316;
}
.btn-clear {
    background: transparent;
    border: 1px solid #475569;
    color: #94a3b8;
    width: 36px;
    height: 36px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-clear:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: white;
}

/* Tabla */
.doc-table {
    margin: 0;
}
.doc-table thead {
    background: #f8fafc;
}
.doc-table thead th {
    padding: 10px 12px;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    border-bottom: 2px solid #e2e8f0;
}
.doc-table tbody td {
    padding: 10px 12px;
    font-size: 13px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.doc-table tbody tr:hover {
    background: #f8fafc;
}

/* Estados */
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.status-success { background: #dcfce7; color: #166534; }
.status-warning { background: #fef3c7; color: #92400e; }
.status-info { background: #dbeafe; color: #1e40af; }

/* Archivos */
.file-btn {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    text-decoration: none;
    margin: 1px;
}
.file-btn.xml { background: #dbeafe; color: #1e40af; }
.file-btn.pdf { background: #fce7f3; color: #be185d; }
.file-btn:hover { opacity: 0.8; text-decoration: none; color: inherit; }

/* DIAN */
.btn-dian {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}
.btn-dian:hover { 
    background: linear-gradient(135deg, #ea580c, #c2410c);
    color: white; 
    text-decoration: none; 
}
.env-badge {
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 3px;
    margin-left: 4px;
    font-weight: 600;
}
.env-hab { background: #fef3c7; color: #92400e; }
.env-prod { background: #dcfce7; color: #166534; }

/* Paginación */
.pagination-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}
.page-info {
    color: #64748b;
    font-size: 13px;
}
.page-buttons {
    display: flex;
    align-items: center;
    gap: 8px;
}
.page-btn {
    background: white;
    border: 1px solid #e2e8f0;
    color: #64748b;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.page-btn:hover:not(:disabled) {
    background: #f97316;
    border-color: #f97316;
    color: white;
}
.page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.page-current {
    background: #f97316;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
}

/* Reenvío */
.btn-resend {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 3px 6px;
    border-radius: 4px;
    font-size: 10px;
    cursor: pointer;
    margin-left: 4px;
}
.btn-resend:hover { background: #2563eb; }

/* Número documento */
.doc-number {
    font-weight: 600;
    color: #1e293b;
}
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

document.addEventListener('DOMContentLoaded', loadDocuments);

function debounceFilter() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(applyFilters, 400);
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
    
    const urlParams = new URLSearchParams(window.location.search);
    const company = urlParams.get('company');
    if (company) url += '&company=' + company;
    
    const type = document.getElementById('filter-type').value;
    const client = document.getElementById('filter-client').value;
    const number = document.getElementById('filter-number').value;
    
    if (type) url += '&type=' + type;
    if (client) url += '&client=' + encodeURIComponent(client);
    if (number) url += '&number=' + encodeURIComponent(number);
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            renderTable(data.data);
            totalRecords = data.total || 0;
            totalPages = Math.ceil(totalRecords / perPage) || 1;
            updatePagination();
        })
        .catch(e => {
            document.getElementById('documents-body').innerHTML = 
                '<tr><td colspan="9" class="text-center text-danger py-4">Error al cargar</td></tr>';
        });
}

function renderTable(docs) {
    const tbody = document.getElementById('documents-body');
    
    if (!docs || !docs.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;">No se encontraron documentos</td></tr>';
        return;
    }
    
    tbody.innerHTML = docs.map((doc, i) => {
        const num = (currentPage - 1) * perPage + i + 1;
        
        let status = `<span class="status-badge status-${doc.state_class}">${doc.state_name}</span>`;
        if (doc.can_resend) status += `<button class="btn-resend" onclick="resendDocument(${doc.id})"><i class="fa fa-redo"></i></button>`;
        
        let files = '';
        if (doc.xml && doc.xml !== 'INITIAL_NUMBER.XML') files += `<a href="/documents/downloadxml/${doc.xml}" class="file-btn xml">XML</a>`;
        if (doc.pdf && doc.pdf !== 'INITIAL_NUMBER.PDF') files += `<a href="/documents/downloadpdf/${doc.pdf}" class="file-btn pdf">PDF</a>`;
        
        let dian = '-';
        if (doc.cufe && doc.cufe.length > 10) {
            const url = DIAN_URLS[doc.environment] || DIAN_URLS[2];
            const env = doc.environment == 1 ? ['PROD','env-prod'] : ['HAB','env-hab'];
            dian = `<a href="${url}${doc.cufe}" target="_blank" class="btn-dian">Ver</a><span class="env-badge ${env[1]}">${env[0]}</span>`;
        }
        
        return `<tr>
            <td>${num}</td>
            <td>${doc.type_document_name || 'Doc'}</td>
            <td class="doc-number">${(doc.prefix||'') + (doc.number||'')}</td>
            <td>${doc.client || 'N/A'}</td>
            <td>${doc.date ? doc.date.split(' ')[0] : '-'}</td>
            <td class="text-right">$${doc.total ? Number(doc.total).toLocaleString('es-CO') : '0'}</td>
            <td>${status}</td>
            <td class="text-center">${files || '-'}</td>
            <td class="text-center" style="white-space:nowrap">${dian}</td>
        </tr>`;
    }).join('');
}

function updatePagination() {
    const bar = document.getElementById('pagination-controls');
    const info = document.getElementById('page-info');
    const curr = document.getElementById('page-current');
    const prev = document.getElementById('btn-prev');
    const next = document.getElementById('btn-next');
    
    bar.style.display = 'flex';
    info.textContent = `${totalRecords} documento${totalRecords !== 1 ? 's' : ''}`;
    curr.textContent = currentPage;
    prev.disabled = currentPage <= 1;
    next.disabled = currentPage >= totalPages;
}

function changePage(dir) {
    const newPage = currentPage + dir;
    if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        loadDocuments();
    }
}

function resendDocument(id) {
    if (confirm('¿Reenviar documento a la DIAN?')) {
        alert('Función pendiente de implementar.');
    }
}
</script>
@endsection
