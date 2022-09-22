<template>
  <div class="home">
    <AlertSuccess ref="alertSuccess" :message="alertMessage"></AlertSuccess>

    <v-app>
      <div class="d-flex">
        <div class="flex-grow-1"></div>

        <b-dropdown dropleft class="mr-2">
          <template #button-content>
            <Button
              :icondepan="'mdi-filter-outline'"
              text="Filter"
              style="height: 40px;"
              :bg-color="'bg-warning'"
              class="ml-2"
            ></Button>
          </template>
          <div class="p-2" style="width: 300px;">
            <a>Periode:</a>
            <v-menu
              ref="menutanggal"
              v-model="menutanggal"
              :close-on-content-click="false"
              :return-value.sync="lapTanggal"
              transition="scale-transition"
              offset-y
              min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }" style="padding: 0;">
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
                    loadDataPembayaran()
                  "
                >
                  OK
                </v-btn>
              </v-date-picker>
            </v-menu>


          </div>

          <Button
            @click.native="loadDataPembayaran()"
            text="Cari"
            style="height: 40px;"
            :bg-color="'bg-success'"
            class="ml-2 mb-2 mr-2"
          ></Button>
        </b-dropdown>


        <div style="padding: 0.375rem 0;">
          <Button
            @click.native="showTambahData"
            :icondepan="'mdi-printer'"
            text="Cetak"
            style="height: 40px;"
            :bg-color="'bg-success'"
            class="ml-2"
          ></Button>
        </div>
      </div>
      <v-row>
        <v-col cols="12">
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

<template v-slot:item.created_at="{ item }">
    <span>{{ new Date(item.created_at).toLocaleString() }}</span>
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
          <div style="margin-left: 30px; margin-right: 50px; margin-top: 30px;">
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

                <p v-else class="mr-2" style="margin-bottom: 0;">
                  {{ kStatus }}
                </p>
              </v-col>

              <v-col cols="2" offset="4">
                <p class="text-right mr-2" style="margin-bottom: 0;">
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
                <p class="text-right mr-2" style="margin-bottom: 0;">
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
                <p class="text-right mr-2" style="margin-bottom: 0;">
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
import Button from './component/Button.vue'
import { validationMixin } from 'vuelidate'
import { required, maxLength, numeric } from 'vuelidate/lib/validators'
import AlertSuccess from './component/alert/AlertSuccess.vue'
import VueHtml2pdf from 'vue-html2pdf'

