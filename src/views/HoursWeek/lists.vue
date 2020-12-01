<template>
    <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Ore Settimanali
                    </h1>
                    <h2 class="subtitle">
                        Elenco Ore Settimanali previsti
                    </h2>
                    <div class="is-pulled-right">
                        <b-button tag="router-link" to="/impostazioni/ore/nuovo" type="is-link is-light">
                            <b-icon icon="plus-box" size="is-small" class="pr-2"></b-icon> Inserisci un Nuovo Format
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

                <b-table-column field="type" label="Tipologia di Contrattao" sortable v-slot="props">
                    {{ props.row.type }}
                </b-table-column>

                <b-table-column field="days" label="Giorni di Lavoro" sortable v-slot="props">
                    {{ props.row.days }}
                </b-table-column>

                <b-table-column field="mon" label="Lun" sortable v-slot="props">
                    {{ props.row.mon }}
                </b-table-column>
                <b-table-column field="tue" label="Mar" sortable v-slot="props">
                    {{ props.row.tue }}
                </b-table-column>
                <b-table-column field="wed" label="Mer" sortable v-slot="props">
                    {{ props.row.wed }}
                </b-table-column>
                <b-table-column field="thu" label="Gio" sortable v-slot="props">
                    {{ props.row.thu }}
                </b-table-column>
                <b-table-column field="fri" label="Ven" sortable v-slot="props">
                    {{ props.row.fri }}
                </b-table-column>
                <b-table-column field="sab" label="Sab" sortable v-slot="props">
                    {{ props.row.sat }}
                </b-table-column>
                <b-table-column field="sun" label="Dom" sortable v-slot="props">
                    {{ props.row.sun }}
                </b-table-column>

                <b-table-column field="action" label="Azioni" v-slot="props">
                    <div class="buttons">
                        <b-button tag="router-link" :to="`/impostazioni/ore/${props.row.id}`" type="is-link is-light">
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
                this.$http.get(`/hoursweek?${params}`)
                    .then(({ data }) => {
                        this.data = []
                        let currentTotal = data.totalResults
                        if (data.totalResults / this.pageSize > 1000) {
                            currentTotal = this.pageSize * 1000
                        }
                        this.total = currentTotal
                        data.items.forEach((item) => {
                            if (item.type === 'FT') {
                                item.type = 'Tempo Pieno';
                            } else {
                                item.type = 'Tempo Parziale';
                            }
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
