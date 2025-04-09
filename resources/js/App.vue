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
      currentView: 'quotes-list',
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
/* Agrega estilos globales para la app */
</style>
