<template>
    <section>
        <form @submit="checkForm" method="post">
            <div class="modal-card" style="width: auto">
                <header class="modal-card-head">
                    <p class="modal-card-title">Nuovo Periodo</p>
                    <button type="button" class="delete" @click="$emit('close')" />
                </header>
                <section class="modal-card-body">
                    <div class="columns">
                        <div class="column">
                            <b-field label="Data Inizio" label-position="on-border">
                                <b-datepicker
                                    v-model="engagement.begin"
                                    locale="it-IT"
                                    icon="calendar-today"
                                    size="is-small"
                                    inline
                                    trap-focus>
                                </b-datepicker>
                            </b-field>
                        </div>
                        <div class="column">
                            <b-field label="Data Fine" label-position="on-border">
                                <b-datepicker
                                    v-model="engagement.end"
                                    locale="it-IT"
                                    icon="calendar-today"
                                    size="is-small"
                                    inline
                                    trap-focus>
                                </b-datepicker>
                            </b-field>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <b-field label="Orario" label-position="on-border">
                                <b-select
                                    required
                                    v-if="hoursWeek"
                                    v-model="engagement.hoursWeekId"
                                    validation-message="Questo campo è obbligatorio!">
                                    <option
                                        v-for="hoursWeek in hoursWeek"
                                        :value="hoursWeek.id"
                                        :key="hoursWeek.id">
                                        {{ hoursWeek.totalHours }} Ore su {{ hoursWeek.days }} gg. ({{ hoursWeek.mon }}, {{ hoursWeek.tue }}, {{ hoursWeek.wed }}, {{ hoursWeek.thu }}, {{ hoursWeek.fri }}, {{ hoursWeek.sat }}, {{ hoursWeek.sun }})
                                    </option>
                                </b-select>
                            </b-field>
                        </div>
                        <div class="column buttons">
                            <b-button type="is-primary" icon-right="content-save" native-type="submit">Salva</b-button>
                            <b-button v-if="engagement.id" type="is-danger" icon-right="trash-can" @click="deleteEngagement()">Elimina</b-button>
                        </div>
                    </div>
                </section>
            </div>
        </form>
    </section>
</template>
<script>
    export default {
        name: 'AddEngagement',
        props: ['engagement', 'hoursWeek', 'isNew'],
        methods: {
            checkForm: function(e) {
                e.preventDefault();
                if (this.$lodash.isDate(this.engagement.begin)) {
                    this.engagement.begin = this.engagement.begin.getFullYear() + '-' + (this.engagement.begin.getMonth() + 1) + '-' + this.engagement.begin.getDate();
                }
                if (this.$lodash.isDate(this.engagement.end)) {
                    this.engagement.end = this.engagement.end.getFullYear() + '-' + (this.engagement.end.getMonth() + 1) + '-' + this.engagement.end.getDate();
                }
                if (this.isNew) {
                    this.$http.post(`/employees/${this.engagement.employeeId}/engagement`, this.engagement).then((items) => {
                        this.$parent.$parent.employee.engagements = items.data;
                        this.$parent.close();
                    }).catch(error => {
                        if (error.response) {
                            let data = error.response.data;
                            if (data.errno == 11) {
                                this.$buefy.notification.open({
                                    duration: 5000,
                                    message: `Qualcosa è andato storto, verifica che il periodo selezionato non si stia accavallando con uno già presente`,
                                    position: 'is-bottom-right',
                                    type: 'is-danger',
                                    hasIcon: true
                                })
                            }
                        }
                    });
                } else {
                    this.$http.patch(`/employees/${this.engagement.employeeId}/engagement/${this.engagement.id}`, this.engagement).then((items) => {
                        this.$parent.$parent.employee.engagements = items.data;
                        this.$parent.close();
                    }).catch((error) => {
                        console.log('ciao2')
                        console.log(error);
                    })
                }
            },
            deleteEngagement: function() {
                this.$http.delete(`/employees/${this.engagement.employeeId}/engagement/${this.engagement.id}`).then((items) => {
                    this.$parent.$parent.employee.engagements = items.data;
                    this.$parent.close();
                }).catch((error) => {
                    console.log(error);
                })
            }
        }
    }
</script>