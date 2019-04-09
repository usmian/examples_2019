<template>

        <el-checkbox  :label="label"
                      @change="switchRole({id: role.id, module_id: module.id})"
                      v-model="checked">
        </el-checkbox>

</template>

<script>
    import {mapGetters} from 'vuex';
    import {mapActions} from 'vuex';


    export default {
        name: 'AccessComponent',
        props: [ 'module', 'role' , 'label'],

        data() {
            return {
               checked: null
            }
        },
        computed: {
            nameInput(){
                return this.val.length>0
            },
        },
        methods: {
            ...mapActions('security', {
                   updateRole:'updateRole'
            }),
            switchRole(e){
                this.updateRole(e);
            }
        },
        created(){
            this.checked = this.module.roles.findIndex(x => x.id === this.role.id)!==-1;
        }
    }
</script>
<style scoped>

</style>