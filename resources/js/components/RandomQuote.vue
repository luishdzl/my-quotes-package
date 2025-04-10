<template>
  <div>
    <h2>Quote Aleatoria</h2>
    <div v-if="quote" class="quote-content">
          <p class="quote-text">({{ quote.id }}) "{{ quote.quote }}"</p>
          <p class="author">- {{ quote.author }}</p>
        </div>
    <p v-else>Cargando...</p>
    <button @click="getRandomQuote">Obtener otra</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      quote: null,
      author: null
    }
  },
  mounted() {
    this.getRandomQuote();
  },
  methods: {
    getRandomQuote() {
      fetch('/api/quotes/random')
        .then(response => response.json())
        .then(data => {
          this.quote = data;
        })
        .catch(error => console.error('Error al obtener la quote aleatoria:', error));
    }
  }
}
</script>

<style scoped>
/* Estilos para el componente RandomQuote: diseño minimalista y centrado */
div {
  background-color: #fff;
  padding: 20px;
  margin: 10px auto;
  max-width: 500px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  text-align: center;
}

h2 {
  font-weight: 300;
  margin-bottom: 15px;
}

p {
  font-size: 16px;
  margin: 10px 0;
}

button {
  padding: 10px 20px;
  background-color: #28a745;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #218838;
}
</style>
