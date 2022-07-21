import Vue from "vue";

import VueRouter from "vue-router";
Vue.use(VueRouter);

Vue.component(
    "root-base",
    require("../components/admin/Root.vue").default
);

const AdminBase = require("../components/admin/AdminBase.vue").default;
const Dashboard = require("../components/admin/content/DashBoard.vue").default;
const Expenses = require("../components/admin/content/Expenses.vue").default;
const Revenue = require("../components/admin/content/Revenue.vue").default;
const Pesanan = require("../components/admin/content/Pesanan.vue").default;
const JenisBarang = require("../components/admin/content/JenisBarang.vue").default;
const Pelanggan = require("../components/admin/content/Pelanggan.vue").default;
const User = require("../components/admin/content/User.vue").default;
const Login = require("../components/admin/Login.vue").default;
const LaporanPemasukan = require("../components/admin/content/LaporanPemasukan.vue").default;
const LaporanPengeluaran = require("../components/admin/content/LaporanPengeluaran.vue").default;

const routes = [

    {
        path: "/",
        name: "Root",
        component:{
            template: '<router-view />',
        },
        children:[
            {
                path: "login-page",
                name: "Login",
                component: Login
            },
            {
                path: "/admin",
                name: "Admin",
                redirect: { name: 'Dashboard' },
                component: AdminBase,
                children: [
                    {
                        path: "dashboard",
                        name: "Dashboard",
                        component: Dashboard
                    },
                    
                    {
                        path: "pemasukan",
                        name: "Revenue",
                        component:  Revenue 
                    },
                    {
                        path: "pengeluaran",
                        name: "Expenses",
                        component: Expenses
                    },
                    {
                        path: "pesanan",
                        name: "Pesanan",
                        component: Pesanan
                    },
                
                    {
                        path: "jenis-barang",
                        name: "JenisBarang",
                        component: JenisBarang
                    },
                
                    {
                        path: "pelanggan",
                        name: "Pelanggan",
                        component: Pelanggan
                    },
                
                    {
                        path: "LaporanPemasukan",
                        name: "LaporanPemasukan",
                        component: LaporanPemasukan
                    },

                    {
                        path: "LaporanPengeluaran",
                        name: "LaporanPengeluaran",
                        component: LaporanPengeluaran
                    },

                    {
                        path: "user",
                        name: "User",
                        component: User
                    }
                ]
            },
        ]
    },
    
    
];

const router = new VueRouter({
    mode: "history",
    linkActiveClass: "active",
    routes
});

export default router;
