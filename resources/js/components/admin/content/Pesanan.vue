<template>
  <div class="home">
    <AlertSuccess ref="alertSuccess" :message="alertMessage"></AlertSuccess>

    <div class="d-flex flex-row mb-3">
      <div class="flex-grow-1"></div>

      <Button
        @click.native="showTambahModalPesanan"
        :icondepan="'mdi-plus'"
        text="Tambah Data"
        :bg-color="'bg-success'"
        class="ml-2"
      ></Button>
    </div>

    <div class="card-table">
      <div v-if="loading" class="text-center m-5">
        <b-spinner variant="primary" label="Text Centered" class="mb-3"></b-spinner>
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
          <template v-slot:item.total_biaya="{ item }">
            {{ formatPrice(item.total_biaya) }}
          </template>
          <template v-slot:item.actions="{ item }">
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
                  item.total_biaya,
                  item.akun
                )
              "
            >
              <v-icon dark> mdi-pencil-outline </v-icon>
            </v-btn>
            <v-btn
              x-small
              class="mx-1"
              fab
              dark
              color="red"
              @click="showHapusModal(item.id, 'pesamam')"
            >
              <v-icon dark> mdi-delete-outline </v-icon>
            </v-btn>
          </template>
          <template v-slot:body.append></template>
        </v-data-table>
      </v-app>
    </div>

    <b-modal
      no-close-on-backdrop
      ref="add-pesanan-modal"
      size="xl"
      hide-footer
      :title="judulModal"
    >
      <div v-if="loading" class="text-center m-5">
        <b-spinner variant="primary" label="Text Centered" class="mb-3"></b-spinner>
        <p>Tunggu Sebentar Ya...</p>
      </div>
      <div v-else-if="errored">
        <p>Data tidak bisa di muat...</p>
      </div>

      <v-app v-else>
        <v-card outlined class="rounded-xl mb-5">
          <Button
            @click.native="showModalKeranjang"
            :icondepan="'mdi-plus'"
            text="Tambah Data Keranjang"
            :bg-color="'bg-primary'"
            class="ml-2 mt-3 mb-2"
          ></Button>

          <v-data-table
            :headers="headersKeranjang"
            :items="tabelKeranjang"
            :search="search"
            :custom-filter="filterOnlyCapsText"
          >
            <template v-slot:top>
              <v-text-field
                v-model="search"
                label="Cari disini (huruf kecil)"
                class="mx-4"
              >
              </v-text-field>
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
                    'keranjang'
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
                color="warning"
                @click="
                  showEditModalKeranjang(
                    item.id,
                    item.id_jenisBarang,
                    item.namaJenisBarang,
                    item.ukuran,
                    item.qty,
                    item.catatan,
                    item.biayaTambahan,
                    item.total
                  )
                "
              >
                <v-icon dark> mdi-pencil-outline </v-icon>
              </v-btn>
              <v-btn
                x-small
                class="mx-1"
                fab
                dark
                color="red"
                @click="showHapusModal(item.id, 'keranjang')"
              >
                <v-icon dark> mdi-delete-outline </v-icon>
              </v-btn>
            </template>
            <template v-slot:body.append></template>
          </v-data-table>
        </v-card>
        <form @submit.prevent="InputDatapesanan">
          <v-row>
            <v-col cols="12" md="8">
              <v-row>
                <!-- <v-col cols="2">
                  <v-card class="rounded-xl p-3 mb-3">
                    <p>Akun</p>
                    <v-radio-group v-model="pesAkun" mandatory>
                      <v-radio label="B" value="b" ></v-radio>
                      <v-radio label="R" value="r" ></v-radio>
                    </v-radio-group>
                  </v-card>

                </v-col> -->

                <v-col cols="12">
                  <div class="d-flex">
                    <v-autocomplete
                      :items="itemsPelanggan"
                      :filter="customFilterPelanggan"
                      outlined
                      dense
                      v-model="pPelanggan"
                      :error-messages="pPelangganError"
                      class="rounded-lg mr-2"
                      item-text="nama_pelanggan"
                      item-value="id"
                      label="Pelanggan"
                      @change="pilihPelanggan"
                    >
                      {{ namaPelanggan }}
                    </v-autocomplete>

                    <Button
                      @click.native="showTambahDataPelanggan"
                      type="button"
                      :icondepan="'mdi-plus'"
                      :bg-color="'bg-primary'"
                      style="height: 40px"
                      class="rounded-lg"
                    ></Button>
                  </div>

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
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker v-model="pesDeadline" no-title scrollable>
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="menu = false"> Cancel </v-btn>
                      <v-btn text color="primary" @click="$refs.menu.save(pesDeadline)">
                        OK
                      </v-btn>
                    </v-date-picker>
                  </v-menu>
                </v-col>
              </v-row>

              <v-textarea
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
                <vuetify-money
                  dense
                  v-model="pesSubTotal"
                  class="rounded-lg"
                  outlined
                  label="Sub Total"
                  disabled
                  v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
                  v-bind:options="options"
                >
                  {{ pesSubTotal }}
                </vuetify-money>

                <v-text-field
                  dense
                  v-model="pesDiskon"
                  class="rounded-lg"
                  :counter="11"
                  outlined
                  type="number"
                  label="Diskon"
                  @blur="hitungTotalPesanan"
                >
                  {{ pesDiskon }}
                </v-text-field>

                <vuetify-money
                  dense
                  v-model="pesTotal"
                  class="rounded-lg"
                  outlined
                  disabled
                  label="Total"
                  v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
                  v-bind:options="options"
                >
                  {{ pesTotal }}
                </vuetify-money>
              </v-card>
            </v-col>
          </v-row>

          <div class="mt-3 d-flex flex-row">
            <div class="flex-grow-1"></div>

            <div v-if="inputLoading">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
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

    <b-modal
      no-close-on-backdrop
      ref="add-keranjang-modal"
      size="md"
      hide-footer
      :title="judulModal"
    >
      <v-app>
        <form @submit.prevent="InputDataKeranjang">
          <div class="d-flex">
            <v-autocomplete
              :items="itemsJenisBarang"
              :filter="customFilterJenisBarang"
              outlined
              dense
              v-model="kJenisBarang"
              :error-messages="idJenisBarangError"
              class="rounded-lg mr-2"
              item-text="nama"
              item-value="id"
              label="Jenis Barang"
              @change="pilihJenisBarang"
            >
              {{ kNamaJenisBarang }}
            </v-autocomplete>

            <Button
              @click.native="showTambahDataJenisBarang"
              type="button"
              :icondepan="'mdi-plus'"
              :bg-color="'bg-primary'"
              style="height: 40px; color: #fff"
              class="rounded-lg"
            ></Button>
          </div>

          <v-text-field
            v-model="kUkuran"
            class="rounded-lg"
            :counter="50"
            outlined
            :suffix="satuan"
            label="Total Ukuran"
            type="number"
            v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
            v-bind:options="options"
          >
            {{ kUkuran }}
          </v-text-field>

          <vuetify-money
            v-model="kQty"
            class="rounded-lg"
            :counter="50"
            outlined
            type="number"
            label="Qty"
            @blur="hitungTotalKeranjang"
            v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
            v-bind:options="options"
          >
            {{ kQty }}
          </vuetify-money>

          <v-text-field
            v-model="kBiayaTambahan"
            class="rounded-lg"
            :counter="50"
            outlined
            type="number"
            label="Biaya Tambahan"
            @blur="hitungTotalKeranjang"
          >
            {{ kBiayaTambahan }}
          </v-text-field>

          <vuetify-money
            v-model="kTotal"
            class="rounded-lg"
            :counter="50"
            outlined
            disabled
            label="Total"
            v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
            v-bind:options="options"
          >
            {{ kTotal }}
          </vuetify-money>

          <v-textarea
            v-model="kCatatan"
            class="rounded-lg"
            :counter="50"
            outlined
            label="Catatan"
          >
            {{ kCatatan }}
          </v-textarea>

          <div class="mt-3 d-flex flex-row">
            <div class="flex-grow-1"></div>

            <div v-if="inputLoading">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
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

    <b-modal ref="add-jenisBarang-modal" hide-footer :title="judulModal">
      <v-app>
        <form @submit.prevent="InputDataJenisBarang">
          <v-text-field
            v-model="jNama"
            class="rounded-lg"
            :error-messages="jNameErrors"
            :counter="50"
            outlined
            label="Nama Jenis Barang"
            @input="$v.jNama.$touch()"
            @blur="$v.jNama.$touch()"
          >
          </v-text-field>

          <v-text-field
            v-model="jSatuan"
            class="rounded-lg"
            :error-messages="jSatuanErrors"
            :counter="25"
            outlined
            label="Satuan"
            @input="$v.jSatuan.$touch()"
            @blur="$v.jSatuan.$touch()"
          ></v-text-field>

          <v-text-field
            v-model="jHarga"
            class="rounded-lg"
            :error-messages="jHargaErrors"
            :counter="11"
            outlined
            type="number"
            label="Harga"
            @input="$v.jHarga.$touch()"
            @blur="$v.jHarga.$touch()"
          >
          </v-text-field>

          <div class="mt-3 d-flex flex-row">
            <div class="flex-grow-1"></div>

            <div v-if="inputLoading">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
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

    <b-modal ref="add-pelanggan-modal" hide-footer :title="judulModal">
      <v-app>
        <form @submit.prevent="inputDataPelanggan" enctype="multipart/form-data">
          <v-text-field
            v-model="pNama"
            class="rounded-lg"
            :error-messages="pNamaErrors"
            :counter="50"
            outlined
            label="Nama Pelanggan"
            @input="$v.pNama.$touch()"
            @blur="$v.pNama.$touch()"
          >
          </v-text-field>

          <v-text-field
            v-model="pNotelp"
            class="rounded-lg"
            :error-messages="pNotelpErrors"
            :counter="25"
            outlined
            label="no_telp"
            @input="$v.pNotelp.$touch()"
            @blur="$v.pNotelp.$touch()"
          >
          </v-text-field>

          <v-text-field
            v-model="pAlamat"
            class="rounded-lg"
            :error-messages="pAlamatErrors"
            :counter="225"
            outlined
            label="alamat"
            @input="$v.pAlamat.$touch()"
            @blur="$v.pAlamat.$touch()"
          ></v-text-field>

          <div class="mt-3 d-flex flex-row">
            <div class="flex-grow-1"></div>

            <div v-if="inputLoading">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
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
        <p>Apa kamu yakin ingin menghapus data ?</p>

        <div class="mt-3 d-flex flex-row">
          <div class="flex-grow-1"></div>

          <div v-if="inputLoading">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
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

    <b-modal ref="modal-status" hide-footer :title="judulModal">
      <v-app>
        <p>
          {{ msgModal }}
        </p>

        <div class="mt-3 d-flex flex-row">
          <div class="flex-grow-1"></div>

          <div v-if="inputLoading">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
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
    kJenisBarang: { required },

    pPelanggan: { required },

    jNama: { required, maxLength: maxLength(50) },
    jSatuan: { required, maxLength: maxLength(25) },
    jHarga: { required, maxLength: maxLength(11), numeric },

    pNama: { required, maxLength: maxLength(50) },
    pNotelp: { required, maxLength: maxLength(25) },
    pAlamat: { required, maxLength: maxLength(255) },

    pesDeadline: { required },
  },

  components: {
    Button,
    AlertSuccess,
  },

  mounted() {
    this.loadData();
  },

  data() {
    return {
      idPesanan: null,
      menu: false,
      idPelanggan: null,
      namaPelanggan: null,
      pesDeadline: new Date().toISOString().substr(0, 10),
      pesSubTotal: null,
      pesDiskon: null,
      pesTotal: null,
      pesAkun: "b",
      pesCatatan: null,
      no_telp: "",
      alamat: "",
      items: [],
      tabelKeranjang: [],
      inputan: {},
      inputanKeranjang: {},
      mapItemsJenisBarang: {},
      mapItemsPelanggan: {},

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
        precision: 0,
      },
    };
  },

  computed: {
    headers() {
      return [
        {
          text: "Nama Pelanggan",
          align: "start",
          sortable: true,
          value: "pelanggan.nama_pelanggan",
        },
        {
          text: "Total Biaya",
          value: "total_biaya",
        },
        {
          text: "Pengerjaan",
          value: "status_proses",
        },
        {
          text: "Pembayaran",
          value: "status_bayar",
        },
        // {
        //   text: 'akun',
        //   value: 'akun',
        // },
        {
          text: "Tanggal Pesan",
          value: "tanggal_pesan",
        },
        {
          text: "Deadline",
          value: "deadline",
        },
        {
          text: "Actions",
          value: "actions",
          sortable: false,
          align: "center",
        },
      ];
    },

    headersKeranjang() {
      return [
        {
          text: "Jenis Barang",
          value: "jenis_barang.nama",
        },
        {
          text: "Ukuran",
          value: "ukuran",
        },
        {
          text: "Qty",
          value: "qty",
        },
        {
          text: "Status Pengerjaan",
          value: "status_pengerjaan",
        },

        {
          text: "Total",
          value: "total",
        },
        {
          text: "Catatan",
          value: "catatan",
        },
        {
          text: "Actions",
          value: "actions",
          sortable: false,
          align: "center",
        },
      ];
    },

    idJenisBarangError() {
      const errors = [];
      if (!this.$v.kJenisBarang.$dirty) return errors;
      !this.$v.kJenisBarang.required && errors.push("Harus memilih jenis barang.");
      return errors;
    },

    pPelangganError() {
      const errors = [];
      if (!this.$v.pPelanggan.$dirty) return errors;
      !this.$v.pPelanggan.required && errors.push("Harus memilih Pelanggan.");
      return errors;
    },

    pesDeadlineError() {
      const errors = [];
      if (!this.$v.pesDeadline.$dirty) return errors;
      !this.$v.pesDeadline.required && errors.push("Harus Mengisi Deadline.");
      return errors;
    },

    jNameErrors() {
      const errors = [];
      if (!this.$v.jNama.$dirty) return errors;
      !this.$v.jNama.required && errors.push("Name Jenis Barang Harus Di Isi.");
      return errors;
    },
    jSatuanErrors() {
      const errors = [];
      if (!this.$v.jSatuan.$dirty) return errors;
      !this.$v.jSatuan.maxLength && errors.push("Text terlalu panjang");
      !this.$v.jSatuan.required && errors.push("Satuan Harus Di Isi.");
      return errors;
    },
    jHargaErrors() {
      const errors = [];
      if (!this.$v.jHarga.$dirty) return errors;
      !this.$v.jHarga.maxLength && errors.push("Text terlalu panjang");
      !this.$v.jHarga.required && errors.push("Harga Harus Di Isi.");
      return errors;
    },

    pNamaErrors() {
      const errors = [];
      if (!this.$v.pNama.$dirty) return errors;
      !this.$v.pNama.required && errors.push("Name PelangganHarus Di Isi.");
      return errors;
    },
    pNotelpErrors() {
      const errors = [];
      if (!this.$v.pNotelp.$dirty) return errors;
      !this.$v.pNotelp.maxLength && errors.push("Text terlalu panjang");
      !this.$v.pNotelp.required && errors.push("no_telp Harus Di Isi.");
      return errors;
    },
    pAlamatErrors() {
      const errors = [];
      if (!this.$v.pAlamat.$dirty) return errors;
      !this.$v.pAlamat.maxLength && errors.push("Text terlalu panjang");
      !this.$v.pAlamat.required && errors.push("alamat Harus Di Isi.");
      return errors;
    },
  },

  methods: {
    customFilterPelanggan(item, queryText, itemText) {
      const textOne = item.nama_pelanggan.toLowerCase();
      const searchText = queryText.toLowerCase();

      return textOne.indexOf(searchText) > -1;
    },

    formatAsCurrency(value, dec) {
      dec = dec || 0;
      if (value === null) {
        return 0;
      }
      return "" + value.toFixed(dec).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    },

    customFilterJenisBarang(item, queryText, itemText) {
      const textOne = item.nama.toLowerCase();
      const searchText = queryText.toLowerCase();

      return textOne.indexOf(searchText) > -1;
    },

    filterOnlyCapsText(value, search, item) {
      return (
        value != null &&
        search != null &&
        typeof value === "string" &&
        value.toString().toLocaleLowerCase().indexOf(search) !== -1
      );
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    },

    berhasilSimpan() {
      (this.idPesanan = 0), (this.pPelanggan = null);
      this.pesDeadline = "";
      this.pesCatatan = "";
      this.pesSubTotal = "";
      this.pesDiskon = "";
      this.pesTotal = "";

      this.hideModal();
      this.loadData();
    },

    loadData() {
      this.axios
        .get(this.$store.state.apiUrl + "pesanan", {
          headers: {
            Authorization: "Bearer " + this.$store.state.biggertoken,
          },
        })
        .then((response) => {
          console.log(response);
          this.items = response.data;
        })
        .catch((error) => {
          console.log(error);
          this.errored = true;
        })
        .finally(() => (this.loading = false));
    },

    loadDataKeranjang() {
      let tempUrl = "keranjang/showNewKeranjang";
      if (this.idPesanan != 0) {
        tempUrl = "keranjang/showKeranjangbyID/" + this.idPesanan;
      }

      this.axios
        .get(this.$store.state.apiUrl + tempUrl, {
          headers: {
            Authorization: "Bearer " + this.$store.state.biggertoken,
          },
        })
        .then((response) => {
          console.log("data keranjang", response.data["data"]);
          this.tabelKeranjang = response.data["data"];
          this.pesSubTotal = response.data["subTotal"];
          this.hitungTotalPesanan();
        })
        .catch((error) => {
          console.log(error);
          this.errored = true;
        })
        .finally(() => (this.loading = false));
    },

    loadDataJenisBarang() {
      this.axios
        .get(this.$store.state.apiUrl + "jenis-barang/pencarian", {
          headers: {
            Authorization: "Bearer " + this.$store.state.biggertoken,
          },
        })
        .then((response) => {
          console.log(response);
          this.itemsJenisBarang = response.data;
          var myMap = {};

          $.each(response.data, function (key, val) {
            console.log("key " + key);
            console.log("val " + val["id"]);
            myMap[val["id"]] = val;
          });

          this.mapItemsJenisBarang = myMap;
        })
        .catch((error) => {
          console.log(error);
          this.errored = true;
        })
        .finally(() => {
          (this.loading = false),
            console.log("itemsJenisBarang", this.itemsJenisBarang[0]["nama"]);
        });
    },

    InputDatapesanan() {
      if (this.$v.pesDeadline.$invalid || this.$v.pPelanggan.$invalid) {
        this.submitStatus = "ERROR";
        this.inputError = "Periksa kembali isi data kamu";
        alert("Periksa kembali isi data kamu");
      } else if (this.tabelKeranjang.length < 1) {
        console.log("this.tabelKeranjang", this.tabelKeranjang);
        this.submitStatus = "ERROR";
        this.inputError = "Keranjang Masih Kosong";
        alert("Keranjang Masih Kosong");
      } else {
        this.hitungTotalPesanan();

        this.inputLoading = true;
        this.inputError = null;
        (this.inputan.id_pelanggan = this.pPelanggan),
          (this.inputan.sub_total = this.pesSubTotal),
          (this.inputan.diskon = this.pesDiskon),
          (this.inputan.total_biaya = this.pesTotal),
          (this.inputan.deadline = this.pesDeadline),
          (this.inputan.akun = this.pesAkun),
          (this.inputan.catatan = this.pesCatatan);

        console.log("inputan", this.inputan);

        if (this.idPesanan == 0) {
          this.axios
            .post(this.$store.state.apiUrl + "pesanan/store", this.inputan, {
              headers: {
                Authorization: "Bearer " + this.$store.state.biggertoken,
              },
            })
            .then(
              (response) => {
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
            .catch((error) => {
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
              this.$store.state.apiUrl + "pesanan/update/" + this.idPesanan,
              this.inputan,
              {
                headers: {
                  Authorization: "Bearer " + this.$store.state.biggertoken,
                },
              }
            )
            .then(
              (response) => {
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
            .catch((error) => {
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

      let tempUrl = "pesanan/delete/";
      if (this.jenisHapus == "keranjang") {
        tempUrl = "keranjang/delete/";
      }
      this.axios
        .delete(this.$store.state.apiUrl + tempUrl + this.id, {
          headers: {
            Authorization: "Bearer " + this.$store.state.biggertoken,
          },
        })
        .then(
          (response) => {
            console.log(response);

            if (response.status === 200) {
              this.alertMessage = response["data"]["msg"];
              this.hideHapusModal();
              this.loadDataKeranjang();
              this.loadData();
              this.$refs.alertSuccess.showAlert();
            } else {
              // throw error and go to catch block
              alert("error bos");
              this.inputError = "Gagal Hapus data";
            }
          }
          // console.log(response.data)
        )
        .catch((error) => {
          this.inputLoading = false;
          this.inputError = "Gagal Hapus data";
        })
        .finally(() => {
          this.inputLoading = false;
          id = null;
        });
    },

    showModal() {
      this.loadDataPelanggan();
      this.$refs["add-pesanan-modal"].show();
    },

    showTambahModalPesanan() {
      this.loadDataPelanggan();
      this.idPesanan = 0;
      this.pPelanggan = null;
      this.namaPelanggan = null;
      this.pesDeadline = "";
      this.pesCatatan = "";
      this.pesSubTotal = 0;
      this.pesDiskon = 0;
      this.pesTotal = 0;
      this.inputError = null;
      this.loadDataKeranjang();
      this.judulModal = "Tambah Data ";
      this.$refs["add-pesanan-modal"].show();
    },

    hideModal() {
      this.$refs["add-pesanan-modal"].hide();
    },

    showEditModalPesanan(
      idPesanan,
      pPelanggan,
      namaPelanggan,
      pesDeadline,
      pesCatatan,
      pesSubTotal,
      pesDiskon,
      pesTotal,
      pesAkun
    ) {
      this.loadDataPelanggan();
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
      this.pesAkun = pesAkun;
      this.loadDataKeranjang();
      this.judulModal = "Edit Data ";
      this.$refs["add-pesanan-modal"].show();
    },

    showHapusModal(id, jenisHapus) {
      this.id = id;
      this.jenisHapus = jenisHapus;
      this.judulModal = "Hapus Data !";
      this.inputError = null;
      this.$refs["modal-hapus"].show();
    },

    hideHapusModal() {
      this.$refs["modal-hapus"].hide();
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

    //PESANAN
    hitungTotalPesanan() {
      if (this.pesDiskon == null) {
        this.pesDiskon = 0;
      }

      if (this.pesSubTotal == null) {
        this.pesSubTotal = 0;
      }

      this.pesTotal = parseInt(this.pesSubTotal) - parseInt(this.pesDiskon);

      console.log("heheheheh");
    },

    //Keranjang

    UpdateStatusKeranjang() {
      let tempUrl = "keranjang/updateStatus/";
      this.inputLoading = true;
      this.inputError = null;

      this.inputanKeranjang.status_pengerjaan = this.statusUp;

      if (this.stateStatus == "pesanan") {
        tempUrl = "pesanan/updateStatus/";
      }
      this.axios
        .put(this.$store.state.apiUrl + tempUrl + this.id, this.inputanKeranjang, {
          headers: {
            Authorization: "Bearer " + this.$store.state.biggertoken,
          },
        })
        .then(
          (response) => {
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
        .catch((error) => {
          console.log(error);
          this.inputLoading = false;
          this.inputError = "Gagal input data";
        })
        .finally(() => {
          this.inputLoading = false;
        });
    },

    showModalKeranjang() {
      this.loadDataJenisBarang();
      this.idKeranjang = null;
      this.kUkuran = 0;
      this.kJenisBarang = null;
      this.kNamaJenisBarang = null;
      this.kQty = 0;
      this.kCatatan = "";
      this.kBiayaTambahan = 0;
      this.kTotal = 0;
      this.inputError = null;
      this.judulModal = "Tambah Data ";
      this.$refs["add-keranjang-modal"].show();
    },

    showEditModalKeranjang(
      idKeranjang,
      kJenisBarang,
      kNamaJenisBarang,
      kUkuran,
      kQty,
      kCatatan,
      kBiayaTambahan,
      kTotal
    ) {
      this.loadDataJenisBarang();
      this.idKeranjang = idKeranjang;
      this.kJenisBarang = kJenisBarang;
      this.kNamaJenisBarang = kNamaJenisBarang;
      this.kUkuran = kUkuran;
      this.kQty = kQty;
      this.kCatatan = kCatatan;
      this.kBiayaTambahan = kBiayaTambahan;
      this.kTotal = kTotal;
      this.inputError = null;
      this.judulModal = "Edit Data ";
      this.$refs["add-keranjang-modal"].show();
    },

    hideModalKeranjang() {
      this.$refs["add-keranjang-modal"].hide();
    },

    berhasilSimpanKeranjang() {
      this.hideModalKeranjang();
      this.loadDataKeranjang();
    },

    InputDataKeranjang() {
      if (this.$v.kJenisBarang.$invalid) {
        alert("masuk sini");

        this.submitStatus = "ERROR";
        this.inputError = "Periksa kembali isi data kamu";
      } else {
        this.hitungTotalKeranjang();
        this.inputLoading = true;
        this.inputError = null;

        this.inputanKeranjang.kJenisBarang = this.kJenisBarang;
        this.inputanKeranjang.kUkuran = this.kUkuran;
        this.inputanKeranjang.kQty = this.kQty;
        this.inputanKeranjang.kBiayaTambahan = this.kBiayaTambahan;
        this.inputanKeranjang.kTotal = this.kTotal;
        this.inputanKeranjang.kCatatan = this.kCatatan;

        if (this.idKeranjang == null) {
          let tempUrl = "keranjang/store";
          if (this.idPesanan != 0) {
            tempUrl = "keranjang/storeInEdit/" + this.idPesanan;
          }
          this.axios
            .post(this.$store.state.apiUrl + tempUrl, this.inputanKeranjang, {
              headers: {
                Authorization: "Bearer " + this.$store.state.biggertoken,
              },
            })
            .then(
              (response) => {
                console.log(response);

                if (response.status === 200) {
                  this.alertMessage = response["data"]["msg"];
                  this.berhasilSimpanKeranjang();
                  this.$refs.alertSuccess.showAlert();
                } else {
                  // throw error and go to catch block
                  alert("error bos");
                  this.inputError = "Gagal input data";
                }
              }
              // console.log(response.data)
            )
            .catch((error) => {
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
              this.$store.state.apiUrl + "keranjang/update/" + this.idKeranjang,
              this.inputanKeranjang,
              {
                headers: {
                  Authorization: "Bearer " + this.$store.state.biggertoken,
                },
              }
            )
            .then(
              (response) => {
                console.log(response);

                if (response.status === 200) {
                  this.alertMessage = response["data"]["msg"];
                  this.berhasilSimpanKeranjang();
                  this.$refs.alertSuccess.showAlert();
                } else {
                  // throw error and go to catch block
                  alert("error bos");
                  this.inputError = "Gagal input data";
                }
              }
              // console.log(response.data)
            )
            .catch((error) => {
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

    hitungTotalKeranjang() {
      var harga = 0;

      if (this.kUkuran == null) {
        this.kUkuran = 0;
      }
      if (this.kQty == null) {
        this.kQty = 0;
      }

      if (this.kBiayaTambahan == null) {
        this.kBiayaTambahan = 0;
      }

      if (this.modelJenisBarang != null) {
        harga = this.modelJenisBarang["harga"];
      }

      this.kTotal =
        parseInt(this.kUkuran * this.kQty * harga) + parseInt(this.kBiayaTambahan);
    },

    //Jenis Barang
    pilihJenisBarang(value, index) {
      this.modelJenisBarang = this.mapItemsJenisBarang[value];
      console.log("modal Jenis", this.modelJenisBarang);
      console.log("value", value);
      console.log("index", index);

      this.satuan = "";
      if (this.modelJenisBarang != null) {
        this.satuan = this.modelJenisBarang["satuan"];
      }
    },

    showTambahDataJenisBarang() {
      this.$refs["add-jenisBarang-modal"].show();
    },

    hideModalJenisBarang() {
      this.$refs["add-jenisBarang-modal"].hide();
    },
    berhasilSimpanJenisBarang() {
      this.hideModalJenisBarang();
      this.jNama = "";
      this.jSatuan = "";
      this.jHarga = "";
      this.loadDataJenisBarang();
    },

    InputDataJenisBarang() {
      if (this.$v.jNama.$invalid || this.$v.jHarga.$invalid || this.$v.jSatuan.$invalid) {
        this.submitStatus = "ERROR";
        this.inputError = "Periksa kembali isi data kamu";
      } else {
        this.inputLoading = true;
        this.inputError = null;

        this.inputanJenisBarang.nama = this.jNama;
        this.inputanJenisBarang.satuan = this.jSatuan;
        this.inputanJenisBarang.harga = this.jHarga;

        this.axios
          .post(
            this.$store.state.apiUrl + "jenis-barang/store",
            this.inputanJenisBarang,
            {
              headers: {
                Authorization: "Bearer " + this.$store.state.biggertoken,
              },
            }
          )
          .then(
            (response) => {
              console.log(response);

              if (response.status === 200) {
                this.alertMessage = response["data"]["msg"];
                this.berhasilSimpanJenisBarang();
                this.$refs.alertSuccess.showAlert();
              } else {
                // throw error and go to catch block
                alert("error bos");
                this.inputError = "Gagal input data";
              }
            }
            // console.log(response.data)
          )
          .catch((error) => {
            console.log(error);
            this.inputLoading = false;
            this.inputError = "Gagal input data";
          })
          .finally(() => {
            this.inputLoading = false;
          });
      }
    },

    // PELANGGAN

    loadDataPelanggan() {
      this.axios
        .get(this.$store.state.apiUrl + "pelanggan/pencarian", {
          headers: {
            Authorization: "Bearer " + this.$store.state.biggertoken,
          },
        })
        .then((response) => {
          console.log(response);
          this.itemsPelanggan = response.data;

          var myMap = {};

          $.each(response.data, function (key, val) {
            console.log("key " + key);
            console.log("val " + val["id"]);
            myMap[val["id"]] = val;
          });

          this.mapItemsPelanggan = myMap;
        })
        .catch((error) => {
          console.log(error);
          this.errored = true;
        })
        .finally(() => {
          (this.loading = false),
            console.log("itemsPelanggan", this.itemsPelanggan[0]["nama"]);
        });
    },

    pilihPelanggan(value, index) {
      this.modelPelanggan = this.mapItemsPelanggan[value];
      this.pPelanggan = value;
    },

    showTambahDataPelanggan() {
      this.$refs["add-pelanggan-modal"].show();
    },

    hideModalPelanggan() {
      this.$refs["add-pelanggan-modal"].hide();
    },

    berhasilSimpanPelanggan() {
      this.pNama = "";
      this.pNotelp = "";
      this.pAlamat = "";
      this.hideModalPelanggan();
      this.loadDataPelanggan();
    },

    inputDataPelanggan() {
      if (
        this.$v.pNama.$invalid ||
        this.$v.pNotelp.$invalid ||
        this.$v.pAlamat.$invalid
      ) {
        this.submitStatus = "ERROR";
        this.inputError = "Periksa kembali isi data kamu";
      } else {
        this.inputLoading = true;
        this.inputError = null;

        this.inputanPelanggan.nama_pelanggan = this.pNama;
        this.inputanPelanggan.no_telp = this.pNotelp;
        this.inputanPelanggan.alamat = this.pAlamat;

        console.log("inputan Pelanggan", this.inputanPelanggan);
        this.axios
          .post(this.$store.state.apiUrl + "pelanggan/store", this.inputanPelanggan, {
            headers: {
              Authorization: "Bearer " + this.$store.state.biggertoken,
            },
          })
          .then(
            (response) => {
              console.log(response);

              if (response.status === 200) {
                this.alertMessage = response["data"]["msg"];
                this.berhasilSimpanPelanggan();
                this.$refs.alertSuccess.showAlert();
              } else {
                // throw error and go to catch block
                alert("error bos");
                this.inputError = "Gagal input data";
              }
            }
            // console.log(response.data)
          )
          .catch((error) => {
            console.log(error);
            this.inputLoading = false;
            this.inputError = "Gagal input data";
          })
          .finally(() => {
            this.inputLoading = false;
          });
      }
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    },
  },
};
</script>
