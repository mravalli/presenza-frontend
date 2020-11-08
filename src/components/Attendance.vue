<template>
    <section class="attendance">
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Calendario Presenze
                    </h1>
                    <h2 v-if="first_day" class="subtitle">
                        Periodo <strong>{{ first_day | dateFormat('DD MMMM YYYY') }}</strong> - <strong>{{ last_day | dateFormat('DD MMMM YYYY') }}</strong>
                    </h2>
                </div>
            </div>
        </section>
        <section class="is-medium">
            <b-table
            :data="employees"
            :loading="loading">

                <b-table-column field="employee" label="Collaboratore" v-slot="props">
                    {{ props.row.employee }}
                </b-table-column>

                <b-table-column field="hours" label="Ore" v-slot="props">
                    {{ props.row.hours }}
                </b-table-column>

                <b-table-column field="office" label="Sede" v-slot="props">
                    {{ props.row.office }}
                </b-table-column>

            </b-table>
        </section>
    </section>
</template>

<script>
    export default {
        name: 'Attendance',
        data() {
            return {
                first_day: null,
                last_day: null,
                loading: false,
                employees: []
            }
        },
        methods: {
            loadAsyncData() {
                this.loading = true;
                this.$http.get(`/`).then(({ data }) => {
                    this.employees = [];
                    this.first_day = new Date(data.first_day);
                    this.last_day = new Date(data.last_day);
                    data.offices.forEach((office) => {
                        this.formatData(office);
                    })
                    
                    this.loading = false
                }).catch((error) => {
                    this.employees = []
                    this.loading = false
                    throw error
                })
            },
            formatData(office) {
                for (const key in office.employees) {
                    this.employees.push({
                        color: office.color,
                        office: office.name,
                        employee: office.employees[key].fullname,
                        hours: office.employees[key].hoursWeek.totalHours,
                    })
                }
            }
        },
        mounted() {
            this.loadAsyncData()
        }
    }
</script>
