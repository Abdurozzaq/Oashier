<template>
  <div class="ma-8">

    <v-row justify="start">
      <v-col
        cols="12"
        
      >
        <v-card
          class="d-flex align-center"
          color="#26c6da"
          dark
          elevation="8"
          height="200"
        >

          <v-card-text class="text-h3 font-weight-bold" style="text-align: center;">
            Welcome To Your Dashboard
          </v-card-text>

        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col xs="12" sm="4" md="4">
        <v-card
            color="green"
            dark
          >
            <div class="d-flex flex-no-wrap justify-space-between">
              <div>
                <v-card-text>
                  <div class="text-h3">{{ totalSuccessOrder }}</div>
                  <div class="text-subtitle1">Success Orders</div>
                </v-card-text>
              </div>

              <v-avatar
                class="ma-3"
                size="125"
                tile
              >
                <v-img src="/statics/success-icon.png"></v-img>
              </v-avatar>
            </div>
          </v-card>
      </v-col>

      <v-col xs="12" sm="4" md="4">
        <v-card
            color="red"
            dark
          >
            <div class="d-flex flex-no-wrap justify-space-between">
              <div>
                <v-card-text>
                  <div class="text-h3">{{ totalCancelledOrder }}</div>
                  <div class="text-subtitle1">Cancelled Orders</div>
                </v-card-text>
              </div>

              <v-avatar
                class="ma-3"
                size="125"
                tile
              >
                <v-img src="/statics/fail-icon.png"></v-img>
              </v-avatar>
            </div>
          </v-card>
      </v-col>

      <v-col xs="12" sm="4" md="4">
        <v-card
            color="blue"
            dark
          >
            <div class="d-flex flex-no-wrap justify-space-between">
              <div>
                <v-card-text>
                  <div class="text-h3">{{ totalAllOrder }}</div>
                  <div class="text-subtitle1">Unpaid Orders</div>
                </v-card-text>
              </div>

              <v-avatar
                class="ma-3"
                size="125"
                tile
              >
                <v-img src="/statics/order-list-icon.png"></v-img>
              </v-avatar>
            </div>
          </v-card>
      </v-col>

    </v-row>
  </div>
</template>

<script>
  import axios from 'axios'
  export default {
    name: 'HomePage',
    data() {
      return {
        dialog: false,
        totalSuccessOrder: 0,
        totalCancelledOrder: 0,
        totalAllOrder: 0,
      }
    }, // End of Data

    methods: {
      getSuccessOrders: function() {
        let currentObj = this
        axios.get('api/order/successed/list')
        .then(function (response) {
          currentObj.totalSuccessOrder = response.data.data.length || 0
        })
      },
      getCancelledOrders: function() {
        let currentObj = this
        axios.get('api/order/cancelled/list')
        .then(function (response) {
          currentObj.totalCancelledOrder = response.data.data.length || 0
        })
      },
      getAllOrders: function() {
        let currentObj = this
        axios.get('api/order/list')
        .then(function (response) {
          currentObj.totalAllOrder = response.data.data.length || 0
        })
      },
    }, // End of Methods
    mounted: function () {
      let currentObj = this

      currentObj.getCancelledOrders()
      currentObj.getSuccessOrders()
      currentObj.getAllOrders()
    }, // End of Mounted
  }
</script>