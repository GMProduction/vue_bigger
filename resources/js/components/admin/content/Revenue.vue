<template>
    <div class="home">
        <AlertSuccess ref="alertSuccess" :message="alertMessage"></AlertSuccess>

        <v-app>
            <v-row>
                <v-col cols="8">
                    <h5>Pesanan yang belum lunas</h5>

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
                        <div v-else>
                            <v-data-table
                                :headers="headersPesanan"
                                :items="tablePesanan"
                            >
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
                                            gotoWa(
                                                item.pelanggan.no_telp,
                                                item.pelanggan.nama_pelanggan
                                            )
                                        "
                                    >
                                        <v-icon dark>
                                            mdi-whatsapp
                                        </v-icon>
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
                                        <v-icon dark>
                                            mdi-cash-multiple
                                        </v-icon>
                                    </v-btn>
                                </template>
                                <template v-slot:body.append></template>
                            </v-data-table>
                        </div>
                    </div>
                </v-col>

                <v-col cols="4">
                    <h5>Data Pembayaran</h5>

                    <div>
                        <v-menu
                            ref="menu"
                            v-model="menu"
                            :close-on-content-click="false"
                            :return-value.sync="lapTanggal"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="lapTanggal"
                                    label="Tanggal"
                                    append-icon="mdi-calendar"
                                    readonly
                                    dense
                                    class="rounded-lg"
                                    outlined
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="lapTanggal"
                                no-title
                                scrollable
                                range
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
                                    @click="
                                        $refs.menu.save(lapTanggal);
                                        loadDataPembayaran();
                                    "
                                >
                                    OK
                                </v-btn>
                            </v-date-picker>
                        </v-menu>
                    </div>
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
                        <div v-else>
                            <v-data-table
                                :headers="headersPembayaran"
                                :items="tablePembayaran"
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

                                <template v-slot:item.nominal="{ item }">
                                    {{ formatPrice(item.nominal) }}
                                </template>

                                <template v-slot:item.actions="{ item }">
                                    <v-btn
                                        x-small
                                        class="mx-1"
                                        fab
                                        dark
                                        color="warning"
                                        @click="
                                            generateReport(
                                                item.id,
                                                item.id_pesanan,
                                                item.nominal,
                                                item.created_at
                                            )
                                        "
                                    >
                                        <v-icon dark>mdi-printer</v-icon>
                                    </v-btn>

                                    <v-btn
                                        x-small
                                        class="mx-1"
                                        fab
                                        dark
                                        color="red"
                                        @click="
                                            showHapusModal(
                                                item.id,
                                                item.id_pesanan
                                            )
                                        "
                                    >
                                        <v-icon dark>
                                            mdi-delete-outline
                                        </v-icon>
                                    </v-btn>
                                </template>
                                <template v-slot:body.append></template>
                            </v-data-table>

                            <div class="mt-5">
                                <vuetify-money
                                    dense
                                    v-model="totalPembayaran"
                                    class="rounded-lg"
                                    outlined
                                    label="Total"
                                    disabled
                                    v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
                                    v-bind:options="options"
                                >
                                    {{ totalPembayaran }}
                                </vuetify-money>
                            </div>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-app>

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

        <b-modal ref="modal-hapus" hide-footer :title="judulModal">
            <v-app>
                <p>
                    Apa kamu yakin ingin menghapus data ?
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
                                @click.native="hapusData()"
                            ></Button>

                            <Button
                                text="Tidak"
                                :bg-color="'bg-danger'"
                                :color="'text-white'"
                                @click.native="hideHapusModal()"
                            ></Button>
                        </div>
                    </div>
                </div>
            </v-app>
        </b-modal>

        <vue-html2pdf
            :show-layout="false"
            :float-layout="true"
            :enable-download="true"
            :preview-modal="true"
            :paginate-elements-by-height="1400"
            :filename="namaKwitansi"
            :pdf-quality="2"
            :manual-pagination="false"
            pdf-format="a4"
            pdf-orientation="portrait"
            pdf-content-width="800px"
            @hasStartedGeneration="hasStartedGeneration()"
            @hasGenerated="hasGenerated($event)"
            ref="html2Pdf"
        >
            <section slot="pdf-content">
                <v-app>
                    <div>
                        <img :src="'/img/kop.jpg'" class="w-100" />
                    </div>
                    <div
                        style="margin-left: 30px; margin-right: 50px; margin-top: 30px;"
                    >
                        <v-row>
                            <v-col cols="3">
                                <p>Nomor</p>
                                <v-text-field
                                    dense
                                    class="rounded-lg"
                                    :value="id"
                                ></v-text-field>
                            </v-col>

                            <v-col cols="3">
                                <p>Tanggal</p>
                                <v-text-field
                                    dense
                                    class="rounded-lg"
                                    :value="kTanggal"
                                ></v-text-field>
                            </v-col>

                            <v-col cols="4" offset="2">
                                <p>Telah Terima Dari</p>
                                <v-text-field
                                    dense
                                    class="rounded-lg"
                                    :value="kTerimaDari"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="4">
                                <p>Uang Sejumlah</p>
                                <v-text-field
                                    dense
                                    class="rounded-lg"
                                    :value="'Rp ' + formatPrice(pemNominal)"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-card class="rounded-lg p-3 mt-5" outlined>
                            <v-data-table
                                :headers="headersKwitansi"
                                :items="tabelKwitansi"
                                hide-default-footer
                            ></v-data-table>
                        </v-card>

                        <v-row class="mt-3">
                            <v-col cols="4">
                                <p
                                    v-if="kStatus == 'Lunas'"
                                    class="mr-2 text-success"
                                    style="margin-bottom: 0;"
                                >
                                    {{ kStatus }}
                                </p>

                                <p
                                    v-else
                                    class="mr-2"
                                    style="margin-bottom: 0;"
                                >
                                    {{ kStatus }}
                                </p>
                            </v-col>

                            <v-col cols="2" offset="4">
                                <p
                                    class="text-right mr-2"
                                    style="margin-bottom: 0;"
                                >
                                    Sub Total
                                </p>
                            </v-col>

                            <v-col cols="2">
                                <v-text-field
                                    dense
                                    reverse
                                    class="rounded-lg text-right"
                                    :value="'Rp ' + formatPrice(kSubTotal)"
                                    hide-details="true"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="2" offset="8">
                                <p
                                    class="text-right mr-2"
                                    style="margin-bottom: 0;"
                                >
                                    Diskon
                                </p>
                            </v-col>

                            <v-col cols="2">
                                <v-text-field
                                    dense
                                    reverse
                                    class="rounded-lg text-right"
                                    :value="'Rp ' + formatPrice(kDiskon)"
                                    hide-details="true"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="2" offset="8">
                                <p
                                    class="text-right mr-2"
                                    style="margin-bottom: 0;"
                                >
                                    Total
                                </p>
                            </v-col>

                            <v-col cols="2">
                                <v-text-field
                                    dense
                                    reverse
                                    class="rounded-lg text-right"
                                    :value="'Rp ' + formatPrice(kTotal)"
                                    hide-details="true"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </div>
                </v-app>
            </section>
        </vue-html2pdf>
    </div>
