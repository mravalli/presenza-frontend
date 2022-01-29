<template>
  <section v-if="employee" class="mt-4">
    <form method="post" @submit="checkForm">
      <div class="columns">
        <div class="column is-two-thirds">
          <h2 class="title is-3"><center>Anagrafica</center></h2>
          <div class="columns">
            <div class="column is-half">
              <b-field label="Nome *" label-position="on-border">
                <b-input
                    v-model="employee.firstname"
                    maxlength=64
                    pattern="^[\sa-zA-Zàèìòù]{3,64}$"
                    placeholder="Mario"
                    required
                    validation-message="Il Nome è obbligatorio e deve essere compreso tra 3 e 64 caratteri!">
                </b-input>
              </b-field>
            </div>
            <div class="column is-half">
              <b-field label="Cognome *" label-position="on-border">
                <b-input
                    v-model="employee.lastname"
                    maxlength=64
                    pattern="^[\sa-zA-Zàèìòù]{3,64}$"
                    placeholder="Rossi"
                    required
                    validation-message="Il Cognome è obbligatorio e deve essere compreso tra 3 e 64 caratteri!">
                </b-input>
              </b-field>
            </div>
          </div>
          <div class="columns">
            <div class="column is-one-fifth">
              <b-field label="Matr./Livello *" label-position="on-border">
                <b-input
                    v-model="employee.badge_id"
                    required
                    validation-message="Inserire un numero di matricola o il livello!">
                </b-input>
              </b-field>
            </div>
            <div class="column is-two-fifths">
              <b-field label="Codice Fiscale *" label-position="on-border">
                <b-input
                    v-model="employee.cf"
                    pattern="^(?:[a-zA-Z][aeiouAEIOU][aeiouxAEIOUX]|[b-df-hj-np-tv-zB-DF-HJ-NP-TV-Z]{2}[a-zA-Z]){2}(?:[\dlmnp-vLMNP-V]{2}(?:[a-ehlmpr-tA-EHLMPR-T](?:[04lqLQ][1-9mnp-vMNP-V]|[15mrMR][\dmlnp-vLMNP-V]|[26nsNS][0-8lmnp-uLMNP-U])|[dhpsDHPS][37ptPT][0lL]|[acelmrtACELMRT][37ptPT][01lmLM]|[ac-ehlmlpr-tAC-EHLMPR-T][26nsNS][9vV])|(?:[02468lnqsuLNQSU][048lquLQU]|[13579mprtvMPRTV][26nsNS])bB[26nsNS][9vV])(?:[a-mzA-MZ][1-9mnp-vMNP-V][\dlmnp-vLMNP-V]{2}|[a-mA-M][0lL](?:[1-9mnp-vMNP-V][\dlmnp-vLMNP-V]|[0lL][1-9mnp-vMNP-V]))[a-zA-Z]$"
                    required
                    validation-message="Il Codice Fiscale è obbligatorio e deve rispettare le norme vigenti!">
                </b-input>
              </b-field>
            </div>
            <div class="column is-one-fifth">
              <b-field label="Data di Nascita *" label-position="on-border">
                <b-datepicker
                    v-model="employee.birthday"
                    icon="calendar-today"
                    locale="it-IT"
                    required
                    trap-focus>
                </b-datepicker>
              </b-field>
            </div>
            <div class="column is-one-fifth">
              <b-field label="Data Prima Assunzione *" label-position="on-border">
                <b-datepicker
                    v-model="employee.first_engagement"
                    icon="calendar-today"
                    locale="it-IT"
                    required
                    trap-focus>
                </b-datepicker>
              </b-field>
            </div>
          </div>
          <div class="columns">
            <div class="column is-three-fifths">
              <b-field label="Indirizzo *" label-position="on-border">
                <b-input
                    v-model="employee.address"
                    maxlength=150
                    placeholder="Via dei Ciliegi, 99"
                    required
                    validation-message="Questo campo non può rimanere vuoto">
                </b-input>
              </b-field>
            </div>
            <div class="column is-one-fifth">
              <b-field label="CAP *" label-position="on-border">
                <b-input
                    v-model="employee.cap"
                    maxlength=5
                    pattern="[0-9]{5}"
                    placeholder="00100"
                    required
                    validation-message="Sono accettati solamente dei numeri">
                </b-input>
              </b-field>
            </div>
            <div class="column is-one-fifth">
              <b-field label="Città *" label-position="on-border">
                <b-input
                    v-model="employee.city"
                    maxlength="150"
                    pattern="^[\sa-zA-Zàèìòù]{3,150}$"
                    placeholder="Città del Capo"
                    required
                    validation-message="Questo campo non può rimanere vuoto">
                </b-input>
              </b-field>
            </div>
          </div>
          <div class="columns">
            <div class="column is-half">
              <b-field label="Telefono Fisso" label-position="on-border">
                <b-input
                    v-model="employee.phone"
                    placeholder="0123 456 789 o 0123 45 6789 o 0123 456789 o 012 3456789"
                    validation-message="Il numero inserito non sembra essere valido">
                </b-input>
              </b-field>
            </div>
            <div class="column is-half">
              <b-field label="Cellulare" label-position="on-border">
                <b-input
                    v-model="employee.mobile"
                    placeholder="333 123 4567 o 333 12 34 567 o 333 1234567 o 333 123 45 67"
                    validation-message="Il numero inserito non sembra essere valido">
                </b-input>
              </b-field>
            </div>
          </div>
          <div class="columns">
            <div class="column is-full">
              <b-field label="Email" label-position="on-border">
                <b-input
                    v-model="employee.email"
                    placeholder="nome@dominio.it"
                    type="email"
                    validation-message="L'Indirizzo email inserito non sembra essere valido">
                </b-input>
              </b-field>
            </div>
          </div>
        </div>
        <div class="column is-one-third">
          <div class="columns">
            <div class="column is-full">
              <h2 class="title is-3"><center>Contratti</center></h2>
              <table class="table is-striped is-pulled-right">
                <thead>
                <tr>
                  <th>Inizio</th>
                  <th>Fine</th>
                  <th>Tipo</th>
                  <th>
                    <b-button v-if="employee.id" icon-right="plus" type="is-light is-success is-small" @click="newEngagement()">Nuova</b-button>
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in employee.engagements" :key="row.id">
                  <td>{{ row.begin }}</td>
                  <td>{{ row.end }}</td>
                  <td>{{ row.hoursWeek.type }} - {{ row.hoursWeek.totalHours }}</td>
                  <td>
                    <div class="buttons is-grouped-right">
                      <b-button :disabled="row.disabled==1" size="is-small" type="is-info is-light" @click="modEngagement(row)"><b-icon icon="pencil" size="is-small"></b-icon></b-button>
                      <b-button v-if="row.end && row.disabled == 0" size="is-small" type="is-warning" @click="renewEngagement(row)"><b-icon icon="account-reactivate" size="is-small"></b-icon></b-button>
                    </div>
                  </td>
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
        <p class="control"><b-button icon-right="content-save" native-type="submit" type="is-primary">Salva</b-button></p>
        <p v-if="employee.id" class="control"><b-button icon-right="trash-can" type="is-danger" v-on:click="deleteEmployee">Elimina</b-button></p>
        <p class="control"><b-button icon-right="cancel" type="is-light" @click="backToList">Annulla</b-button></p>
      </div>
    </form>
  </section>
