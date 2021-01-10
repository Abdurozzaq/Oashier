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
              <span color="white" class="title grey--text">Add Menu To Order</span>
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
                @click:append-outer="searchMenu()"
              ></v-text-field>
            </v-card-title>

            <v-sheet
              class="mx-auto"

            >
              <v-slide-group 
                center-active
                multiple 
                show-arrows
                v-if="menuList && menuListFiltered == null"
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
                      :src="menu.menu_picture"
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
                        :disabled="menu.menu_stock_qty == 0"
                      >
                        Add To Order
                      </v-btn>
                    </v-card-actions>
                  </v-card>

                </v-slide-item>
              </v-slide-group>

              <v-slide-group 
                center-active
                multiple 
                show-arrows
                v-if="menuList && menuListFiltered != null"
              >
                <v-slide-item
                  v-for="(menu, index) in menuListFiltered"
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
                      :src="menu.menu_picture"
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
                        :disabled="menu.menu_stock_qty == 0"
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
              <v-btn
                :disabled="addedMenu.length == 0"
                color="teal lighten-1"
                class="ma-2 white--text"
                @click.prevent="preparePayCash()"
              >
                Pay
                <v-icon right dark>mdi-currency-usd</v-icon>
              </v-btn>
            </v-card-title>
            <v-data-table
              :headers="headers"
              :items="addedMenu"
              hide-default-footer
            >
              <template v-slot:[`item.quantity`]="props">
                <v-edit-dialog
                  :return-value.sync="props.item.quantity"
                  large
                  persistent
                  @save="save(props.item)"
                  @cancel="cancel"
                >
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <div 
                      v-bind="attrs" 
                      v-on="on"
                      @click="setMaxSlider(props.item)"
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
                  <template v-slot:[`input`]>
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
                      v-else-if="maxSlider && props.item.menu_stock_qty > 0"
                      v-model="props.item.quantity"
                      min="1"
                      :max="maxSlider"
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


              <template v-slot:[`item.action`]="props">
        
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

            <v-card-text>
              <div class="d-flex justify-end text-h6 black--text font-weight-bold">TOTAL : {{ totalAllRp }}</div>
            </v-card-text>
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
        <v-snackbar top v-model="snack" :timeout="3000" :color="snackColor">
          {{ snackText }}

          <template v-slot:action="{ attrs }">
            <v-btn v-bind="attrs" text @click="snack = false">Close</v-btn>
          </template>
        </v-snackbar>

      <!-- Pay Dialog -->
      <v-dialog v-model="payCashDialog" persistent max-width="290">
        <v-card>
          <v-card-title class="headline">Pay Cash</v-card-title>
          <v-divider></v-divider>
          <v-card-text class="mt-3 d-flex justify-center text-h6">{{ totalAllRp }}</v-card-text>
          <v-card-text>Enter the nominal customer money, which is given.</v-card-text>
          <v-card-text>
            <v-text-field
              label="Enter Nominal"
              hint="No punctuation | Hanya Angka"
              persistent-hint
              filled
              v-model="customerNominal"
              :error-messages="customerNominalErrors"
              @input="$v.customerNominal.$touch()"
              @blur="$v.customerNominal.$touch()"
            ></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="green darken-1" text @click="payCashDialog = false">Cancel</v-btn>
            <v-btn color="green darken-1" text @click="payCash">Pay Now</v-btn>
          </v-card-actions>
        </v-card>
        <v-overlay
          :absolute="true"
          :value="overlayPayFormDialog"
        >
          <v-progress-circular
            :size="50"
            color="white"
            indeterminate
          ></v-progress-circular>
        </v-overlay>
      </v-dialog>

      <!-- Receipt Dialog -->
      <v-dialog v-model="receiptDialog" persistent max-width="290">
        <v-card>
          <v-card-title class="headline">Pay Cash</v-card-title>
          <v-divider></v-divider>
          <v-card-text class="mt-3 d-flex justify-center">
            <img
              class="mt-3"
              style="height: 64px;"
              src="/statics/success-icon.png"
            />
          </v-card-text>
          <v-card-text class="d-flex justify-center">Transaction has been successful!</v-card-text>
          <v-card-text  class="mt-3 d-flex justify-center text-h6">Change Money :</v-card-text>
          <v-card-text  class="d-flex justify-center text-h6">{{ changeMoneyRp }}</v-card-text>
          <!-- <v-card-text>Print Receipt?</v-card-text> -->
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" text @click="successRedirect()">Ok, Thanks!</v-btn>
            <!-- <v-btn color="red darken-1" text @click="successRedirect()">No</v-btn> -->
            <!-- <v-btn color="green darken-1" text>Print Receipt</v-btn> -->
          </v-card-actions>
        </v-card>
        <v-overlay
          :absolute="true"
          :value="overlayPayFormDialog"
        >
          <v-progress-circular
            :size="50"
            color="white"
            indeterminate
          ></v-progress-circular>
        </v-overlay>
      </v-dialog>
    </div>
