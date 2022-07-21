<template>
    <div class="dflex">
        <div class="dLeft">
            <div class="logo wow fadeInDown">
                <img
                    :src="'/img/web/bigger.png'"
                    alt="Logo"
                />
            </div>
            <div class="loginForm">
                <form @submit.prevent="goLogin">
                    <div class="loginTitle wow fadeInUp">
                        Login Sebagai Admin
                    </div>
                    <div class="wow fadeInUp">
                        <v-text-field
                            v-model="email"
                            class="rounded-lg"
                            :error-messages="emailErrors"
                            type="email"
                            label="Email"
                            @input="$v.email.$touch()"
                            @blur="$v.email.$touch()"
                            >{{ email }}</v-text-field
                        >
                    </div>

                    <div class="wow fadeInUp">
                        <v-text-field
                            v-model="password"
                            type="Password"
                            class="rounded-lg"
                            :error-messages="passwordErrors"
                            label="Password"
                            @input="$v.password.$touch()"
                            @blur="$v.password.$touch()"
                            >{{ password }}</v-text-field
                        >
                    </div>

                    <!-- <div class="wow fadeInUp">
                        <input
                            type="password"
                            name="password"
                            value=""
                            id="password"
                            placeholder="Password"
                            class="form-control"
                        />
                        <div id="password-error" class="error">
                            Please enter a valid Password.
                        </div>
                    </div> -->
                    <div class="loginExtra wow fadeInUp">
                        <!-- <label class="kt-checkbox">
                            <input type="checkbox" name="remember" /> Remember
                            me
                            <span></span>
                        </label> -->
                        <!-- <a href="javascript:;" id="login-forgot"
                            >Forget Password ?</a
                        > -->
                    </div>

                    <div v-if="inputLoading">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                        ></v-progress-circular>
                    </div>

                    <div v-else class="loginAction wow flipInX">
                        <input
                            type="submit"
                            name="submit"
                            value="Login"
                            id=""
                            class="btn-default"
                        />
                    </div>
                </form>
            </div>
        </div>
        <div class="dRight">
            <div>
                <h3 class="companyName wow fadeInUp">Bigger</h3>
                <div class="companyDesc wow fadeInDown" data-wow-delay="0.2s">
                    Percetakan - offset - packaging
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, maxLength } from "vuelidate/lib/validators";

export default {
    mixins: [validationMixin],

    data() {
        return {
            email: null,
            password: null,
            submitStatus: null,
            inputLoading: null,
            inputan: {}
        };
    },

    validations: {
        email: { required },
        password: { required }
    },

    computed: {
        emailErrors() {
            const errors = [];
            if (!this.$v.email.$dirty) return errors;
            !this.$v.email.required && errors.push("Email harus di isi.");
            return errors;
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.password.$dirty) return errors;
            !this.$v.password.required && errors.push("Password harus di isi.");
            return errors;
        }
    },

    methods: {
        updateToken(newToken) {
            this.$store.state.token = newToken;
        },

        updateToken(newToken) {
            localStorage.setItem("biggerToken", newToken);
        },

        goLogin() {
            if (this.$v.$invalid) {
                this.submitStatus = "ERROR";
                alert("periksa kembali email dan password mu");
            } else {
                this.inputLoading = true;
                this.inputError = null;

                this.inputan.email = this.email;
                this.inputan.password = this.password;

                this.axios
                    // .post("https://bigger.web.id/api/auth/login", this.inputan)
                    // .post("http://localhost:8000/api/auth/login",  this.inputan )
                    .post("https://bigger.genossys.com/api/auth/login",  this.inputan )
                    .then(
                        response => {
                            console.log(response);

                            if (response.status === 200) {
                                // this.alertMessage = response["data"]["msg"];
                                // this.berhasilSimpan();
                                // this.$refs.alertSuccess.showAlert();
                                // alert(response["data"]["access_token"]);
                                this.updateToken(
                                    response["data"]["access_token"]
                                );
                                window.location.replace("/admin/dashboard");
                            } else {
                                // throw error and go to catch block
                                alert("error bos");
                                // this.inputError = "Gagal input data";
                            }
                        }
                        // console.log(response.data)
                    )
                    .catch(error => {
                        console.log(error);
                        alert("Email atau password tidak terdaftar");
                        this.inputLoading = false;
                        // this.inputError = "Gagal input data";
                    })
                    .finally(() => {
                        this.inputLoading = false;
                    });
            }
        }
    },
    mounted(){
        if(localStorage.getItem("biggerToken") != null){
            this.$router.push({name: "Dashboard"});
        }
    }
};
</script>

<style scoped>
img {
    max-width: 100%;
}
.logo {
    text-align: center;
    max-width: 300px;
    margin: auto;
}
.dflex {
    display: flex;
    height: 100vh;
    align-items: center;
    flex-direction: row;
    justify-content: space-between;
}
.dLeft {
    border-radius: 25px;
    padding: 2rem;
    background: #fff;
    width: 450px;
    margin: auto;
}
.dRight {
    background-color: #ccc;
    height: 100vh;
    background-image: linear-gradient(
            to bottom,
            rgba(0, 0, 0, 0.5),
            rgba(0, 0, 0, 0.82)
        ),
        url(http://www.psd2concrete5.com/images/slider-img1.jpg);
    background-size: cover;
    width: 60%;
    float: right;
    display: flex;
    align-items: center;
    padding: 40px;
}
.loginForm {
    padding: 20px 0;
}
.loginForm .loginTitle {
    text-align: center;
    font-size: 1.5rem;
    color: #000;
    font-weight: 500;
    margin-bottom: 50px;
}
.loginForm .field-group {
    margin-bottom: 15px;
}
/* input[type="text"],
input[type="password"] {
    height: 46px;
    border-radius: 0;
    border: 0;
    background-color: aliceblue !important;
    padding: 1rem 0;
    color: #6c7293;
    width: 100%;
    margin-bottom: 5px;
    font-family: "Poppins", sans-serif;
}
input:focus {
    outline: none;
} */
.error {
    color: #f00;
    font-size: 12px;
}
.loginExtra {
    margin-top: 26px;
    display: flex;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    font-size: 14px;
    font-weight: 500;
}
/* a {
    color: #6c7293;
    text-decoration: none;
    transition: all ease-in-out 0.5s;
}
a:hover {
    color: #000;
} */
.loginAction {
    margin-top: 35px;
    text-align: center;
}
.btn-default {
    font-family: "Poppins", sans-serif;
    background-color: #ff2222;
    border: 1px solid #ff2222;
    color: #fff;
    height: 46px;
    padding-left: 25px;
    padding-right: 25px;
    font-weight: 500;
    border-radius: 30px;
    cursor: pointer;
    transition: all ease-in-out 0.5s;
}
.btn-default:hover {
    background-color: #000;
    border-color: #000;
}
h3 {
    color: #fff;
    font-size: 42px;
    margin: 0 0 15px;
}
.companyDesc {
    color: #fff;
    display: block;
}
.wow {
    visibility: hidden;
}

@media only screen and (max-width: 800px) {
    .dflex {
        flex-direction: column;
    }
    .dRight {
        width: 100%;
        text-align: center;
    }
}
@media only screen and (max-width: 479px) {
    .dLeft {
        width: 100%;
        padding: 30px 15px;
    }
    h3 {
        font-size: 20px;
    }
    .dRight {
        padding: 40px 15px;
    }
}
</style>
