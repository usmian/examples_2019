<template>
    <el-card class="box-card">
        <div slot="header" class="clearfix">
            <span><strong>{{module.title}}</strong></span>
        </div>
            <checkbox-component v-for="(role, i) in roles"
                          :label="role.title"
                          :key="role.id"
                          :role = "role"
                          :module="module"
                          ></checkbox-component>
    </el-card>
</template>

<script>
    import {mapGetters} from 'vuex';
    import {mapActions} from 'vuex';
    import CheckboxComponent from '../security/CheckboxComponent.vue';

    export default {
        name: 'AccessComponent',
        props: [ 'module', 'roles'],
        components: {
            CheckboxComponent
        }
    }
</script>

<style scoped>
    .box-card {
        width: 40%;
        display: inline-flex;
        margin: 10px;
    }
    @keyframes slideIn {
        from {
            transform: rotateY(90deg);
        }
        to {
            transform: rotateY(0deg);
        }
    }
    @keyframes slideOut {
        from {
            transform: rotateY(0deg);
        }
        to {
            transform: rotateY(90deg);
        }
    }
</style>
