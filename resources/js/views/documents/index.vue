<template>
  <div class="card">
    <div class="card-header">
      <span v-if="companyFilter">Documentos de: {{ companyName }}</span>
      <span v-else>Lista de Documentos</span>
      <a v-if="companyFilter" href="/documents" class="btn btn-sm btn-light float-right">
        <i class="fa fa-arrow-left"></i> Ver Todos
      </a>
    </div>

    <div class="card-body">
      <el-table :data="tableData" style="width: 100%">
        <el-table-column prop="key" label="#" width="60"></el-table-column>
        <el-table-column label="Numero" width="130">
          <template slot-scope="scope">
            {{ scope.row.prefix }}{{ scope.row.number }}
          </template>
        </el-table-column>
        <el-table-column prop="client" label="Cliente" width="160"></el-table-column>
        <el-table-column prop="date" label="Fecha" width="110"></el-table-column>
        <el-table-column prop="total" label="Total" width="100"></el-table-column>
        <el-table-column label="Estado" width="130">
          <template slot-scope="scope">
            <span 
              :class="'badge badge-' + scope.row.state_class"
              style="padding: 5px 10px; border-radius: 4px;"
            >
              {{ scope.row.state_name }}
            </span>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="XML" width="70">
          <template slot-scope="scope">
            <a 
              v-if="scope.row.xml && scope.row.xml !== 'INITIAL_NUMBER.XML'" 
              :href="`/${resource}/downloadxml/${scope.row.xml}`" 
              target="_blank" 
              class="btn btn-xs btn-info"
              title="Descargar XML"
            >
              <i class="fa fa-download"></i>
            </a>
            <span v-else class="text-muted">-</span>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="PDF" width="70">
          <template slot-scope="scope">
            <a 
              v-if="scope.row.pdf && scope.row.pdf !== 'INITIAL_NUMBER.PDF'" 
              :href="`/${resource}/downloadpdf/${scope.row.pdf}`" 
              target="_blank" 
              class="btn btn-xs btn-info"
              title="Descargar PDF"
            >
              <i class="fa fa-download"></i>
            </a>
            <span v-else class="text-muted">-</span>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="CUFE" width="100">
          <template slot-scope="scope">
            <el-tooltip 
              v-if="scope.row.cufe" 
              :content="scope.row.cufe" 
              placement="top"
              effect="dark"
            >
              <span class="cufe-badge" style="cursor: pointer;">
                <i class="fa fa-check-circle text-success"></i> Ver
              </span>
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
    // Obtener parámetro de empresa de la URL
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
    }
  }
};
</script>

<style scoped>
.badge-success {
  background-color: #22c55e;
  color: white;
}
.badge-warning {
  background-color: #f59e0b;
  color: white;
}
.badge-danger {
  background-color: #ef4444;
  color: white;
}
.cufe-badge {
  font-size: 12px;
}
</style>
