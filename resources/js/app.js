/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue").default;

import { BootstrapVue, BootstrapVueIcons } from "bootstrap-vue";
import axios from "axios";
import VueAxios from "vue-axios";
import Vuetify from "../plugins/vuetify";
// import './saas/app.scss';
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
import Vuelidate from "vuelidate";
import router from "./router/admin";
import Vue from "vue";
import Vuex from "vuex";
import VuetifyMoney from "vuetify-money";
//Vue Select


Vue.use(Vuex);
Vue.use(Vuelidate);
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);
Vue.use(VueAxios, axios);
Vue.use(VuetifyMoney);
Vue.use(require('vue-moment'));

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const store = new Vuex.Store({
    state: {
        count: 1,
        biggertoken: localStorage.getItem("biggerToken"),
        // apiUrl: "http://localhost:8000/api/v1/"
        // apiUrl: "https://bigger.web.id/api/v1/"
        apiUrl: "https://bigger.genossys.com/api/v1/"
    },
    mutations: {
        // setToken(state, value) {
        //     localStorage.setItem("biggerToken", value);
        // },

        // getToken(state) {
        //     this.biggertoken = localStorage.getItem("biggerToken");
        // }
    },
    methods: {
        
    }
});
const app = new Vue({
    el: "#app",
    vuetify: Vuetify,
    store: store,
    router: router,
   
    methods: {
      getToken() {
        this.token = localStorage.getItem("biggerToken");
    },
    updateToken(newToken) {
        localStorage.setItem("biggerToken", "asu");
        this.getToken();
    }
    }
});
