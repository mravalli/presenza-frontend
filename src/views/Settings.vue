<template>
    <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Impostazioni
                    </h1>
                    <h2 class="subtitle">
                        Tutti i campi con l'asterisco <span style="color: red;">*</span> sono obbligatori
                    </h2>
                </div>
            </div>
        </section>
        <section>
            <section class="mt-4" v-if="company">
                <form @submit="checkForm" method="post">
                    <div class="box">
                        <h3 class="title">Dati Anagrafici Azienda</h3>
                        <div class="columns">
                            <div class="column is-full">
                                <b-field label="Società *" label-position="on-border">
                                    <b-input
                                        required
                                        v-model="company.fullname"
                                        validation-message="Il Nome della società è obbligatorio e deve essere compreso tra 3 e 150 caratteri!"
                                        pattern="^[\sa-zA-Z]{3,150}$"
                                        placeholder="Società S.r.l."
                                        maxlength=150>
                                    </b-input>
                                </b-field>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-half">
                                <b-field label="Codice Fiscale *" label-position="on-border">
                                    <b-input
                                        v-model="company.cf"
                                        pattern="((?:[a-zA-Z][aeiouAEIOU][aeiouxAEIOUX]|[b-df-hj-np-tv-zB-DF-HJ-NP-TV-Z]{2}[a-zA-Z]){2}(?:[\dlmnp-vLMNP-V]{2}(?:[a-ehlmpr-tA-EHLMPR-T](?:[04lqLQ][1-9mnp-vMNP-V]|[15mrMR][\dmlnp-vLMNP-V]|[26nsNS][0-8lmnp-uLMNP-U])|[dhpsDHPS][37ptPT][0lL]|[acelmrtACELMRT][37ptPT][01lmLM]|[ac-ehlmlpr-tAC-EHLMPR-T][26nsNS][9vV])|(?:[02468lnqsuLNQSU][048lquLQU]|[13579mprtvMPRTV][26nsNS])bB[26nsNS][9vV])(?:[a-mzA-MZ][1-9mnp-vMNP-V][\dlmnp-vLMNP-V]{2}|[a-mA-M][0lL](?:[1-9mnp-vMNP-V][\dlmnp-vLMNP-V]|[0lL][1-9mnp-vMNP-V]))[a-zA-Z])|[a-zA-Z]{0,2}[\d]{11}$"
                                        validation-message="Il Codice Fiscale è obbligatorio e deve rispettare le norme vigenti!"
                                        required>
                                    </b-input>
                                </b-field>
                            </div><div class="column is-half">
                                <b-field label="Partita Iva" label-position="on-border">
                                    <b-input
                                        v-model="company.vat"
                                        pattern="^[\w]{2}[\d]{11}$"
                                        validation-message="Il formato della Partita Iva sembra non essere corretto!"
                                        placeholder="IT00000000001">
                                    </b-input>
                                </b-field>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-half">
                                <div class="columns">
                                    <div class="column is-full">
                                        <b-field label="Indirizzo" label-position="on-border">
                                            <b-input
                                                v-model="company.address"
                                                placeholder="Via dei Ciliegi, 99"
                                                maxlength=150
                                                required>
                                            </b-input>
                                        </b-field>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-one-third">
                                        <b-field label="CAP" label-position="on-border">
                                            <b-input
                                                v-model="company.cap"
                                                pattern="[0-9]{5}"
                                                maxlength=5
                                                placeholder="00100"
                                                validation-message="Sono accettati solamente dei numeri"
                                                required>
                                            </b-input>
                                        </b-field>
                                    </div>
                                    <div class="column">
                                        <b-field label="Città" label-position="on-border">
                                            <b-input
                                                maxlength="150"
                                                pattern="^[\sa-zA-Z]{3,150}$"
                                                placeholder="Città del Capo"
                                                v-model="company.city"
                                                required>
                                            </b-input>
                                        </b-field>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-half">
                                <div class="columns">
                                    <div class="column is-full">
                                        <b-field label="Telefono Fisso" label-position="on-border">
                                            <b-input
                                                v-model="company.phone"
                                                validation-message="Il numero inserito non sembra essere valido"
                                                pattern="(0{1}[1-9]{1,3})[\s|\.|\-]?(\d{4,})"
                                                placeholder="XXXX XXX XXX"
                                                required>
                                            </b-input>
                                        </b-field>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-full">
                                        <b-field label="Email" label-position="on-border">
                                            <b-input
                                                v-model="company.email"
                                                validation-message="L'Indirizzo email inserito non sembra essere valido"
                                                pattern="^((?!\.)[\w-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$"
                                                placeholder="nome@dominio.it"
                                                required>
                                            </b-input>
                                        </b-field>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <h3 class="title">Riferimenti Contrattuali</h3>
                        <div class="columns">
                            <div class="column is-one-third">
                                <b-field label="Ferie Retribuite Annue" label-position="on-border">
                                    <b-input
                                        v-model="company.daysOff"
                                        type="number"
                                        min="28">
                                    </b-input>
                                    <p class="control">
                                        <span class="button is-static">giorni</span>
                                    </p>
                                </b-field>
                            </div>
                            <div class="column is-one-third">
                                <b-field label="Ore di Permesso Annue" label-position="on-border">
                                    <b-input
                                        v-model="company.hoursLeave"
                                        type="number"
                                        min="88">
                                    </b-input>
                                    <p class="control">
                                        <span class="button is-static">ore</span>
                                    </p>
                                </b-field>
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <div class="columns">
                            <div class="column">
                                <b-field label="Note" label-position="on-border">
                                    <wysiwyg v-model="company.notes" />
                                </b-field>
                            </div>
                        </div>
                    </div>
                        <div class="field is-grouped is-grouped-right">
                            <p class="control"><b-button type="is-primary" icon-right="content-save" native-type="submit">Salva</b-button></p>
                            <p class="control"><b-button type="is-light" icon-right="cancel" @click="backToHome">Annulla</b-button></p>
                        </div>
                </form>
            </section>
        </section>
    </section>
</template>
<script>
    export default {
        data() {
            let company;
            this.$http.get(`/settings`)
                .then(({data}) => {
                    this.company = data;
                })
                .catch((error) => {
                    console.debug(error);
                });
            return {
                company: company,
            }
        },
        methods: {
            backToHome: function() {
                this.$router.push({name: 'Home'});
            },
            checkForm: function(e) {
                e.preventDefault();
                this.company.daysOff = parseInt(this.company.daysOff);
                this.company.hoursLeave = parseInt(this.company.hoursLeave);
                this.$http.patch(`/settings`, this.company).then(({ response }) => {
                    console.log(response)
                    this.$router.push({name: 'Home'});
                }).catch((error) => {
                    console.log(error);
                })
            }
        },
    }
</script>