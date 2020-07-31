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
        <template v-slot:item.action="props">

          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="green" @click.prevent="edit(props.item.id)">
                <v-icon dark>mdi-file-document-edit-outline</v-icon>
              </v-btn>
            </template>
            <span>Edit Order</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="blue" @click.prevent="editMenu(props.item.id)">
                <v-icon dark>mdi-circle-edit-outline</v-icon>
              </v-btn>
            </template>
            <span>Edit Menu</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="red" @click.prevent="deleteOrder(props.item.id)">
                <v-icon dark>mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>
            <span>Delete Order</span>
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
        <template v-slot:item.action="props">

          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="green" @click.prevent="edit(props.item.id)">
                <v-icon dark>mdi-file-document-edit-outline</v-icon>
              </v-btn>
            </template>
            <span>Edit Order</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="blue" @click.prevent="editMenu(props.item.id)">
                <v-icon dark>mdi-circle-edit-outline</v-icon>
              </v-btn>
            </template>
            <span>Edit Menu</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark small color="red" @click.prevent="deleteOrder(props.item.id)">
                <v-icon dark>mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>
            <span>Delete Order</span>
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

  </div>
</template>

<script>
  import axios from 'axios'
  export default {
    data() {
      return {
        search: '',
        serverError: null,
        errorAlert: false,
        orderList: null,
        orderListFiltered: null,
        overlay: false,
        headers: [
          {
            text: 'Buyer Name',
            align: 'start',
            sortable: true,
            value: 'buyer_name',
          },
          {
            text: 'Note',
            value: 'note'
          },
          {
            text: 'Action',
            value: 'action',
            sortable: false
          }
        ],

        orders: [
          {
            id: 1,
            buyer_name: 'Abdurozzaq',
            note: '089603363136'
          },
          {
            id: 2,
            buyer_name: 'Hanif',
            note: '089603363136'
          },
          {
            id: 3,
            buyer_name: 'Sari',
            note: '089603363136'
          }
        ]
      }
    },

    methods: {
      edit: function(data) {
        console.log(data)
      },
      editMenu: function(data) {
        console.log(data)
      },
      deleteOrder: function(data) {
        console.log(data)
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
            order => order.note.toLowerCase().includes(currentObj.search.toLowerCase()) ||
            order.buyer_name.toLowerCase().includes(currentObj.search.toLowerCase())
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
