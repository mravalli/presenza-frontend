<template>
  <div id="app">
    <div v-if="user.isLoggedIn" class="page-wrapper">
      <div class="content-wrapper">
        <div class="container is-fluid">
          <b-navbar>
            <template slot="brand">
              <b-navbar-item tag="router-link" :to="{ path: '/' }">
                <img
                  src="/img/logo.png"
                  alt="Foglio Presenze"
                >
              </b-navbar-item>
            </template>
            <template slot="start">
              <b-navbar-item tag="router-link" :to="{ path: '/' }">
                Calendario
              </b-navbar-item>
              <b-navbar-item tag="router-link" :to="{ path: '/dipendenti' }">
                Dipendenti
              </b-navbar-item>
              <b-navbar-item tag="router-link" :to="{ path: '/sedi' }">
                Sedi
              </b-navbar-item>
            </template>
            <template slot="end">
              <b-navbar-dropdown :label="user.name" right>
                <b-navbar-item tag="router-link" :to="{ path: '/impostazioni' }">
                  Azienda
                </b-navbar-item>
                <b-navbar-item tag="router-link" :to="{ path: '/utenti' }">
                  Utenti
                </b-navbar-item>
                <b-navbar-item tag="router-link" :to="{ path: '/impostazioni/ore' }">
                  Tabella Ore Settimanali
                </b-navbar-item>
                <b-navbar-item tag="router-link" :to="{ path: '/impostazioni/giustificativi' }">
                  Tabella Giustificativi
                </b-navbar-item>
                <b-navbar-item @click="logout()">
                  <b-icon icon="power" size="small"></b-icon> <span>Esci</span>
                </b-navbar-item>
              </b-navbar-dropdown>
            </template>
          </b-navbar>
          <router-view/>
        </div>
      </div>
      <footer class="footer mt-6">
        <div class="content has-text-centered">
          <p>
            <strong>Presenza</strong> è un software realizzato e distribuito da <a href="https://www.lareclameitalia.com" target="_blank">La Reclame Digital Agency</a>.
          </p>
        </div>
      </footer>
    </div>
    <div v-if="!user.isLoggedIn">
      <router-view/>
    </div>
  </div>
</template>

<style>
.page-wrapper {
  display: flex;
  min-height: 100vh;
  flex-direction: column;
}
  
.content-wrapper {
  flex: 1;
}

.router-link-exact-active {
    color: #7957d5;
    font-weight: bold;
}
</style>
<script>
  export default {
    computed: {
      user() {
        return this.$store.state.user
      },
    },
    methods: {
      logout: function() {
        this.$store.dispatch('logout')
      }
    }
  }
</script>