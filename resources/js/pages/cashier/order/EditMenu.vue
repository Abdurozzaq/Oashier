<template>
    <div>
        <div class="ma-1">

          <v-card class="mb-5">
            <v-card-title>
              <v-icon
                large
                left
                color="grey"
              >
                mdi-food-fork-drink
              </v-icon>
              <span color="white" class="title grey--text">Add Menu To Ozzaq's Order</span>
            </v-card-title>
          </v-card>

          <v-card class="mb-4">
            <v-card-title>
            Add Any Menu
              <v-spacer></v-spacer>
              <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                single-line
                hide-details
                append-outer-icon="mdi-send"

              ></v-text-field>
            </v-card-title>

            <v-sheet
              class="mx-auto"

            >
              <v-slide-group 
                center-active
                multiple 
                show-arrows
              >
                <v-slide-item
                  v-for="(menu, index) in menuList"
                  :key="index"
                  class="mx-auto"
                >

                  <v-card
                    :loading="loading"
                    class="mx-5 my-8 blue-grey lighten-5"
                    width="200px"
                  >
                    <v-img
                      v-if="menu.menu_picture"
                      height="150"
                      src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
                    ></v-img>

                    <v-img
                      v-else
                      height="150"
                      src="/statics/menu.jpg"
                    ></v-img>

                    <div class="mx-4 mt-3">
                      <div class="text-h6">{{ menu.menu_name }}</div>
                      <div class="subtitle-1">
                        Rp.{{ menu.menu_price }}
                      </div>
                      <div class="subtitle-1">
                        {{ menu.menu_stock_qty }} left
                      </div>
                    </div>

                    <v-card-text>
                      {{ menu.menu_description}}
                    </v-card-text>

                    <v-divider class="mx-4"></v-divider>

                    <v-card-actions>
                      <v-btn
                        color="deep-purple lighten-2"
                        text
                        @click.prevent="addMenuToOrder(menu)"
                      >
                        Add To Order
                      </v-btn>
                    </v-card-actions>
                  </v-card>

                </v-slide-item>
              </v-slide-group>
            </v-sheet>
          </v-card>

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

          <v-card class="mb-3">
            <v-card-title>
              Added Menu:
              <v-spacer></v-spacer>
              <v-btn
                color="blue-grey"
                class="ma-2 white--text"
                @click.prevent="saveAddedMenu()"
              >
                Save
                <v-icon right dark>mdi-send</v-icon>
              </v-btn>
            </v-card-title>
            <v-data-table
              :headers="headers"
              :items="addedMenu"
            >
              <template v-slot:item.quantity="props">
                <v-edit-dialog
                  :return-value.sync="props.item.quantity"
                  large
                  persistent
                  @save="save(props.item)"
                  @cancel="cancel"
                  @open="open"
                  @close="close"
                >
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <div 
                      v-bind="attrs" 
                      v-on="on"
                      @click="setMaxSlider(props.item.quantity)"
                    >
                      {{ props.item.quantity }} 
                      <v-icon>mdi-tooltip-edit-outline</v-icon>
                    </div>
                  </template>
                  <span>Edit Quantity</span>
                </v-tooltip>
                  
                  <template v-slot:input>
                    <div class="mt-4 title">Update Quantity</div>
                  </template>
                  <template v-slot:input>
                    <v-slider
                    
                      class="mt-10"
                      v-if="maxSlider && props.item.menu_stock_qty == 0"
                      v-model="props.item.quantity"
                      min="1"
                      :max="maxSlider"
                      :thumb-size="24"
                      thumb-label="always"
                    ></v-slider>

                    <v-slider
                      class="mt-10"
                      v-else-if="props.item.menu_stock_qty > 0"
                      v-model="props.item.quantity"
                      min="1"
                      :max="props.item.menu_stock_qty"
                      :thumb-size="24"
                      thumb-label="always"
                    ></v-slider>

                    <v-slider
                      class="mt-10"
                      v-else
                      v-model="props.item.quantity"
                      min="1"
                      :max="props.item.stock_qty"
                      :thumb-size="24"
                      thumb-label="always"
                    ></v-slider>
                  </template>
                </v-edit-dialog>
              </template>


              <template v-slot:item.action="props">
        
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="red" @click.prevent="removeMenuFromOrder(props.item)">
                      <v-icon dark>mdi-trash-can-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Delete Order</span>
                </v-tooltip>

              </template>

            </v-data-table>

            <v-overlay
              :absolute="true"
              :value="overlayAddedMenu"
            >
              <v-progress-circular
                :size="50"
                color="white"
                indeterminate
              ></v-progress-circular>
            </v-overlay>
          </v-card>
        </div>
        <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
          {{ snackText }}

          <template v-slot:action="{ attrs }">
            <v-btn v-bind="attrs" text @click="snack = false">Close</v-btn>
          </template>
        </v-snackbar>
    </div>
</template>

