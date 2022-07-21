<template>
    <div>
        <router-view></router-view>
    </div>
</template>

<script>
import AdminBase from "./AdminBase.vue";
import Login from "./Login.vue";

export default {
      components: {
        AdminBase,
        Login
    },
    methods: {
     
    }
};
</script>


<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
