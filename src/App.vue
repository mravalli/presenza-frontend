<template>
  <div id="app">
    <div class="container is-widescreen">
      <b-navbar>
        <template slot="brand">
          <b-navbar-item tag="router-link" :to="{ path: '/' }">
            <img
              src="https://raw.githubusercontent.com/buefy/buefy/dev/static/img/buefy-logo.png"
              alt="Lightweight UI components for Vue.js based on Bulma"
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
          </b-navbar-dropdown>
          <b-navbar-dropdown label="Info">
            <b-navbar-item href="#">
              Assistenza
            </b-navbar-item>
            <b-navbar-item tag="router-link" :to="{ path: '/about' }">
              About
            </b-navbar-item>
          </b-navbar-dropdown>
        </template>
        <template slot="end">
          <b-navbar-item tag="div">
            <div class="buttons">
              <a class="button is-primary">
                <strong>Sign up</strong>
              </a>
              <a class="button is-light">
                Log in
              </a>
            </div>
          </b-navbar-item>
        </template>
      </b-navbar>
      <router-view/>
    </div>
  </div>
</template>

<style>

</style>
