<template>
  <div id="app">
    <h1>Quotes App</h1>
    <nav>
      <button @click="currentView = 'quotes-list'">Todas las Quotes</button>
      <button @click="currentView = 'random-quote'">Quote Aleatoria</button>
      <button @click="currentView = 'quote-detail'">Buscar Quote por ID</button>
    </nav>
    <component :is="currentComponent"></component>
  </div>
</template>

<script>
import QuotesList from './components/QuotesList.vue';
import RandomQuote from './components/RandomQuote.vue';
import QuoteDetail from './components/QuoteDetail.vue';

export default {
  data() {
    return {
      currentView: 'quotes-list'
    }
  },
  computed: {
    currentComponent() {
      switch (this.currentView) {
        case 'quotes-list': return QuotesList;
        case 'random-quote': return RandomQuote;
        case 'quote-detail': return QuoteDetail;
        default: return QuotesList;
      }
    }
  }
};
</script>

<style>
/* Estilos globales para un diseño minimalista y mobile first */
body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f5f5f5;
  color: #333;
}

#app {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  text-align: center;
  font-weight: 300;
  margin-bottom: 20px;
}

nav {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.quote-content {
  padding: 15px;
  margin: 10px 0;
  background-color: #f8f9fa;
  border-radius: 4px;
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

nav button {
  margin: 5px 10px;
  padding: 10px 20px;
  font-size: 14px;
  background-color: #fff;
  border: none;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: background-color 0.3s ease;
}

nav button:hover {
  background-color: #e0e0e0;
}
</style>
