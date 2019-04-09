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

    @keyframes slideIn{
        from{transform: rotateY(90deg);}
        to{transform: rotateY(0deg);}
    }

    @keyframes slideOut{
        from{transform: rotateY(0deg);}
        to{transform: rotateY(90deg);}
    }
    .submenu .nav-item{
        transition: background 0.3s, color 0.3s;
    }

    .active {
        background-color: #4980b9; }

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

    .loading2 {
        background-color: #D2E0E6;
        content: "";
        position: absolute;
        z-index: 1000000;
        width: 100%;
        height: 100%;
        opacity: 0.4;
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