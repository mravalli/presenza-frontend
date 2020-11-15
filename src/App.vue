<template>
  <div id="app">
    <div class="container is-widescreen">
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
          <b-navbar-item tag="router-link" :to="{ path: '/collaboratori' }">
            Collaboratori
          </b-navbar-item>
          <b-navbar-item tag="router-link" :to="{ path: '/sedi' }">
            Sedi
          </b-navbar-item>
          <b-navbar-dropdown label="Impostazioni">
            <b-navbar-item tag="router-link" :to="{ path: '/impostazioni' }">
              Azienda
            </b-navbar-item>
            <b-navbar-item tag="router-link" :to="{ path: '/impostazioni/ore' }">
              Tabella Ore Settimanali
            </b-navbar-item>
            <b-navbar-item tag="router-link" :to="{ path: '/impostazioni/giustificativi' }">
              Tabella Giustificativi
            </b-navbar-item>
          </b-navbar-dropdown>
        </template>
        <template slot="end">
          
        </template>
      </b-navbar>
      <router-view/>
    </div>
  </div>
</template>

<style>

</style>
