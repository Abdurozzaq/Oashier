<template>
  <div class="ma-1">

    <v-row justify="start" class="mx-auto">
      <v-col
        cols="12"
      >
        <div class="text-h6 font-weight-bold blue-grey--text text--lighten-1 mb-5">Your Menu :</div>

        <v-text-field
          label="Search By Name or Price"
          hint="Case Sensitive"
          outlined
          v-model="searchValue"
          append-outer-icon="mdi-send"
          @click:append-outer="search"
          max-width="64px"
          class="mb-1"
        ></v-text-field>

        <v-btn
          small
          @click.prevent="getMenu"
          class="mb-5"
        >
          Reload Data
        </v-btn>

        <v-banner v-if="menuList == null || menuList.length == 0">
          You haven't created any menu at this time, make it by clicking on <a href="/home/menu/create">this.</a>
        </v-banner>

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

        <br>

        <v-row>
          <v-col xs="12" sm="12" md="6">
            <div class="text-h6 font-weight-bold blue-grey--text text--lighten-1 mb-5">
              ACTIVATED MENU
              <v-btn @click="activeMenu = !activeMenu" icon color="grey">
                <v-icon v-if="activeMenu">mdi-arrow-down-drop-circle-outline</v-icon>
                <v-icon v-else>mdi-arrow-up-drop-circle-outline</v-icon>
              </v-btn>
            </div>

            <v-banner v-if="menuList == null || menuList.length == 0 && activatedMenu == null || activatedMenu.length == 0">
              There is no Activated Menu Available.</a>
            </v-banner>

            <v-card v-if="activeMenu" class="ma-2" v-for="(ma, index) in activatedMenu" :key="'ma' + index">
              <v-list>

                <v-list-item color="#B3E5F">
                  <v-list-item-avatar size="62">
                    <v-avatar v-if="ma.menu_picture" size="62" color="primary">
                      <v-img :src="ma.menu_picture"/>
                    </v-avatar>
                    <v-avatar v-else color="primary" size="62">
                      <v-icon dark>mdi-food-fork-drink</v-icon>
                    </v-avatar>
                  </v-list-item-avatar>

                  <v-list-item-content>
                    <v-list-item-title>{{ ma.menu_name }}</v-list-item-title>
                    <v-list-item-subtitle>Rp{{ ma.menu_price }}</v-list-item-subtitle>
                  </v-list-item-content>

                  <v-list-item-action>

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          icon
                          v-bind="attrs"
                          v-on="on"
                          @click.prevent="toMenuEdit(ma.id)"
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
                          @click="stockPrepare(ma)"
                        >
                          <v-icon color="grey lighten-1">mdi-circle-edit-outline</v-icon>
                        </v-btn>
                      </template>
                      <span>Edit Menu Stock</span>
                    </v-tooltip>

                  </v-list-item-action>

                  <v-list-item-action>

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          icon
                          v-bind="attrs"
                          v-on="on"
                          @click.prevent="deleteMenu(ma.id)"
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

          <v-col xs="12" sm="12" md="6">

            <div class="text-h6 font-weight-bold blue-grey--text text--lighten-1 mb-5">
              DEACTIVATED MENU
              <v-btn @click="unActiveMenu = !unActiveMenu" icon color="grey">
                <v-icon v-if="unActiveMenu">mdi-arrow-down-drop-circle-outline</v-icon>
                <v-icon v-else>mdi-arrow-up-drop-circle-outline</v-icon>
              </v-btn>
            </div>

            <v-banner v-if="menuList == null || menuList.length == 0 && deactivatedMenu == null || deactivatedMenu.length == 0">
              There is no Deactivated Menu Available.</a>
            </v-banner>

            <v-card v-if="unActiveMenu" class="ma-2" v-for="(md, index) in deactivatedMenu" :key="'md' + index">
              <v-list>

                <v-list-item color="#B3E5F">
                  <v-list-item-avatar size="62">
                    <v-avatar v-if="md.menu_picture" size="62" color="pridry">
                      <v-img :src="md.menu_picture"/>
                    </v-avatar>
                    <v-avatar v-else color="primary" size="62">
                      <v-icon dark>mdi-food-fork-drink</v-icon>
                    </v-avatar>
                  </v-list-item-avatar>

                  <v-list-item-content>
                    <v-list-item-title>{{ md.menu_name }}</v-list-item-title>
                    <v-list-item-subtitle>Rp{{ md.menu_price }}</v-list-item-subtitle>
                  </v-list-item-content>

                  <v-list-item-action>

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          icon
                          v-bind="attrs"
                          v-on="on"
                          @click.prevent="toMenuEdit(md.id)"
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
                          @click="stockPrepare(md)"
                        >
                          <v-icon color="grey lighten-1">mdi-circle-edit-outline</v-icon>
                        </v-btn>
                      </template>
                      <span>Edit Menu Stock</span>
                    </v-tooltip>

                  </v-list-item-action>

                  <v-list-item-action>

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          icon
                          v-bind="attrs"
                          v-on="on"
                          @click.prevent="deleteMenu(md.id)"
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

        


      </v-col>
    </v-row>

    <v-dialog
      v-model="stockDialog"
      width="500"
      persistent
    >
      <v-card>
        <v-overlay
          :absolute="true"
          :value="overlayStock"
        >
          <v-progress-circular
            :size="50"
            color="white"
            indeterminate
          ></v-progress-circular>
        </v-overlay>

        <v-card-title class="headline grey lighten-2">
          Edit Your Menu Stock Here.
        </v-card-title>

        <v-card-text>
          <br>
          <v-text-field
            label="Stock Quantity"
            filled
            v-model="menu_stock_qty"
            :error-messages="menuStockQtyErrors"
            @input="$v.menu_stock_qty.$touch()"
            @blur="$v.menu_stock_qty.$touch()"
          ></v-text-field>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            text
            @click.prevent="stockCleanUpVar()"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            text
            @click.prevent="editStock()"
          >
            Send
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>


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

    <v-snackbar
      v-model="successEditStockSnackbar"
      :timeout="5000"
      color="success"
    >
      Menu Stock Quantity has been Edited successfully from database.
      <v-btn
        color="white"
        text
        @click="successEditStockSnackbar = false"
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
        activeMenu: true,
        unActiveMenu: true,
        dialog: false,
        stockDialog: false,
        menu_activate: [
          'Yes',
          'No'
        ],

        // Search
        searchValue: null,

        // payload
        menu_name: null,
        menu_description: null,
        menu_price: null,
        menu_availability: null,
        menu_picture: null,
        menu_stock_qty: null,

        menuList: null,
        activatedMenu: null,
        deactivatedMenu: null,

        // dynamic var for edit stock
        menu_id: null,

        // Response
        errorAlert: false,
        successSnackbar: false,
        serverErrorCode: null,
        serverError: null,
        errorSnackbar: false,
        successDeleteSnackbar: false,
        successEditStockSnackbar: false,
        overlay: false,
        overlayStock: false,
      }
    }, // end of data()

    validations: {
      menu_stock_qty: {
        numeric,
        required
      },
    },

    computed: {
      menuStockQtyErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.menu_stock_qty.$dirty) return errors
        !currentObj.$v.menu_stock_qty.numeric && errors.push('Menu Stock Quantity must be an numeric.')
        !currentObj.$v.menu_stock_qty.required && errors.push('Menu Stock Quantity is required.')
        return errors
      },
    },

    methods: {

      stockPrepare: function(item) {
        let currentObj = this

        currentObj.menu_id = item.id
        currentObj.menu_stock_qty = item.menu_stock_qty
        currentObj.stockDialog = true
      },

      stockCleanUpVar: function(menuId) {
        let currentObj = this

        currentObj.menu_id = null
        currentObj.menu_stock_qty = null
        currentObj.stockDialog = false
      },

      editStock: function() {
        let currentObj = this

        currentObj.overlayStock = true
        if (currentObj.$v.$invalid) {
          currentObj.errorSnackbar = true
        } else {
          axios.post('api/menu/stock/edit', {
            menu_id: currentObj.menu_id,
            menu_stock_qty: currentObj.menu_stock_qty
          })
            .then(function (response) {
              // after success show successSnackbar
              currentObj.successEditStockSnackbar = true
              currentObj.overlayStock = false
              currentObj.stockCleanUpVar()
              currentObj.getMenu()



            })
            .catch(function (error) {
              if(error.response) {
                currentObj.serverError = error.response.data.errors
                currentObj.errorAlert = true
              }
              currentObj.overlayStock = false
            })
        }

      },

      toMenuEdit: function(menuId) {
        let currentObj = this
        currentObj.$router.push({ path: '/home/menu/edit', query: { menuId: menuId }})
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
            currentObj.getMenu()

          })
          .catch(function (error) {
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }
          })

      },

      filterActiveMenu: function() {
        let currentObj = this

        currentObj.activatedMenu = currentObj.menuList.filter(menu => menu.menu_availability === 'Yes')
      },

      filterNotActiveMenu: function() {
        let currentObj = this

        currentObj.deactivatedMenu = currentObj.menuList.filter(menu => menu.menu_availability === 'No')
      },

      getMenu: function() {
        let currentObj = this
        axios.get('api/menu/list')
          .then(function (response) {

            currentObj.menuList = response.data.menu || null
            // after success show successSnackbar
            currentObj.successSnackbar = true

            currentObj.filterActiveMenu()
            currentObj.filterNotActiveMenu()
          })
      },

      search: function() {
        let currentObj = this
        if (currentObj.searchValue) {
          axios.get('api/menu/list')
          .then(function (response) {

            currentObj.menuList = response.data.menu || null
            // after success show successSnackbar
            currentObj.menuList = currentObj.menuList.filter(menuList => menuList.menu_name.toLowerCase().includes(currentObj.searchValue.toLowerCase()) || menuList.menu_price.includes(currentObj.searchValue))
            console.log('success filter data')

            currentObj.filterActiveMenu()
            currentObj.filterNotActiveMenu()
          })
          
        } else {
          currentObj.getMenu()
        }
        
      }
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
