<template>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span v-if="companyFilter">Documentos de: {{ companyName }}</span>
      <span v-else>Lista de Documentos</span>
      <a v-if="companyFilter" href="/documents" class="btn btn-sm btn-outline-secondary">
        <i class="fa fa-arrow-left"></i> Ver Todos
      </a>
    </div>

    <div class="card-body">
      <el-table :data="tableData" style="width: 100%" stripe>
        <el-table-column prop="key" label="#" width="50"></el-table-column>
        <el-table-column label="Tipo" width="120">
          <template slot-scope="scope">
            <span class="doc-type">
              <i :class="'fa fa-' + scope.row.type_document_icon"></i>
              {{ scope.row.type_document_name }}
            </span>
          </template>
        </el-table-column>
        <el-table-column label="Número" width="140">
          <template slot-scope="scope">
            <strong>{{ scope.row.prefix }}{{ scope.row.number }}</strong>
          </template>
        </el-table-column>
        <el-table-column prop="client" label="Cliente" min-width="150"></el-table-column>
        <el-table-column label="Fecha" width="100">
          <template slot-scope="scope">
            {{ formatDate(scope.row.date) }}
          </template>
        </el-table-column>
        <el-table-column label="Total" width="100" align="right">
          <template slot-scope="scope">
            ${{ formatNumber(scope.row.total) }}
          </template>
        </el-table-column>
        <el-table-column label="Estado" width="110" align="center">
          <template slot-scope="scope">
            <span :class="'status-badge status-' + scope.row.state_class">
              {{ scope.row.state_name }}
            </span>
          </template>
        </el-table-column>
        <el-table-column label="Archivos" width="100" align="center">
          <template slot-scope="scope">
            <div class="file-buttons">
              <a 
                v-if="scope.row.xml && scope.row.xml !== 'INITIAL_NUMBER.XML'" 
                :href="`/${resource}/downloadxml/${scope.row.xml}`" 
                class="file-btn xml"
                title="Descargar XML"
              >XML</a>
              <a 
                v-if="scope.row.pdf && scope.row.pdf !== 'INITIAL_NUMBER.PDF'" 
                :href="`/${resource}/downloadpdf/${scope.row.pdf}`" 
                class="file-btn pdf"
                title="Descargar PDF"
              >PDF</a>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="CUFE" width="70" align="center">
          <template slot-scope="scope">
            <el-tooltip 
              v-if="scope.row.cufe" 
              :content="scope.row.cufe" 
              placement="left"
            >
              <i class="fa fa-check-circle cufe-ok"></i>
            </el-tooltip>
            <span v-else class="text-muted">-</span>
          </template>
        </el-table-column>
      </el-table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      resource: "documents",
      tableData: [],
      companyFilter: null,
      companyName: ""
    };
  },
  created() {
    const urlParams = new URLSearchParams(window.location.search);
    this.companyFilter = urlParams.get('company');
    this.companyName = urlParams.get('name') || this.companyFilter;
    this.getRecords();
  },
  methods: {
    getRecords() {
      let url = `/${this.resource}/records`;
      if (this.companyFilter) {
        url += `?company=${this.companyFilter}`;
      }
      
      this.$http
        .get(url)
        .then(response => {
          this.tableData = response.data.data;
        })
        .catch(error => {
          console.error("Error:", error);
        });
    },
    formatDate(date) {
      if (!date) return '-';
      return date.split(' ')[0];
    },
    formatNumber(num) {
      if (!num) return '0';
      return Number(num).toLocaleString('es-CO');
    }
  }
};
</script>

<style scoped>
.doc-type {
  font-size: 12px;
  color: #64748b;
}
.doc-type i {
  margin-right: 5px;
  color: #94a3b8;
}

.status-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
}
.status-success {
  background: #dcfce7;
  color: #166534;
}
.status-warning {
  background: #fef3c7;
  color: #92400e;
}
.status-danger {
  background: #fee2e2;
  color: #991b1b;
}

.file-buttons {
  display: flex;
  gap: 4px;
  justify-content: center;
}
.file-btn {
  padding: 3px 6px;
  border-radius: 3px;
  font-size: 10px;
  font-weight: 600;
  text-decoration: none;
}
.file-btn.xml {
  background: #dbeafe;
  color: #1e40af;
}
.file-btn.pdf {
  background: #fee2e2;
  color: #991b1b;
}
.file-btn:hover {
  opacity: 0.8;
}

.cufe-ok {
  color: #22c55e;
  font-size: 16px;
}
</style>
