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
      Your Order List
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
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="green" @click.prevent="editOrderPrepare(props.item)">
                <v-icon dark>mdi-file-document-edit-outline</v-icon>
              </v-btn>
            </template>
            <span>Edit Order</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="blue" @click.prevent="toEditMenu(props.item.id)">
                <v-icon dark>mdi-circle-edit-outline</v-icon>
              </v-btn>
            </template>
            <span>Edit Menu</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="red" @click.prevent="cancelOrder(props.item)">
                <v-icon dark>mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>
            <span>Cancel Order</span>
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
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="green" @click.prevent="editOrderPrepare(props.item)">
                <v-icon dark>mdi-file-document-edit-outline</v-icon>
              </v-btn>
            </template>
            <span>Edit Order</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="blue" @click.prevent="toEditMenu(props.item.id)">
                <v-icon dark>mdi-circle-edit-outline</v-icon>
              </v-btn>
            </template>
            <span>Edit Menu</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="red" @click.prevent="cancelOrder(props.item)">
                <v-icon dark>mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>
            <span>Cancel Order</span>
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
      v-model="editOrderDialog"
      width="500"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2">
          Edit Order Information
        </v-card-title>

        <v-card-text>
          <v-textarea
            class="mt-10"
            filled
            label="Order Note"
            v-model="order_note"
            :error-messages="orderNoteErrors"
            @input="$v.order_note.$touch()"
            @blur="$v.order_note.$touch()"
          ></v-textarea>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            text
            @click.prevent="editOrderCleanUpVar()"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            text
            @click.prevent="edit()"
          >
            Edit
          </v-btn>
        </v-card-actions>
      </v-card>

      <v-overlay
        :absolute="true"
        :value="overlayOrderInformation"
      >
        <v-progress-circular
          :size="50"
          color="white"
          indeterminate
        ></v-progress-circular>
      </v-overlay>
    </v-dialog>

    <v-snackbar top v-model="snack" :timeout="3000" :color="snackColor">
      {{ snackText }}

      <template v-slot:action="{ attrs }">
        <v-btn v-bind="attrs" text @click="snack = false">Close</v-btn>
      </template>
    </v-snackbar>

  </div>
</template>

<script>
  import { required, numeric } from 'vuelidate/lib/validators'
  import axios from 'axios'
  export default {
    data() {
      return {

        // Snack Bar
        snack: false,
        snackColor: null,
        snackText: null,

        // Edit Order form
        editOrderDialog: false,
        order_note: null,
        order_id: null,
        overlayOrderInformation: false,
        successEditOrderInfo: false,

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
          },
          
        ],
      }
    }, // end of data()

    validations: {
      order_note: {
        required,
      },
    }, // end of validations

    computed: {
      orderNoteErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.order_note.$dirty) return errors
        !currentObj.$v.order_note.required && errors.push('Order Note is required.')
        return errors
      },
    },

    methods: {
      editOrderPrepare: function(item) {
        let currentObj = this

        currentObj.order_id = item.id
        currentObj.order_note = item.order_note
        currentObj.order_number = item.order_number
        currentObj.editOrderDialog = true
      },

      editOrderCleanUpVar: function() {
        let currentObj = this

        currentObj.order_id = null
        currentObj.order_note = null
        currentObj.order_number= null
        currentObj.editOrderDialog = false
      },

      edit: function(data) {
        let currentObj = this

        currentObj.overlayOrderInformation = true
        if (currentObj.$v.$invalid) {
          currentObj.snack = true
          currentObj.snackColor = 'error'
          currentObj.snackText = 'Input Data Invalid'
          currentObj.overlayOrderInformation = false
        } else {
          axios.post('api/order/edit', {
            order_id: currentObj.order_id,
            order_note: currentObj.order_note,
            order_number: currentObj.order_number
          })
            .then(function (response) {
              // after success show successSnackbar
              currentObj.snack = true
              currentObj.snackColor = 'success'
              currentObj.snackText = 'Order Information has been Edited successfully from database.'
              currentObj.overlayOrderInformation = false
              currentObj.editOrderCleanUpVar()
              currentObj.getData()



            })
            .catch(function (error) {
              if(error.response) {
                currentObj.serverError = error.response.data.errors
                currentObj.errorAlert = true
              }
              currentObj.overlayOrderInformation = false
            })
        }
      },
      cancelOrder: function(order) {
        let currentObj = this
        axios.post('api/order/cancel/' + order.id)
          .then(function (response) {
            if(currentObj.orderListFiltered) {
              currentObj.orderList.splice(currentObj.orderList.indexOf(order), 1);
              currentObj.orderListFiltered.splice(currentObj.orderListFiltered.indexOf(order), 1);
            } else {
              currentObj.orderList.splice(currentObj.orderList.indexOf(order), 1);
            }
            currentObj.snack = true
            currentObj.snackColor = 'success'
            currentObj.snackText = 'Order Cancelled Successfully && Menu Stock On Order Returned Back.'
          })
          .catch(function (error) {
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }
            currentObj.overlay = false
          })
      },

      toEditMenu: function(id) {
        let currentObj = this

        currentObj.$router.push({ path: '/home/order/edit', query: { id: id }})
      },

      getData: function() {
        let currentObj = this

        currentObj.overlay = true
        axios.get('api/order/list')
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
