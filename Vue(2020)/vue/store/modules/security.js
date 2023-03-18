import axios from 'axios';
export default {
    namespaced: true,
    state: {
        pending: false,
        isFetch: false,
        accessList: null,
        grantedList: null,
        roles: null,
        user: null,
        basePath: '',
        error: null,
        accessGranted: false,
        items: [
            {
                url: '/dashboard',
                name: 'dashboard',
                text: 'Главная',
                icon: 'fa fa-bar-chart-o'
            },
            {
                url: '/calls',
                name: 'calls',
                text: 'Холодные звонки',
                icon: 'fa fa-phone'
            },
            {
                url: '/requests',
                name: 'requests',
                text: 'Запросы',
                icon: 'fa fa-fire'
            },
            {
                url: '/clients',
                name: 'clients',
                text: 'Клиенты',
                icon: 'fa fa-address-book'
            },
            {
                url: '/directories',
                name: 'directories',
                text: 'Тэги',
                icon: 'fa fa-desktop'
            },
            {
                url: '/rbac',
                name: 'rbac',
                text: 'Права доступа',
                icon: 'fa fa-exclamation-triangle'
            },
            {
                url: '/statistics',
                name: 'statistics',
                text: 'Статистика',
                icon: 'fa fa-pie-chart'
            },
            {
                url: '/settings',
                name: 'settings',
                text: 'Настройки',
                icon: 'fa fa-cogs'
            }
        ]
    },
    getters: {
        accessGranted(state) {
            return state.accessGranted;
        },
        items(state) {
            return state.items;
        },
        user(state) {
            return state.user;
        },
        roles(state) {
            return state.roles;
        },
        access(state) {
            return state.accessList;
        },
        basePath(state) {
            return state.basePath;
        },
        grantedList(state) {
            return state.grantedList;
        },
        pending(state) {
            return state.pending;
        }
    },
    mutations: {
        ['SET_INIT_DATA'](state, payload) {
            state.roles = payload.roles;
            state.basePath = payload.basePath;
            state.avatar = payload.avatar;
        },
        ['INIT_APP'](state, payload) {
            state.user = JSON.parse(payload.user);
            state.grantedList = payload.grantedList;
            state.accessList = payload.access;

            let granted = state.grantedList;
            var grantedMenu = [{
                url: '/dashboard',
                name: 'dashboard',
                text: 'Главная',
                icon: 'fa fa-bar-chart-o'
            }];
            for (let key in granted) {
                let idx = state.items.findIndex(x = > x.name == granted[key]);
                grantedMenu.push(state.items[idx]);
            }
            state.items = grantedMenu;
            state.accessGranted = true;
            state.pending = false;
        },
        ['INIT_ACCESS'](state, payload) {
            state.roles = payload.roles;
            state.accessList = payload.access;
            state.pending = false;
        },
        ['SWITCHED_ROLE'](state, payload) {
            let module = state.accessList.findIndex(x = > x.id === payload.id
        );
            state.accessList[module] = payload;
            state.pending = false;
        },
        ['START_PENDING'](state) {
            state.pending = true;
        }
    },
    actions: {
        init(store, payload) {
            store.commit('START_PENDING');
            store.commit('SET_INIT_DATA', payload);

            let path = payload.path + 'api/init_app';
            axios.get(path)
                .then(response = > {
                store.commit('INIT_APP', response.data)
            }).catch(e = > {
                console.log(e)
            });
        },
        updateRole(store, payload) {
            store.commit('START_PENDING');
            let path = '/api/security/switch_role';
            axios.post(path, {
                payload
            })
                .then(response = > {
                store.commit('SWITCHED_ROLE', response.data)
            }).catch(e = > {
                console.log(e)
            });
        },
        ['LOAD_ACCESS_MODULES'](store) {
            store.commit('START_PENDING');
            axios.get('/api/security/get_access_modules')
                .then(response = > {
                store.commit('INIT_ACCESS', response.data)
            }).catch(e = > {
                console.log(e)
            });
        }
    }
};
