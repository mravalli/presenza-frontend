<template>
    <section class="mt-4" v-if="office">
        <form @submit="checkForm" method="post">
            <div class="columns">
                <div class="column is-two-thirds">
                    <div class="columns">
                        <div class="column is-half">
                            <b-field label="Nome *" label-position="on-border">
                                <b-input
                                    required
                                    v-model="office.name"
                                    validation-message="Il Nome è obbligatorio e deve essere compreso tra 3 e 64 caratteri!"
                                    pattern="^[\sa-zA-Z]{3,64}$"
                                    placeholder="Filiale 1"
                                    maxlength=64>
                                </b-input>
                            </b-field>
                        </div>
                        <div class="column is-half">
                            <b-field label="Responsabile" label-position="on-border">
                                <b-select v-model="office.managerId">
                                    <option
                                        v-for="manager in managers"
                                        :value="manager.id"
                                        :key="manager.id">
                                        {{ manager.fullname }} ({{ manager.birthday }})
                                    </option>
                                </b-select>
                            </b-field>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <b-field label="Dislocazione" label-position="on-border">
                                <b-input
                                    required
                                    v-model="office.location"
                                    validation-message="La dislocazione della sede deve essere compreso tra 3 e 64 caratteri!"
                                    pattern="^[\sa-zA-Z]{3,64}$"
                                    placeholder="Piazza Navona"
                                    maxlength=64>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="columns" v-if="office.id">
                        <div class="column">
                            <b-field label="Personale assegnato alla sede">
                                <b-taginput
                                    v-model="office.employees"
                                    :data="filteredEmployees"
                                    autocomplete
                                    field="fullname"
                                    icon="label"
                                    placeholder="Aggiungi un collaboratore"
                                    @typing="getFilteredEmployees">
                                    <template slot-scope="props">
                                        {{ props.option.fullname }} <i>({{ props.option.birthday }})</i>
                                    </template>
                                    <template slot="empty">Non ci sono collaboratori</template>
                                </b-taginput>
                            </b-field>
                        </div>
                    </div>
                </div>
                <div class="column is-one-thirds">
                    <center>
                        <b-field label="Colore" label-position="on-border">
                            <color-picker :hue="hue" @input="updateColor" />
                        </b-field>
                    </center>
                </div>                
            </div>
            

            <div class="field is-grouped is-grouped-right">
                <p class="control"><b-button type="is-primary" icon-right="content-save" native-type="submit">Salva</b-button></p>
                <p class="control" v-if="office.id"><b-button type="is-danger" icon-right="trash-can" v-on:click="deleteoffice">Elimina</b-button></p>
                <p class="control"><b-button type="is-light" icon-right="cancel" @click="backToList">Annulla</b-button></p>
            </div>
        </form>
    </section>
</template>

<script>
    import ColorPicker from '@radial-color-picker/vue-color-picker';
    export default {
        components: { ColorPicker },
        props: ['office', 'employees', 'managers'],
        computed: {
            filteredEmployees: function() {
                return this.employees
            },
            hue: function() {
                return this.office.color
            }
        },
        methods: {
            backToList: function() {
                this.$router.push({name: 'Sedi'});
            },
            checkForm: function(e) {
                e.preventDefault();
                this.office.color = Math.ceil(this.hue);
                if (this.office.id === null) {
                    this.$http.post(`/offices`, this.office).then(() => {
                        this.$router.push({name: 'Sedi'});
                    }).catch((error) => {
                        console.log(error);
                    })
                } else {
                    this.$http.patch(`/offices/` + this.office.id, this.office).then(() => {
                        this.$router.push({name: 'Sedi'});
                    }).catch((error) => {
                        console.log(error);
                    })
                }
            },
            deleteoffice: function() {
                this.$http.delete(`/offices/` + this.office.id).then(() => {
                    this.$router.push({name: 'Sedi'});
                }).catch((error) => {
                    console.log(error);
                })
            },
            getFilteredEmployees(text) {
                console.log(this.employees)
                this.filteredEmployees = this.employees.filter((option) => {
                    return option.fullname
                        .toString()
                        .toLowerCase()
                        .indexOf(text.toLowerCase()) >= 0
                })
            },
            updateColor(hue) {
                this.hue = hue;
            }
        },
    }
</script>
<style>
@import '@radial-color-picker/vue-color-picker/dist/vue-color-picker.min.css';
</style>