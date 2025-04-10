<template>
  <div>
    <h2>Lista de Quotes</h2>
    <div class="pagination-settings">
      <label>Mostrar por página:</label>
      <select v-model="itemsPerPage">
        <option>5</option>
        <option>10</option>
        <option>20</option>
      </select>
    </div>
    <ul>
      <li v-for="quote in paginatedQuotes" :key="quote.id">
        <div class="quote-content">
          <p class="quote-text">({{ quote.id }}) "{{ quote.quote }}"</p>
          <p class="author">- {{ quote.author }}</p>
        </div>
      </li>
    </ul>
    <div class="pagination-controls">
      <button 
        @click="previousPage" 
        :disabled="currentPage === 1"
      >
        Anterior
      </button>
      <span>Página {{ currentPage }} de {{ totalPages }}</span>
      <button 
        @click="nextPage" 
        :disabled="currentPage === totalPages"
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
      itemsPerPage: 10
    }
  },
  computed: {
    paginatedQuotes() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.quotes.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.quotes.length / this.itemsPerPage);
    }
  },
  watch: {
    itemsPerPage() {
      this.currentPage = 1;
    }
  },
  methods: {
    previousPage() {
      if (this.currentPage > 1) this.currentPage--;
    },
    nextPage() {
      if (this.currentPage < this.totalPages) this.currentPage++;
    }
  },
  mounted() {
    fetch('/api/quotes')
      .then(response => response.json())
      .then(data => {
        if(data && data.quotes) this.quotes = data.quotes;
      })
      .catch(error => console.error('Error al cargar quotes:', error));
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

.pagination-controls span {
  font-size: 0.95em;
  color: #495057;
}
</style>