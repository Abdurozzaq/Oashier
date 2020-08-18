<template>
  <div class="ma-10">

    <v-row justify="start" class="mx-auto">
      <v-col
        cols="12"
        
      >
        <v-form @submit.prevent="createCashierUser">
          <v-card
            class=" mx-auto"
            color="red lighten-5"
            elevation="8"
          >
            <v-card-title>
              <v-icon
                large
                left
                color="grey darken-4"
              >
                mdi-food-fork-drink
              </v-icon>
              <span class="title grey--text text--darken-4">Create User</span>
            </v-card-title>
            
              <v-card-text class="headline font-weight-bold">
                <v-row>
                  
                  <v-col cols="12" sm="12">

                    <v-alert
                      v-model="errorAlert"
                      border="top"
                      color="red lighten-2"
                      
                      dismissible
                    >
                      <ul v-for="(error, index) in serverError" v-bind:key="index">
                        <li>
                          {{ error[0] }} 
                        </li>
                      </ul>
                    </v-alert>

                    <v-text-field
                      label="First Name"
                      hint="First Name"
                      persistent-hint
                      single-line
                      filled
                      
                      required
                      :error-messages="firstNameErrors"
                      v-model="first_name"
                      @input="$v.first_name.$touch()" 
                      @blur="$v.first_name.$touch()"
                    ></v-text-field>

                    <v-text-field
                      label="Last Name"
                      hint="Last Name"
                      persistent-hint
                      single-line
                      filled
                      
                      required
                      :error-messages="lastNameErrors"
                      v-model="last_name"
                      @input="$v.last_name.$touch()" 
                      @blur="$v.last_name.$touch()"
                    ></v-text-field>

                    <v-text-field
                      v-if="role == 'cashier'"
                      label="Restaurant Name"
                      hint="Restaurant Name"
                      persistent-hint
                      single-line
                      filled
                      
                      required
                      v-model="restaurant_name"
                    ></v-text-field>

                    <v-text-field
                      label="Email"
                      hint="Email"
                      persistent-hint
                      single-line
                      filled
                      
                      required
                      :error-messages="emailErrors"
                      v-model="email"
                      @input="$v.email.$touch()" 
                      @blur="$v.email.$touch()"
                    ></v-text-field>

                    <v-text-field
                      v-if="role == 'cashier'"
                      label="Address"
                      hint="Address"
                      persistent-hint
                      single-line
                      filled
                      
                      required
                      v-model="address"
                    ></v-text-field>

                    <v-text-field
                      label="Your New Password"
                      single-line
                      filled
                      
                      required
                      type="password"
                      :error-messages="passwordErrors"
                      v-model="password"
                      @input="$v.password.$touch()" 
                      @blur="$v.password.$touch()"
                    ></v-text-field>

                    <v-text-field
                      label="Confirm Your Password"
                      single-line
                      filled
                      
                      required
                      type="password"
                      :error-messages="passwordConfirmationErrors"
                      v-model="password_confirmation"
                      @input="$v.password_confirmation.$touch()" 
                      @blur="$v.password_confirmation.$touch()"
                    ></v-text-field>
                    
                    <v-select
                      :items="roles"
                      v-model="role"
                      filled
                      
                      label="User Role"
                      hint="User Role"
                      persistent-hint
                    ></v-select>

                  </v-col>

                </v-row>
              </v-card-text>

              <v-card-actions>
                <v-list-item class="grow">
                  <v-row
                    align="center"
                    justify="end"
                  >
                    <v-btn color="red darken-1" type="submit" >Create</v-btn>
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
      User has been created successfully.
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
    name: 'createCashierUserPage',
    data() {
      return {
        dialog: false,

        // Form
        first_name: null,
        last_name: null,
        restaurant_name: null,
        email: null,
        address: null,
        password: null,
        password_confirmation: null,
        role: 'cashier',
        roles: [
          'admin',
          'cashier'
        ],

        // TextFields Activate
        restaurant_name_act: true,
        address_act: true,

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
      first_name: {
        required,
      },
      last_name: {
        required,
      },
      email: { 
        required 
      },
      password: {
        required,
      },
      password_confirmation: {
        required,
      },
    }, // end of validations

    computed: {
      firstNameErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.first_name.$dirty) return errors
        !currentObj.$v.first_name.required && errors.push('First Name is required.')
        return errors
      },

      lastNameErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.last_name.$dirty) return errors
        !currentObj.$v.last_name.required && errors.push('Last Name is required.')
        return errors
      },

      emailErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.email.$dirty) return errors
        !currentObj.$v.email.required && errors.push('Email is required.')
        return errors
      },

      passwordErrors () {
          let currentObj = this
          const errors = []
          if (!currentObj.$v.password.$dirty) return errors
          !currentObj.$v.password.required && errors.push('Password is required.')
          return errors
      },
      passwordConfirmationErrors () {
          let currentObj = this
          const errors = []
          if (!currentObj.$v.password_confirmation.$dirty) return errors
          !currentObj.$v.password_confirmation.required && errors.push('Password Confirmation is required.')
          return errors
      },
  
    }, //End of Computed

    methods: {

      createCashierUser: function() {

        let currentObj = this

        currentObj.$v.$touch()

        if (currentObj.$v.$invalid) {
          currentObj.errorSnackbar = true
        } else {
          currentObj.errorAlert = false
          currentObj.overlay = true

          axios.post('api/siAdmino/users/create', {
            first_name: currentObj.first_name,
            last_name: currentObj.last_name,
            restaurant_name: currentObj.restaurant_name,
            email: currentObj.email,
            address: currentObj.address,
            password: currentObj.password,
            password_confirmation: currentObj.password_confirmation,
            role: currentObj.role
          })
            .then(function (response) {

              // after success show successSnackbar
              currentObj.successSnackbar = true

              currentObj.overlay = false

              currentObj.$v.$reset()
              
              if(currentObj.role == 'admin') {
                currentObj.$router.push('/siAdmino/users/admin/list')
              } else {
                currentObj.$router.push('/siAdmino/users/cashier/list')
              }
              

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