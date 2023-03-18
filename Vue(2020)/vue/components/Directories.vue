<template>
        <div :class="loaderShow">
                <div id="cube-loader" v-if="pending">
                    <div class="caption">
                        <h3>Подожите...</h3>
                        <div class="cube-loader">
                            <div class="cube loader-1"></div>
                            <div class="cube loader-2"></div>
                            <div class="cube loader-4"></div>
                            <div class="cube loader-3"></div>
                        </div>
                    </div>
                </div>
        <div class="sub-menu-wrap">
            <ul class="list-inline">
                <li class="list-inline-item">
                    <b-form-input
                            v-model="query"
                            size="sm"
                            class="mr-sm-2"
                            type="text"
                            placeholder="Поиск"/>
                </li>
            </ul>
        </div>
        <hr>
        <b-tabs>
            <b-tab title="Товарные группы" active>
                <div class="form-group" style="margin-top: 10px;max-width: 600px;display: inline-block">
                    <!--root component -->
                    <b-input-group-prepend>
                        <b-btn slot="prepend"
                               :style="{backgroundColor: isActiveGoods}"
                               class="fa fa-window-restore"
                               disabled></b-btn>
                        <b-form-input id="type__goods"
                                      style="border-radius: 0px !important;"
                                      name="value"
                                      type="text"
                                      @input="onFocusGoods"
                                      :state="nameStateGoods"
                                      v-model.lazy="typeGoods"
                                      required
                                      placeholder="группа">
                        </b-form-input>
                        <b-input-group-append>
                            <b-btn variant="success"
                                   style="border-radius: 0px !important;"
                                   :disabled="!nameGoods"
                                   @click="addDirectory({type:1, text:typeGoods})">
                                <i class="fa fa-plus" style="color: white"></i>
                                Добавить
                            </b-btn>
                        </b-input-group-append>
                    </b-input-group-prepend>
                </div>
                <div class="input__directory" v-if="directories.goods.length" style="max-height: 100vh;">
                    <DirectoryInput
                         v-for="(good,i) in computedListGoods"
                         :key="i"
                         :type="'1'"
                         :value.sync="good.name"
                         :faClass="'fa-window-restore'"
                         @update:value="onChangeValue(i, $event, 1)"
                         :directory="good">
                    </DirectoryInput>
                </div>
            </b-tab>
            <b-tab title="Маршруты">
                <div class="form-group" style="margin-top: 10px;max-width: 600px;display: inline-block">
                    <b-input-group-prepend>
                        <b-btn slot="prepend" class="fa fa-map"
                               :style="{backgroundColor:  isActiveRegions}"
                               disabled></b-btn>
                        <b-form-input id="type__regions"
                                      style="border-radius: 0px !important;"
                                      name="value"
                                      type="text"
                                      @input="onFocusRegions"
                                      :state="nameStateRegions"
                                      v-model.lazy="typeRegions"
                                      required
                                      placeholder="Маршрут">
                        </b-form-input>
                        <b-input-group-append>
                            <b-btn variant="success"
                                   tyle="border-radius: 0px !important;"
                                   :disabled="!nameRegions"
                                   @click="addDirectory({type:2, text:typeRegions})">
                                <i class="fa fa-plus" style="color: white"></i>
                                Добавить
                            </b-btn>
                        </b-input-group-append>
                    </b-input-group-prepend>
                </div>
                <div class="input__directory" v-if="directories.regions.length" style="max-height: 100vh; overflow: auto">
                    <DirectoryInput
                            v-for="(region,i) in computedListRegions"
                            :key="i"
                            :type="'2'"
                            :value.sync="region.name"
                            :faClass="'fa-window-restore'"
                            @update:value="onChangeValue(i, $event, 2)"
                            :directory="region">
                    </DirectoryInput>
                </div>
            </b-tab>
            <b-tab title="Условия поставок">
                <div class="form-group" style="margin-top: 10px;max-width: 600px;display: inline-block">
                    <b-input-group-prepend>
                        <b-btn slot="prepend"
                               class="fa fa-plane"
                               :style="{backgroundColor:  isActiveDelivery}"
                               disabled></b-btn>
                        <b-form-input id="type__delivery"
                                      style="border-radius: 0px !important;"
                                      name="value"
                                      type="text"
                                      @input="onFocusDelivery"
                                      v-model.lazy="typeDelivery"
                                      :state="nameStateDelivery"
                                      required
                                      placeholder="Условия поставок">
                        </b-form-input>
                        <b-input-group-append>
                            <b-btn variant="success"
                                   style="border-radius: 0px !important;"
                                   :disabled="!nameDelivery"
                                   @click="addDirectory({type:3, text: typeDelivery})">
                                <i class="fa fa-plus" style="color: white"></i>
                                Добавить
                            </b-btn>
                        </b-input-group-append>
                    </b-input-group-prepend>
                </div>
                <div class="input__directory"
                     v-if="directories.types.length"
                     style="max-height: 100vh; overflow: auto">
                    <DirectoryInput
                            v-for="(type,i) in computedListTypes"
                            :key="i"
                            :type="'3'"
                            :value="type.name"
                            :faClass="'fa-window-restore'"
                            :directory="type">
                    </DirectoryInput>
                </div>
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>
    import DirectoryInput from './directories/Directory.vue';

    import {mapGetters} from 'vuex';
    import {mapActions} from 'vuex';
    import {mapState} from 'vuex';
    import 'vuejs-noty/dist/vuejs-noty.css'

    export default {
        components: {DirectoryInput},
        name: "Directories",
        data() {
            return {
                query:'',
                type: 0,
                typeDelivery: '',
                typeRegions: '',
                typeGoods: '',
                activatedDelivery:false,
                activatedRegions:false,
                activatedGoods:false,
            }
        },
        methods: {
            ...mapActions('directories', {
                addDirectory: 'add',
                removeDirectory: 'remove',
                setIsAlert: 'setIsAlertFalse'
            }),
            onFocusDelivery() {
                this.activatedDelivery = true;
            },
            onFocusRegions() {
                this.activatedRegions = true;
            },
            onFocusGoods() {
                this.activatedGoods = true;
            },
            showAlert () {
                this.$noty.success(this.alertMsg);
                this.setIsAlert();
            }
        },
        computed: {
            ...mapGetters('directories', {
                pending: 'pending',
                isAlertMessage:'isAlertMessage',
                isAddDirectory:'isAddDirectory',
                alertMsg: 'alertMsg',
            }),
            ...mapState('directories',
                ['directories']
            ),
            computedListGoods: function () {
                let vm = this;

                return this.directories.goods.filter(function (item) {
                    return item.name.toLowerCase().indexOf(vm.query.toLowerCase()) !== -1
                })
            },
            computedListRegions: function () {
                let vm = this;

                return this.directories.regions.filter(function (item) {
                    return item.name.toLowerCase().indexOf(vm.query.toLowerCase()) !== -1
                })
            },
            computedListTypes: function () {
                let vm = this;

                return this.directories.types.filter(function (item) {
                    return item.name.toLowerCase().indexOf(vm.query.toLowerCase()) !== -1
                })
            },
            loaderShow() {
                return this.pending ? 'loading2' : ''
            },
            nameStateDelivery(){
                    return this.activatedDelivery ? this.nameDelivery : null
            },
            nameStateRegions(){
                return this.activatedRegions ? this.nameRegions : null
            },
            nameStateGoods(){
                return this.activatedGoods ? this.nameGoods : null
            },
            nameDelivery(){
                return this.typeDelivery.length>0
            },
            nameRegions(){
                return this.typeRegions.length>0
            },
            nameGoods(){
                return this.typeGoods.length>0
            },
            isActiveGoods(){
                return this.nameGoods ? '#4ebc00' : ''
            },
            isActiveRegions(){
                return this.nameRegions ? '#4ebc00' : ''
            },
            isActiveDelivery(){
                return this.nameDelivery ? '#4ebc00' : ''
            }
        },
        watch: {
            isAlertMessage(){
                if (this.isAlertMessage==true) {
                    this.showAlert();
                }
           },
            isAddDirectory(){
                 this.typeDelivery ='';
                 this.typeRegions = '';
                 this.typeGoods = '';
           }
        }
    }
