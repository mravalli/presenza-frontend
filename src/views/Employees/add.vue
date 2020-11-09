<template>
   <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Nuovo Collaboratore/Collaboratorice
                    </h1>
                    <h2 class="subtitle">
                        Tutti i campi con l'asterisco <span style="color: red;">*</span> sono obbligatori
                    </h2>
                </div>
            </div>
        </section>
        <section>
            <employee-form :employee="employee" :hoursWeeks="hoursWeeks"></employee-form>
        </section>
   </section>
</template>

<script>
import EmployeeForm from './_form.vue'
export default {
    components: { 'employee-form': EmployeeForm},
    data() {
        let hoursWeeks;
        this.$http.get(`/hoursweek`).then(({data}) => {
            this.hoursWeeks = data.items;
        }).catch((error) => {
            console.debug(error)
        })
        return {
            employee: {
                id: null,
                firstname: null,
                lastname: null,
                address: null,
                cap: null,
                city: null,
                cf: null,
                phone: null,
                mobile: null,
                notes: null
            },
            hoursWeeks: hoursWeeks
        }
    }
}
</script>