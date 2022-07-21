<template>
    <div class="dashboard">
        <AlertSuccess ref="alertSuccess" :message="alertMessage"></AlertSuccess>

        <section class="d-flex flex-wrap">
            <div class="gen-card">
                <div class="header">
                    <p class="mb-0">Pemasukan</p>

                    <b-dropdown text="Bulan" class="mr-2" right> </b-dropdown>
                </div>

                <div class="body">
                    <p>Rp {{ formatPrice(hasilPemasukan) }}</p>
                </div>
            </div>

            <div :class="colorPengeluaran">
                <div class="header">
                    <p class="mb-0">Pengeluaran</p>

                    <b-dropdown text="Bulan" class="mr-2" right> </b-dropdown>
                </div>

                <div class="body">
                    <p>Rp {{ formatPrice(hasilPengeluaran) }}</p>
                </div>
            </div>
        </section>

        <section class="reminder mt-5">
            <p class="font-weight-bold">Pengingat</p>
            <div class="card-table">
                <div v-if="loading" class="text-center m-5">
                    <b-spinner
                        variant="primary"
                        label="Text Centered"
                        class="mb-3"
                    ></b-spinner>
                    <p>Tunggu Sebentar Ya...</p>
                </div>
                <div v-else-if="errored">
                    <p>Data tidak bisa di muat...</p>
                </div>
                <!-- <datatable v-else :columns="columns" :data="items"></datatable> -->
                <!-- <b-table v-else striped hover :items="items" borderless></b-table> -->
                <v-app v-else>
                    <v-data-table
                        :headers="headers"
                        :items="items"
                        :search="search"
                        :custom-filter="filterOnlyCapsText"
                    >
                        <template v-slot:item.deadline="{ item }">
                            <v-chip :color="getColor(item.deadline)" dark>
                                {{
                                    item.deadline | moment("DD-MM-YYYY")
                                }}
                            </v-chip>
                        </template>
                        <template v-slot:top>
                            <v-text-field
                                v-model="search"
                                label="Cari disini (huruf kecil)"
                                class="mx-4"
                            ></v-text-field>
                        </template>
                        <template v-slot:item.total_biaya="{ item }">
                            {{ formatPrice(item.total_biaya) }}
                        </template>
                        <template v-slot:item.kekurangan="{ item }">
                            {{ formatPrice(item.kekurangan) }}
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-btn
                                x-small
                                class="mx-1"
                                fab
                                dark
                                color="success"
                                @click="
                                    gotoWa(item.pelanggan.no_telp, item.pelanggan.nama_pelanggan)
                                "
                            >
                                <v-icon dark> mdi-whatsapp </v-icon>
                            </v-btn>

                            <v-btn
                                v-if="item.status_proses == 'menunggu'"
                                x-small
                                class="mx-1"
                                fab
                                dark
                                color="success"
                                @click="
                                    showStatusModal(
                                        item.id,
                                        'selesai',
                                        'Pesanan Selesai',
                                        'Apakah pekerjaan ini sudah selesai ?',
                                        'pesanan'
                                    )
                                "
                            >
                                <v-icon dark> mdi-check </v-icon>
                            </v-btn>

                            <v-btn
                                v-else
                                x-small
                                class="mx-1"
                                fab
                                dark
                                color="red"
                                @click="
                                    showStatusModal(
                                        item.id,
                                        'menunggu',
                                        'Pesanan Belum Selesai',
                                        'Apakah pekerjaan belum sudah selesai ?',
                                        'pesanan'
                                    )
                                "
                            >
                                <v-icon dark> mdi-close </v-icon>
                            </v-btn>

                            <v-btn
                                x-small
                                class="mx-1"
                                fab
                                dark
                                color="info"
                                @click="
                                    showModalTambahPembayaran(
                                        item.id,
                                        item.pelanggan.nama_pelanggan,
                                        item.total_biaya,
                                        item.kekurangan
                                    )
                                "
                            >
                                <v-icon dark> mdi-cash-multiple </v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                class="mx-1"
                                fab
                                dark
                                color="warning"
                                @click="
                                    showEditModalPesanan(
                                        item.id,
                                        item.id_pelanggan,
                                        item.nama_pelanggan,
                                        item.deadline,
                                        item.catatan,
                                        item.sub_total,
                                        item.diskon,
                                        item.total_biaya
                                    )
                                "
                            >
                                <v-icon dark>
                                    mdi-clipboard-text-outline
                                </v-icon>
                            </v-btn>
                        </template>
                        <template v-slot:body.append></template>
                    </v-data-table>
                </v-app>
            </div>
        </section>

        <b-modal
            no-close-on-backdrop
            ref="add-pembayaran-modal"
            size="xl"
            hide-footer
            :title="judulModal"
        >
            <v-app>
                <v-row>
                    <v-col cols="8">
                        <v-card outlined class="rounded-xl p-3">
                            <p>Barang yang di pesan</p>

                            <v-data-table
                                :headers="headersKeranjang"
                                :items="tabelKeranjang"
                            >
                                <template v-slot:item.total="{ item }">
                                    {{ formatPrice(item.total) }}
                                </template>
                            </v-data-table>
                        </v-card>
                    </v-col>

                    <v-col cols="4">
                        <form @submit.prevent="inputPembayaran">
                            <v-card class="rounded-xl p-3">
                                <p>Pembayaran</p>
                                <v-text-field
                                    dense
                                    v-model="pesNamaPelanggan"
                                    class="rounded-lg"
                                    outlined
                                    label="Nama Pelanggan"
                                    disabled
                                >
                                    {{ pesNamaPelanggan }}
                                </v-text-field>

                                <vuetify-money
                                    dense
                                    v-model="pesTotalBiaya"
                                    class="rounded-lg"
                                    outlined
                                    disabled
                                    label="Total"
                                    v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
                                    v-bind:options="options"
                                >
                                    {{ pesTotalBiaya }}
                                </vuetify-money>

                                <vuetify-money
                                    dense
                                    v-model="pesKekurangan"
                                    class="rounded-lg"
                                    outlined
                                    disabled
                                    label="Kekurangan"
                                    v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
                                    v-bind:options="options"
                                >
                                    {{ pesKekurangan }}
                                </vuetify-money>

                                <vuetify-money
                                    dense
                                    v-model="pemNominal"
                                    class="rounded-lg"
                                    :counter="11"
                                    outlined
                                    type="number"
                                    label="Nominal Bayar"
                                    v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
                                    v-bind:options="options"
                                >
                                    {{ pemNominal }}
                                </vuetify-money>
                            </v-card>

                            <div class="mt-3 d-flex flex-row">
                                <div class="flex-grow-1"></div>

                                <div v-if="inputLoading">
                                    <v-progress-circular
                                        indeterminate
                                        color="primary"
                                    ></v-progress-circular>
                                </div>

                                <div v-else class="w-100">
                                    <v-alert
                                        v-show="inputError"
                                        :value="true"
                                        color="error"
                                        icon="mdi mdi-alert-outline"
                                        class="rounded-xl"
                                        outlined
                                    >
                                        {{ inputError }}
                                    </v-alert>

                                    <div class="mt-3 d-flex flex-row">
                                        <div class="flex-grow-1"></div>
                                        <Button
                                            type="submit"
                                            :icondepan="
                                                'mdi-content-save-outline'
                                            "
                                            text="Simpan"
                                            :bg-color="'bg-success'"
                                            :color="'text-white'"
                                        ></Button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </v-col>
                </v-row>
            </v-app>
        </b-modal>

        <b-modal ref="modal-status" hide-footer :title="judulModal">
            <v-app>
                <p>
                    {{ msgModal }}
                </p>

                <div class="mt-3 d-flex flex-row">
                    <div class="flex-grow-1"></div>

                    <div v-if="inputLoading">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                        ></v-progress-circular>
                    </div>

                    <div v-else class="w-100">
                        <v-alert
                            v-show="inputError"
                            :value="true"
                            color="error"
                            icon="mdi mdi-alert-outline"
                            class="rounded-xl"
                            outlined
                        >
                            {{ inputError }}
                        </v-alert>

                        <div class="mt-3 d-flex flex-row">
                            <div class="flex-grow-1"></div>
                            <Button
                                class="mr-3"
                                text="Ya"
                                :bg-color="'bg-success'"
                                :color="'text-white'"
                                @click.native="UpdateStatusKeranjang()"
                            ></Button>

                            <Button
                                text="Tidak"
                                :bg-color="'bg-danger'"
                                :color="'text-white'"
                                @click.native="hideStatusModal()"
                            ></Button>
                        </div>
                    </div>
                </div>
            </v-app>
        </b-modal>

        <b-modal
            no-close-on-backdrop
            ref="add-pesanan-modal"
            size="xl"
            hide-footer
            :title="judulModal"
        >
            <div v-if="loading" class="text-center m-5">
                <b-spinner
                    variant="primary"
                    label="Text Centered"
                    class="mb-3"
                ></b-spinner>
                <p>Tunggu Sebentar Ya...</p>
            </div>
            <div v-else-if="errored">
                <p>Data tidak bisa di muat...</p>
            </div>

            <v-app v-else>
                <v-card outlined class="rounded-xl mb-5">
                    <v-data-table
                        :headers="headersKeranjangp"
                        :items="tabelKeranjang"
                        :search="search"
                        :custom-filter="filterOnlyCapsText"
                    >
                        <template v-slot:top>
                            <v-text-field
                                v-model="search"
                                label="Cari disini (huruf kecil)"
                                class="mx-4"
                            ></v-text-field>
                        </template>

                        <template v-slot:item.total="{ item }">
                            {{ formatPrice(item.total) }}
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-btn
                                v-if="item.status_pengerjaan == 'menunggu'"
                                x-small
                                class="mx-1"
                                fab
                                dark
                                color="success"
                                @click="
                                    showStatusModal(
                                        item.id,
                                        'selesai',
                                        'Pesanan Selesai',
                                        'Apakah pekerjaan ini sudah selesai ?',
                                        'keranjang'
                                    )
                                "
                            >
                                <v-icon dark>
                                    mdi-check
                                </v-icon>
                            </v-btn>

                            <v-btn
                                v-else
                                x-small
                                class="mx-1"
                                fab
                                dark
                                color="red"
                                @click="
                                    showStatusModal(
                                        item.id,
                                        'menunggu',
                                        'Pesanan Belum Selesai',
                                        'Apakah pekerjaan belum sudah selesai ?',
                                        'keranjang'
                                    )
                                "
                            >
                                <v-icon dark>
                                    mdi-close
                                </v-icon>
                            </v-btn>
                        </template>
                        <template v-slot:body.append></template>
                    </v-data-table>
                </v-card>
                <form @submit.prevent="InputDatapesanan">
                    <v-row>
                        <v-col cols="12" md="8">
                            <v-text-field
                                outlined
                                dense
                                v-model="pPelanggan"
                                disabled
                                class="rounded-lg mr-2"
                                item-text="nama_pelanggan"
                                item-value="id"
                                label="Pelanggan"
                                >{{ pPelanggan }}</v-text-field
                            >

                            <v-menu
                                ref="menu"
                                v-model="menu"
                                :close-on-content-click="false"
                                :return-value.sync="pesDeadline"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="pesDeadline"
                                        label="Deadline"
                                        append-icon="mdi-calendar"
                                        readonly
                                        dense
                                        class="rounded-lg"
                                        outlined
                                        disabled
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="pesDeadline"
                                    no-title
                                    scrollable
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="menu = false"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.menu.save(pesDeadline)"
                                    >
                                        OK
                                    </v-btn>
                                </v-date-picker>
                            </v-menu>

                            <v-textarea
                                disabled
                                dense
                                v-model="pesCatatan"
                                class="rounded-lg"
                                :counter="255"
                                outlined
                                label="Catatan"
                            >
                                {{ pesCatatan }}
                            </v-textarea>
                        </v-col>

                        <v-col cols="12" md="4">
                            <v-card class="rounded-xl p-3">
                                <p>Total Biaya</p>
                                <v-text-field
                                    dense
                                    v-model="pesSubTotal"
                                    class="rounded-lg"
                                    outlined
                                    label="Sub Total"
                                    disabled
                                >
                                    {{ pesSubTotal }}
                                </v-text-field>

                                <vuetify-money
                                    dense
                                    v-model="pesDiskon"
                                    class="rounded-lg"
                                    :counter="11"
                                    outlined
                                    disabled
                                    type="number"
                                    label="Diskon"
                                    v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
                                    v-bind:options="options"
                                    @blur="hitungTotalPesanan"
                                >
                                    {{ pesDiskon }}
                                </vuetify-money>

                                <v-text-field
                                    dense
                                    v-model="pesTotal"
                                    class="rounded-lg"
                                    outlined
                                    disabled
                                    label="Total"
                                >
                                    {{ pesTotal }}
                                </v-text-field>
                            </v-card>
                        </v-col>
                    </v-row>
                </form>
            </v-app>
        </b-modal>
    </div>
