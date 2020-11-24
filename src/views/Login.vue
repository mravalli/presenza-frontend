<template>
    <section>
        <div class="hero is-primary">
            <div class="hero-body">
                <center><img src="/img/logo-big.png" alt="Neuro3 Presenza" width="450" height="150" /></center>
                <div class="columns is-centered">
                    <div class="column is-one-third">
                        <div class="notification is-primary is-light">
                            <b-field label="Username" label-position="on-border">
                                <b-input v-model="username" maxlength="30" placeholder="username" required/>
                            </b-field>
                            <b-field label="Password" label-position="on-border">
                                <b-input v-model="password" type="password" placeholder="password" password-reveal required>
                                </b-input>
                            </b-field>
                            <b-button type="is-ligth" expanded @click="login()">Login</b-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<style>
    .hero {
        height: 100vh;
        position: relative;
    }
    .notification{
        padding-top: 20px;
        padding-bottom: 30px;
    }
</style>
<script>
    export default {
        name: 'Login',
        data() {
            return {
                username: null,
                password: null
            }
        },
        methods: {
            login: function() {
                if (this.username && this.password) {
                    this.$store.dispatch('login', {username: this.username, password: this.password})
                }
            }
        }
    }
</script>