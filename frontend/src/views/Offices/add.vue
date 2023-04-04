<template>
   <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Nuovo Sede
                    </h1>
                    <h2 class="subtitle">
                        Tutti i campi con l'asterisco <span style="color: red;">*</span> sono obbligatori
                    </h2>
                </div>
            </div>
        </section>
        <section>
            <office-form :office="office" :managers="managers"></office-form>
        </section>
   </section>
</template>

<script>
import OfficeForm from './_form.vue'
export default {
    components: { 'office-form': OfficeForm},
    data() {
        let managers;
        this.$http.get(`/employees`).then(({ data }) => {
            this.managers = data.items
        });
        return {
            office: {
                id: null,
                name: null,
                location: null,
                managerId: null,
            },
            managers: managers
        }
    }
}
</script>