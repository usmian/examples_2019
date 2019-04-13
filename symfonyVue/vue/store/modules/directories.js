import axios from 'axios';

export default {
    namespaced: true,
    state: {
        directories: [],
        pending: false,
        isFetch: false,
        isAddDirectory: false,
        isAlertMessage:false,
        alertMsg: '',
        error: null
    },
    getters: {
        directories(state) {
            return state.directories;
        },
        pending(state) {
            return state.pending;
        },
        alertMsg(state){
            return state.alertMsg;
        },
        isAlertMessage(state){
          return state.isAlertMessage
        },
        isAddDirectory(state){
            return state.isAddDirectory
        },

    },
    mutations: {
        ['CLEAR_DIRECTORIES'](state) {
            state.directories = [];
        },
        ['LOAD_DIRECTORIES'](state, payload) {
            let directories = payload.data;
            state.pending = false;
            if (Boolean(directories)) {
                state.directories = directories;
            }
        },
        ['ADD_DIRECTORY'](state, data) {
            state.pending = false;
            state.alertMsg = 'Успешно добавлено';

            switch (data.type) {
                case 1:
                    state.directories.goods.unshift(data.directory);
                    break;
                case 2:
                    state.directories.regions.unshift(data.directory);
                    break;
                case 3:
                    state.directories.types.unshift(data.directory);
                    break;
            }
            state.isAddDirectory = false;

        },
        ['REMOVE_DIRECTORY'](state, data) {

            state.pending = false;
            let directory;

            switch (data.type) {
                case '1':
                    directory = state.directories.goods.findIndex(x => x.id === data.id);
                    state.directories.goods.splice(directory,1);
                    break;
                case '2':
                    directory = state.directories.regions.findIndex(x => x.id === data.id);
                    state.directories.regions.splice(directory,1);
                    break;
                case '3':
                    directory = state.directories.types.findIndex(x => x.id === data.id);
                    state.directories.types.splice(directory,1);
                    break;
            }

        },
        ['EDIT_DIRECTORY'](state) {
            state.pending = false;
        },
        ['END_ALERT'](state) {
            state.isAlertMessage = false;
        },
        ['START_PENDING'](state) {
            state.pending = true;
        },
        ['SET_MESSAGE_ADD'](state) {
            state.alertMsg = 'Успешно добавлено';
            state.isAlertMessage = true;
        },
        ['SET_MESSAGE_REMOVE'](state) {
            state.alertMsg = 'Успешно удалено';
            state.isAlertMessage = true;
        },
        ['SET_MESSAGE_EDIT'](state) {
            state.alertMsg = 'Успешно отредактировано';
            state.isAlertMessage = true;
        },
        ['SET_MESSAGE_REMOVE_ERROR'](state) {
            state.pending = false;
            state.alertMsg = 'Произошла ошибка при удалении. Обратитесь к администратору';
            state.isAlertMessage = true;
        },
        ['SET_MESSAGE_ADD_ERROR'](state) {
            state.pending = false;
            state.alertMsg = 'Произошла ошибка при добавлении. Обратитесь к администратору';
            state.isAlertMessage = true;
        },
        ['SET_MESSAGE_EDIT_ERROR'](state) {
            state.pending = false;
            state.alertMsg = 'Произошла ошибка при редактировании. Обратитесь к администратору';
            state.isAlertMessage = true;
        },
        ['ADDING_DIRECTORY'](state) {
            state.isAddDirectory=true;

        },
    },
    actions: {
        ['LOAD_DIRECTORIES'](store) {
            store.commit('CLEAR_DIRECTORIES');
            store.commit('START_PENDING');

            axios.get('/api/get_directories')
                .then(response => {
                    console.log(response);

                    store.commit('LOAD_DIRECTORIES', response)
                }).catch(e => {
                console.log(e)
            });

        },
        add(store, payload) {
            store.commit('START_PENDING');
            store.commit('ADDING_DIRECTORY');
            axios.post('/api/add_directory',{
                   type: payload.type,
                   value: payload.text
                })
                .then(response => {
                    store.commit('SET_MESSAGE_ADD');
                    store.commit('ADD_DIRECTORY', response.data)
                }).catch(e => {
                    store.commit('SET_MESSAGE_ADD_ERROR');
                console.log(e)
            });

        },
        remove(store, payload) {

            store.commit('START_PENDING');

            axios.post('/api/remove_directory',{
                type: payload.type,
                id: payload.id
            })
                .then(response => {
                    store.commit('SET_MESSAGE_REMOVE');
                    store.commit('REMOVE_DIRECTORY', response.data)
                }).catch(e => {
                    store.commit('SET_MESSAGE_REMOVE_ERROR');
                    console.log(e)
            });

        },
        edit(store, payload) {

            store.commit('START_PENDING');

            axios.post('/api/edit_directory',{
                type: payload.type,
                id: payload.id,
                value:payload.value
            })
                .then(response => {
                    store.commit('SET_MESSAGE_EDIT');
                    store.commit('EDIT_DIRECTORY', response.data)
                }).catch(e => {
                store.commit('SET_MESSAGE_EDIT_ERROR');
                console.log(e)
            });

        },
        setIsAlertFalse(store){
            store.commit('END_ALERT');
        }
    }
};