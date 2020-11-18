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
                                <b-select v-if="hoursWeek" v-model="engagement.hoursWeekId">
                                    <option
                                        v-for="hoursWeek in hoursWeek"
                                        :value="hoursWeek.id"
                                        :key="hoursWeek.id">
                                        {{ hoursWeek.totalHours }} Ore su {{ hoursWeek.days }} gg. ({{ hoursWeek.mon }}, {{ hoursWeek.tue }}, {{ hoursWeek.wed }}, {{ hoursWeek.thu }}, {{ hoursWeek.fri }}, {{ hoursWeek.sat }}, {{ hoursWeek.sun }})
                                    </option>
                                </b-select>
                            </b-field>
                        </div>
                        <div class="column">
                            <b-button type="is-primary" native-type="submit">Aggiungi</b-button>
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
                        console.log(items);
                        this.$parent.$parent.employee.engagements = items.data;
                        this.$parent.close();
                    }).catch((error) => {
                        console.log(error);
                    })
                }
            }
        }
    }
</script>