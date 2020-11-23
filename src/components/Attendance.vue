<template>
    <section class="is-medium">
         <div class="ml-6 my-2 is-size-7">
            <strong>O</strong>: Ore Ordinarie - <strong>E</strong>: Ore Aggiuntive come da Giustificativo - <strong>G</strong>: Giustificativo
        </div>
        <table class="table is-narrow">
            <thead>
                <tr>
                    <th></th>
                    <th>Ore</th>
                    <th></th>
                    <th v-for="column in days" :key="column.label" :class="column.classes">{{ column.label }}</th>
                    <th>Sede</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in orderedEmployees" :key="row.id" :style="{backgroundColor: row.color}">
                    <td class="is-size-7">{{ row.employee.fullname }}</td>
                    <td class="is-size-7"><div>{{ row.employee.hours }}</div></td>
                    <td class="is-size-7"><div><strong>O</strong></div><div><strong>E</strong></div><div><strong>G</strong></div></td>
                    <td v-for="(day, d_index) of row.days" :key="day.day" :class="day.classes">
                        <input class="is-size-7" v-model="row.days[d_index].hours" :class="day.classes" size=2 @change="dayChanged(row.office_id,e_index,d_index)">
                        <input class="is-size-7" v-model="row.days[d_index].disease" :class="day.classes" size=2 @change="dayChanged(row.office_id,e_index,d_index)">
                        <input class="is-size-7" v-model="row.days[d_index].justificationCode" :class="day.classes" size=2 @change="dayChanged(row.office_id,e_index,d_index)" @click="openJustificationsBox()">
                    </td>
                    <td class="is-size-7">{{ row.office }}</td>
                </tr>
            </tbody>
        </table>
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
    table.is-narrow td div {
        padding: 0.25em 0.2em !important;
    }
    table.is-narrow td input {
        border: none;
    }
    table.notification {
        font-size: 0.6em;
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
        props: ['employees', 'offices', 'first_day', 'last_day', 'justifications'],
        data() {
            let days = this.formatColumn(this.first_day, this.last_day);
            //let employees = this.formatData(this.offices, days);
            let justifications = '<table class="table is-narrow is-striped notification"><thead><tr><th>Codice</th><th>Descrizione</th></tr></thead><tbody>';
            for (const i in this.justifications) {
                justifications += '<tr><td>' + this.justifications[i].code + '</td><td>' + this.justifications[i].name + '</td></tr>';
            }
            justifications += '</tbody></table>';
            return {
                loading: false,
                days: days,
                employees2: this.formatData(this.employees, days),
                justificationList: justifications,
                justificationActive: false
            }
        },
        computed: {
          orderedEmployees: function () {
            return this.$lodash.orderBy(this.employees2, 'id')
          }
        },
        watch: {
            first_day: function() {
                let days = this.formatColumn(this.first_day, this.last_day);
                let employees = this.formatData(this.offices, days);
                this.days = days;
                this.employees = employees;
            }
        },
        methods: {
            formatColumn(first_day, last_day) {
                let days = [];
                let end_date = new Date(last_day);
                for (var d = new Date(first_day); d <= end_date; d.setDate(d.getDate() + 1)) {
                    let classes = 'workday';
                    if (this.isHoliday(d)) {
                        classes = 'holiday'
                    } else if (d.getDay() == 0) {
                        classes = 'sunday'
                    } else if (d.getDay() == 6) {
                        classes = 'saturday'
                    }
                    days.push({
                        classes: classes,
                        label: `${d.getDate()}`,
                        day: `${d.getFullYear()}-${(d.getMonth() + 1)}-${d.getDate()}`
                    });
                }
                return days;
            },
            formatData(employees, days) {
                let _employees = [];
                for (const key in employees) {
                    let employee = employees[key].employee;
                    let office = employees[key].office;
                    const params = [
                        `first_day=${this.first_day}`,
                        `last_day=${this.last_day}`,
                        `office_id=${office.id}`,
                        `employee_id=${employee.id}`
                    ].join('&')

                    let eDay = []
                    for (const d of days) {
                        eDay.push({classes: d.classes, day: d.day, hours: null, disease: null, justificationCode: null})
                    }

                    this.$http.get(`/days?${params}`).then(({data}) => {
                        for (const d of data) {
                            let key = this.$lodash.findIndex(eDay, ['day', d.day]);
                            if (key != -1) {
                                let classes = eDay[key]['classes'];
                                eDay[key] = {
                                    classes: classes,
                                    day: d.day,
                                    hours: d.hours,
                                    disease: d.disease,
                                    justificationCode: d.justificationCode
                                };
                            }
                        }
                        let totalHours = employee.actualEngagement ? employee.actualEngagement.hoursWeek.totalHours : null;
                        _employees.push({
                            id: key,
                            employee_id: employee.id,
                            office_id: office.id,
                            color: `hsla(${office.color},100%, 54%, 12%)`,
                            office: office.name,
                            employee: employee.fullname,
                            hours: totalHours,
                            days: eDay
                        })
                    }).catch((error) => {
                        console.error(error)
                    });
                }
                return _employees;
            },
            dayChanged(office_id, e_index, d_index) {
                let day = this.employees[e_index].days[d_index];
                let employee_id = this.employees[e_index].employee_id;
                this.$http.post(`/modday`, {officeId: office_id, employeeId: employee_id, day: day}).catch((error) => {
                   console.error(error)
                })
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
            },
            openJustificationsBox() {
                this.justificationActive = true
                this.$buefy.notification.open({
                    active: this.justificationActive,
                    has_icon: true,
                    duration: 6000,
                    //position: 'is-left',
                    message: this.justificationList
                })
            },
            closeJustificationBox() {
                this.justificationActive = false
            }
        },
    }
</script>

