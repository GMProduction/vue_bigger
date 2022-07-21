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
                    <template v-slot:item.harga="{ item }">
                        {{ formatPrice(item.harga) }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            x-small
                            class="mx-1"
                            fab
                            dark
                            color="warning"
                            @click="
                                showEditModal(
                                    item.id,
                                    item.nama,
                                    item.satuan,
                                    item.harga
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
                            @click="showHapusModal(item.id, item.nama)"
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
                        v-model="nama"
                        class="rounded-lg"
                        :error-messages="nameErrors"
                        :counter="50"
                        outlined
                        label="Nama Jenis Barang"
                        @input="$v.nama.$touch()"
                        @blur="$v.nama.$touch()"
                        >{{ nama }}</v-text-field
                    >

                    <v-text-field
                        v-model="satuan"
                        class="rounded-lg"
                        :error-messages="satuanErrors"
                        :counter="25"
                        outlined
                        label="Satuan"
                        @input="$v.satuan.$touch()"
                        @blur="$v.satuan.$touch()"
                        >{{ satuan }}</v-text-field
                    >

                    <vuetify-money
                        v-model="harga"
                        class="rounded-lg"
                        :error-messages="hargaErrors"
                        :counter="11"
                        outlined
                        type="number"
                        label="Harga"
                        @input="$v.harga.$touch()"
                        @blur="$v.harga.$touch()"
                        v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
                        v-bind:options="options"
                        >{{ harga }}</vuetify-money
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
                <p>Apa kamu yakin ingin menghapus data {{ nama }} ?</p>

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
        nama: { required, maxLength: maxLength(50) },
        satuan: { required, maxLength: maxLength(25) },
        harga: { required, maxLength: maxLength(11), numeric }
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
            nama: "",
            satuan: "",
            harga: "",
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

            valueWhenIsEmpty: "",
            options: {
                locale: "id-ID",
                length: 11,
                precision: 0
            },
            search: "",
            submitStatus: null,
            namaRules: [v => !!v || "Nama jenis barang harus di isi"],
            satuanRules: [v => !!v || "Satuan harus di isi"],
            hargaRules: [v => !!v || "harga harus di isi"]
        };
    },

    computed: {
        headers() {
            return [
                {
                    text: "Nama",
                    align: "start",
                    sortable: true,
                    value: "nama"
                },
                {
                    text: "Satuan",
                    value: "satuan"
                },
                {
                    text: "harga",
                    value: "harga",
                    formatter: this.formatCurrency
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
            if (!this.$v.nama.$dirty) return errors;
            !this.$v.nama.required &&
                errors.push("Name Jenis Barang Harus Di Isi.");
            return errors;
        },
        satuanErrors() {
            const errors = [];
            if (!this.$v.satuan.$dirty) return errors;
            !this.$v.satuan.maxLength && errors.push("Text terlalu panjang");
            !this.$v.satuan.required && errors.push("Satuan Harus Di Isi.");
            return errors;
        },
        hargaErrors() {
            const errors = [];
            if (!this.$v.harga.$dirty) return errors;
            !this.$v.harga.maxLength && errors.push("Text terlalu panjang");
            !this.$v.harga.required && errors.push("Harga Harus Di Isi.");
            return errors;
        }
    },

    methods: {
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
                .get(this.$store.state.apiUrl + "jenis-barang", {
                    headers: {
                        Authorization: "Bearer " + this.$store.state.biggertoken
                    }
                })
                .then(response => {
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

                this.inputan.nama = this.nama;
                this.inputan.satuan = this.satuan;
                this.inputan.harga = this.harga;

                if (this.id == null) {
                    this.axios
                        .post(
                            this.$store.state.apiUrl + "jenis-barang/store",
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
                            this.$store.state.apiUrl +
                                "jenis-barang/update/" +
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
                    this.$store.state.apiUrl + "jenis-barang/delete/" + this.id,
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

        showHapusModal(id, nama) {
            this.id = id;
            this.nama = nama;
            this.judulModal = "Hapus Data !";
            this.inputError = null;
            this.$refs["modal-hapus"].show();
        },

        hideHapusModal() {
            this.$refs["modal-hapus"].hide();
        },

        showEditModal(id, nama, satuan, harga) {
            this.id = id;
            this.nama = nama;
            this.satuan = satuan;
            this.harga = harga;
            this.inputError = null;
            this.judulModal = "Edit Data ";
            this.showModal();
        },

        showTambahData() {
            this.id = null;
            this.nama = null;
            this.satuan = null;
            this.harga = null;
            this.inputError = null;
            this.judulModal = "Tambah Data";
            this.showModal();
        }
    }
};
</script>
