@extends('layouts.app')

@section('content')
<div class="doc-container">
    <!-- Barra de filtros -->
    <div class="filter-bar">
        <select id="filter-type" onchange="applyFilters()">
            <option value="">Todos los tipos</option>
            <option value="1">Facturas</option>
            <option value="4">Notas Crédito</option>
            <option value="5">Notas Débito</option>
            <option value="11">Doc. Soporte</option>
        </select>
        <div class="search-wrapper">
            <i class="fa fa-search"></i>
            <input type="text" id="filter-search" placeholder="Buscar por número o cliente..." onkeyup="debounceFilter()">
        </div>
        <span class="doc-count" id="doc-count">0 documentos</span>
    </div>

    <!-- Tabla -->
    <table class="doc-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tipo</th>
                <th>Número</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th class="text-right">Total</th>
                <th>Estado</th>
                <th class="text-center">Archivos</th>
                <th class="text-center">DIAN</th>
            </tr>
        </thead>
        <tbody id="documents-body">
            <tr><td colspan="9" class="loading">Cargando documentos...</td></tr>
        </tbody>
    </table>

    <!-- Paginación -->
    <div class="pagination" id="pagination" style="display:none;">
        <button id="btn-prev" onclick="changePage(-1)"><i class="fa fa-chevron-left"></i></button>
        <span id="page-num">1</span>
        <button id="btn-next" onclick="changePage(1)"><i class="fa fa-chevron-right"></i></button>
    </div>
</div>

<style>
.doc-container { margin: -15px -15px 0; }

