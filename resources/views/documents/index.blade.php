@extends('layouts.app')

@section('content')
<div class="card" style="margin-top: -20px;">
    <div class="card-header d-flex justify-content-between align-items-center" style="padding: 10px 15px;">
        <span style="font-weight: 600;">Lista de Documentos</span>
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
.status-danger { background: #fee2e2; color: #991b1b; }
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
    cursor: pointer;
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
    display: inline-block;
}
.env-hab { background: #fef3c7; color: #92400e; }
.env-prod { background: #dcfce7; color: #166534; }
</style>

<script>
let currentPage = 1;
let totalPages = 1;
let totalRecords = 0;
const perPage = 15;

const DIAN_URLS = {
    1: 'https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey=',
    2: 'https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey='
};

document.addEventListener('DOMContentLoaded', function() {
    loadDocuments();
});

function loadDocuments() {
    let url = '/documents/records?page=' + currentPage + '&per_page=' + perPage;
    const urlParams = new URLSearchParams(window.location.search);
    const company = urlParams.get('company');
    if (company) url += '&company=' + company;
    
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
        
        // Estado con botón reenvío si aplica
        let statusHtml = '<span class="status-badge status-' + doc.state_class + '">' + doc.state_name + '</span>';
        if (doc.can_resend) {
            statusHtml += '<button class="btn-resend" onclick="resendDocument(' + doc.id + ')" title="Reenviar"><i class="fa fa-redo"></i></button>';
        }
        
        // Archivos
        let filesHtml = '';
        if (doc.xml && doc.xml !== 'INITIAL_NUMBER.XML') {
            filesHtml += '<a href="/documents/downloadxml/' + doc.xml + '" class="file-btn xml">XML</a>';
        }
        if (doc.pdf && doc.pdf !== 'INITIAL_NUMBER.PDF') {
            filesHtml += '<a href="/documents/downloadpdf/' + doc.pdf + '" class="file-btn pdf">PDF</a>';
        }
        if (!filesHtml) filesHtml = '-';
        
        // DIAN
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
        controls.style.display = 'none';
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
