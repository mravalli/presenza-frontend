<template>
    <section class="attendance">
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Calendario Presenze
                    </h1>
                    <h2 v-if="first_day" class="subtitle">
                        Periodo <strong>{{ first_day | dateParse('YYYY-MM-DD') | dateFormat('DD MMMM YYYY') }}</strong> - <strong>{{ last_day | dateParse('YYYY-MM-DD') | dateFormat('DD MMMM YYYY') }}</strong>
                    </h2>
                </div>
            </div>
        </section>
        <section class="is-medium">
            <table class="table is-narrow">
                <thead>
                    <tr>
                        <th></th>
                        <th>Ore</th>
                        <th v-for="column in days" :key="column.label" :class="column.classes">{{ column.label }}</th>
                        <th>Sede</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in employees" :key="row.index">
                        <td class="is-size-7">{{ row.employee }}</td>
                        <td class="is-size-7">{{ row.hours }}</td>
                        <td v-for="day of row.days" :key="day.day" :class="day.classes">
                            <input class="is-size-7" :value="day.hours" :class="day.classes" size=2 min=0 max=8 step=0.05 @change="dayChanged($event, row.office_id, row.employee_id, day.day)">
                        </td>
                        <td class="is-size-7">{{ row.office }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </section>
</template>

<style>
    table.is-narrow td,
    table.is-narrow th {
        border-left: 1px solid #ccc;
    }
    table.is-narrow th {
        padding: 0.7em 0.1em !important;
    }
    table.is-narrow td {
        padding: 0.25em 0.2em !important;
    }
    table.is-narrow td:nth-child(1) {
        border-left: none !important;
    }
    table.is-narrow td:nth-child(1),
    table.is-narrow td:nth-child(2),
    table.is-narrow th:nth-child(2) {
        padding: 0.7em 0.5em !important;
    }
    table.is-narrow td input {
        border: none;
    }
    .holiday,
    .sunday {
        background-color: hsl(0, 100%, 53%, 45%);
    }
    .saturday {
        background-color: hsl(48, 100%, 53%, 45%);
    }
    .workday,
    .holiday,
    .sunday,
    .saturday {
        text-align: center !important;
    }
</style>
<script>
    export default {
        name: 'Attendance',
        data() {
            return {
                first_day: null,
                last_day: null,
                loading: false,
                employees: [],
                days: []
            }
        },

        methods: {
            loadAsyncData() {
                this.loading = true;
                this.$http.get(`/`).then(({ data }) => {
                    this.employees = [];
                    this.first_day = data.first_day;
                    this.last_day = data.last_day;
                    let end_date = new Date(this.last_day);
                    for (var d = new Date(this.first_day); d <= end_date; d.setDate(d.getDate() + 1)) {
                        let classes = 'workday';
                        if (this.isHoliday(d)) {
                            classes = 'holiday'
                        } else if (d.getDay() == 0) {
                            classes = 'sunday'
                        } else if (d.getDay() == 6) {
                            classes = 'saturday'
                        }
                        this.days.push({
                            classes: classes,
                            label: `${d.getDate()}`,
                            day: `${d.getFullYear()}-${(d.getMonth() + 1)}-${d.getDate()}`
                        });
                    }
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
                    
                    const params = [
                        `first_day=${this.first_day}`,
                        `last_day=${this.last_day}`,
                        `office_id=${office.id}`,
                        `employee_id=${office.employees[key].id}`
                    ].join('&')
                    let eDay = []
                    for (const d of this.days) {
                        eDay.push({classes: d.classes, day: d.day, hours: 0})
                    }
                    this.$http.get(`/days?${params}`).then(({data}) => {
                        for (const d of data) {
                            let key = this.$lodash.findIndex(eDay, ['day', d.day]);
                            if (key != -1) {
                                let classes = eDay[key]['classes'];
                                eDay[key] = {classes: classes, day: d.day, hours: d.hours};
                            }
                        }
                        this.employees.push({
                            employee_id: office.employees[key].id,
                            office_id: office.id,
                            color: office.color,
                            office: office.name,
                            employee: office.employees[key].fullname,
                            hours: office.employees[key].hoursWeek.totalHours,
                            days: eDay
                        })
                    }).catch((error) => {
                        console.error(error)
                    });
                }
            },
            dayChanged(e, office_id, employee_id, day) {
                this.$http.post(`/modday`, {hours: e.target.value, officeId: office_id, employeeId: employee_id, day: day, performanceId: 1}).catch((error) => {
                    console.error(error)
                })
            },
            modday(e) {
                console.log(e)
            },
            // pasqua(yyyy) {
            //     // https://forum.html.it/forum/showthread/t-1364932.html
            //     // RITORNA DATA DELLA PASQUA fra il 1753 e il 2500
            //     var Ap, Bp, Cp, Dp, Ep, Fp, Mp;
            //     if (yyyy<100) yyyy = 1900 + yyyy;
            //     Ap = yyyy % 19;
            //     Bp = yyyy % 4;
            //     Cp = yyyy % 7;
            //     Dp = (19*Ap + 24) % 30;
            //     Fp = 0; // correzione per secoli
            //     if (yyyy<2500) Fp=3;
            //     if (yyyy<2300) Fp=2;
            //     if (yyyy<2200) Fp=1;
            //     if (yyyy<2100) Fp=0;
            //     if (yyyy<1900) Fp=6;
            //     if (yyyy<1800) Fp=5;
            //     if (yyyy<1700) Fp=4;
            //     Ep = (2*Bp + 4*Cp + 6*Dp + Fp + 5) % 7;
            //     Ep = 22 + Dp + Ep;
            //     Mp = 3;
            //     if (Ep>31) {
            //     Mp = 4;
            //     Ep = Ep - 31;
            //     }
            //     return (new Date(yyyy, Mp-1, Ep));
            // },
            isHoliday(data) {
                // https://forum.html.it/forum/showthread/t-1364932.html
                //var p,ff,f
                // pasquetta
                //let pp = this.pasqua(data.getFullYear());
                //let pp
                //let qq = data;
                //qq.setDate(qq.getDate()-1);
                //p = (this.date2str(qq) == this.date2str(pp));
                // FISSI
                let ff = " 0101 0106 0425 0501 0602 0815 1101 1208 1225 1226 "
                // PATRONO
                ff += " 0829 "
                // data in stringa
                let ss = this.date2str(data);
                let f = (ff.indexOf(ss.substr(4))>0);
                //return (p || f);
                return (f);
            },
            date2str(dd) {
                // https://forum.html.it/forum/showthread/t-1364932.html
                return String(dd.getFullYear()*10000 + (dd.getMonth()+1)*100 + dd.getDate())
            }
        },
        mounted() {
            this.loadAsyncData()
        }
    }
</script>

