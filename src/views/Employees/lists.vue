<template>
    <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Dipendenti
                    </h1>
                    <h2 class="subtitle">
                        Elenco Generale
                    </h2>
                    <div class="is-pulled-right">
                        <b-button tag="router-link" to="/dipendente/nuovo" type="is-link is-light">
                            <b-icon icon="account-plus" size="is-small" class="pr-2"></b-icon> Aggiungi Dipendente
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="is-medium mt-6">
            <b-table
                :data="data"
                :loading="loading"

                paginated
                backend-pagination
                :total="total"
                :per-page="perPage"
                @page-change="onPageChange"
                aria-next-label="Prossima pagina"
                aria-previous-label="Pagina precedente"
                aria-page-label="Pagina"
                aria-current-label="Pagina corrente"

                backend-sorting
                :default-sort-direction="defaultSortOrder"
                :default-sort="[sortField, sortOrder]"
                @sort="onSort">

                <b-table-column field="firstname" label="Nome" sortable v-slot="props">
                    {{ props.row.firstname }}
                </b-table-column>

                <b-table-column field="lastname" label="Cognome" sortable v-slot="props">
                    {{ props.row.lastname }}
                </b-table-column>

                <b-table-column field="badge_id" label="Matricola" sortable v-slot="props">
                    {{ props.row.badge_id }}
                </b-table-column>

                <b-table-column field="cf" label="Codice Fiscale" sortable v-slot="props">
                    {{ props.row.cf }}
                </b-table-column>

                <b-table-column field="city" label="Città" sortable centered v-slot="props">
                    {{ props.row.city }}
                </b-table-column>

                <b-table-column field="phone" label="Telefono Fisso" centered v-slot="props">
                    {{ props.row.phone }}
                </b-table-column>

                <b-table-column field="mobile" label="Cellulare" centered v-slot="props">
                    {{ props.row.mobile }}
                </b-table-column>

                <b-table-column field="email" label="Indirizzo eMail" centered v-slot="props">
                    {{ props.row.email }}
                </b-table-column>

                <b-table-column field="action" label="Azioni" v-slot="props">
                    <b-button tag="router-link" :to="`/dipendente/${props.row.id}`" type="is-link is-light">
                        <b-icon icon="account-edit" size="is-small"></b-icon>
                    </b-button>
                </b-table-column>
                    
            </b-table>
        </section>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                data: [],
                total: 0,
                loading: false,
                sortField: 'firstname',
                sortOrder: 'asc',
                defaultSortOrder: 'asc',
                page: 1,
                perPage: 20
            }
        },
        methods: {
            /*
            * Load async data
            */
            loadAsyncData() {
                const params = [
                    `sort_by=${this.sortField}.${this.sortOrder}`,
                    `page=${this.page}`
                ].join('&')

                this.loading = true
                this.$http.get(`/employees?${params}`)
                    .then(({ data }) => {
                        this.data = []
                        let currentTotal = data.totalResults
                        // if (data.totalResults / this.pageSize > 1000) {
                        //     currentTotal = this.pageSize * 1000
                        // }
                        this.total = currentTotal
                        data.items.forEach((item) => {
                            this.data.push(item)
                        })
                        this.loading = false
                    })
                    .catch((error) => {
                        this.data = []
                        this.total = 0
                        this.loading = false
                        throw error
                    })
            },
            /*
        * Handle page-change event
        */
            onPageChange(page) {
                this.page = page
                this.loadAsyncData()
            },
            /*
        * Handle sort event
        */
            onSort(field, order) {
                this.sortField = field
                this.sortOrder = order
                this.loadAsyncData()
            },
            /*
        * Type style in relation to the value
        */
            type(value) {
                const number = parseFloat(value)
                if (number < 6) {
                    return 'is-danger'
                } else if (number >= 6 && number < 8) {
                    return 'is-warning'
                } else if (number >= 8) {
                    return 'is-success'
                }
            }
        },
        filters: {
            /**
        * Filter to truncate string, accepts a length parameter
        */
            truncate(value, length) {
                return value.length > length
                    ? value.substr(0, length) + '...'
                    : value
            }
        },
        mounted() {
            this.loadAsyncData()
        }
    }
</script>
