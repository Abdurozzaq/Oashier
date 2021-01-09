<template>
  <div class="ma-10">

    <v-row justify="start" class="mx-auto">
      <v-col
        cols="12"

      >
        <v-form @submit.prevent="createMenu">
          <v-card
            class=" mx-auto"
            color="#5B86E5"
            elevation="8"
          >
            <v-card-title>
              <v-icon
                large
                left
                color="white"
              >
                mdi-food-fork-drink
              </v-icon>
              <span color="white" class="title white--text">Create New Order</span>
            </v-card-title>

              <v-card-text class="headline font-weight-bold">
                <v-row>

                  <v-col cols="12" sm="12">

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
                    
                    <v-text-field
                      :value="order_number"
                      label="Order Number"
                      hint="Auto Added"
                      filled
                      dark
                      disabled
                      style="max-width: 150px;"
                    ></v-text-field>

                    <v-textarea
                      filled
                      dark
                      label="Order Note"
                      hint="Optional"
                      v-model="order_note"
                    ></v-textarea>

                  </v-col>

                </v-row>
              </v-card-text>

              <v-card-actions>
                <v-list-item class="grow">
                  <v-row
                    align="center"
                    justify="end"
                  >
                    <v-btn type="submit" >Create</v-btn>
                  </v-row>
                </v-list-item>
              </v-card-actions>

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
        </v-form>
      </v-col>
    </v-row>


    <v-snackbar
      v-model="successSnackbar"
      :timeout="5000"
      color="success"
    >
      Order has been created successfully. Now you can add some menus.
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
  </div>
</template>

<script>
  import axios from 'axios'
  export default {
    name: 'CreateMenuPage',
    data() {
      return {
        dialog: false,
        order_note: null,
        order_number: null,


        // Response
        errorAlert: false,
        successSnackbar: false,
        overlay: false,
        serverErrorCode: null,
        serverError: null,
        errorSnackbar: false,
      }
    }, // end of data()

    methods: {

      createMenu: function() {

        let currentObj = this

        currentObj.errorAlert = false
        currentObj.overlay = true

        axios.post('api/order/create', {
          'order_number': currentObj.order_number,
          'order_note': currentObj.order_note,
        })
          .then(function (response) {

            // after success show successSnackbar
            currentObj.successSnackbar = true

            currentObj.overlay = false

            let id = response.data.id
            currentObj.$router.push({ path: '/home/order/edit', query: { id: id }})

            // currentObj.$router.push('/home/menu/list')

          })
          .catch(function (error) {
            currentObj.overlay = false
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }
          })
      },

      setOrderNumber: function() {
        let currentObj = this

        axios.get('api/order/order-number')
          .then(function (response) {
            
            currentObj.order_number = response.data.order_number
          })
          .catch(function (error) {
              currentObj.overlay = false
              if(error.response) {
                currentObj.serverError = error.response.data.errors
                currentObj.errorAlert = true
              }
            })
      }
    }, // end of methods


    mounted: function () {
      let currentObj = this

      currentObj.setOrderNumber()
    }

  }
</script>
