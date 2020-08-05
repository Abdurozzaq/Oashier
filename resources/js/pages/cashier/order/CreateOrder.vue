<template>
  <div class="ma-10">

    <v-row justify="start" class="mx-auto">
      <v-col
        cols="12"

      >
        <v-form @submit.prevent="createMenu">
          <v-card
            class=" mx-auto"
            color="#26c6da"
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
                      label="Buyer Name"
                      single-line
                      solo
                      required
                      :error-messages="orderBuyerNameErrors"
                      v-model="order_buyer_name"
                      @input="$v.order_buyer_name.$touch()"
                      @blur="$v.order_buyer_name.$touch()"

                    ></v-text-field>

                    <v-textarea
                      solo
                      label="Order Note"
                      v-model="order_note"
                      :error-messages="orderNoteErrors"
                      @input="$v.order_note.$touch()"
                      @blur="$v.order_note.$touch()"
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
  import { required, numeric } from 'vuelidate/lib/validators'
  import axios from 'axios'
  export default {
    name: 'CreateMenuPage',
    data() {
      return {
        dialog: false,
        order_note: null,
        order_buyer_name: null,


        // Response
        errorAlert: false,
        successSnackbar: false,
        overlay: false,
        serverErrorCode: null,
        serverError: null,
        errorSnackbar: false,
      }
    }, // end of data()

    validations: {
      order_note: {
        required,
      },
      order_buyer_name: {
        required,
      }
    }, // end of validations

    computed: {
      orderNoteErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.order_note.$dirty) return errors
        !currentObj.$v.order_note.required && errors.push('Order Note is required.')
        return errors
      },

      orderBuyerNameErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.order_buyer_name.$dirty) return errors
        !currentObj.$v.order_buyer_name.required && errors.push('Buyer Name is required.')
        return errors
      },
    },

    methods: {

      createMenu: function() {

        let currentObj = this

        currentObj.$v.$touch()

        if (currentObj.$v.$invalid) {
          currentObj.errorSnackbar = true
        } else {
          currentObj.errorAlert = false
          currentObj.overlay = true

          axios.post('api/order/create', {
            'order_buyer_name': currentObj.order_buyer_name,
            'order_note': currentObj.order_note,
          })
            .then(function (response) {

              // after success show successSnackbar
              currentObj.successSnackbar = true

              currentObj.overlay = false

              currentObj.$v.$reset()

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
        }
      },
    },


  }
</script>
