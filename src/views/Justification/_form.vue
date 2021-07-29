<template>
    <section class="mt-4" v-if="justification">
        <form @submit="checkForm" method="post">
            <div class="columns">
                <div class="column">
                    <div class="columns">
                        <div class="column is-three-fifths">
                            <b-field label="Nome *" label-position="on-border">
                                <b-input
                                    required
                                    v-model="justification.name"
                                    validation-message="Il Nome è obbligatorio e deve essere compreso tra 3 e 64 caratteri!"
                                    pattern="^[\sa-zA-Zàèìòù]{3,64}$"
                                    placeholder="Malattia Figli"
                                    maxlength=64>
                                </b-input>
                            </b-field>
                        </div>
                        <div class="column is-one-fifth">
                            <b-field label="Codice Breve *" label-position="on-border">
                                <b-input
                                    required
                                    v-model="justification.code"
                                    validation-message="Il Codice è obbligatorio e deve essere compreso massimo 3 caratteri!"
                                    pattern="^[\sa-zA-Zàèìòù]{1,3}$"
                                    placeholder="Malattia Figli"
                                    maxlength=3>
                                </b-input>
                            </b-field>
                        </div>
                        <div class="column is-one-fifth">
                            <b-field label-position="on-border">
                                <template slot="label">
                                    Limite
                                    <b-tooltip type="is-dark" label="Inserire un valore solo se questo tipo di giustificativo è limitato nel tempo">
                                        <b-icon size="is-small" icon="help-circle-outline"></b-icon>
                                    </b-tooltip>
                                </template>
                                <b-input
                                    v-model="justification.limit"
                                    type="number">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                </div>            
            </div>
            

            <div class="field is-grouped is-grouped-right">
                <p class="control"><b-button type="is-primary" icon-right="content-save" native-type="submit">Salva</b-button></p>
                <p class="control" v-if="justification.id"><b-button type="is-danger" icon-right="trash-can" v-on:click="deleteJustification">Elimina</b-button></p>
                <p class="control"><b-button type="is-light" icon-right="cancel" @click="backToList">Annulla</b-button></p>
            </div>
        </form>
    </section>
</template>
<script>
    export default {
        props: ['justification'],
        methods: {
            backToList: function() {
                this.$router.push({name: 'Giustificativi'});
            },
            checkForm: function(e) {
                e.preventDefault();
                if (this.justification.id === null) {
                    this.$http.post(`/justification`, this.justification).then(() => {
                        this.backToList();
                    }).catch((error) => {
                        console.log(error)
                    })
                } else {
                    this.$http.patch(`/justification/${this.justification.id}`, this.justification).then(() => {
                        this.backToList();
                    }).catch((error) => {
                        console.log(error)
                    })
                }
            },
            deleteJustification: function() {
                this.$http.delete(`/justification/${this.justification.id}`).then(() => {
                    this.backToList();
                }).catch((error) => {
                    console.log(error)
                })
            }
        }
    }
</script>