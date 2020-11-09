<template>
    <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        {{ title }}
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
            let title;
            let hoursWeek;
            this.$http.get(`/hoursweek/${this.$route.params.id}`)
                .then(({data}) => {
                    if (data.hoursWeek.type === 'FT') {
                        this.title = 'Tempo Pieno ';
                    } else {
                        this.title = 'Tempo Parziale ';
                    }
                    this.title += 'su ' + data.hoursWeek.days + ' giorni la settimana';
                    this.hoursWeek = data.hoursWeek;
                })
                .catch((error) => {
                    console.debug(error);
                });
            return {
                title:     title,
                hoursWeek: hoursWeek
            }
        }
    }
</script>