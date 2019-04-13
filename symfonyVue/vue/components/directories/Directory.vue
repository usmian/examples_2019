<template>
        <div class="form-group" style="margin-right:10px;max-width: 500px; display: inline-block"> <!--root component -->

            <b-input-group-prepend>
                <b-form-input style="border-radius: 0px !important;"
                              type="text"
                              :value="value"
                              @input="onInput"
                              required
                              >
                </b-form-input>
                <b-input-group-append>
                   <b-btn style="border-radius: 0px !important;"
                          :disabled="!nameInput"
                          title="редактировать"
                          @click="editDirectory({id: directory.id, type: type, value: val})">
                        <i class="fa fa-edit" style="color: white"></i>
                    </b-btn>

                    <b-btn variant="danger"
                           style="border-radius: 0px !important;"
                           title="удалить"
                           :disabled="!nameInput"
                           @click="removeDirectory({id: directory.id, type: type})">
                        <i class="fa fa-minus" style="color: white"></i>
                    </b-btn>
                </b-input-group-append>
            </b-input-group-prepend>
        </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import {mapActions} from 'vuex';

    export default {
        name: 'directoryInput',
        props: {
            'directory': Object,
            'placeholder':String,
            'value' : String,
            'faClass':String,
            'type': null,
        }
        ,
        data() {
            return {
              val:''
            }
        },
        computed: {
            nameInput(){
                return this.val.length>0
            }
        },
        methods: {
            ...mapActions('directories', {
                editDirectory : 'edit',
                removeDirectory: 'remove'
            }),
            onInput(e) {
                this.val = e;
            },
        }
    }
</script>
<style scoped>

</style>