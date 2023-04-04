<template>
   <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Nuovo Format
                    </h1>
                    <h2 class="subtitle">
                        Tutti i campi con l'asterisco <span style="color: red;">*</span> sono obbligatori
                    </h2>
                </div>
            </div>
        </section>
        <section>
            <hours-week-form :hoursWeek="hoursWeek"></hours-week-form>
        </section>
   </section>
</template>

<script>
    import HoursWeekForm from './_form.vue'
    export default {
        components: { 'hours-week-form': HoursWeekForm},
        data() {
            return {
                hoursWeek: {
                    id: null,
                    type: null,
                    days: null,
                    mon: null,
                    tue: null,
                    wed: null,
                    thu: null,
                    fri: null,
                    sat: null,
                    sun: null
                },
            }
        }
    }
</script>