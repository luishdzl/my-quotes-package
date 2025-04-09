<template>
  <div>
    <h2>Lista de Quotes</h2>
    <ul>
      <li v-for="quote in quotes" :key="quote.id">
        ({{ quote.id }}) {{ quote.quote }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      quotes: []
    }
  },
  mounted() {
    fetch('/api/quotes')
      .then(response => response.json())
      .then(data => {
        if(data && data.quotes){
          this.quotes = data.quotes;
        }
      })
      .catch(error => console.error('Error al cargar quotes:', error));
  }
}
</script>

<style scoped>
/* Agrega estilos específicos para este componente */
</style>