</script>

<style scoped>
    /*  ---------------------------------------------------    */
    #cube-loader {
        align-items: center;
        display: flex;
        height: 100%;
        width: 100%;
        position: fixed;
        z-index: 1001;
    }

    #cube-loader .caption {
        margin: 0 auto;
    }

    #cube-loader .cube-loader {
        width: 73px;
        height: 73px;
        margin: 0 auto;
        margin-top: 49px;
        position: relative;
        transform: rotateZ(45deg);
    }

    #cube-loader .cube-loader .cube {
        position: relative;
        transform: rotateZ(45deg);
        width: 50%;
        height: 50%;
        float: left;
        transform: scale(1.1);
    }

    #cube-loader .cube-loader .cube:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #34495e;
        animation: cube-loader 2.76s infinite linear both;
        transform-origin: 100% 100%;
    }

    #cube-loader .cube-loader .loader-2 {
        transform: scale(1.1) rotateZ(90deg);
    }

    #cube-loader .cube-loader .loader-3 {
        transform: scale(1.1) rotateZ(180deg);
    }

    #cube-loader .cube-loader .loader-4 {
        transform: scale(1.1) rotateZ(270deg);
    }

    #cube-loader .cube-loader .loader-2:before {
        animation-delay: 0.35s;
    }

    #cube-loader .cube-loader .loader-3:before {
        animation-delay: 0.69s;
    }

    #cube-loader .cube-loader .loader-4:before {
        animation-delay: 1.04s;
    }

    @keyframes cube-loader {
        0%, 10% {
            transform: perspective(136px) rotateX(-180deg);
            opacity: 0;
        }
        25%, 75% {
            transform: perspective(136px) rotateX(0deg);
            opacity: 1;
        }
        90%, 100% {
            transform: perspective(136px) rotateY(180deg);
            opacity: 0;
        }
    }
</style>
