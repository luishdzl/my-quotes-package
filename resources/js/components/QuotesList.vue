<template>
  <div>
    <h2>Lista de Quotes</h2>
    <div class="pagination-settings">
      <label>Mostrar por página:</label>
      <select v-model.number="itemsPerPage" @change="handleItemsPerPageChange">
        <option>5</option>
        <option>10</option>
        <option>20</option>
      </select>
    </div>

    <div v-if="loading" class="loading">Cargando...</div>
    
    <template v-else>
      <ul v-if="quotes.length">
        <li v-for="quote in quotes" :key="quote.id">
          <div class="quote-content">
            <p class="quote-text">({{ quote.id }}) "{{ quote.quote }}"</p>
            <p class="author">- {{ quote.author }}</p>
          </div>
        </li>
      </ul>
      <div v-else class="no-results">No se encontraron quotes</div>
    </template>

    <div class="pagination-controls">
      <button 
        @click="previousPage" 
        :disabled="currentPage === 1 || loading"
      >
        Anterior
      </button>
      <span>Página {{ currentPage }} de {{ totalPages }}</span>
      <button 
        @click="nextPage" 
        :disabled="currentPage === totalPages || loading"
      >
        Siguiente
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      quotes: [],
      currentPage: 1,
      itemsPerPage: 10,
      totalQuotes: 0,
      loading: false,
      error: null
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.totalQuotes / this.itemsPerPage);
    },
    skip() {
      return (this.currentPage - 1) * this.itemsPerPage;
    }
  },
  methods: {
    async fetchQuotes() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await fetch(
          `/api/quotes?skip=${this.skip}&limit=${this.itemsPerPage}`
        );
        
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const data = await response.json();
        this.quotes = data.quotes || [];
        this.totalQuotes = data.total || 0;
        
      } catch (error) {
        this.error = error.message;
        console.error('Error al cargar quotes:', error);
      } finally {
        this.loading = false;
      }
    },
    
    previousPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.fetchQuotes();
      }
    },
    
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.fetchQuotes();
      }
    },
    
    handleItemsPerPageChange() {
      this.currentPage = 1;
      this.fetchQuotes();
    }
  },
  
  mounted() {
    this.fetchQuotes();
  }
}
</script>

<style scoped>
.pagination-settings {
  margin-bottom: 20px;
  text-align: center;
}

.pagination-settings select {
  padding: 6px 12px;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  margin-left: 10px;
}

.pagination-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 25px;
}

.pagination-controls button {
  padding: 8px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.pagination-controls button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.pagination-controls button:hover:not(:disabled) {
  background-color: #0056b3;
}

.loading, .no-results {
  text-align: center;
  padding: 20px;
  color: #666;
}

.error {
  color: #dc3545;
  text-align: center;
  padding: 20px;
}

ul {
  list-style: none;
  padding: 0;
}

li {
  margin-bottom: 15px;
}

.quote-content {
  background-color: #fff;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.quote-text {
  font-size: 16px;
  line-height: 1.6;
  margin: 0;
}

.author {
  color: #6c757d;
  font-style: italic;
  text-align: right;
  margin: 10px 0 0 0;
}
</style>