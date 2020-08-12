<template>
  <div class="ma-5">

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

    <v-card>
      <v-card-title>
      Your Successed Order List
        <v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Search"
          single-line
          hide-details
          append-outer-icon="mdi-send"
          @click:append-outer="searchOrder"
        ></v-text-field>
      </v-card-title>

      <v-data-table
        v-if="orderList && orderListFiltered == null"
        :headers="headers"
        :items="orderList"
        :items-per-page="5"
        class="elevation-1"
      >

        <template v-slot:[`item.created_at`]="{ item }">
          <span>{{ new Date(item.created_at).toLocaleString() }}</span>
        </template>

        <template v-slot:[`item.action`]="props">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="green" @click.prevent="seeMenuList(props.item)">
                <v-icon dark>mdi-file-document-edit-outline</v-icon>
              </v-btn>
            </template>
            <span>See The Menus</span>
          </v-tooltip>
        </template>
      </v-data-table>

      <v-data-table
        v-if="orderList && orderListFiltered != null"
        :headers="headers"
        :items="orderListFiltered"
        :items-per-page="5"
        class="elevation-1"
      >

        <template v-slot:[`item.created_at`]="{ item }">
          <span>{{ new Date(item.created_at).toLocaleString() }}</span>
        </template>

        <template v-slot:[`item.action`]="props">

          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="green" @click.prevent="seeMenuList(props.item)">
                <v-icon dark>mdi-ballot</v-icon>
              </v-btn>
            </template>
            <span>See The Menus</span>
          </v-tooltip>

        </template>
      </v-data-table>

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

    <v-dialog
      v-model="menuListDialog"
      width="500"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2">
          Menu List
        </v-card-title>

        <v-card-text>
          <v-simple-table>
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">Name</th>
                  <th class="text-left">QTY</th>
                  <th class="text-left">Price</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in tempMenuList" :key="item.id">
                  <td>{{ item.menu_name }}</td>
                  <td>{{ item.quantity }}</td>
                  <td>{{ item.total_price }}</td>
                </tr>
                <tr colspan="2">
                  <td></td>
                  <td class="text-right">TOTAL :</td>
                  <td>{{ total }}</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            text
            @click.prevent="closeAndResetVar()"
          >
            OK
          </v-btn>
        </v-card-actions>
      </v-card>

      <v-overlay
        :absolute="true"
        :value="overlayMenuList"
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
  import { required, numeric } from 'vuelidate/lib/validators'
  import axios from 'axios'
  import moment from 'moment';
  export default {
    data() {
      return {

        // Menu List
        menuListDialog: false,
        tempMenuList: null,
        overlayMenuList: false,
        total: null,

        // Data Table
        search: null,
        serverError: null,
        errorAlert: false,
        orderList: null,
        orderListFiltered: null,
        overlay: false,
        headers: [
          {
            text: 'Order Number',
            align: 'start',
            sortable: true,
            value: 'order_number',
          },
          {
            text: 'Note',
            sortable: true,
            value: 'order_note'
          },
          {
            text: 'Date & Time',
            sortable: true,
            value: 'created_at'
          },
          {
            text: 'Action',
            value: 'action',
            sortable: false
          }
        ],
      }
    }, // end of data()

    validations: {
      order_note: {
        required,
      },
    }, // end of validations

    methods: {
      seeMenuList: function(item) {
        let currentObj = this
        currentObj.menuListDialog = true
        currentObj.overlayMenuList = true

        axios.get('api/order/successed/details/list/' + item.id)
          .then(function (response) {

            currentObj.tempMenuList = response.data.menu
            currentObj.total = response.data.total
            currentObj.overlayMenuList = false
          })
          .catch(function (error) {
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }
            currentObj.overlayMenuList = false
          })
      },

      closeAndResetVar: function () {
        let currentObj = this

        currentObj.tempMenuList = null

        currentObj.menuListDialog = false
      },

      getData: function() {
        let currentObj = this

        currentObj.overlay = true
        axios.get('api/order/successed/list')
          .then(function (response) {

            currentObj.orderList = response.data.data
            currentObj.overlay = false
          })
          .catch(function (error) {
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }
            currentObj.overlay = false
          })
      },

      searchOrder: function() {
        let currentObj = this
        if (currentObj.search != null) {
          currentObj.orderListFiltered = currentObj.orderList.filter(
            order => order.order_note.toLowerCase().includes(currentObj.search.toLowerCase()) ||
            order.order_number.toLowerCase().includes(currentObj.search.toLowerCase())
          )

        } else {
          currentObj.orderListFiltered = null
        }

      }
    },

    mounted: function() {
      let currentObj = this

      currentObj.getData()
    }
  }
</script>
