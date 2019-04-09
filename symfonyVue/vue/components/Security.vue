<template>
    <div style="margin-top: 5px;">
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
        <el-container :class="loaderShow" style="height: 100vh; overflow: auto;padding: 1px 0;margin-bottom: 10vh;">
            <el-main>
                <el-header>
                    <h4>Права доступа к модулям системы</h4>
                </el-header>
                <access-component
                        v-for="(module, i) in accessList"
                        :key="module.id"
                        :module="module"
                        :roles = "roles"
                >

            </access-component>

            </el-main>
            <el-footer style="height: 10vh">Copyright Perfecta</el-footer>
        </el-container>
    </div>
</template>

<script>
    import AccessComponent from './security/AccessComponent.vue';
    import {mapGetters} from 'vuex';
    import {mapActions} from 'vuex';
    import axios from 'axios';

    export default {
        components: {
            AccessComponent
        },
        name: "Security",
        data() {
            return {

            }
        },
        methods: {
            ...mapActions('security', {

            })
        },
        computed: {
            ...mapGetters('security', {
                basePath: 'basePath',
                accessList: 'access',
                roles:'roles',
                pending:'pending'
            }),
            loaderShow() {
                return this.pending ? 'loading2' : ''
            }
        }
    }
</script>

<style scoped>

    .el-select .tags__select {
        display: inline-flex;
        width: 200px;
    }

    .sub-menu-wrap {
        margin: 2px;
    }

    .btn-success {
        background: #4ebc00;
        color: white !important;
    }

    .slide-move {
        transition: transform 1s;
    }

    .slide-enter {
        animation: slideIn 0.9s;
    }

    .slide-enter-active {
        animation: slideIn 0.9s;
    }

    .slide-enter-to {

    }

    .slide-leave {
        animation: slideIn 0.9s;
    }

    .slide-leave-active {
        animation: slideOut 0.9s;
    }

    .slide-leave-to {

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

    .submenu .nav-item {
        transition: background 0.3s, color 0.3s;
    }

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