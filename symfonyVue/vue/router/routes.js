import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import Call from '../components/Call.vue';
import Directories from '../components/Directories.vue';
import Clients from '../components/Clients.vue';
import ClientForm from '../components/clients/ClientForm.vue';
import Requests from '../components/Requests.vue';
import Security from '../components/Security.vue';
import Dashboard from '../components/Dashboard.vue';


import {store} from '../store';

const routes = [

    {
        name: 'calls',
        path: '/calls',
        component: Call,
        beforeEnter(from, to, next) {
            store.dispatch('calls/LOAD_CALLS');
            next();
        }
    },
    {
        name: 'call',
        path: '/call/:id',
        component: Call,
        beforeEnter(from, to, next) {
            store.dispatch('calls/LOAD_CALL', {id: from.params.id});
            next();
        }
    },
    {
        name: 'directories',
        path: '/directories',
        component: Directories,
        beforeEnter(from, to, next) {
            store.dispatch('directories/LOAD_DIRECTORIES');
            next();
        }
    },
    {
        name: 'clients',
        path: '/clients',
        component: Clients,
        beforeEnter(from, to, next) {
            store.dispatch('clients/FETCH_CLIENTS');
            next();
        }
    },
    {
        name: 'client',
        path: '/client',
        component: ClientForm,
        props: {editMode: false},
        beforeEnter(from, to, next) {
            store.dispatch('client/CLEAR_CLIENT');
            next();
        }
    },
    {
        name: 'clientEdit',
        path: '/client/:id',
        component: ClientForm,
        props: {editMode: true},
        beforeEnter(from, to, next) {
            store.dispatch('client/LOAD_CLIENT', {id: from.params.id});
            next();
        }
    },
    {
        name: 'requests',
        path: '/requests',
        component: Requests,
        beforeEnter(from, to, next) {
            store.dispatch('calls/LOAD_CALLS');
            next();
        }
    },
    {
        name: 'rbac',
        path: '/rbac',
        component: Security,
        beforeEnter(from, to, next) {
            store.dispatch('security/LOAD_ACCESS_MODULES');
            next();
        }
    },
    {
        path: '/',
        redirect: {name: 'dashboard'}
    },
    {
        name: 'dashboard',
        path: '/dashboard',
        component: Dashboard
    }
    /* {
         path: '/requests',
         component: Requests
     },
     {
         path: '/statistics',
         component: Statistics
     },
     {
         path: '/settings',
         component: Settings
     },
     {
         path: '*',
         component: E404
     }*/
];

export const router = new VueRouter({
    routes,
    mode: 'history'
});