</template>

<script>
const Str = require('@supercharge/strings')
import axios from 'axios'
import { required, numeric } from 'vuelidate/lib/validators'
export default {
    data() {
        return {
          menuList: null,
          menuListFiltered: null,
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
          addedMenu: [],
          totalAll: 0,
          totalAllRp: 0,
          payCashDialog: false,
          customerNominal: null,
          overlayPayFormDialog: false,
          receiptDialog: false,
          changeMoney: null,
          changeMoneyRp: null,
      }
    },

    validations: {
      customerNominal: {
        required,
        numeric
      },
    }, // end of validations

    computed: {
      customerNominalErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.customerNominal.$dirty) return errors
        !currentObj.$v.customerNominal.required && errors.push('Nominal is required.')
        !currentObj.$v.customerNominal.numeric && errors.push('Nominal is must be an numeric.')
        return errors
      },
    }, // end of computed

    methods: {
      successRedirect: function() {
        let currentObj = this
        currentObj.receiptDialog = false
        currentObj.$router.push({ path: '/home/order/successed/list'})
      },
      payCash: function () {
        let currentObj = this

        let order_id = currentObj.$route.query.id

        currentObj.overlayPayFormDialog = true
        
        currentObj.changeMoney = parseInt(currentObj.customerNominal) - parseInt(currentObj.totalAll) 
        
        currentObj.changeMoneyRp = new Intl.NumberFormat('id-ID', {
          style:'currency',
          currency: 'IDR',
          minimumFractionDigits: 2        
        }).format(currentObj.changeMoney);

        if (currentObj.changeMoney) {
          axios.post('api/order/pay/cash/' + order_id, {
            customerNominal: currentObj.customerNominal,
            changeMoney: currentObj.changeMoney
          })
          .then(function (response) {
            currentObj.receiptDialog = true
            currentObj.payCashDialog = false
            currentObj.overlayPayFormDialog = false

            currentObj.snack = true
            currentObj.snackColor = 'success'
            currentObj.snackText = 'Order has been successfully Paid'
          })
          .catch(function (error) {
            currentObj.overlay = false
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }

            currentObj.overlayPayFormDialog = false
          })
        }
        
      },
      preparePayCash: function() {
        let currentObj = this
        currentObj.saveAddedMenu()
        currentObj.payCashDialog = true
      },
      setTotal: function() {
        let currentObj = this
        currentObj.totalAll = 0
        if(currentObj.totalAll == 0) {
          currentObj.addedMenu.forEach(function (menu) {

            currentObj.totalAll = parseInt(currentObj.totalAll) + parseInt(menu.total_price)

            currentObj.totalAllRp = new Intl.NumberFormat('id-ID', {
                style:'currency',
                currency: 'IDR',
                minimumFractionDigits: 2        
            }).format(currentObj.totalAll);


          })
        } 
        
      },
      setMaxSlider: function(item) {
        let currentObj = this
        console.log(item)
        currentObj.maxSlider = parseInt(item.quantity) + parseInt(item.menu_stock_qty) 
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
        currentObj.maxSlider = null
        currentObj.setTotal()
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
            if(response.data.status == "success") {
              currentObj.getMenu()
              currentObj.getAddedMenu()
              currentObj.setTotal()
            }
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
            'stock_qty': menu.menu_stock_qty,
            'not_saved': true
          })
          currentObj.setTotal()
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
              'stock_qty': menu.menu_stock_qty,
              'not_saved': true
            })
            currentObj.setTotal()
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
            if(currentObj.addedMenu.length === 0) {
              currentObj.addedMenu = response.data.menu || []
            } else {
              var any_not_saved = currentObj.addedMenu.filter(ans => ans.not_saved == true)
              if(any_not_saved.length === 0) {
                currentObj.addedMenu = response.data.menu || []
                console.log('ans = 0')
              } else {
                // currentObj.addedMenu.push(response.data.menu)
                console.log('ans != 0')
              }
            }
            
            currentObj.overlayAddedMenu = false
            currentObj.setTotal()
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
            currentObj.addedMenu = []
            currentObj.getMenu()
            currentObj.getAddedMenu()
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

      searchMenu: function() {
        let currentObj = this
        console.log('test')
        if (currentObj.search != null) {
          currentObj.menuListFiltered = currentObj.menuList.filter(
            menu => menu.menu_name.toLowerCase().includes(currentObj.search.toLowerCase()) ||
            menu.menu_price.toLowerCase().includes(currentObj.search.toLowerCase()) ||
            menu.menu_description.toLowerCase().includes(currentObj.search.toLowerCase())
          )

        } else {
          currentObj.orderListFiltered = null
        }

      }
    }, // end of methods

    beforeMount: function () {
      let currentObj = this
      currentObj.getMenu()
      currentObj.getAddedMenu()

      // setTimeout(function(){ 
      //   currentObj.getAddedMenu()
      // }, 3000);
    }
}
</script>
