<template>
  <div class="companies-container">
    <!-- Header -->
    <div class="page-header">
      <div class="header-info">
        <h1>Empresas Registradas</h1>
        <p>Gestiona las empresas conectadas al sistema de facturación</p>
      </div>
      <div class="header-stats">
        <div class="stat-badge">
          <i class="fa fa-building"></i>
          <span>{{ tableData.length }} empresas</span>
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="search-card">
      <el-input
        v-model="searchQuery"
        placeholder="Buscar por NIT, nombre o email..."
        prefix-icon="el-icon-search"
        clearable
        class="search-input"
      ></el-input>
    </div>

    <!-- Companies Grid -->
    <div class="companies-grid">
      <div 
        v-for="company in filteredData" 
        :key="company.key" 
        class="company-card"
      >
        <div class="card-header">
          <div class="company-avatar">
            {{ getInitials(company.name) }}
          </div>
          <div class="company-badge">
            <i class="fa fa-check-circle"></i>
            Activa
          </div>
        </div>
        
        <div class="card-body">
          <h3 class="company-name">{{ company.name }}</h3>
          
          <div class="info-row">
            <i class="fa fa-id-card"></i>
            <span>NIT: {{ company.identification_number }}</span>
          </div>
          
          <div class="info-row">
            <i class="fa fa-envelope"></i>
            <span>{{ company.email }}</span>
          </div>
          
          <div class="info-row">
            <i class="fa fa-calendar"></i>
            <span>{{ formatDate(company.created_at) }}</span>
          </div>
        </div>

        <div class="card-footer">
          <div class="token-section">
            <label>Token API</label>
            <div class="token-display">
              <code>{{ truncateToken(company.token) }}</code>
              <button 
                class="btn-copy" 
                @click="copyToken(company.token)"
                title="Copiar token"
              >
                <i class="fa fa-copy"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredData.length === 0" class="empty-state">
      <i class="fa fa-building"></i>
      <h3>No hay empresas</h3>
      <p>No se encontraron empresas con los criterios de búsqueda</p>
    </div>

    <!-- Toast notification -->
    <transition name="fade">
      <div v-if="showToast" class="toast-notification">
        <i class="fa fa-check-circle"></i>
        Token copiado al portapapeles
      </div>
    </transition>
  </div>
</template>

<style scoped>
.companies-container {
  padding: 0;
}

/* Page Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.header-info h1 {
  margin: 0 0 4px;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.header-info p {
  margin: 0;
  color: #64748b;
  font-size: 0.95rem;
}

.stat-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #f97316, #ea580c);
  color: white;
  padding: 10px 20px;
  border-radius: 10px;
  font-weight: 500;
  box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
}

/* Search */
.search-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.search-input {
  max-width: 400px;
}

/* Companies Grid */
.companies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 24px;
}

.company-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.company-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.card-header {
  background: linear-gradient(135deg, #1e293b, #334155);
  padding: 24px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.company-avatar {
  width: 56px;
  height: 56px;
  background: linear-gradient(135deg, #f97316, #ea580c);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 1.25rem;
  box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4);
}

.company-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(34, 197, 94, 0.2);
  color: #22c55e;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.card-body {
  padding: 24px;
}

.company-name {
  margin: 0 0 16px;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
}

.info-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
  color: #64748b;
  font-size: 0.9rem;
}

.info-row i {
  width: 16px;
  color: #94a3b8;
}

.info-row:last-child {
  margin-bottom: 0;
}

.card-footer {
  padding: 16px 24px 24px;
  border-top: 1px solid #f1f5f9;
}

.token-section label {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.token-display {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px 12px;
}

.token-display code {
  flex: 1;
  font-family: 'Monaco', 'Consolas', monospace;
  font-size: 0.8rem;
  color: #1e293b;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.btn-copy {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.2s;
}

.btn-copy:hover {
  background: #e2e8f0;
  color: #f97316;
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

/* Toast */
.toast-notification {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #1e293b;
  color: white;
  padding: 14px 24px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  z-index: 1000;
}

.toast-notification i {
  color: #22c55e;
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Responsive */
@media (max-width: 768px) {
  .companies-grid {
    grid-template-columns: 1fr;
  }
  
  .page-header {
    flex-direction: column;
  }
}
</style>

<script>
export default {
  data() {
    return {
      hostname: window.location.hostname,
      resource: "configuration",
      tableData: [],
      searchQuery: "",
      showToast: false
    };
  },
  computed: {
    filteredData() {
      if (!this.searchQuery) {
        return this.tableData;
      }
      const query = this.searchQuery.toLowerCase();
      return this.tableData.filter(company => 
        (company.name && company.name.toLowerCase().includes(query)) ||
        (company.identification_number && company.identification_number.toString().includes(query)) ||
        (company.email && company.email.toLowerCase().includes(query))
      );
    }
  },
  created() {
    this.getRecords();
  },
  methods: {
    getInitials(name) {
      if (!name) return "?";
      return name.split(" ").map(w => w[0]).join("").substring(0, 2).toUpperCase();
    },
    formatDate(date) {
      if (!date) return "-";
      const d = new Date(date);
      return d.toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    truncateToken(token) {
      if (!token) return "-";
      if (token.length <= 20) return token;
      return token.substring(0, 20) + "...";
    },
    copyToken(token) {
      if (!token) return;
      navigator.clipboard.writeText(token).then(() => {
        this.showToast = true;
        setTimeout(() => {
          this.showToast = false;
        }, 2000);
      });
    },
    getFilterRecord(array) {
      return array.filter(function(x) {
        return x.identification_number != null;
      });
    },
    getRecords() {
      return new Promise((resolve, reject) => {
        this.$http
          .get(`/${this.resource}/records`)
          .then(response => {
            this.tableData = this.getFilterRecord(response.data.data);
          })
          .catch(error => {
            console.error("Error loading companies:", error);
          });
      });
    }
  }
};
</script>
