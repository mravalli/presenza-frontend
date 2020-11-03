<template>
    <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        {{ fullname }}
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
            let employee;
            let hoursWeeks;
            let fullname = '';
            this.$http.get(`/employees/${this.$route.params.id}`)
                .then(({data}) => {
                    this.employee   = data.employee;
                    this.employee.birthday = new Date(data.employee.birthday);
                    this.fullname   = data.employee.firstname + ' ' + data.employee.lastname;
                    this.hoursWeeks = data.hoursWeeks;
                })
                .catch((error) => {
                    console.debug(error);
                });
            return {
                employee:   employee,
                fullname:   fullname,
                hoursWeeks: hoursWeeks,
            }
        }
    }
</script>