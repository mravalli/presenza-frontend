<template>
   <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Nuovo Collaboratore
                    </h1>
                    <h2 class="subtitle">
                        Tutti i campi con l'asterisco <span style="color: red;">*</span> sono obbligatori
                    </h2>
                </div>
            </div>
        </section>
        <section>
            <employee-form :employee="employee"></employee-form>
        </section>
   </section>
</template>

<script>
import EmployeeForm from './_form.vue'
export default {
    components: { 'employee-form': EmployeeForm},
    data() {
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
            }
        }
    }
}
</script>