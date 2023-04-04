<template>
   <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Nuovo Giustificativo
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
        return {
            justification: {
                id: null,
                name: null,
                code: null,
                limit: null
            }
        }
    }
}
</script>