<template>
  <div class="documents-container">
    <!-- Header con estadísticas -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon blue">
          <i class="fa fa-file-alt"></i>
        </div>
        <div class="stat-info">
          <span class="stat-value">{{ totalDocuments }}</span>
          <span class="stat-label">Total Documentos</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <i class="fa fa-building"></i>
        </div>
        <div class="stat-info">
          <span class="stat-value">{{ Object.keys(groupedData).length }}</span>
          <span class="stat-label">Empresas</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange">
          <i class="fa fa-calendar"></i>
        </div>
        <div class="stat-info">
          <span class="stat-value">{{ todayDocuments }}</span>
          <span class="stat-label">Hoy</span>
        </div>
      </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
      <div class="filters-row">
        <el-input
          v-model="searchQuery"
          placeholder="Buscar por número, cliente..."
          prefix-icon="el-icon-search"
          clearable
          class="search-input"
        ></el-input>
        <el-select v-model="selectedCompany" placeholder="Filtrar por empresa" clearable class="company-filter">
          <el-option
            v-for="company in companies"
            :key="company"
            :label="company"
            :value="company"
          ></el-option>
        </el-select>
      </div>
    </div>

    <!-- Documentos agrupados por empresa -->
    <div v-for="(documents, companyName) in filteredGroupedData" :key="companyName" class="company-section">
      <div class="company-header" @click="toggleCompany(companyName)">
        <div class="company-info">
          <div class="company-avatar">
            {{ getInitials(companyName) }}
          </div>
          <div class="company-details">
            <h3>{{ companyName }}</h3>
            <span class="doc-count">{{ documents.length }} documento(s)</span>
          </div>
        </div>
        <div class="company-toggle">
          <i :class="expandedCompanies[companyName] ? 'fa fa-chevron-up' : 'fa fa-chevron-down'"></i>
        </div>
      </div>

      <transition name="slide">
        <div v-show="expandedCompanies[companyName]" class="company-documents">
          <el-table :data="documents" style="width: 100%" stripe>
            <el-table-column prop="key" label="#" width="70"></el-table-column>
            <el-table-column prop="number" label="Número" width="120">
              <template slot-scope="scope">
                <span class="doc-number">{{ scope.row.number }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="client" label="Cliente" min-width="180">
              <template slot-scope="scope">
                <div class="client-cell">
                  <span class="client-name">{{ scope.row.client }}</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="currency" label="Moneda" width="140"></el-table-column>
            <el-table-column prop="date" label="Fecha" width="160">
              <template slot-scope="scope">
                <span class="date-cell">{{ formatDate(scope.row.date) }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="total" label="Total" width="130" align="right">
              <template slot-scope="scope">
                <span class="total-amount">${{ formatNumber(scope.row.total) }}</span>
              </template>
            </el-table-column>
            <el-table-column fixed="right" label="Acciones" width="140" align="center">
              <template slot-scope="scope">
                <div class="action-buttons">
                  <a 
                    :href="`${resource}/downloadxml/${scope.row.xml}`" 
                    target="_blank" 
                    class="btn-action btn-xml"
                    title="Descargar XML"
                  >
                    <i class="fa fa-code"></i>
                  </a>
                  <a 
                    :href="`${resource}/downloadpdf/${scope.row.pdf}`" 
                    target="_blank" 
                    class="btn-action btn-pdf"
                    title="Descargar PDF"
                  >
                    <i class="fa fa-file-pdf"></i>
                  </a>
                </div>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </transition>
    </div>

    <!-- Empty state -->
    <div v-if="Object.keys(filteredGroupedData).length === 0" class="empty-state">
      <i class="fa fa-inbox"></i>
      <h3>No hay documentos</h3>
      <p>No se encontraron documentos con los filtros aplicados</p>
    </div>
  </div>
</template>

<style scoped>
.documents-container {
  padding: 0;
}

/* Stats Row */
.stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.stat-icon.blue {
  background: #dbeafe;
  color: #2563eb;
}

.stat-icon.green {
  background: #dcfce7;
  color: #16a34a;
}

.stat-icon.orange {
  background: #fed7aa;
  color: #ea580c;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.stat-label {
  font-size: 0.875rem;
  color: #64748b;
}

/* Filters */
.filters-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.filters-row {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.search-input {
  flex: 1;
  min-width: 250px;
}

.company-filter {
  min-width: 200px;
}

/* Company Section */
.company-section {
  background: white;
  border-radius: 12px;
  margin-bottom: 16px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.company-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  cursor: pointer;
  transition: background 0.2s;
  border-bottom: 1px solid #f1f5f9;
}

.company-header:hover {
  background: #f8fafc;
}

.company-info {
  display: flex;
  align-items: center;
  gap: 16px;
}

.company-avatar {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #f97316, #ea580c);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 1rem;
}

.company-details h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
}

.doc-count {
  font-size: 0.875rem;
  color: #64748b;
}

.company-toggle {
  color: #64748b;
  transition: transform 0.2s;
}

.company-documents {
  padding: 0 24px 24px;
}

/* Table Styles */
.doc-number {
  font-weight: 600;
  color: #1e293b;
}

.client-cell {
  display: flex;
  flex-direction: column;
}

.client-name {
  font-weight: 500;
  color: #1e293b;
}

.date-cell {
  color: #64748b;
  font-size: 0.875rem;
}

.total-amount {
  font-weight: 600;
  color: #16a34a;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 8px;
  justify-content: center;
}

.btn-action {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-xml {
  background: #1e293b;
}

.btn-xml:hover {
  background: #334155;
  transform: translateY(-2px);
}

.btn-pdf {
  background: #ef4444;
}

.btn-pdf:hover {
  background: #dc2626;
  transform: translateY(-2px);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
}

.empty-state i {
  font-size: 48px;
  color: #cbd5e1;
  margin-bottom: 16px;
}

.empty-state h3 {
  margin: 0 0 8px;
  color: #1e293b;
}

.empty-state p {
  color: #64748b;
  margin: 0;
}

/* Transitions */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter,
.slide-leave-to {
  opacity: 0;
  max-height: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .stats-row {
    grid-template-columns: 1fr;
  }
  
  .filters-row {
    flex-direction: column;
  }
  
  .search-input,
  .company-filter {
    width: 100%;
    min-width: auto;
  }
}
</style>

<script>
export default {
  data() {
    return {
      resource: "documents",
      tableData: [],
      searchQuery: "",
      selectedCompany: "",
      expandedCompanies: {}
    };
  },
  computed: {
    // Agrupar documentos por empresa
    groupedData() {
      const grouped = {};
      this.tableData.forEach(doc => {
        const company = doc.company_name || "Sin Empresa";
        if (!grouped[company]) {
          grouped[company] = [];
        }
        grouped[company].push(doc);
      });
      return grouped;
    },
    // Filtrar datos agrupados
    filteredGroupedData() {
      let result = {};
      
      Object.keys(this.groupedData).forEach(company => {
        // Filtrar por empresa seleccionada
        if (this.selectedCompany && company !== this.selectedCompany) {
          return;
        }
        
        // Filtrar por búsqueda
        let docs = this.groupedData[company];
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase();
          docs = docs.filter(doc => 
            (doc.number && doc.number.toString().toLowerCase().includes(query)) ||
            (doc.client && doc.client.toLowerCase().includes(query))
          );
        }
        
        if (docs.length > 0) {
          result[company] = docs;
        }
      });
      
      return result;
    },
    // Lista de empresas para el filtro
    companies() {
      return Object.keys(this.groupedData);
    },
    // Total de documentos
    totalDocuments() {
      return this.tableData.length;
    },
    // Documentos de hoy
    todayDocuments() {
      const today = new Date().toISOString().split('T')[0];
      return this.tableData.filter(doc => {
        if (!doc.date) return false;
        return doc.date.startsWith(today);
      }).length;
    }
  },
  created() {
    this.getRecords();
  },
  methods: {
    toggleCompany(companyName) {
      this.$set(this.expandedCompanies, companyName, !this.expandedCompanies[companyName]);
    },
    getInitials(name) {
      if (!name) return "?";
      return name.split(" ").map(w => w[0]).join("").substring(0, 2).toUpperCase();
    },
    formatDate(date) {
      if (!date) return "-";
      const d = new Date(date);
      return d.toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    formatNumber(num) {
      if (!num) return "0";
      return parseFloat(num).toLocaleString('es-CO');
    },
    getRecords() {
      return new Promise((resolve, reject) => {
        this.$http
          .get(`/${this.resource}/records`)
          .then(response => {
            this.tableData = response.data.data;
            // Expandir todas las empresas por defecto
            Object.keys(this.groupedData).forEach(company => {
              this.$set(this.expandedCompanies, company, true);
            });
          })
          .catch(error => {
            console.error("Error loading documents:", error);
          });
      });
    }
  }
};
</script>
