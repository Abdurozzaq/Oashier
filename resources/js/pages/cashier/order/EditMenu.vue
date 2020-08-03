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
                        @click.prevent="addMenuToOrder(menu, index)"
                      >
                        Add To Order
                      </v-btn>
                    </v-card-actions>
                  </v-card>

                </v-slide-item>
              </v-slide-group>
            </v-sheet>
          </v-card>



          <v-card class="mb-3" >
            <v-card-title>Added Menu:</v-card-title>
            <v-data-table
              :headers="headers"
              :items="addedMenu"
            >
              <template v-slot:item.quantity="props">
                <v-edit-dialog
                  :return-value.sync="props.item.quantity"
                  large
                  persistent
                  @save="save"
                  @cancel="cancel"
                  @open="open"
                  @close="close"
                >
                  <div>{{ props.item.quantity }}</div>
                  <template v-slot:input>
                    <div class="mt-4 title">Update Quantity</div>
                  </template>
                  <template v-slot:input>
                    <v-text-field
                      v-model="props.item.quantity"
                      label="Edit"
                      single-line
                      counter
                      autofocus
                    ></v-text-field>
                  </template>
                </v-edit-dialog>
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
import axios from 'axios'
export default {
    data() {
        return {
          menuList: null,
          search: null,
          loading: false,
          overlayAddedMenu: false,
          snack: false,
          snackColor: null,
          snackText: null,
          headers: [
            {
              text: 'Menu Name',
              sortable: true,
              value: 'menu_name',
            },
            {
              text: 'Menu Qty',
              sortable: true,
              value: 'quantity',
            },
            {
              text: 'Harga',
              sortable: true,
              value: 'total_price',
            },
          ],
          addedMenu: []
      }
    },

    methods: {
        save () {
          this.snack = true
          this.snackColor = 'success'
          this.snackText = 'Data saved'
        },
        cancel () {
          this.snack = true
          this.snackColor = 'error'
          this.snackText = 'Canceled'
        },
        open () {
          this.snack = true
          this.snackColor = 'info'
          this.snackText = 'Dialog opened'
        },
        close () {
          console.log('Dialog closed')
        },
        addMenuToOrder: function(menu, index) {
          let currentObj = this
          if (currentObj.addedMenu.length == 0) {
            currentObj.addedMenu.push({
              'order_id': menu.id,
              'menu_name': menu.menu_name,
              'quantity': 1,
              'total_price': menu.menu_price,
              'tempId': menu.id
            })
          } else {
            var am = currentObj.addedMenu.filter(am => am.tempId == menu.id)
            
            if (am.length == 0) {
              currentObj.addedMenu.push({
                'order_id': menu.id,
                'menu_name': menu.menu_name,
                'quantity': 1,
                'total_price': menu.menu_price,
                'tempId': menu.id
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
    }, // end of methods

    mounted: function () {
        let currentObj = this
        currentObj.getMenu()

    }
}
</script>