</template>

<script>
//import Cleave from 'cleave.js'
//import 'cleave.js/dist/addons/cleave-phone.it';
import AddEngagement from './addEngagement.vue';

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
      this.$router.push({name: 'Dipendenti'});
    },
    checkForm: function(e) {
      e.preventDefault();
      if (this.$lodash.isDate(this.employee.birthday)) {
        this.employee.birthday = this.employee.birthday.getFullYear() + '-' + (this.employee.birthday.getMonth() + 1) + '-' + this.employee.birthday.getDate();
      }
      if (this.$lodash.isDate(this.employee.first_engagement)) {
        this.employee.first_engagement = this.employee.first_engagement.getFullYear() + '-' + (this.employee.first_engagement.getMonth() + 1) + '-' + this.employee.first_engagement.getDate();
      }
      if (this.employee.id === null) {
        this.$http.post(`/employees`, this.employee).then(({ response }) => {
          console.log(response)
          this.$router.push({name: 'Dipendenti'});
        }).catch((error) => {
          console.log(error);
        })
      } else {
        this.$http.patch(`/employees/` + this.employee.id, this.employee).then(({ response }) => {
          console.log(response)
          this.$router.push({name: 'Dipendenti'});
        }).catch((error) => {
          console.log(error);
        })
      }
    },
    deleteEmployee: function() {
      this.$http.delete(`/employees/` + this.employee.id).then(({response}) => {
        console.log(response);
        this.$router.push({name: 'Dipendenti'});
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
    modEngagement: function(row) {
      this.$http.get(`/hoursweek`).then(({ data }) => {
        let hoursWeek = data.items;
        let begin = row.begin ? new Date(row.begin) : null;
        let end = row.end ? new Date(row.end) : null;

        let engagement = { id: row.id, begin: begin, end: end, employeeId: this.employee.id, hoursWeekId: row.hoursWeekId };
        this.$buefy.modal.open({
          parent: this,
          component: AddEngagement,
          props: { engagement: engagement, hoursWeek: hoursWeek, isNew: false },
          hasModalCard: true,
          trapFocus: true
        })
      })
    },
    renewEngagement: function(row) {
      this.$http.get(`/hoursweek`).then(({ data }) => {
        let hoursWeek = data.items;
        let begin = row.begin ? new Date(row.begin) : null;
        let end = row.end ? new Date(row.end) : null;

        let engagement = { begin: begin, end: end, employeeId: this.employee.id, hoursWeekId: row.hoursWeekId };
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
