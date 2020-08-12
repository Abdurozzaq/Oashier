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
              <span color="white" class="title white--text">Create Menu</span>
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
                      label="Menu Name"
                      single-line
                      filled
                      dark
                      required
                      :error-messages="menuNameErrors"
                      v-model="menu_name"
                      @input="$v.menu_name.$touch()" 
                      @blur="$v.menu_name.$touch()"
                      
                    ></v-text-field>

                    <v-textarea
                      filled
                      dark
                      label="Menu Description"
                      v-model="menu_description"
                      :error-messages="menuDescriptionErrors"
                      @input="$v.menu_description.$touch()" 
                      @blur="$v.menu_description.$touch()"
                    ></v-textarea>

                    <v-text-field
                      label="Menu Price"
                      single-line
                      filled
                      dark
                      v-model="menu_price"
                      :error-messages="menuPriceErrors"
                      @input="$v.menu_price.$touch()" 
                      @blur="$v.menu_price.$touch()"
                    ></v-text-field>

                    <v-select
                      :items="menu_activate"
                      label="Set Menu To Be Active?"
                      filled
                      dark
                      v-model="menu_availability"
                      :error-messages="menuAvailabilityErrors"
                      @change="$v.menu_availability.$touch()" 
                      @blur="$v.menu_availability.$touch()"
                    ></v-select>

                    <v-file-input 
                      prepend-icon="mdi-camera" 
                      filled 
                      dark
                      accept="image/*" 
                      label="Menu Picture"
                      v-model="menu_picture"
                      :error-messages="menuPictureErrors"
                      @change="$v.menu_picture.$touch()" 
                      @blur="$v.menu_picture.$touch()"
                      ></v-file-input>

                      <div class="white--text text-caption mb-1">Optional</div>

                      <v-text-field
                      label="Set Initial Stock Quantity"
                      single-line
                      filled
                      dark
                      v-model="menu_stock_qty"
                      :error-messages="menuStockQtyErrors"
                      @input="$v.menu_stock_qty.$touch()" 
                      @blur="$v.menu_stock_qty.$touch()"
                    ></v-text-field>
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
      Menu has been created successfully.
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
        menu_activate: [
          'Yes',
          'No'
        ],

        // Form
        menu_name: null,
        menu_description: null,
        menu_price: null,
        menu_availability: null,
        menu_picture: null,
        menu_stock_qty: null,
        image: null,

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
      menu_name: {
        required,
      },
      menu_description: {
        required,
      },
      menu_price: {
        required, 
        numeric
      },
      menu_availability: { 
        required 
      },
      menu_picture: {
        required
      },
      menu_stock_qty: {
        numeric
      },
    }, // end of validations

    computed: {
      menuNameErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.menu_name.$dirty) return errors
        !currentObj.$v.menu_name.required && errors.push('Menu Name is required.')
        return errors
      },
      menuDescriptionErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.menu_description.$dirty) return errors
        !currentObj.$v.menu_description.required && errors.push('Menu description is required.')
        return errors
      },
      menuPriceErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.menu_price.$dirty) return errors
        !currentObj.$v.menu_price.required && errors.push('Menu Price is required.')
        !currentObj.$v.menu_price.numeric && errors.push('Menu Price must an numeric.')
        return errors
      },
      menuAvailabilityErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.menu_availability.$dirty) return errors
        !currentObj.$v.menu_availability.required && errors.push('Menu availability is required.')
        return errors
      },
      menuPictureErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.menu_picture.$dirty) return errors
        !currentObj.$v.menu_picture.required && errors.push('Menu picture is required.')
        return errors
      },
      menuStockQtyErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.menu_stock_qty.$dirty) return errors
        !currentObj.$v.menu_stock_qty.numeric && errors.push('Menu Stock Quantity must be an numeric.')
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

          let formData = new FormData();

          // files
          formData.append("menu_picture", currentObj.menu_picture)

          // additional data
          formData.append("menu_name", currentObj.menu_name)
          formData.append("menu_description", currentObj.menu_description)
          formData.append("menu_price", currentObj.menu_price)
          formData.append("menu_availability", currentObj.menu_availability)

          // check if stock qty true or not null
          if (currentObj.menu_stock_qty) {
            // then, add it to context
            formData.append("menu_stock_qty", currentObj.menu_stock_qty)
          }

          axios.post('api/menu/create', formData)
            .then(function (response) {

              // after success show successSnackbar
              currentObj.successSnackbar = true

              currentObj.overlay = false

              currentObj.$v.$reset()
              
              currentObj.$router.push('/home/menu/list')

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