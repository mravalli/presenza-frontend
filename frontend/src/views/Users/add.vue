<template>
   <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Nuovo Utente
                    </h1>
                    <h2 class="subtitle">
                        Tutti i campi con l'asterisco <span style="color: red;">*</span> sono obbligatori
                    </h2>
                </div>
            </div>
        </section>
        <section class="mt-4" v-if="user">
            <user-form :user="user"></user-form>
        </section>
   </section>
</template>

<script>
import UserForm from './_form.vue'
export default {
    components: { 'user-form': UserForm},
    data() {
        return {
            user: {
                id: null,
                firstname: null,
                lastname: null,
                email: null,
                username: null,
            },
        }
    }
}
</script>