</template>

<script>
import Button from "./component/Button.vue";
import { validationMixin } from "vuelidate";
import { required, maxLength, numeric } from "vuelidate/lib/validators";
import AlertSuccess from "./component/alert/AlertSuccess.vue";
import VueHtml2pdf from "vue-html2pdf";

export default {
    mixins: [validationMixin],

    validations: {
        pemNominal: { required, maxLength: maxLength(11), numeric }
    },

    components: {
        Button,
        AlertSuccess,
        VueHtml2pdf
    },

    mounted() {
        this.loadDataPesanan();
        this.loadDataPembayaran();
    },

    data() {
        return {
            //PEMBAYARAN
            pemNominal: null,
            tablePembayaran: [],
            inputanPembayaran: {},
            inputanHapus: {},
            lapTanggal: null,
            menu: false,
            inputPencarian: {},
            totalPembayaran: null,

            //PESANAN
            tablePesanan: [],
            idPesanan: null,
            pesNamaPelanggan: null,
            pesTotalBiaya: null,
            pesKekurangan: null,

            //KERANJANG
            tabelKeranjang: [],

            //KWITANSI
            tabelKwitansi: [],
            kTanggal: null,
            kTerimaDari: null,
            kSubTotal: null,
            kDiskon: null,
            kTotal: null,

            kStatus: null,

            //ETC
            id: null,
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
            namaKwitansi: null,

            valueWhenIsEmpty: "",
            options: {
                locale: "id-ID",
                length: 11,
                precision: 0
            }

            //TEST
        };
    },

    computed: {
        headersPesanan() {
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
                    text: "Kekurangan",
                    value: "kekurangan"
                },
                {
                    text: "Actions",
                    value: "actions",
                    sortable: false,
                    align: "center"
                }
            ];
        },

        headersPembayaran() {
            return [
                // {
                //     text: "Nama Pelanggan",
                //     align: "start",
                //     sortable: true,
                //     value: "pelanggan.nama_pelanggan"
                // },
                {
                    text: "Nama",
                    value: "pesanan.pelanggan.nama_pelanggan"
                },
                {
                    text: "Nominal",
                    value: "nominal"
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

        headersKwitansi() {
            return [
                {
                    text: "Deskripsi",
                    value: "deskripsi"
                },

                {
                    text: "Qty",
                    value: "qty"
                },

                {
                    text: "Biaya Tambahan",
                    value: "biayaTambahan"
                },

                {
                    text: "Total",
                    value: "total"
                }
            ];
        }
    },

    methods: {
        //ETC

        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace(".", ",");
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        generateReport(id, idPesanan, pemNominal, kTanggal) {
            let tempTableKeranjang = [];
            this.id = id;
            this.idPesanan = idPesanan;
            this.pemNominal = pemNominal;
            // this.kTanggal = kTanggal

            const months = [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember"
            ];
            let current_datetime = new Date(kTanggal);
            this.kTanggal =
                current_datetime.getDate() +
                " " +
                months[current_datetime.getMonth()] +
                " " +
                current_datetime.getFullYear();
            // this.kTanggal = new Date(kTanggal).toString("MMMM yyyy");

            this.namaKwitansi = "kwitansi bigger " + id;
            this.axios
                .get(
                    this.$store.state.apiUrl +
                        "pesanan/showPesananID/" +
                        idPesanan,
                    {
                        headers: {
                            Authorization:
                                "Bearer " + this.$store.state.biggertoken
                        }
                    }
                )
                .then(response => {
                    $.each(response.data.data, function(
                        dataKeranjang,
                        valueDataKeranjang
                    ) {
                        let eachITem = valueDataKeranjang;
                        let catatan = " ";

                        if (valueDataKeranjang.catatan != null) {
                            catatan = valueDataKeranjang.catatan;
                        }

                        let deskripsi =
                            valueDataKeranjang.jenis_barang.nama +
                            " (" +
                            valueDataKeranjang.ukuran +
                            " " +
                            valueDataKeranjang.jenis_barang.satuan +
                            ") " +
                            catatan;
                        eachITem["deskripsi"] = deskripsi;
                        tempTableKeranjang.push(eachITem);
                    });
                    this.tabelKwitansi = tempTableKeranjang;

                    console.log("tabelKwitansi ", response.data);
                    this.kSubTotal = response.data.subTotal;
                    this.kDiskon = response.data.dataPesanan.diskon;
                    this.kTotal = response.data.dataPesanan.total_biaya;
                    this.kTerimaDari =
                        response.data.dataPesanan.pelanggan.nama_pelanggan;

                    if (response.data.dataPesanan.status_bayar == "lunas") {
                        this.kStatus = "Lunas";
                    } else {
                        this.kStatus = "Belum Lunas";
                    }

                    this.$refs.html2Pdf.generatePdf();
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
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

        //PESANAN
        loadDataPesanan() {
            let tempTablePesanan = [];

            this.axios
                .get(this.$store.state.apiUrl + "pesanan/showPembayaran", {
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

                    this.tablePesanan = tempTablePesanan;
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => (this.loading = false));
        },

        loadDataPembayaran() {
            this.inputPencarian.lapTanggal = this.lapTanggal;

            this.axios
                .post(
                    this.$store.state.apiUrl + "pembayaran/pencarian",
                    this.inputPencarian,
                    {
                        headers: {
                            Authorization:
                                "Bearer " + this.$store.state.biggertoken
                        }
                    }
                )
                .then(response => {
                    console.log(response);
                    this.tablePembayaran = response.data.pembayaran;
                    this.totalPembayaran = response.data.total;
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
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
            this.loadDataPesanan();
            this.loadDataPembayaran();
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

        showHapusModal(id, idPesanan) {
            this.id = id;
            this.idPesanan = idPesanan;

            console.log("showHapus", id, " ", idPesanan);
            this.judulModal = "Hapus Data !";
            this.inputError = null;
            this.$refs["modal-hapus"].show();
        },

        hideHapusModal() {
            this.$refs["modal-hapus"].hide();
        },

        hapusData() {
            this.inputLoading = true;
            this.inputError = null;

            let tempUrl = "pembayaran/delete/";

            this.axios
                .delete(
                    this.$store.state.apiUrl +
                        tempUrl +
                        this.id +
                        "/" +
                        this.idPesanan,
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
                            this.hideHapusModal();
                            this.loadDataPesanan();
                            this.loadDataPembayaran();
                            this.$refs.alertSuccess.showAlert();
                        } else {
                            // throw error and go to catch block
                            this.inputError = "Gagal Hapus data";
                        }
                    }
                    // console.log(response.data)
                )
                .catch(error => {
                    this.inputLoading = false;
                    this.inputError = "Gagal Hapus data";
                })
                .finally(() => {
                    this.inputLoading = false;
                    id = null;
                });
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
        //KERANJANG
        loadDataKeranjang() {
            let tempUrl = "keranjang/showKeranjangbyID/" + this.idPesanan;

            this.axios
                .get(this.$store.state.apiUrl + tempUrl, {
                    headers: {
                        Authorization: "Bearer " + this.$store.state.biggertoken
                    }
                })
                .then(response => {
                    console.log("data keranjang", response.data["data"]);
                    this.tabelKeranjang = response.data["data"];
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => (this.loading = false));
        }
    }
};
</script>
