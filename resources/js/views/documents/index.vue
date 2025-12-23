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
        <el-table-column prop="key" label="#" width="80"></el-table-column>
        <el-table-column prop="number" label="Numero" width="120"></el-table-column>
        <el-table-column prop="client" label="Cliente" width="180"></el-table-column>
        <el-table-column prop="currency" label="Moneda" width="150"></el-table-column>
        <el-table-column prop="date" label="Fecha" width="180"></el-table-column>
        <el-table-column prop="total" label="Total" width="120"></el-table-column>
        <el-table-column fixed="right" label="XML" width="80">
          <template slot-scope="scope">
            <a :href="`${resource}/downloadxml/${scope.row.xml}`" target="_blank" class="btn btn-xs btn-info">
              <i class="fa fa-download"></i>
            </a>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="PDF" width="80">
          <template slot-scope="scope">
            <a :href="`${resource}/downloadpdf/${scope.row.pdf}`" target="_blank" class="btn btn-xs btn-info">
              <i class="fa fa-download"></i>
            </a>
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
