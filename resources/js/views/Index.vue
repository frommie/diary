<template>
  <div id="main" class="container">
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
                Home
            </b-navbar-item>
            <b-navbar-item tag="router-link" :to="{ path: '/trend' }">
                Trend
            </b-navbar-item>
        </template>
        <template slot="end">
            <b-navbar-item tag="div">
                <div class="buttons">
                  <router-link to="/register" class="button is-primary" v-if="!isLogged">Sign Up</router-link>
                  <button type="button" class="button is-light" @click="logout()" v-if="isLogged">
                    Logout
                  </button>
                  <router-link to="/login" class="button is-light" v-if="!isLogged">Login</router-link>
                </div>
            </b-navbar-item>
        </template>
    </b-navbar>
    <div id="content">
      <router-view></router-view>
    </div>
  </div>
</template>
<script>
import { mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters([
      'isLogged'
    ])
  },

  methods: {
    logout () {
      this.$store.dispatch('logout')
    }
  }
}
</script>

<style scoped>
.navbar {
  margin-bottom: 1px;
}
</style>
