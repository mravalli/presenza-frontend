<template>
    <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title" v-if="office">
                        {{ office.name }}
                    </h1>
                    <h2 class="subtitle">
                        Tutti i campi con l'asterisco <span style="color: red;">*</span> sono obbligatori
                    </h2>
                </div>
            </div>
        </section>
        <section>
            <office-form :office="office" :managers="managers" :employees="employees"></office-form>
        </section>
    </section>
</template>
<script>
    import OfficeForm from './_form.vue'
    export default {
        components: { 'office-form': OfficeForm},
        data() {
            let office;
            let managers;
            let employees;
            this.$http.get(`/offices/${this.$route.params.id}`)
                .then(({data}) => {
                    this.office    = data.office;
                    this.managers  = data.employees;
                    this.employees = data.employees;
                })
                .catch((error) => {
                    console.debug(error);
                });
            return {
                office:    office,
                managers:  managers,
                employees: employees
            }
        }
    }
</script>