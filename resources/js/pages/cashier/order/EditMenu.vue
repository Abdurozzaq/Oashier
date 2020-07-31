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
            <v-card-text>
              <v-text-field
                label="Search Menu"
                hint="Search Menu With Name And Price"
                filled
                persistent-hint
              ></v-text-field>
            </v-card-text>
          </v-card>

          <v-sheet
            class="mx-auto"

          >
            <v-slide-group multiple show-arrows>
              <v-slide-item
              v-for="n in 25"
              :key="n"
              >

                <v-card
                  :loading="loading"
                  class="mx-5 my-8"
                  max-width="250px"
                >
                  <v-img
                    height="150"
                    src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
                  ></v-img>

                  <div class="mx-4 mt-3">
                    <div class="text-h6">Ayam Bakar</div>
                    <div class="subtitle-1">
                      Rp.5000
                    </div>
                  </div>

                  <v-card-text>

                    <div>Small plates, salads & sandwiches - an intimate setting</div>
                  </v-card-text>

                  <v-divider class="mx-4"></v-divider>

                  <v-card-actions>
                    <v-btn
                      color="deep-purple lighten-2"
                      text
                    >
                      Add To Order
                    </v-btn>
                  </v-card-actions>
                </v-card>

              </v-slide-item>
            </v-slide-group>
          </v-sheet>

          <div class="text-h6">Added Menu:</div>

          <v-card class="mb-3" v-for="(ma, index) in activatedMenu" :key="'ma' + index">
            <v-list>

              <v-list-item color="#B3E5F">
                <v-list-item-avatar size="62">
                  <v-avatar size="62" color="primary">
                    <v-img :src="ma.menu_picture"/>
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
                        @click="stockPrepare(ma.id)"
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
        </div>
    </div>
</template>

<script>
import axios from 'axios'
export default {
    data() {
        return {
           menuList: null,

        }
    },

    methods: {
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
    }, // end of methods

    mounted: function () {
        let currentObj = this


    }
}
</script>
