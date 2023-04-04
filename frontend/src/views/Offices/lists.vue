<template>
    <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Sedi
                    </h1>
                    <h2 class="subtitle">
                        Elenco Generale
                    </h2>
                    <div class="is-pulled-right">
                        <b-button tag="router-link" to="/sede/nuova" type="is-link is-light">
                            <b-icon icon="account-plus" size="is-small" class="pr-2"></b-icon> Inserisci una Nuova Sede
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

                <b-table-column field="name" label="Nome" sortable v-slot="props">
                    {{ props.row.name }}
                </b-table-column>

                <b-table-column field="location" label="Dislocazione" sortable v-slot="props">
                    {{ props.row.location }}
                </b-table-column>

                <b-table-column field="manager" label="Responsabile" sortable v-slot="props">
                    {{ props.row.manager.fullname }}
                </b-table-column>

                <b-table-column field="action" label="Azioni" v-slot="props">
                    <div class="buttons">
                        <b-button tag="router-link" :to="`/sede/${props.row.id}`" type="is-link is-light">
                            <b-icon icon="home-edit" size="is-small"></b-icon>
                        </b-button>
                    </div>
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
                sortField: 'name',
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
                this.$http.get(`/offices?${params}`)
                    .then(({ data }) => {
                        this.data = []
                        let currentTotal = data.totalResults
                        if (data.totalResults / this.pageSize > 1000) {
                            currentTotal = this.pageSize * 1000
                        }
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
