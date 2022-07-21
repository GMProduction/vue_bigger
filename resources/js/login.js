/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

 require('./bootstrap')

 window.Vue = require('vue').default
 
 import VueRouter from 'vue-router'
 import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
 import axios from 'axios'
 import VueAxios from 'vue-axios'
 import Vuetify from '../plugins/vuetify'
 // import './saas/app.scss';
 import 'bootstrap/dist/css/bootstrap.css'
 import 'bootstrap-vue/dist/bootstrap-vue.css'
 import Vuelidate from 'vuelidate'
 
 Vue.use(Vuelidate)
 Vue.use(BootstrapVue)
 Vue.use(BootstrapVueIcons)
 Vue.use(VueRouter)
 Vue.use(VueAxios, axios)
 
 /**
  * The following block of code may be used to automatically register your
  * Vue components. It will recursively scan this directory for the Vue
  * components and automatically register them with their "basename".
  *
  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
  */
 
 // const files = require.context('./', true, /\.vue$/i)
 // files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
 
 Vue.component('login-page', require('./components/admin/Login.vue').default)
 
 

 /**
  * Next, we will create a fresh Vue application instance and attach it to
  * the page. Then, you may begin adding components to this application
  * or customize the JavaScript scaffolding to fit your unique needs.
  */
 
 const login = new Vue({
   el: '#login',
   vuetify: Vuetify,
  
 })
 