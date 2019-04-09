import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue'
import VueNoty from 'vuejs-noty'

import locale from 'element-ui/lib/locale/lang/ru-RU'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

import 'bootstrap-vue/dist/bootstrap-vue.css'
require('../css/app.scss');

import App from './vue/components/App.vue';

import * as moment from 'moment';
import 'moment/locale/ru';

import infiniteScroll from 'vue-infinite-scroll';

import {store} from './vue/store';
import {router} from './vue/router/routes.js';

moment.locale('ru');

Vue.use(infiniteScroll);
Vue.use(BootstrapVue);

let basePath = window.location.protocol + '//' + window.location.host + '/';
Object.defineProperty(Vue.prototype, '$moment', {value: moment});
Object.defineProperty(Vue.prototype, '$basePath', {value: basePath});

Vue.use(VueNoty, {
    timeout: 1000,
    progressBar: true,
    layout: 'topCenter',
    theme: 'metroui'
});
Vue.use(ElementUI, {locale});

const vm = new Vue({
    el: '#app',
    store,
    router,
    components: {App},
    template: '<App/>'
});

export {vm}