<template>
    <div class="home">
        <section class="attendance">
            <section class="hero is-primary">
                <div class="hero-body">
                    <div class="container">
                        <div class="columns">
                            <div class="column is-three-quarters">
                                <h1 class="title">
                                    Calendario Presenze
                                </h1>
                                <h2 class="subtitle">
                                    Periodo <strong>{{ dates[0] | dateFormat('DD MMMM YYYY', {timezone: 0}) }}</strong> - <strong>{{ dates[1] | dateFormat('DD MMMM YYYY', {timezone: 0}) }}</strong>
                                </h2>
                            </div>
                            <div class="column is-one-quarters">
                                <b-field label="Seleziona il periodo">
                                    <b-datepicker
                                        placeholder="Click to select..."
                                        v-model="dates"
                                        :input="rangeChanged(dates)"
                                        range>
                                    </b-datepicker>
                                </b-field>
                                <b-button type="is-light" icon-right="download" @click="exportThis()">Esporta Il Periodo Selezionato</b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <Attendance v-if="employees" :employees="employees" :first_day="first_day" :last_day="last_day" :justifications="justifications"/>
        </section>
    </div>
</template>

<script>
// @ is an alias to /src
import Attendance from '@/components/Attendance.vue'

export default {
    name: 'Calendar',
    components: {
        Attendance
    },
    data() {
        let date = new Date();
        let year = date.getFullYear();
        let month = date.getMonth() + 1;
        let first_day = this.getFirstDayOfMonth(year, month);
        let last_day = this.getLastDayOfMonth(year, month);
        let dates = [new Date(first_day), new Date(last_day)];
        return {
            dates: dates,
            first_day: first_day,
            last_day: last_day,
            employees: null,
            justifications: null,
            loading: false,
        }
    },
    methods: {
        loadAsyncData() {
            this.loading = true;
            const params = [
                `first_day=${this.first_day}`,
                `last_day=${this.last_day}`
            ].join('&')
            this.$http.get(`/?${params}`).then(({ data }) => {
                this.employees = data.employees;
                this.justifications = data.justifications;

                this.loading = false
            }).catch((error) => {
                this.justifications = null    
                this.loading = false
                throw error
            })
        },
        rangeChanged(dates) {
            this.first_day = dates[0].getFullYear() + '-' + (dates[0].getMonth() +1) + '-' + dates[0].getDate();
            this.last_day = dates[1].getFullYear() + '-' + (dates[1].getMonth() +1) + '-' + dates[1].getDate();
        },
        getFirstDayOfMonth(year, month) {
            return year + '-' + month + '-01'
        },
        getLastDayOfMonth(year, month) {
            let date = new Date(year, month, 0);
            return year + '-' + month + '-' + date.getDate();
        },
        exportThis() {
            const params = [
                `first_day=${this.first_day}`,
                `last_day=${this.last_day}`
            ].join('&')
            this.$http.get(`/export?${params}`, {responseType: 'blob'}).then((response) => {
                var fileURL = window.URL.createObjectURL(new Blob([response.data], {type: response.headers['content-type']}));
                var fileLink = document.createElement('a');
                fileLink.href = fileURL;
                fileLink.setAttribute('download', 'presenze.xls');
                document.body.appendChild(fileLink);
                fileLink.click();
            });
        }
    },
    mounted() {
        this.loadAsyncData()
    }
}
</script>