</template>

<script>
// @ is an alias to /src

import Button from "./component/Button.vue";
import { validationMixin } from "vuelidate";
import { required, maxLength, numeric } from "vuelidate/lib/validators";
import AlertSuccess from "./component/alert/AlertSuccess.vue";
import moment from "moment";

export default {
    components: {
        Button,
        AlertSuccess
    },

    data() {
        return {
            //PEMBAYARAN
            pemNominal: null,
            tablePembayaran: [],
            inputanPembayaran: {},
            inputanHapus: {},

            idPesanan: null,
            menu: false,
            idPelanggan: null,
            namaPelanggan: null,
            pesDeadline: null,
            pesSubTotal: null,
            pesDiskon: null,
            pesTotal: null,
            pesCatatan: null,
            no_telp: "",
            alamat: "",
            items: [],
            tabelKeranjang: [],
            inputan: {},
            inputanKeranjang: {},
            kekurangan: null,
            pesNamaPelanggan: null,
            pesTotalBiaya: null,
            pesKekurangan: null,
            hasilPemasukan: null,
            hasilPengeluaran: null,
            colorPengeluaran: "gen-card ",
            colorPemasukan: "gen-card ",

            // KERANJANG
            idKeranjang: null,
            itemsKeranjang: [],
            kJenisBarang: null,
            kNamaJenisBarang: null,
            kUkuran: 0,
            kQty: 0,
            kTotal: 0,
            kBiayaTambahan: 0,
            kCatatan: "",

            // JENISBARANG
            jNama: null,
            jSatuan: null,
            jHarga: null,
            inputanJenisBarang: {},
            itemsJenisBarang: [],
            modelJenisBarang: null,
            satuan: "",

            // PELANGGAN
            itemsPelanggan: [],
            modelPelanggan: null,
            pPelanggan: null,
            pNama: null,
            pNotelp: null,
            pAlamat: null,
            inputanPelanggan: {},

            //ETC
            loading: true,
            inputLoading: false,
            errored: false,
            inputError: null,
            show: true,
            showAlert: false,
            alertMessage: "",
            judulModal: null,
            jenisHapus: null,
            search: "",
            submitStatus: null,
            statusUp: null,
            msgModal: null,
            stateStatus: null,

            valueWhenIsEmpty: "",
            options: {
                locale: "id-ID",
                length: 11,
                precision: 0
            }
        };
    },

    mounted() {
        this.loadData();
        this.loadDataPemasukan();
        this.loadDataPengeluaran();
    },

    computed: {
        headers() {
            return [
                {
                    text: "Nama Pelanggan",
                    align: "start",
                    sortable: true,
                    value: "pelanggan.nama_pelanggan"
                },
                {
                    text: "Total Biaya",
                    value: "total_biaya"
                },
                {
                    text: "Pengerjaan",
                    value: "status_proses"
                },
                {
                    text: "Pembayaran",
                    value: "status_bayar"
                },
                {
                    text: "Kekurangan",
                    value: "kekurangan"
                },
                {
                    text: "Deadline",
                    value: "deadline"
                },
                {
                    text: "Actions",
                    value: "actions",
                    sortable: false,
                    align: "center"
                }
            ];
        },

        headersKeranjang() {
            return [
                {
                    text: "Jenis Barang",
                    value: "jenis_barang.nama"
                },
                {
                    text: "Ukuran",
                    value: "ukuran"
                },
                {
                    text: "Qty",
                    value: "qty"
                },
                {
                    text: "Status Pengerjaan",
                    value: "status_pengerjaan"
                },

                {
                    text: "Total",
                    value: "total"
                },
                {
                    text: "Catatan",
                    value: "catatan"
                }
            ];
        },

        headersKeranjangp() {
            return [
                {
                    text: "Jenis Barang",
                    value: "jenis_barang.nama"
                },
                {
                    text: "Ukuran",
                    value: "ukuran"
                },
                {
                    text: "Qty",
                    value: "qty"
                },
                {
                    text: "Status Pengerjaan",
                    value: "status_pengerjaan"
                },

                {
                    text: "Total",
                    value: "total"
                },
                {
                    text: "Catatan",
                    value: "catatan"
                },
                {
                    text: "Actions",
                    value: "actions",
                    sortable: false,
                    align: "center"
                }
            ];
        }
    },

    methods: {
        getColor(tanggal) {
            var parseDate = Date.parse(tanggal);
            var now = new Date();

            var terlewat3 = new Date();
            terlewat3.setDate(now.getDate() + 3);

            var terlewat1 = new Date();
            terlewat1.setDate(now.getDate());

            var terlewat = new Date();
            terlewat.setDate(now.getDate() - 1);

            if (parseDate < terlewat) {return "red"}
            else if (parseDate < terlewat1){ return "orange"}
            else if (parseDate < terlewat3) {return "#D5B90C"}
            else {return "green"};
        },
        showEditModalPesanan(
            idPesanan,
            pPelanggan,
            namaPelanggan,
            pesDeadline,
            pesCatatan,
            pesSubTotal,
            pesDiskon,
            pesTotal
        ) {
            this.idPesanan = idPesanan;
            this.pPelanggan = pPelanggan;
            this.namaPelanggan = namaPelanggan;
            this.pesDeadline = pesDeadline;
            this.pesCatatan = pesCatatan;
            this.pesSubTotal = pesSubTotal;
            this.pesDiskon = pesDiskon;
            this.pesTotal = pesTotal;
            this.inputError = null;
            this.modelPelanggan = pPelanggan;
            this.loadDataKeranjang();
            this.judulModal = "Edit Data ";
            this.$refs["add-pesanan-modal"].show();
        },

        hideModal() {
            this.$refs["add-pesanan-modal"].hide();
        },
        filterOnlyCapsText(value, search, item) {
            return (
                value != null &&
                search != null &&
                typeof value === "string" &&
                value
                    .toString()
                    .toLocaleLowerCase()
                    .indexOf(search) !== -1
            );
        },
        inputPembayaran() {
            if (this.pemNominal == null) {
                this.submitStatus = "ERROR";
                this.inputError = "Kamu Harus mengisi nominal";
                alert("Kamu Harus mengisi nominal");
            } else if (this.pemNominal > this.pesKekurangan) {
                this.submitStatus = "ERROR";
                this.inputError =
                    "Nominalmu yang kamu masukan lebih banyak dari kekurangan";
                alert(
                    "Nominalmu yang kamu masukan lebih banyak dari kekurangan"
                );
            } else {
                let tempUrl = "pembayaran/store";
                if (this.pemNominal == this.pesKekurangan) {
                    tempUrl = "pembayaran/storeLunas";
                    this.inputanPembayaran.status_bayar = "lunas";
                }
                this.inputLoading = true;
                this.inputError = null;

                this.inputanPembayaran.nominal = this.pemNominal;
                this.inputanPembayaran.id_pesanan = this.idPesanan;

                this.axios
                    .post(
                        this.$store.state.apiUrl + tempUrl,
                        this.inputanPembayaran,
                        {
                            headers: {
                                Authorization:
                                    "Bearer " + this.$store.state.biggertoken
                            }
                        }
                    )
                    .then(
                        response => {
                            console.log(response);

                            if (response.status === 200) {
                                this.alertMessage = response["data"]["msg"];
                                this.berhasilSimpanPembayaran();
                                this.$refs.alertSuccess.showAlert();
                            } else {
                                // throw error and go to catch block
                                this.inputError = "Gagal input data";
                            }
                        }
                        // console.log(response.data)
                    )
                    .catch(error => {
                        console.log(error);
                        this.inputLoading = false;
                        this.inputError = "Gagal input data";
                    })
                    .finally(() => {
                        this.inputLoading = false;
                    });
            }
        },
        showStatusModal(id, statusUp, judulModal, msgModal, stateStatus) {
            this.id = id;
            this.statusUp = statusUp;
            this.judulModal = judulModal;
            this.msgModal = msgModal;
            this.stateStatus = stateStatus;

            console.log("id ", id, "statusUp", statusUp);
            this.inputError = null;
            this.$refs["modal-status"].show();
        },

        hideStatusModal() {
            this.$refs["modal-status"].hide();
        },
        showModalTambahPembayaran(
            idPesanan,
            pesNamaPelanggan,
            pesTotalBiaya,
            pesKekurangan
        ) {
            this.idPesanan = idPesanan;
            this.pesNamaPelanggan = pesNamaPelanggan;
            this.pesTotalBiaya = pesTotalBiaya;
            this.pesKekurangan = pesKekurangan;

            this.loadDataKeranjang();
            this.$refs["add-pembayaran-modal"].show();
            this.judulModal = "Tambah Data";
        },

        berhasilSimpanPembayaran() {
            this.$refs["add-pembayaran-modal"].hide();
            this.pemNominal = "";
            this.loadData();
        },

        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace(".", ",");
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        UpdateStatusKeranjang() {
            let tempUrl = "keranjang/updateStatus/";
            this.inputLoading = true;
            this.inputError = null;

            this.inputanKeranjang.status_pengerjaan = this.statusUp;

            if (this.stateStatus == "pesanan") {
                tempUrl = "pesanan/updateStatus/";
            }
            this.axios
                .put(
                    this.$store.state.apiUrl + tempUrl + this.id,
                    this.inputanKeranjang,
                    {
                        headers: {
                            Authorization:
                                "Bearer " + this.$store.state.biggertoken
                        }
                    }
                )
                .then(
                    response => {
                        console.log(response);

                        if (response.status === 200) {
                            this.alertMessage = response["data"]["msg"];
                            this.hideStatusModal();
                            if (this.stateStatus == "pesanan") {
                                this.loadData();
                            } else {
                                this.loadDataKeranjang();
                            }
                            this.$refs.alertSuccess.showAlert();
                        } else {
                            // throw error and go to catch block
                            this.inputError = "Gagal input data";
                        }
                    }
                    // console.log(response.data)
                )
                .catch(error => {
                    console.log(error);
                    this.inputLoading = false;
                    this.inputError = "Gagal input data";
                })
                .finally(() => {
                    this.inputLoading = false;
                });
        },
        loadDataKeranjang() {
            let tempUrl = "keranjang/showNewKeranjang";
            if (this.idPesanan != 0) {
                tempUrl = "keranjang/showKeranjangbyID/" + this.idPesanan;
            }

            this.axios
                .get(this.$store.state.apiUrl + tempUrl, {
                    headers: {
                        Authorization: "Bearer " + this.$store.state.biggertoken
                    }
                })
                .then(response => {
                    console.log("data keranjang", response.data["data"]);
                    this.tabelKeranjang = response.data["data"];
                    this.pesSubTotal = response.data["subTotal"];
                    this.hitungTotalPesanan();
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => (this.loading = false));
        },
        hitungTotalPesanan() {
            if (this.pesDiskon == null) {
                this.pesDiskon = 0;
            }

            if (this.pesSubTotal == null) {
                this.pesSubTotal = 0;
            }

            this.pesTotal =
                parseInt(this.pesSubTotal) - parseInt(this.pesDiskon);
        },

        loadData() {
            let tempTablePesanan = [];

            this.axios
                .get(this.$store.state.apiUrl + "pesanan/showDashboard", {
                    headers: {
                        Authorization: "Bearer " + this.$store.state.biggertoken
                    }
                })
                .then(response => {
                    console.log(response);

                    $.each(response.data, function(
                        dataPesanan,
                        valueDataPesanan
                    ) {
                        let eachItem = valueDataPesanan;
                        let totalBiaya = valueDataPesanan.total_biaya;
                        let bayar = 0;
                        $.each(valueDataPesanan.pembayarans, function(
                            dataPembayaran,
                            valueDataPembayaran
                        ) {
                            bayar += valueDataPembayaran.nominal;
                        });

                        let kekurangan = totalBiaya - bayar;
                        eachItem["kekurangan"] = kekurangan;

                        console.log("this.tablePesanan2", eachItem);
                        tempTablePesanan.push(eachItem);
                    });

                    this.items = tempTablePesanan;
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => (this.loading = false));
        },

        loadDataPemasukan() {
            this.axios
                .get(
                    this.$store.state.apiUrl + "pembayaran/pemasukanDashboard",
                    {
                        headers: {
                            Authorization:
                                "Bearer " + this.$store.state.biggertoken
                        }
                    }
                )
                .then(response => {
                    let nominalNow = 0;

                    let nominalThen = 0;

                    if (response.data.length > 0) {
                        nominalNow = response.data[0]["sums"];

                        if (response.data.length > 1) {
                            nominalThen = response.data[1]["sums"];
                        }
                    }
                    let hasil = nominalNow - nominalThen;

                    this.hasilPemasukan = nominalNow;

                    if (hasil < 0) {
                        this.colorPemasukan = "gen-card bg-prim-light";
                    } else {
                        this.colorPemasukan = "gen-card";
                    }
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => (this.loading = false));
        },

        loadDataPengeluaran() {
            this.axios
                .get(
                    this.$store.state.apiUrl +
                        "pengeluaran/pengeluaranDashboard",
                    {
                        headers: {
                            Authorization:
                                "Bearer " + this.$store.state.biggertoken
                        }
                    }
                )
                .then(response => {
                    let nominalNow = 0;

                    let nominalThen = 0;

                    if (response.data.length > 0) {
                        nominalNow = response.data[0]["sums"];

                        if (response.data.length > 1) {
                            nominalThen = response.data[1]["sums"];
                        }
                    }
                    let hasil = nominalNow - nominalThen;

                    if (hasil < 0) {
                        this.colorPengeluaran = "gen-card ";
                    } else {
                        this.colorPengeluaran = "gen-card bg-prim-light";
                    }
                    this.hasilPengeluaran = nominalNow;
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => (this.loading = false));
        },
        gotoWa(noWa, namaPelanggan) {
            if (noWa == null || namaPelanggan == null) {
                alert("Whatsapp tidak tersedia pada pelanggan ini");
            } else {
                let isNol = noWa.substr(0, 1) == "0";
                console.log("nowa", isNol);
                if (isNol) {
                    noWa = noWa.replace("0", "62");
                }
                let msg = encodeURIComponent("Halo bpk/ibu " + namaPelanggan);
                window.open("https://wa.me/" + noWa + "?text=" + msg, "_blank");
            }
        },
    }
};
</script>
