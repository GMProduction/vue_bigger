<template>
  <div class="home">
    <AlertSuccess ref="alertSuccess" :message="alertMessage"></AlertSuccess>

    <div class="d-flex flex-row mb-3">
      <div style="width: 300px;">
        <v-menu
          ref="menutanggal"
          v-model="menutanggal"
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
          <v-date-picker v-model="lapTanggal" no-title scrollable range>
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="menutanggal = false">
              Cancel
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="
                $refs.menutanggal.save(lapTanggal)
                loadData()
              "
            >
              OK
            </v-btn>
          </v-date-picker>
        </v-menu>
      </div>
      <div class="flex-grow-1"></div>

      <Button
        @click.native="showTambahData"
        :icondepan="'mdi-plus'"
        text="Tambah Data"
        style="height: 40px;"
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

          <template v-slot:item.urlGambar="{ item }">
            <a :href="'/img/pengeluaran/' + item.urlGambar" target="_blank">
              <img
                :src="'/img/pengeluaran/' + item.urlGambar"
                alt=""
                width="50"
              />
            </a>
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
              @click="showEditModal(item.id, item.tanggal, item.nominal, item.akun, item.keterangan)"
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

        <div class="mt-5">
          <vuetify-money
            dense
            v-model="totalPengeluaran"
            class="rounded-lg"
            outlined
            label="Total"
            disabled
            v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
            v-bind:options="options"
          >
            {{ totalPengeluaran }}
          </vuetify-money>
        </div>
      </v-app>
    </div>

    <b-modal ref="add-pengeluaran-modal" hide-footer :title="judulModal">
      <v-app>
        <form @submit.prevent="InputData">
          <v-menu
            ref="menu"
            v-model="menu"
            :close-on-content-click="false"
            :return-value.sync="tanggal"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="tanggal"
                label="Tanggal"
                prepend-icon="mdi-calendar"
                readonly
                dense
                :error-messages="tanggalErrors"
                class="rounded-lg"
                outlined
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker v-model="tanggal" no-title scrollable>
              <v-spacer></v-spacer>
              <v-btn text color="primary" @click="menu = false">
                Cancel
              </v-btn>
              <v-btn text color="primary" @click="$refs.menu.save(tanggal)">
                OK
              </v-btn>
            </v-date-picker>
          </v-menu>

          <vuetify-money
            v-model="nominal"
            class="rounded-lg"
            :error-messages="nominalErrors"
            :counter="11"
            outlined
            type="number"
            label="Nominal"
            @input="$v.nominal.$touch()"
            @blur="$v.nominal.$touch()"
            v-bind:valueWhenIsEmpty="valueWhenIsEmpty"
            v-bind:options="options"
          >
            {{ nominal }}
          </vuetify-money>

          <v-file-input
            :rules="rules"
            accept="image/png, image/jpeg, image/bmp"
            placeholder="Pilih Gambar"
            prepend-icon="mdi-camera"
            label="Bukti Nota Pengeluaran"
            outlined
            v-model="urlGambar"
          >
            {{ urlGambar }}
          </v-file-input>


             <v-textarea
                dense
                v-model="keterangan"
                class="rounded-lg"
                :counter="199"
                outlined
                label="Keterangan"
              >
                {{ keterangan }}
              </v-textarea>

          <v-card class="rounded-xl p-3 mb-3">
            <p>Akun</p>
            <v-radio-group v-model="akun" mandatory>
              <v-radio label="B" value="b"></v-radio>
              <v-radio label="R" value="r"></v-radio>
            </v-radio-group>
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
import Button from './component/Button.vue'
import { validationMixin } from 'vuelidate'
import { required, maxLength, numeric } from 'vuelidate/lib/validators'
import AlertSuccess from './component/alert/AlertSuccess.vue'

