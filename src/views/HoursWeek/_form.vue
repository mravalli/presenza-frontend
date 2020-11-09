<template>
    <section class="mt-4">
        <form @submit="checkForm" method="post">
            <div class="columns">
                <div class="column">
                    <b-field label="Tipo *">
                        <b-select
                            required
                            v-model="hoursWeek.type"
                            validation-message="Devi distinguere se a Tempo Pieno o Parziale!">
                            <option value="FT">Tempo Pieno</option>
                            <option value="PT">Tempo Parziale</option>
                        </b-select>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Giorni *">
                        <b-input
                            required
                            v-model="hoursWeek.days"
                            type="number"
                            min="1"
                            max="7">
                        </b-input>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Lun *">
                        <b-input
                            required
                            v-model="hoursWeek.mon"
                            type="number"
                            min="0"
                            max="8"
                            step="0.05">
                        </b-input>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Mar *">
                        <b-input
                            required
                            v-model="hoursWeek.tue"
                            type="number"
                            min="0"
                            max="8"
                            step="0.05">
                        </b-input>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Mer *">
                        <b-input
                            required
                            v-model="hoursWeek.wed"
                            type="number"
                            min="0"
                            max="8"
                            step="0.05">
                        </b-input>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Gio *">
                        <b-input
                            required
                            v-model="hoursWeek.thu"
                            type="number"
                            min="0"
                            max="8"
                            step="0.05">
                        </b-input>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Ven *">
                        <b-input
                            required
                            v-model="hoursWeek.fri"
                            type="number"
                            min="0"
                            max="8"
                            step="0.05">
                        </b-input>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Sab *">
                        <b-input
                            required
                            v-model="hoursWeek.sat"
                            type="number"
                            min="0"
                            max="8"
                            step="0.05">
                        </b-input>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Dom *">
                        <b-input
                            required
                            v-model="hoursWeek.sun"
                            type="number"
                            min="0"
                            max="8"
                            step="0.05">
                        </b-input>
                    </b-field>
                </div>
            </div>
            <div class="field is-grouped is-grouped-right">
                <p class="control"><b-button type="is-primary" icon-right="content-save" native-type="submit">Salva</b-button></p>
                <p class="control" v-if="hoursWeek.id"><b-button type="is-danger" icon-right="trash-can" v-on:click="deleteFormat">Elimina</b-button></p>
                <p class="control"><b-button type="is-light" icon-right="cancel" @click="backToList">Annulla</b-button></p>
            </div>
        </form>
    </section>
</template>

<script>
    export default {
        props: ['hoursWeek'],
        methods: {
            backToList: function() {
                this.$router.push({name: 'Ore Settimanali'});
            },
            checkForm: function(e) {
                e.preventDefault()
                if (this.hoursWeek.id === null) {
                    this.$http.post(`/hoursweek`, this.hoursWeek).then(() => {
                        this.$router.push({name: 'Ore Settimanali'})
                    }).catch((error) => {
                        console.log(error)
                    })
                } else {
                    this.$http.patch(`/hoursWeek/` + this.hoursWeek.id, this.hoursWeek).then(() => {
                        this.$router.push({name: 'Ore Settimanali'})
                    }).catch((error) => {
                        console.log(error)
                    })
                }
            },
            deleteFormat: function() {
                this.$http.delete(`/hoursweek/` + this.hoursWeek.id).then(() => {
                    this.$router.push({name: 'Ore Settimanali'});
                }).catch((error) => {
                    console.log(error);
                })
            }
        }
    }
</script>