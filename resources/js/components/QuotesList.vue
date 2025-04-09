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
/* Estilos para el componente QuotesList: diseño con sombras y responsive */
div {
  background-color: #fff;
  padding: 20px;
  margin: 10px auto;
  max-width: 600px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

h2 {
  font-weight: 300;
  margin-bottom: 15px;
}

ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

li {
  padding: 10px;
  border-bottom: 1px solid #eee;
}

li:last-child {
  border-bottom: none;
}
</style>