export default {
  mixins: [validationMixin],

  validations: {
    tanggal: { required, maxLength: maxLength(50) },
    nominal: { required, maxLength: maxLength(11), numeric },
  },

  components: {
    Button,
    AlertSuccess,
  },

  mounted() {
    this.loadData()
  },

  data() {
    return {
      id: '',
      tanggal: '',
      nominal: '',
      urlGambar: '',
      keterangan: '',
      lapTanggal: null,
      akun: null,
      inputPencarian: {},
      menu: false,
      menutanggal: false,

      items: [],
      inputan: {},
      menu: false,

      loading: true,
      inputLoading: false,
      errored: false,
      inputError: null,
      show: true,
      showAlert: false,
      alertMessage: '',
      judulModal: null,
      totalPengeluaran: null,

      search: '',
      submitStatus: null,
      tanggalRules: [(v) => !!v || 'Tanggal harus di isi'],
      nominalRules: [(v) => !!v || 'Nominal harus di isi'],

      valueWhenIsEmpty: '',
      options: {
        locale: 'id-ID',
        length: 11,
        precision: 0,
      },

      rules: [
        (value) =>
          !value ||
          value.size < 2000000 ||
          'Gambar tidak boleh lebih dari 2 MB!',
      ],
    }
  },

  computed: {
    headers() {
      return [
        {
          text: 'Tanggal',
          align: 'start',
          sortable: true,
          value: 'tanggal',
        },
        {
          text: 'Nominal',
          value: 'nominal',
        },
        {
          text: 'Bukti',
          value: 'urlGambar',
        },
        {
          text: 'Keterangan',
          value: 'keterangan',
        },
        {
          text: 'Akun',
          value: 'akun',
        },
        {
          text: 'Actions',
          value: 'actions',
          sortable: false,
          align: 'center',
        },
      ]
    },
    tanggalErrors() {
      const errors = []
      if (!this.$v.tanggal.$dirty) return errors
      !this.$v.tanggal.required && errors.push('Tanggal Harus Di Isi.')
      return errors
    },

    nominalErrors() {
      const errors = []
      if (!this.$v.nominal.$dirty) return errors
      !this.$v.nominal.maxLength && errors.push('Text terlalu panjang')
      !this.$v.nominal.required && errors.push('Nominal Harus Di Isi.')
      return errors
    },
  },

  methods: {
    filterOnlyCapsText(value, search, item) {
      return (
        value != null &&
        search != null &&
        typeof value === 'string' &&
        value.toString().toLocaleLowerCase().indexOf(search) !== -1
      )
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
    },

    berhasilSimpan() {
      this.hideModal()
      this.loadData()
    },

    loadData() {
      this.inputPencarian.lapTanggal = this.lapTanggal

      this.axios
        .post(
          this.$store.state.apiUrl + 'pengeluaran/pencarian',
          this.inputPencarian,
          {
            headers: {
              Authorization: 'Bearer ' + this.$store.state.biggertoken,
            },
          },
        )
        .then((response) => {
          this.items = response.data['pengeluaran']
          this.totalPengeluaran = response.data['total']

          console.log(this.items)
        })
        .catch((error) => {
          console.log(error)
          this.errored = true
        })
        .finally(() => (this.loading = false))
    },

    InputData() {
      this.validate()
      if (this.$v.$invalid) {
        this.submitStatus = 'ERROR'
        this.inputError = 'Periksa kembali isi data kamu'
      } else {
        let formData = new FormData()

        formData.append('file', this.urlGambar)
        formData.append('tanggal', this.tanggal)
        formData.append('nominal', this.nominal)
        formData.append('keterangan', this.keterangan)
        formData.append('akun', this.akun)
        if (this.urlGambar != null) {
          formData.append('urlGambar', this.urlGambar)
          formData.append('isFile', true)
        }

        console.log('formData', this.urlGambar)

        this.inputLoading = true
        this.inputError = null

        if (this.id == null) {
          this.axios
            .post(this.$store.state.apiUrl + 'pengeluaran/store', formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
                Authorization: 'Bearer ' + this.$store.state.biggertoken,
              },
            })
            .then(
              (response) => {
                console.log(response)

                if (response.status === 200) {
                  this.alertMessage = response['data']['msg']
                  this.berhasilSimpan()
                  this.$refs.alertSuccess.showAlert()
                } else {
                  // throw error and go to catch block
                  alert('error bos')
                  this.inputError = 'Gagal input data'
                }
              },
              // console.log(response.data)
            )
            .catch((error) => {
              console.log(error)
              this.inputLoading = false
              this.inputError = 'Gagal input data'
            })
            .finally(() => {
              this.inputLoading = false
            })
        } else {
          console.log('asu ', formData)
          this.axios
            .post(
              this.$store.state.apiUrl + 'pengeluaran/update/' + this.id,
              formData,
              {
                headers: {
                  'Content-Type': 'multipart/form-data',
                  Authorization: 'Bearer ' + this.$store.state.biggertoken,
                },
              },
            )
            .then(
              (response) => {
                console.log(response)

                if (response.status === 200) {
                  this.alertMessage = response['data']['msg']
                  this.berhasilSimpan()
                  this.$refs.alertSuccess.showAlert()
                } else {
                  // throw error and go to catch block
                  alert('error bos')
                  this.inputError = 'Gagal input data'
                }
              },
              // console.log(response.data)
            )
            .catch((error) => {
              console.log(error)
              this.inputLoading = false
              this.inputError = 'Gagal input data'
            })
            .finally(() => {
              this.inputLoading = false
            })
        }
      }
    },

    hapusData() {
      this.inputLoading = true
      this.inputError = null

      this.axios
        .delete(this.$store.state.apiUrl + 'pengeluaran/delete/' + this.id, {
          headers: {
            Authorization: 'Bearer ' + this.$store.state.biggertoken,
          },
        })
        .then(
          (response) => {
            console.log(response)

            if (response.status === 200) {
              this.alertMessage = response['data']['msg']
              this.hideHapusModal()
              this.berhasilSimpan()
              this.$refs.alertSuccess.showAlert()
            } else {
              // throw error and go to catch block
              alert('error bos')
              this.inputError = 'Gagal Hapus data'
            }
          },
          // console.log(response.data)
        )
        .catch((error) => {
          this.inputLoading = false
          this.inputError = 'Gagal Hapus data'
        })
        .finally(() => {
          this.inputLoading = false
        })
    },

    validate() {
      this.$v.$touch()
    },

    showModal() {
      this.$refs['add-pengeluaran-modal'].show()
    },

    hideModal() {
      this.$refs['add-pengeluaran-modal'].hide()
    },

    showHapusModal(id, nama) {
      this.id = id
      this.nama = nama
      this.judulModal = 'Hapus Data !'
      this.inputError = null
      this.$refs['modal-hapus'].show()
    },

    hideHapusModal() {
      this.$refs['modal-hapus'].hide()
    },

    showEditModal(id, tanggal, nominal, akun, keterangan) {
      this.id = id
      this.tanggal = tanggal
      this.nominal = nominal
      this.akun = akun
      this.urlGambar = null
      this.keterangan = keterangan
      this.inputError = null
      this.judulModal = 'Edit Data '
      this.showModal()
    },

    showTambahData() {
      this.id = null
      this.tanggal = null
      this.nominal = null
      this.urlGambar = null
      this.keterangan = null
      this.harga = null
      this.inputError = null
      this.judulModal = 'Tambah Data'
      this.showModal()
    },
  },
}
</script>
