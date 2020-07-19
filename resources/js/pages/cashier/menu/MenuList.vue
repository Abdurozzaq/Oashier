<template>
  <div class="ma-1">

    <v-row justify="start" class="mx-auto">
      <v-col
        cols="12"
        
      >
        <div class="text-h6 font-weight-bold blue-grey--text text--lighten-1 mb-5">Your Menu :</div>

        <v-alert
          v-model="errorAlert"
          border="top"
          color="red lighten-2"
          dark
          dismissible
        >
          <ul v-for="(error, index) in serverError" v-bind:key="index">
            <li>
              {{ error[0] }} 
            </li>
          </ul>
        </v-alert>

        <v-card class="mb-3" v-for="(menu, index) in menuList" :key="index">
          <v-list>
            
            <v-list-item color="#B3E5F">
              <v-list-item-avatar size="62">
                <v-avatar size="62" color="primary">
                  <v-img :src="menu.menu_picture"/>
                </v-avatar>
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title>{{ menu.menu_name }}</v-list-item-title>
                <v-list-item-subtitle>Rp{{ menu.menu_price }}</v-list-item-subtitle>
              </v-list-item-content>

              <v-list-item-action>

                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn  
                      icon
                      v-bind="attrs"
                      v-on="on"
                      @click.prevent="toMenuEdit(menu.id)"
                    >
                      <v-icon color="grey lighten-1">mdi-file-document-edit-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Edit Menu</span>
                </v-tooltip>
                
              </v-list-item-action>

              <v-list-item-action>

                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn  
                      icon
                      v-bind="attrs"
                      v-on="on"
                      @click.prevent="deleteMenu(menu.id)"
                    >
                      <v-icon color="grey lighten-1">mdi-delete-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Delete Menu</span>
                </v-tooltip>

              </v-list-item-action>
            </v-list-item>
          </v-list>
          <v-overlay
            :absolute="true"
            :value="overlay"
          >
            <v-progress-circular
              :size="50"
              color="white"
              indeterminate
            ></v-progress-circular>
          </v-overlay>
        </v-card>
      </v-col>
    </v-row>


    <v-snackbar
      v-model="successSnackbar"
      :timeout="5000"
      color="success"
    >
      Menu has been received successfully from database.
      <v-btn
        color="white"
        text
        @click="successSnackbar = false"
      >
        Close
      </v-btn>
    </v-snackbar>

    <v-snackbar
      v-model="errorSnackbar"
      :timeout="5000"
      color="red"
    >
      Please enter the valid data.
      <v-btn
        color="white"
        text
        @click="errorSnackbar = false"
      >
        Close
      </v-btn>
    </v-snackbar>

    <v-snackbar
      v-model="successDeleteSnackbar"
      :timeout="5000"
      color="success"
    >
      Menu has been deleted successfully from database.
      <v-btn
        color="white"
        text
        @click="successDeleteSnackbar = false"
      >
        Close
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script>
  import { required, numeric } from 'vuelidate/lib/validators'
  import axios from 'axios'
  export default {
    name: 'CreateMenuPage',
    data() {
      return {
        dialog: false,
        menu_activate: [
          'Yes',
          'No'
        ],

        // payload
        menu_name: null,
        menu_description: null,
        menu_price: null,
        menu_availability: null,
        menu_picture: null,

        menuList: null,

        // Response
        errorAlert: false,
        successSnackbar: false,
        serverErrorCode: null,
        serverError: null,
        errorSnackbar: false,
        successDeleteSnackbar: false,
        overlay: false,
      }
    }, // end of data()


    methods: {

      toMenuEdit: function(menuId) {
        let currentObj = this
        currentObj.$router.push({ path: '/home/menu/edit', query: { uid: menuId }})
      },

      deleteMenu: function(menuId) {
        let currentObj = this

        currentObj.overlay = true
        axios.post('api/menu/delete', {
          menuId: menuId
        })
          .then(function (response) {
            // after success show successSnackbar
            currentObj.successDeleteSnackbar = true
            currentObj.overlay = false
            
          })
          .catch(function (error) {
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }
          })

      },     

      getMenu: function() {
        let currentObj = this
        axios.get('api/menu/list')
          .then(function (response) {

            currentObj.menuList = response.data.menu
            currentObj.getMenu()
            // after success show successSnackbar
            currentObj.successSnackbar = true

          })
        
      },
    },

    mounted: function() {
      let currentObj = this

      currentObj.getMenu()
    }
  }
</script>


<style>
  .bg-gradient {
    background: #5C258D;  /* fallback for old browsers */
    background: -webkit-linear-gradient(to right, #4389A2, #5C258D);  /* Chrome 10-25, Safari 5.1-6 */
    background: linear-gradient(to right, #4389A2, #5C258D); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
  }
</style>