export default {
  mixins: [validationMixin],

  validations: {
    pemNominal: { required, maxLength: maxLength(11), numeric },
  },

  components: {
    Button,
    AlertSuccess,
    VueHtml2pdf,
  },

  mounted() {
    this.loadDataPembayaran()
  },

  data() {
    return {
      //PEMBAYARAN
      pemNominal: null,
      tablePembayaran: [],
      inputanPembayaran: {},
      inputanHapus: {},
      lapTanggal: null,
      menutanggal: false,
      inputPencarian: {},
      totalPembayaran: null,
        akun: '',

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
      kTanggal: null,
      kStatus: null,

      //ETC
      id: null,
      loading: true,
      inputLoading: false,
      errored: false,
      inputError: null,
      show: true,
      showAlert: false,
      alertMessage: '',
      judulModal: null,
      jenisHapus: null,
      search: '',
      submitStatus: null,
      namaKwitansi: null,

      valueWhenIsEmpty: '',
      options: {
        locale: 'id-ID',
        length: 11,
        precision: 0,
      },

      //TEST
    }
  },

  computed: {

    headersPembayaran() {
      return [
        // {
        //     text: "Nama Pelanggan",
        //     align: "start",
        //     sortable: true,
        //     value: "pelanggan.nama_pelanggan"
        // },
        {
          text: 'Nama',
          value: 'nama_pelanggan',
        },
        {
          text: 'Nominal',
          value: 'nominal',
        },
  {
          text: 'Tanggal',
          value: 'created_at',
        },
        //   {
        //   text: 'Akun',
        //   value: 'akun',
        // },

        {
                    text: "Actions",
                    value: "actions",
                    sortable: false,
                    align: "center"
                }
      ]
    },

    headersKeranjang() {
      return [
        {
          text: 'Jenis Barang',
          value: 'jenis_barang.nama',
        },
        {
          text: 'Ukuran',
          value: 'ukuran',
        },
        {
          text: 'Qty',
          value: 'qty',
        },
        {
          text: 'Status Pengerjaan',
          value: 'status_pengerjaan',
        },

        {
          text: 'Total',
          value: 'total',
        },
        {
          text: 'Catatan',
          value: 'catatan',
        },
      ]
    },

    headersKwitansi() {
      return [
        {
          text: 'Deskripsi',
          value: 'deskripsi',
        },

        {
          text: 'Qty',
          value: 'qty',
        },

        {
          text: 'Biaya Tambahan',
          value: 'biayaTambahan',
        },

        {
          text: 'Total',
          value: 'total',
        },
      ]
    },
  },

  methods: {
    //ETC

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
    },

    generateReport(id, idPesanan, pemNominal, kTanggal) {
      let tempTableKeranjang = []
      this.id = id
      this.idPesanan = idPesanan
      this.pemNominal = pemNominal
      // this.kTanggal = kTanggal

      const months = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
      ]
      let current_datetime = new Date(kTanggal)
      this.kTanggal =
        current_datetime.getDate() +
        ' ' +
        months[current_datetime.getMonth()] +
        ' ' +
        current_datetime.getFullYear()
      // this.kTanggal = new Date(kTanggal).toString("MMMM yyyy");

      this.namaKwitansi = 'kwitansi bigger ' + id
      this.axios
        .get(this.$store.state.apiUrl + 'pesanan/showPesananID/' + idPesanan, {
          headers: {
            Authorization: 'Bearer ' + this.$store.state.biggertoken,
          },
        })
        .then((response) => {
          $.each(response.data.data, function (
            dataKeranjang,
            valueDataKeranjang,
          ) {
            let eachITem = valueDataKeranjang
            let catatan = ' '

            if (valueDataKeranjang.catatan != null) {
              catatan = valueDataKeranjang.catatan
            }

            let deskripsi =
              valueDataKeranjang.jenis_barang.nama +
              ' (' +
              valueDataKeranjang.ukuran +
              ' ' +
              valueDataKeranjang.jenis_barang.satuan +
              ') ' +
              catatan
            eachITem['deskripsi'] = deskripsi
            tempTableKeranjang.push(eachITem)
          })
          this.tabelKwitansi = tempTableKeranjang

          console.log('tabelKwitansi ', response.data)
          this.kSubTotal = response.data.subTotal
          this.kDiskon = response.data.dataPesanan.diskon
          this.kTotal = response.data.dataPesanan.total_biaya
          this.kTerimaDari = response.data.dataPesanan.pelanggan.nama_pelanggan

          if (response.data.dataPesanan.status_bayar == 'lunas') {
            this.kStatus = 'Lunas'
          } else {
            this.kStatus = 'Belum Lunas'
          }

          this.$refs.html2Pdf.generatePdf()
        })
        .catch((error) => {
          console.log(error)
          this.errored = true
        })
        .finally(() => {
          this.loading = false
        })
    },

    filterOnlyCapsText(value, search, item) {
      return (
        value != null &&
        search != null &&
        typeof value === 'string' &&
        value.toString().toLocaleLowerCase().indexOf(search) !== -1
      )
    },

    loadDataPembayaran() {
      this.inputPencarian.lapTanggal = this.lapTanggal
      this.inputPencarian.akun = this.akun

        console.log("ini akun: "+this.akun);
      this.axios
        .post(
          this.$store.state.apiUrl + 'pembayaran/pencarianLaporan',
          this.inputPencarian,
          {
            headers: {
              Authorization: 'Bearer ' + this.$store.state.biggertoken,
            },
          },
        )
        .then((response) => {
          console.log("akunya: "+JSON.stringify(response.data.akunnya))
          this.tablePembayaran = response.data.pembayaran
          this.totalPembayaran = response.data.total
        })
        .catch((error) => {
          console.log(error)
          this.errored = true
        })
        .finally(() => {
          this.loading = false
        })
    },


    //KERANJANG
    loadDataKeranjang() {
      let tempUrl = 'keranjang/showKeranjangbyID/' + this.idPesanan

      this.axios
        .get(this.$store.state.apiUrl + tempUrl, {
          headers: {
            Authorization: 'Bearer ' + this.$store.state.biggertoken,
          },
        })
        .then((response) => {
          console.log('data keranjang', response.data['data'])
          this.tabelKeranjang = response.data['data']
        })
        .catch((error) => {
          console.log(error)
          this.errored = true
        })
        .finally(() => (this.loading = false))
    },
  },
}
</script>

<style>
.v-application ul {
  padding: 0;
}
</style>
