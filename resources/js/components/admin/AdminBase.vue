<template>
    <div>
        <div class="d-flex">
            <div :class="{ sidebar: true, isOpen: isOpen }">
                <div class="d-flex">
                    <div :class="{ header: true, isOpen: isOpen }">
                        <a class="action-header" v-on:click="toggleMenu">
                            <span
                                v-if="isOpen"
                                class="mdi mdi-chevron-left"
                            ></span>
                            <span v-else class="mdi mdi-chevron-right"></span>
                        </a>
                        <h1 class="header-text">Bigger</h1>
                    </div>
                </div>

                <div class="menu">
                    <router-link
                        :to="{ name: 'Dashboard' }"
                        class="menu-item"
                        @click.native="
                            changeTitle(
                                'Hi, Have a nice day :)'
                            )
                        "
                    >
                        <span class="mdi mdi-home-outline mr-2"></span>
                        <span class="menu-text">Dashboard</span>
                    </router-link>

                    <a
                        class="menu-item"
                        v-b-toggle.collapse-2
                        @click="toggleCollapseMaster"
                    >
                        <span
                            v-if="collapseMasterIsOpen"
                            class="mdi mdi-chevron-up mr-2"
                        ></span>
                        <span v-else class="mdi mdi-chevron-down mr-2"></span>
                        <span class="menu-text">Master</span>
                    </a>

                    <b-collapse id="collapse-2" accordion>
                        <router-link
                            :to="{ name: 'JenisBarang' }"
                            class="menu-item"
                            @click.native="changeTitle('Jenis Bahan')"
                        >
                            <span
                                class="mdi mdi-file-document-outline mr-2"
                            ></span>
                            <span class="menu-text">Jenis Bahan</span>
                        </router-link>

                        <router-link
                            :to="{ name: 'Pelanggan' }"
                            class="menu-item"
                            @click.native="changeTitle('Pelanggan')"
                        >
                            <span
                                class="mdi mdi-file-account-outline mr-2"
                            ></span>
                            <span class="menu-text">Pelanggan</span>
                        </router-link>

                        <!-- <router-link
                            :to="{ name: 'User' }"
                            class="menu-item"
                            @click.native="changeTitle('User')"
                        >
                            <span class="mdi mdi-account-outline mr-2"></span>
                            <span class="menu-text">User</span>
                        </router-link> -->
                    </b-collapse>

                    <router-link
                        :to="{ name: 'Pesanan' }"
                        class="menu-item"
                        @click.native="changeTitle('Pesanan')"
                    >
                        <span
                            class="mdi mdi-clipboard-text-multiple-outline mr-2"
                        ></span>
                        <span class="menu-text">Pesanan</span>
                    </router-link>

                    <router-link
                        :to="{ name: 'Revenue' }"
                        class="menu-item"
                        @click.native="changeTitle('Revenue')"
                    >
                        <span class="mdi mdi-currency-usd mr-2"></span>
                        <span class="menu-text">Pemasukan</span>
                    </router-link>
                    <router-link
                        :to="{ name: 'Expenses' }"
                        class="menu-item"
                        @click.native="changeTitle('Expenses')"
                    >
                        <span class="mdi mdi-cart-outline mr-2"></span>
                        <span class="menu-text">Pengeluaran</span>
                    </router-link>

                     <a
                        class="menu-item"
                        v-b-toggle.collapse-3
                        @click="toggleCollapseLaporan"
                    >
                        <span
                            v-if="collapseLaporanIsOpen"
                            class="mdi mdi-chevron-up mr-2"
                        ></span>
                        <span v-else class="mdi mdi-chevron-down mr-2"></span>
                        <span class="menu-text">Laporan</span>
                    </a>

                    <b-collapse id="collapse-3" accordion>
                        <router-link
                            :to="{ name: 'LaporanPemasukan' }"
                            class="menu-item"
                            @click.native="changeTitle('Laporan Pemasukan')"
                        >
                            <span
                                class="mdi mdi-file-document-outline mr-2"
                            ></span>
                            <span class="menu-text">Pemasukan</span>
                        </router-link>

                        <router-link
                            :to="{ name: 'LaporanPengeluaran' }"
                            class="menu-item"
                            @click.native="changeTitle('Pelanggan')"
                        >
                            <span
                                class="mdi mdi-file-account-outline mr-2"
                            ></span>
                            <span class="menu-text">Pengeluaran</span>
                        </router-link>

                   
                    </b-collapse>
                </div>
            </div>

            <div style="width: 100%;">
                <div class="toolbar">
                    <div class="welcome">
                        <h1>{{ title }}</h1>
                    </div>
                    <div class="flex-spacer"></div>

                    <b-dropdown class="mr-2" right>
                        <template #button-content>
                            <b-avatar class="profil"></b-avatar>
                        </template>
                        <b-dropdown-item class="danger" @click="logOut()"
                            >Logout</b-dropdown-item
                        >
                    </b-dropdown>
                </div>
                <router-view class="my-3" />
            </div>
        </div>

        <div class="fixed-bottom m-2" style="font-size: 12px">&copy; genossys 2021</div>
    </div>
</template>

<script>
import Expenses from "./content/Expenses.vue";
import Revenue from "./content/Revenue.vue";
import Reminders from "./content/Pesanan.vue";

export default {
    props: {
        msg: String
    },
    data() {
        return {
            isOpen: true,
            title: "Dashboard",
            collapseMasterIsOpen: false,
            collapseLaporanIsOpen: false,
            token: ""
        };
    },
    methods: {
        toggleMenu: function() {
            this.isOpen = !this.isOpen;
        },
        changeTitle: function(title) {
            this.title = title;
        },
        toggleCollapseMaster: function() {
            this.collapseMasterIsOpen = !this.collapseMasterIsOpen;
        },
        toggleCollapseLaporan: function() {
            this.collapseLaporanIsOpen = !this.collapseLaporanIsOpen;
        },
        getToken() {
            this.token = localStorage.getItem("biggerToken");
        },
        removeToken() {
            localStorage.removeItem("biggerToken");
        },
        logOut() {
            this.removeToken();
            this.$store.state.biggertoken = null;
            this.$router.push({ name: "Login" });
            alert("logout");
        }
    },
    components: {
        Expenses,
        Revenue,
        Reminders
    },

    mounted() {
        if (localStorage.getItem("biggerToken") == null) {
            // alert('bener');
            this.$router.push({ name: "Login" });
        } else {
            //   alert(localStorage.getItem("biggerToken"));
        }
    }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
