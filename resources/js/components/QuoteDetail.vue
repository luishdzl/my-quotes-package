<template>
  <div>
    <h2>Buscar Quote por ID</h2>
    <input v-model.number="id" type="number" placeholder="Ingresa el ID" />
    <button @click="getQuote">Buscar</button>
    <div v-if="quote">
      <p>({{ quote.id }}) {{ quote.quote }}</p>
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
/* Estilos para el componente detail */
</style>
