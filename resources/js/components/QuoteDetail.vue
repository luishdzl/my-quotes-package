<template>
  <div>
    <h2>Buscar Quote por ID</h2>
    <input v-model.number="id" type="number" placeholder="Ingresa el ID" />
    <button @click="getQuote">Buscar</button>
    <div v-if="quote" class="quote-content">
          <p class="quote-text">({{ quote.id }}) "{{ quote.quote }}"</p>
          <p class="author">- {{ quote.author }}</p>
        </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      id: null,
      quote: null
    }
  },
  methods: {
    getQuote() {
      if (!this.id) return;
      fetch(`/api/quotes/${this.id}`)
        .then(response => response.json())
        .then(data => {
          this.quote = data;
        })
        .catch(error => console.error('Error al obtener la quote:', error));
    }
  }
}
</script>

<style scoped>
/* Estilos para el componente QuoteDetail: diseño minimalista con sombras */
div {
  background-color: #fff;
  padding: 20px;
  margin: 10px auto;
  max-width: 500px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

h2 {
  margin-bottom: 15px;
  font-size: 20px;
  font-weight: 300;
}

input {
  width: 100%;
  padding-block: 10px;
  margin: 10px 0;
  border: 1px solid #ddd;
  border-radius: 4px;
}

button {
  padding: 10px 20px;
  background-color: #007BFF;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}

p {
  margin: 10px 0;
  font-size: 16px;
}
</style>