<script>
const Str = require('@supercharge/strings')
import axios from 'axios'
export default {
    data() {
        return {
          menuList: null,
          maxSlider: null,
          search: null,
          loading: false,
          overlayAddedMenu: false,
          snack: false,
          snackColor: null,
          snackText: null,
          serverError: null,
          errorAlert: false,
          headers: [
            {
              text: 'Menu Name',
              sortable: false,
              value: 'menu_name',
            },
            {
              text: 'Menu Qty',
              sortable: false,
              value: 'quantity',
            },
            {
              text: 'Harga',
              sortable: false,
              value: 'total_price',
            },
            {
              text: 'Action',
              value: 'action',
              sortable: false
            }
          ],
          addedMenu: []
      }
    },

    methods: {
      setMaxSlider: function(qty) {
        let currentObj = this
        console.log(qty)
        currentObj.maxSlider = qty
        currentObj.snack = true
        currentObj.snackColor = 'success'
        currentObj.snackText = 'TEZST'
      },
      stock: function(props) {
        let currentObj = this

        currentObj.addedMenu.forEach(function (menu) {
          if (menu.menu_id == props.menu_id) {
            return menu.menu_stock_qty
          }
        })
      },
      save (props) {
        let currentObj = this
        currentObj.addedMenu.forEach(function (menu) {
          if (menu.menu_id == props.menu_id) {
            menu.total_price = menu.one_unit_price * menu.quantity
            console.log('success edit qty')
          }
        })
        currentObj.snack = true
        currentObj.snackColor = 'success'
        currentObj.snackText = 'Data Changed'
      },
      cancel () {
        let currentObj = this
        currentObj.snack = true
        currentObj.snackColor = 'error'
        currentObj.snackText = 'Canceled'
      },
      open () {
        let currentObj = this
        currentObj.snack = true
        currentObj.snackColor = 'info'
        currentObj.snackText = 'Dialog opened'
      },
      close () {
        console.log('Dialog closed')
      },
      removeMenuFromOrder: function(menu) {
        let currentObj = this

        currentObj.overlayAddedMenu = true
        axios.post('api/order/details/delete/' + menu.code)
          .then(function (response) {
            currentObj.addedMenu.splice(currentObj.addedMenu.indexOf(menu), 1);
            currentObj.overlayAddedMenu = false

            currentObj.snack = true
            currentObj.snackColor = 'success'
            currentObj.snackText = 'Menu Has Been Removed From List'
            currentObj.getMenu()
          })
          .catch(function (error) {
            currentObj.overlay = false
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }

            currentObj.overlayAddedMenu = false
          })
        
        
      },
      addMenuToOrder: function(menu) {
        let currentObj = this
        let order_id = currentObj.$route.query.id
        if (currentObj.addedMenu.length == 0) {
          currentObj.addedMenu.push({
            'order_id': order_id,
            'menu_id': menu.id,
            'menu_name': menu.menu_name,
            'code': Str.random(),
            'quantity': 1,
            'one_unit_price': menu.menu_price,
            'total_price': menu.menu_price,
            'stock_qty': menu.menu_stock_qty
          })
        } else {
          var am = currentObj.addedMenu.filter(am => am.menu_id == menu.id)
          
          if (am.length == 0) {
            currentObj.addedMenu.push({
              'order_id': order_id,
              'menu_id': menu.id,
              'menu_name': menu.menu_name,
              'code': Str.random(),
              'quantity': 1,
              'one_unit_price': menu.menu_price,
              'total_price': menu.menu_price,
              'stock_qty': menu.menu_stock_qty
            })
          } else {
            currentObj.snack = true
            currentObj.snackColor = 'error'
            currentObj.snackText = 'Menu already added to list!'
          }
        }
        
      },
      getMenu: function() {
        let currentObj = this
        axios.get('api/menu/list')
          .then(function (response) {
            currentObj.menuList = response.data.menu || null
          })
      },

      getAddedMenu: function() {
        let currentObj = this
        let id = currentObj.$route.query.id
        currentObj.overlayAddedMenu = true
        axios.post('api/order/details/list/' + id)
          .then(function (response) {
            currentObj.addedMenu = response.data.menu || []
            currentObj.overlayAddedMenu = false
          })
        
      },

      saveAddedMenu: function() {
        let currentObj = this

        currentObj.overlayAddedMenu = true
        axios.post('api/order/details/create', currentObj.addedMenu)
          .then(function (response) {
            currentObj.snack = true
            currentObj.snackColor = 'success'
            currentObj.snackText = 'Menu Saved Succesfully'
            currentObj.overlayAddedMenu = false
            currentObj.getMenu()
          })
          .catch(function (error) {
            currentObj.overlay = false
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }

            currentObj.overlayAddedMenu = false
          })
      }
    }, // end of methods

    beforeMount: function () {
        let currentObj = this
        currentObj.getMenu()
        currentObj.getAddedMenu()

    }
}
</script>
