<template>
    <div class="home">
        <AlertSuccess ref="alertSuccess" :message="alertMessage"></AlertSuccess>

        <div class="d-flex flex-row mb-3">
            <div class="flex-grow-1"></div>

            <Button
                @click.native="showTambahData"
                :icondepan="'mdi-plus'"
                text="Tambah Data"
                :bg-color="'bg-success'"
                class="ml-2"
            ></Button>
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
            <v-app v-else>
                <v-data-table
                    :headers="headers"
                    :items="items"
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

                    <template v-slot:item.actions="{ item }">
                        <v-btn
                                    x-small
                                    class="mx-1"
                                    fab
                                    dark
                                    color="success"
                                    @click="
                                        gotoWa(
                                            item.no_telp,
                                            item.nama_pelanggan
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
                            color="warning"
                            @click="
                                showEditModal(
                                    item.id,
                                    item.nama_pelanggan,
                                    item.no_telp,
                                    item.alamat
                                )
                            "
                        >
                            <v-icon dark>
                                mdi-pencil-outline
                            </v-icon>
                        </v-btn>
                        <v-btn
                            x-small
                            class="mx-1"
                            fab
                            dark
                            color="red"
                            @click="
                                showHapusModal(item.id, item.nama_pelanggan)
                            "
                        >
                            <v-icon dark>
                                mdi-delete-outline
                            </v-icon>
                        </v-btn>
                    </template>
                    <template v-slot:body.append></template>
                </v-data-table>
            </v-app>
        </div>

        <b-modal ref="add-jenisbahan-modal" hide-footer :title="judulModal">
            <v-app>
                <form @submit.prevent="InputData">
                    <v-text-field
                        v-model="nama_pelanggan"
                        class="rounded-lg"
                        :error-messages="nameErrors"
                        :counter="50"
                        outlined
                        label="Nama Pelanggan"
                        @input="$v.nama_pelanggan.$touch()"
                        @blur="$v.nama_pelanggan.$touch()"
                        >{{ nama_pelanggan }}</v-text-field
                    >

                    <v-text-field
                        v-model="no_telp"
                        class="rounded-lg"
                        :error-messages="no_telpErrors"
                        :counter="25"
                        outlined
                        label="no_telp"
                        @input="$v.no_telp.$touch()"
                        @blur="$v.no_telp.$touch()"
                        >{{ no_telp }}</v-text-field
                    >

                    <v-text-field
                        v-model="alamat"
                        class="rounded-lg"
                        :error-messages="alamatErrors"
                        :counter="11"
                        outlined
                        label="alamat"
                        @input="$v.alamat.$touch()"
                        @blur="$v.alamat.$touch()"
                        >{{ alamat }}</v-text-field
                    >

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
                                    :icondepan="'mdi-content-save-outline'"
                                    text="Simpan"
                                    :bg-color="'bg-success'"
                                    :color="'text-white'"
                                ></Button>
                            </div>
                        </div>
                    </div>
                </form>
            </v-app>
        </b-modal>

        <b-modal ref="modal-hapus" hide-footer :title="judulModal">
            <v-app>
                <p>
                    Apa kamu yakin ingin menghapus data {{ nama_pelanggan }} ?
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
    </div>
</template>

<script>
import Button from "./component/Button.vue";
import { validationMixin } from "vuelidate";
import { required, maxLength, numeric } from "vuelidate/lib/validators";
import AlertSuccess from "./component/alert/AlertSuccess.vue";

export default {
    mixins: [validationMixin],

    validations: {
        nama_pelanggan: { required, maxLength: maxLength(50) },
        no_telp: { required, maxLength: maxLength(25) },
        alamat: { required, maxLength: maxLength(255) }
    },

    components: {
        Button,
        AlertSuccess
    },

    mounted() {
        this.loadData();
    },

    data() {
        return {
            id: "",
            nama_pelanggan: "",
            no_telp: "",
            alamat: "",
            items: [],
            inputan: {},

            loading: true,
            inputLoading: false,
            errored: false,
            inputError: null,
            show: true,
            showAlert: false,
            alertMessage: "",
            judulModal: null,

            search: "",
            submitStatus: null,
            nama_pelangganRules: [
                v => !!v || "nama_pelanggan jenis barang harus di isi"
            ],
            no_telpRules: [v => !!v || "no_telp harus di isi"],
            alamatRules: [v => !!v || "alamat harus di isi"]
        };
    },

    computed: {
        headers() {
            return [
                {
                    text: "Nama Pelanggan",
                    align: "start",
                    sortable: true,
                    value: "nama_pelanggan"
                },
                {
                    text: "Nomor Telepon",
                    value: "no_telp"
                },
                {
                    text: "Alamat",
                    value: "alamat"
                },
                {
                    text: "Actions",
                    value: "actions",
                    sortable: false,
                    align: "center"
                }
            ];
        },
        nameErrors() {
            const errors = [];
            if (!this.$v.nama_pelanggan.$dirty) return errors;
            !this.$v.nama_pelanggan.required &&
                errors.push("Name Jenis Barang Harus Di Isi.");
            return errors;
        },
        no_telpErrors() {
            const errors = [];
            if (!this.$v.no_telp.$dirty) return errors;
            !this.$v.no_telp.maxLength && errors.push("Text terlalu panjang");
            !this.$v.no_telp.required && errors.push("no_telp Harus Di Isi.");
            return errors;
        },
        alamatErrors() {
            const errors = [];
            if (!this.$v.alamat.$dirty) return errors;
            !this.$v.alamat.maxLength && errors.push("Text terlalu panjang");
            !this.$v.alamat.required && errors.push("alamat Harus Di Isi.");
            return errors;
        }
    },

    methods: {
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

        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace(".", ",");
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        berhasilSimpan() {
            this.hideModal();
            this.loadData();
        },

        loadData() {
            this.axios
                .get(this.$store.state.apiUrl+"pelanggan", {
                    headers: {
                        Authorization: "Bearer " + this.$store.state.biggertoken
                    }
                })
                .then(response => {
                    console.log(response);
                    this.items = response.data;
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => (this.loading = false));
        },

        InputData() {
            this.validate();
            if (this.$v.$invalid) {
                this.submitStatus = "ERROR";
                this.inputError = "Periksa kembali isi data kamu";
            } else {
                this.inputLoading = true;
                this.inputError = null;

                this.inputan.nama_pelanggan = this.nama_pelanggan;
                this.inputan.no_telp = this.no_telp;
                this.inputan.alamat = this.alamat;

                if (this.id == null) {
                    this.axios
                        .post(
                            this.$store.state.apiUrl+"pelanggan/store",
                            this.inputan,
                            {
                                headers: {
                                    Authorization:
                                        "Bearer " +
                                        this.$store.state.biggertoken
                                }
                            }
                        )
                        .then(
                            response => {
                                console.log(response);

                                if (response.status === 200) {
                                    this.alertMessage = response["data"]["msg"];
                                    this.berhasilSimpan();
                                    this.$refs.alertSuccess.showAlert();
                                } else {
                                    // throw error and go to catch block
                                    alert("error bos");
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
                } else {
                    this.axios
                        .put(
                            this.$store.state.apiUrl+"pelanggan/update/" +
                                this.id,
                            this.inputan,
                            {
                                headers: {
                                    Authorization:
                                        "Bearer " +
                                        this.$store.state.biggertoken
                                }
                            }
                        )
                        .then(
                            response => {
                                console.log(response);

                                if (response.status === 200) {
                                    this.alertMessage = response["data"]["msg"];
                                    this.berhasilSimpan();
                                    this.$refs.alertSuccess.showAlert();
                                } else {
                                    // throw error and go to catch block
                                    alert("error bos");
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
            }
        },

        hapusData() {
            this.inputLoading = true;
            this.inputError = null;

            this.axios
                .delete(
                    this.$store.state.apiUrl+"pelanggan/delete/" + this.id,
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
                            this.berhasilSimpan();
                            this.$refs.alertSuccess.showAlert();
                        } else {
                            // throw error and go to catch block
                            alert("error bos");
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
                });
        },

        validate() {
            this.$v.$touch();
        },

        showModal() {
            this.$refs["add-jenisbahan-modal"].show();
        },

        hideModal() {
            this.$refs["add-jenisbahan-modal"].hide();
        },

        showHapusModal(id, nama_pelanggan) {
            this.id = id;
            this.nama_pelanggan = nama_pelanggan;
            this.judulModal = "Hapus Data !";
            this.inputError = null;
            this.$refs["modal-hapus"].show();
        },

        hideHapusModal() {
            this.$refs["modal-hapus"].hide();
        },

        showEditModal(id, nama_pelanggan, no_telp, alamat) {
            alert(id);
            this.id = id;
            this.nama_pelanggan = nama_pelanggan;
            this.no_telp = no_telp;
            this.alamat = alamat;
            this.inputError = null;
            this.judulModal = "Edit Data ";
            this.showModal();
        },

        showTambahData() {
            this.id = null;
            this.nama_pelanggan = null;
            this.no_telp = null;
            this.alamat = null;
            this.inputError = null;
            this.judulModal = "Tambah Data";
            this.showModal();
        }
    },

    
};
</script>
