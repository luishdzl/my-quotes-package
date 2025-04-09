<template>
  <div>
    <h2>Quote Aleatoria</h2>
    <p v-if="quote">({{ quote.id }}) {{ quote.quote }}</p>
    <p v-else>Cargando...</p>
    <button @click="getRandomQuote">Obtener otra</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      quote: null
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
/* Estilos para el componente random */
</style>
