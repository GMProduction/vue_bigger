<template>
    <div class="home">

            <div class="d-flex flex-row mb-3">
                <div class="flex-grow-1"></div>

                <Button
                 @click.native="showModal"
                    :icondepan="'mdi-plus'"
                    text="Tambah Data"
                    :bg-color="'bg-success'"
                    class="ml-2"
                ></Button>

                <Button
                    :icondepan="'mdi-printer'"
                    text="print"
                    :bg-color="'bg-prim'"
                    class="ml-2"
                ></Button>
                <Button
                    text="Filter"
                    :icondepan="'mdi-filter'"
                    :bg-color="'bg-secon'"
                    class="ml-2"

                ></Button>
            </div>

        <div class="card-table">
            <b-table striped hover :items="items" borderless></b-table>
        </div>

        <b-modal ref="add-revenue-modal" hide-footer size="lg"  title="Tambah Pemasukan">
      <div class="d-block text-center">
        <h3>Hello From My Modal!</h3>
      </div>
      <b-button class="mt-3" variant="outline-danger" block @click="hideModal">Close Me</b-button>
      <b-button class="mt-2" variant="outline-warning" block @click="toggleModal">Toggle Me</b-button>
    </b-modal>

    </div>
</template>

<script>
import Button from "./component/Button.vue";

export default {
    components: {
        Button
    },
    data() {
        return {
            items: [
                {
                    Jobs: "Kardus 1000pcs ",
                    Pemesan: "Pak Joko",
                    Pesanan: "12 feb 2021",
                    Deadline: "20 feb 2021"
                },
                {
                    Jobs: "Kardus 2000pcs ",
                    Pemesan: "Mbah Sri",
                    Pesanan: "17 feb 2021",
                    Deadline: "29 feb 2021"
                },
                {
                    Jobs: "MMT 3 meter ",
                    Pemesan: "Ibnu",
                    Pesanan: "18 feb 2021",
                    Deadline: "30 feb 2021"
                }
            ]
        };
    },
    methods: {
         showModal() {
        this.$refs['add-revenue-modal'].show()
      },
      hideModal() {
        this.$refs['add-revenue-modal'].hide()
      },
    },
};
</script>
