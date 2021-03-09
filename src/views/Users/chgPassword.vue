<template>
    <section>
        <form @submit="checkForm" method="post">
            <div class="modal-card" style="width: auto;">
                <header class="modal-card-head">
                    <p class="modal-card-title">Cambio Password</p>
                </header>
                <section class="modal-card-body">
                    <b-field label="Nuova Password" label-position="on-border">
                        <b-input type="password" v-model="password" password-reveal required></b-input>
                    </b-field>
                    <b-field label="Conferma Nuova Password" label-position="on-border" :type="verificationStatus">
                        <b-input type="password" v-model="repeat_password" password-reveal required></b-input>
                    </b-field>
                    <b-button type="is-primary" native-type="submit" expanded>Cambia Password</b-button>
                </section>
            </div>
        </form>
        <password v-model="password" :strength-meter-only="true" />
    </section>
</template>
<script>
    import Password from 'vue-password-strength-meter'
    export default {
        props: ['userId'],
        components: { Password },
        data() {
            return {
                password: null,
                repeat_password: null,
                verificationStatus: null
            }
        },
        methods: {
            checkForm: function(e) {
                e.preventDefault();
                if (this.password !== this.repeat_password) {
                    this.verificationStatus = "is-danger";
                } else {
                    this.verificationStatus = "is-success";
                    this.$http.patch(`/user/${this.userId}`, {password: this.password}).then(() => {
                        this.$parent.close()
                    }).catch((error) => {
                        console.log(error)
                    })
                }
            }
        }
    }
</script>