/* Barra de filtros */
.filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
}
.filter-bar select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    color: #374151;
    background: #fff;
    cursor: pointer;
}
.filter-bar select:focus { outline: none; border-color: #f97316; }
.search-wrapper {
    flex: 1;
    max-width: 300px;
    position: relative;
}
.search-wrapper i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 13px;
}
.search-wrapper input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
}
.search-wrapper input:focus { outline: none; border-color: #f97316; }
.search-wrapper input::placeholder { color: #9ca3af; }
.doc-count {
    margin-left: auto;
    font-size: 13px;
    color: #6b7280;
}

/* Tabla */
.doc-table {
    width: 100%;
    border-collapse: collapse;
}
.doc-table th {
    padding: 10px 12px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}
.doc-table td {
    padding: 12px;
    font-size: 13px;
    color: #374151;
    border-bottom: 1px solid #f3f4f6;
}
.doc-table tbody tr:hover { background: #f9fafb; }
.doc-table .loading { text-align: center; color: #9ca3af; padding: 40px; }
.doc-table .empty { text-align: center; color: #9ca3af; padding: 40px; }

/* Número documento */
.doc-num { font-weight: 600; color: #111827; }

/* Estado */
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}
.badge-success { background: #d1fae5; color: #065f46; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-info { background: #dbeafe; color: #1e40af; }

/* Archivos */
.file-link {
    display: inline-block;
    padding: 4px 8px;
    margin: 1px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    text-decoration: none;
}
.file-xml { background: #eff6ff; color: #1d4ed8; }
.file-pdf { background: #fef2f2; color: #dc2626; }
.file-link:hover { opacity: 0.8; }

/* DIAN */
.btn-ver {
    display: inline-block;
    padding: 5px 12px;
    background: #f97316;
    color: #fff;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
}
.btn-ver:hover { background: #ea580c; color: #fff; }
.env-tag {
    display: inline-block;
    margin-left: 4px;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 600;
}
.env-hab { background: #fef3c7; color: #92400e; }
.env-prod { background: #d1fae5; color: #065f46; }

/* Paginación */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 16px;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}
.pagination button {
    width: 32px;
    height: 32px;
    border: 1px solid #d1d5db;
    background: #fff;
    border-radius: 6px;
    cursor: pointer;
    color: #374151;
}
.pagination button:hover:not(:disabled) { background: #f97316; color: #fff; border-color: #f97316; }
.pagination button:disabled { opacity: 0.4; cursor: not-allowed; }
.pagination span {
    padding: 6px 14px;
    background: #f97316;
    color: #fff;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
}

/* Reenvío */
.btn-resend {
    margin-left: 4px;
    padding: 2px 6px;
    background: #3b82f6;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 10px;
    cursor: pointer;
}
.btn-resend:hover { background: #2563eb; }
</style>

<script>
let page = 1, pages = 1, total = 0, timeout = null;
const DIAN = { 1: 'https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey=', 2: 'https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=' };

document.addEventListener('DOMContentLoaded', load);

function debounceFilter() { clearTimeout(timeout); timeout = setTimeout(() => { page = 1; load(); }, 400); }
function applyFilters() { page = 1; load(); }

function load() {
    let url = '/documents/records?page=' + page + '&per_page=15';
    const company = new URLSearchParams(location.search).get('company');
    if (company) url += '&company=' + company;
    
    const type = document.getElementById('filter-type').value;
    const search = document.getElementById('filter-search').value;
    if (type) url += '&type=' + type;
    if (search) url += '&search=' + encodeURIComponent(search);
    
    fetch(url).then(r => r.json()).then(d => {
        total = d.total || 0;
        pages = Math.ceil(total / 15) || 1;
        document.getElementById('doc-count').textContent = total + ' documento' + (total !== 1 ? 's' : '');
        render(d.data);
        updatePag();
    }).catch(() => {
        document.getElementById('documents-body').innerHTML = '<tr><td colspan="9" class="empty">Error al cargar</td></tr>';
    });
}

function render(docs) {
    const tbody = document.getElementById('documents-body');
    if (!docs || !docs.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="empty">No se encontraron documentos</td></tr>';
        return;
    }
    tbody.innerHTML = docs.map((d, i) => {
        const n = (page - 1) * 15 + i + 1;
        let st = `<span class="badge badge-${d.state_class}">${d.state_name}</span>`;
        if (d.can_resend) st += `<button class="btn-resend" onclick="resend(${d.id})"><i class="fa fa-redo"></i></button>`;
        
        let files = '';
        if (d.xml && d.xml !== 'INITIAL_NUMBER.XML') files += `<a href="/documents/downloadxml/${d.xml}" class="file-link file-xml">XML</a>`;
        if (d.pdf && d.pdf !== 'INITIAL_NUMBER.PDF') files += `<a href="/documents/downloadpdf/${d.pdf}" class="file-link file-pdf">PDF</a>`;
        
        let dian = '<span style="color:#9ca3af">-</span>';
        if (d.cufe && d.cufe.length > 10) {
            const env = d.environment == 1 ? ['PROD', 'env-prod'] : ['HAB', 'env-hab'];
            dian = `<a href="${DIAN[d.environment] || DIAN[2]}${d.cufe}" target="_blank" class="btn-ver">Ver</a><span class="env-tag ${env[1]}">${env[0]}</span>`;
        }
        
        return `<tr>
            <td>${n}</td>
            <td>${d.type_document_name || '-'}</td>
            <td class="doc-num">${(d.prefix || '') + (d.number || '')}</td>
            <td>${d.client || '-'}</td>
            <td>${d.date ? d.date.split(' ')[0] : '-'}</td>
            <td class="text-right">$${d.total ? Number(d.total).toLocaleString('es-CO') : '0'}</td>
            <td>${st}</td>
            <td class="text-center">${files || '-'}</td>
            <td class="text-center">${dian}</td>
        </tr>`;
    }).join('');
}

function updatePag() {
    const pag = document.getElementById('pagination');
    pag.style.display = total > 15 ? 'flex' : 'none';
    document.getElementById('page-num').textContent = page;
    document.getElementById('btn-prev').disabled = page <= 1;
    document.getElementById('btn-next').disabled = page >= pages;
}

function changePage(d) {
    const np = page + d;
    if (np >= 1 && np <= pages) { page = np; load(); }
}

function resend(id) { if (confirm('¿Reenviar a DIAN?')) alert('Función pendiente.'); }
</script>
@endsection
