<template>
    <section class="mt-4" v-if="employee">
        <form @submit="checkForm" method="post">
            <div class="columns">
                <div class="column is-two-thirds">
                    <h2 class="title is-3"><center>Anagrafica</center></h2>
                    <div class="columns">
                        <div class="column is-half">
                            <b-field label="Nome *" label-position="on-border">
                                <b-input
                                    required
                                    v-model="employee.firstname"
                                    validation-message="Il Nome è obbligatorio e deve essere compreso tra 3 e 64 caratteri!"
                                    pattern="^[\sa-zA-Z]{3,64}$"
                                    placeholder="Mario"
                                    maxlength=64>
                                </b-input>
                            </b-field>
                        </div>
                        <div class="column is-half">
                            <b-field label="Cognome *" label-position="on-border">
                                <b-input
                                    required
                                    v-model="employee.lastname"
                                    validation-message="Il Cognome è obbligatorio e deve essere compreso tra 3 e 64 caratteri!"
                                    pattern="^[\sa-zA-Z]{3,64}$"
                                    placeholder="Rossi"
                                    maxlength=64>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-two-fifths">
                            <b-field label="Codice Fiscale *" label-position="on-border">
                                <b-input
                                    v-model="employee.cf"
                                    pattern="^(?:[a-zA-Z][aeiouAEIOU][aeiouxAEIOUX]|[b-df-hj-np-tv-zB-DF-HJ-NP-TV-Z]{2}[a-zA-Z]){2}(?:[\dlmnp-vLMNP-V]{2}(?:[a-ehlmpr-tA-EHLMPR-T](?:[04lqLQ][1-9mnp-vMNP-V]|[15mrMR][\dmlnp-vLMNP-V]|[26nsNS][0-8lmnp-uLMNP-U])|[dhpsDHPS][37ptPT][0lL]|[acelmrtACELMRT][37ptPT][01lmLM]|[ac-ehlmlpr-tAC-EHLMPR-T][26nsNS][9vV])|(?:[02468lnqsuLNQSU][048lquLQU]|[13579mprtvMPRTV][26nsNS])bB[26nsNS][9vV])(?:[a-mzA-MZ][1-9mnp-vMNP-V][\dlmnp-vLMNP-V]{2}|[a-mA-M][0lL](?:[1-9mnp-vMNP-V][\dlmnp-vLMNP-V]|[0lL][1-9mnp-vMNP-V]))[a-zA-Z]$"
                                    validation-message="Il Codice Fiscale è obbligatorio e deve rispettare le norme vigenti!"
                                    required>
                                </b-input>
                            </b-field>
                        </div>
                        <div class="column is-one-fifth">
                            <b-field label="Data di Nascita" label-position="on-border">
                                <b-datepicker
                                    v-model="employee.birthday"
                                    locale="it-IT"
                                    icon="calendar-today"
                                    trap-focus>
                                </b-datepicker>
                            </b-field>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-three-fifths">
                            <b-field label="Indirizzo" label-position="on-border">
                                <b-input
                                    v-model="employee.address"
                                    placeholder="Via dei Ciliegi, 99"
                                    maxlength=150>
                                </b-input>
                            </b-field>
                        </div>
                        <div class="column is-one-fifth">
                            <b-field label="CAP" label-position="on-border">
                                <b-input
                                    v-model="employee.cap"
                                    pattern="[0-9]{5}"
                                    maxlength=5
                                    placeholder="00100"
                                    validation-message="Sono accettati solamente dei numeri">
                                </b-input>
                            </b-field>
                        </div>
                        <div class="column is-one-fifth">
                            <b-field label="Città" label-position="on-border">
                                <b-input
                                    maxlength="150"
                                    pattern="^[\sa-zA-Z]{3,150}$"
                                    placeholder="Città del Capo"
                                    v-model="employee.city">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-half">
                            <b-field label="Telefono Fisso" label-position="on-border">
                                <b-input
                                    v-model="employee.phone"
                                    validation-message="Il numero inserito non sembra essere valido"
                                    pattern="^((\+|00)?39)?\s0\d{1,3}\s?(\d{3}\s(\d{3,4}|(\d{2}\s\d{2}))|\d{6,7})$"
                                    placeholder="XXXX XXX XXX">
                                </b-input>
                            </b-field>
                        </div>
                        <div class="column is-half">
                            <b-field label="Cellulare" label-position="on-border">
                                <b-input
                                    v-model="employee.mobile"
                                    validation-message="Il numero inserito non sembra essere valido"
                                    pattern="^((\+|00)?39)?\s3\d{2}\s?(\d{3}\s(\d{3,4}|(\d{2}\s\d{2}))|\d{6,7})$"
                                    placeholder="XXX XXX X  XXX">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-full">
                            <b-field label="Email" label-position="on-border">
                                <b-input
                                    v-model="employee.email"
                                    validation-message="L'Indirizzo email inserito non sembra essere valido"
                                    type="email"
                                    placeholder="nome@dominio.it">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                </div>
                <div class="column is-one-third">
                    <div class="columns">
                        <div class="column is-full">
                            <h2 class="title is-3"><center>Collaborazioni</center></h2>
                            <table class="table is-striped is-pulled-right">
                                <thead>
                                    <tr>
                                        <th>Inizio</th>
                                        <th>Fine</th>
                                        <th>Tipo</th>
                                        <th>
                                            <b-button type="is-light is-success is-small" icon-right="plus" @click="newEngagement()">Nuova</b-button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in employee.engagements" :key="row.id">
                                        <td>{{ row.begin }}</td>
                                        <td>{{ row.end }}</td>
                                        <td>{{ row.hoursWeek.type }} - {{ row.hoursWeek.totalHours }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <b-field label="Note" label-position="on-border">
                        <wysiwyg v-model="employee.notes" />
                    </b-field>
                </div>
            </div>
            <div class="field is-grouped is-grouped-right">
                <p class="control"><b-button type="is-primary" icon-right="content-save" native-type="submit">Salva</b-button></p>
                <p class="control" v-if="employee.id"><b-button type="is-danger" icon-right="trash-can" v-on:click="deleteEmployee">Elimina</b-button></p>
                <p class="control"><b-button type="is-light" icon-right="cancel" @click="backToList">Annulla</b-button></p>
            </div>
        </form>
    </section>
</template>

<script>
//import Cleave from 'cleave.js'
//import 'cleave.js/dist/addons/cleave-phone.it';
import AddEngagement from './addEngagement';

// const cleave = {
//     name: 'cleave',
//     bind(el, binding) {
//         const input = el.querySelector('input')
//         console.log(input)
//         input._vCleave = new Cleave(input, binding.value)
//     },
//     unbind(el) {
//         const input = el.querySelector('input')
//         input._vCleave.destroy()
//     }
// }
export default {
    // directives: { cleave },
    props: ['employee', 'hoursWeeks'],
    methods: {
        backToList: function() {
            this.$router.push({name: 'Collaboratori'});
        },
        checkForm: function(e) {
            e.preventDefault();
            if (this.$lodash.isDate(this.employee.birthday)) {
                this.employee.birthday = this.employee.birthday.getFullYear() + '-' + (this.employee.birthday.getMonth() + 1) + '-' + this.employee.birthday.getDate();
            }
            if (this.employee.id === null) {
                this.$http.post(`/employees`, this.employee).then(({ response }) => {
                    console.log(response)
                    this.$router.push({name: 'Collaboratori'});
                }).catch((error) => {
                    console.log(error);
                })
            } else {
                this.$http.patch(`/employees/` + this.employee.id, this.employee).then(({ response }) => {
                    console.log(response)
                    this.$router.push({name: 'Collaboratori'});
                }).catch((error) => {
                    console.log(error);
                })
            }
        },
        deleteEmployee: function() {
            this.$http.delete(`/employees/` + this.employee.id).then(({response}) => {
                console.log(response);
                this.$router.push({name: 'Collaboratori'});
            }).catch((error) => {
                console.log(error);
            })
        },
        newEngagement: function() {
            this.$http.get(`/hoursweek`).then(({ data }) => {
                let hoursWeek = data.items;
                let engagement = { begin: null, end: null, employeeId: this.employee.id, hoursWeekId: null };
                this.$buefy.modal.open({
                    parent: this,
                    component: AddEngagement,
                    props: { engagement: engagement, hoursWeek: hoursWeek, isNew: true },
                    hasModalCard: true,
                    trapFocus: true
                })
            })
        },
    },
}
</script>