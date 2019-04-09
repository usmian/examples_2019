import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import security from './modules/security';
import menu from './modules/menu';
import calls from './modules/calls';
import client from './modules/client';
import clients from './modules/clients';
import directories from './modules/directories';
import dashboard from './modules/dashboard';


export const store = new Vuex.Store({
    modules: {
        security,
        menu,
        dashboard,
        calls,
        directories,
        client,
        clients,
    }
});
