<template>
    <form @submit="checkForm" method="post">
        <div class="columns">
            <div class="column is-half">
                <b-field label="Nome *" label-position="on-border">
                    <b-input
                        required
                        v-model="user.firstname"
                        validation-message="Il nome è obbligatorio"
                        placeholder="Mario"
                        maxlength=64>
                    </b-input>
                </b-field>
            </div>
            <div class="column is-half">
                <b-field label="Cognome *" label-position="on-border">
                    <b-input
                        required
                        v-model="user.lastname"
                        validation-message="Il cognome è obbligatorio"
                        placeholder="Rossi"
                        maxlength=64>
                    </b-input>
                </b-field>
            </div>
        </div>
        <div class="columns">
            <div class="column is-half">
                <b-field label="Username *" label-position="on-border">
                    <b-input
                        required
                        v-model="user.username"
                        validation-message="Lo username è obbligatorio"
                        placeholder="username"
                        maxlength=64>
                    </b-input>
                </b-field>
                <b-field v-if="! user.id" label="Password" label-position="on-border">
                    <b-input type="password" v-model="password" password-reveal required></b-input>
                </b-field>
                <div>
                    <password v-model="password" :strength-meter-only="true" />
                </div>
            </div>
            <div class="column is-half">
                <b-field label="email *" label-position="on-border">
                    <b-input
                        required
                        v-model="user.email"
                        validation-message="Senza indirizzo email non sarà possibile ricevere notifiche"
                        placeholder="mario.rossi@example.local"
                        maxlength=150>
                    </b-input>
                </b-field>
            </div>
        </div>
        <div class="columns">
            <div class="column is-half">
                <b-button v-if="user.id" type="is-success is-light" @click="changePassword" icon-right="form-textbox-password" expanded>Cambia Password</b-button>
            </div>
            <div class="column is-half">
                <b-button type="is-success" @click="saveProfile" icon-right="content-save" expanded>Salva le modifiche</b-button>
            </div>
        </div>
    </form>
</template>
<script>
    import ChgPassword from './chgPassword';
    import Password from 'vue-password-strength-meter'
    export default {
        props: ['user'],
        components: { Password },
        data() {
            return {
                password: null
            }
        },
        methods: {
            changePassword: function() {
                this.$buefy.modal.open({
                    parent: this,
                    component: ChgPassword,
                    props: { userId: this.user.id },
                    trapFocus: true
                })
            },
            saveProfile: function() {
                if (this.password) {
                    this.user.password = this.password
                }
                if (this.user.id) {
                    this.$http.patch(`/user/${this.user.id}`, this.user).then(() => {
                        if (this.user.id) {
                            this.$store.dispatch('changeName', this.user.firstname)
                        }
                        this.$router.push({name: 'Utenti'})
                    }).catch((error) => {
                        console.log(error)
                    })
                } else {
                    this.$http.post(`/user`, this.user).then(() => {
                        this.$router.push({name: 'Utenti'})
                    }).catch((error) => {
                        console.log(error)
                    })
                }
            },
            checkForm: function(e) {
                e.preventDefault();
                this.checkForm();
            }
        }
    }
</script>