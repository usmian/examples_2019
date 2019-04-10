<template>
<div id="app__main" >

    <div class="left main-sidebar">

        <div class="sidebar-inner">

            <div id="sidebar-menu">

                <ul v-show="accessGranted">
                    <router-link v-for="(item, index) in menuList"
                                 :key="index"
                                 :to="item.url"
                                 tag="li"
                                 class="submenu nav-item"
                                 active-class="active"
                    >
                        <el-tooltip class="item"
                                    :content="item.text"
                                    effect="dark"
                                    placement="right">
                            <a><i :class="item.icon"></i></a>
                        </el-tooltip>
                    </router-link>

                </ul>

                <div class="clearfix"></div>

            </div>

            <div class="clearfix"></div>

        </div>

    </div>
    <div class="content-page">
        <div class="content">
                <transition name="slide" mode="out-in" >
                    <router-view></router-view>
                </transition>
            <!-- END container-fluid -->
        </div>
    </div>

   </div>
</template>

<script>
    import Call from './Call.vue';
    import {mapGetters} from 'vuex';

    export default {
        data() {
            return {

            }
        },
        components: {},
        computed: {
            ...mapGetters('security', {
                user: 'user',
                basePath: 'basePath',
                accessList: 'access',
                grantedList:'grantedList',
                pending : 'pending',
                menuList: 'items',
                accessGranted: 'accessGranted'
            })
        },
        methods:{
            granted(name){
                return Boolean(this.grantedList) ? this.grantedList.indexOf(name)!==-1 : true;
            }
        },
        created () {
           let avatar = this.$parent.$el.attributes['data-avatar'].value,
                roles = JSON.parse(this.$parent.$el.attributes['data-roles'].value),
                userID = this.$parent.$el.attributes['data-user_id'].value;

                this.$store.dispatch('security/init', {
                    path:this.$basePath,
                    avatar: avatar,
                    roles:roles,
                    user:userID});
        }
    }
</script>

<style scoped>

</style>