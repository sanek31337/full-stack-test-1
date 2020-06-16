require('./bootstrap');

import vuetify from "./vuetify";

window.Vue = require('vue');

Vue.component('articles-list', require('./components/ArticlesList.vue').default);

const app = new Vue({
    vuetify
}).$mount('#app');
