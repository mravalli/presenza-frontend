<template>
    <section>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Giustificativi
                    </h1>
                    <h2 class="subtitle">
                        Elenco Generale
                    </h2>
                    <div class="is-pulled-right">
                        <b-button tag="router-link" to="/impostazioni/giustificativo/nuovo" type="is-link is-light">
                            <b-icon icon="beaker-plus" size="is-small" class="pr-2"></b-icon> Inserisci una Nuovo Giustificativo
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="is-medium">
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

                <b-table-column field="code" label="Codice Breve" sortable v-slot="props">
                    {{ props.row.code }}
                </b-table-column>

                <b-table-column field="limit" label="Limite" v-slot="props">
                    {{ props.row.limit }}
                </b-table-column>

                <b-table-column field="action" label="Azioni" v-slot="props">
                    <div class="buttons">
                        <b-button tag="router-link" :to="`/impostazioni/giustificativo/${props.row.id}`" type="is-link is-light">
                            <b-icon icon="pencil" size="is-small"></b-icon>
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
            loadAsyncData() {
                const params = [
                    `sort_by=${this.sortField}.${this.sortOrder}`,
                    `page=${this.page}`
                ].join('&')

                this.loading = true
                this.$http.get(`/justification?${params}`).then(({ data }) => {
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
                }).catch((error) => {
                    this.data = []
                    this.total = 0
                    this.loading = false
                    throw error
                })
            },
            onPageChange(page) {
                this.page = page
                this.loadAsyncData()
            },
            onSort(field, order) {
                this.sortField = field
                this.sortOrder = order
                this.loadAsyncData()
            },
        },
        mounted() {
            this.loadAsyncData()
        }
    }
</script>