<template>
  <v-app>
    <v-app-bar
      app
      dark
    >
      <v-toolbar-title
        style="width: 300px"
        class="ml-0 pl-4"
      >
        <span>OZZAQDEV</span>
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn v-if="isLoggedIn == false" href="/login" text>
        LOGIN
      </v-btn>
      <v-btn v-if="isLoggedIn == false" href="/register" text>
        REGISTER
      </v-btn>

      <v-btn v-if="isLoggedIn == true" @click="goToDash" text>
        GO TO DASH
      </v-btn>
      <v-btn v-if="isLoggedIn == true" text @click="logout">
        Logout
      </v-btn>
    </v-app-bar>

    <!-- Sizes your content based upon application components -->
    <v-main class="gradient">

      <!-- Provides the application the proper gutter -->
      <v-container fluid>

        <!-- If using vue-router -->
        <router-view></router-view>

        <v-snackbar top v-model="snack" :timeout="3000" :color="snackColor">
          {{ snackText }}

          <template v-slot:action="{ attrs }">
            <v-btn v-bind="attrs" text @click="snack = false">Close</v-btn>
          </template>
        </v-snackbar>

      </v-container>
    </v-main>
  </v-app>
</template>

<script>
  import axios from 'axios'
  export default {
    name: 'LandingPage',
    data() {
      return {
        isLoggedIn: false,

        snack: false,
        snackColor: null,
        snackText: null,
      }
    }, // End of Data

    methods: {
      checkAuth: function() {
        let currentObj = this
        let token = localStorage.getItem('userToken')

        if (token) {
          currentObj.isLoggedIn = true
        } else {
          currentObj.isLoggedIn = false
        }
      },

      goToDash: function() {
        let currentObj = this

        currentObj.snack = true
        currentObj.snackColor = 'success'
        currentObj.snackText = 'Redirecting To Dashboard... Please Wait A Minute!'


        let token = localStorage.getItem('userToken')

        axios.get('api/auth/me', {
        headers: {
          Authorization: 'Bearer ' + token,
          withCredentials: true //the token is a variable which holds the token
        }
        })
        .then(function (response) {
            // handle success
            let userRole = response.data.role
            if (userRole == "admin") {
              currentObj.$router.push('/siAdmino')
            } else {
              currentObj.$router.push('/home')
            }
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        })
      },

      logout: function() {
        let currentObj = this
          axios.post('/api/auth/logout')
          .then(function (response) {
            localStorage.removeItem('userToken')
            currentObj.$router.push('/login')
          })
          .catch(function (error) {
            console.log(error);
          });
      },
    }, // End of Methods

    mounted: function() {
      let currentObj = this

      currentObj.checkAuth()
    }, // End of Mounted
  }
</script>

<style>
  .font {
    font-family: 'Baloo Da 2', cursive;
  }
  .gradient {
    background: #BBD2C5;  /* fallback for old browsers */
    background: -webkit-linear-gradient(to right, #292E49, #536976, #BBD2C5);  /* Chrome 10-25, Safari 5.1-6 */
    background: linear-gradient(to right, #292E49, #536976, #BBD2C5); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
  }
</style>