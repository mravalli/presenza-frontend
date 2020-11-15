<template>
   <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title" v-if="justification">
                        {{ justification.name }}
                    </h1>
                    <h2 class="subtitle">
                        Tutti i campi con l'asterisco <span style="color: red;">*</span> sono obbligatori
                    </h2>
                </div>
            </div>
        </section>
        <section>
            <justification-form :justification="justification"></justification-form>
        </section>
   </section>
</template>

<script>
import JustificationForm from './_form.vue'
export default {
    components: { 'justification-form': JustificationForm},
    data() {
        let justification;
        this.$http.get(`/justification/${this.$route.params.id}`).then(({ data }) => {
            this.justification = data.justification
        });
        return {
            justification: justification
        }
    }
}
</script>