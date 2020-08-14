<template>
    <div class="ma-10">

    <v-row justify="start" class="mx-auto">
      <v-col
        cols="12"
        
      >
        <v-form @submit.prevent="edit">
          <v-card
            class="mx-auto"
            color="deep-orange lighten-1"
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
              <span color="white" class="title white--text">Edit Your Identity</span>
            </v-card-title>
            
              <v-card-text class="headline font-weight-bold">
                <v-row>
                  <v-col cols="12">
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
                      label="First Name"
                      hint="First Name"
                      persistent-hint
                      single-line
                      filled
                      dark
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
                      dark
                      required
                      :error-messages="lastNameErrors"
                      v-model="last_name"
                      @input="$v.last_name.$touch()" 
                      @blur="$v.last_name.$touch()"
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
                    <v-btn type="submit" >Edit</v-btn>
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

    <v-snackbar top v-model="snack" :timeout="3000" :color="snackColor">
      {{ snackText }}

      <template v-slot:action="{ attrs }">
        <v-btn v-bind="attrs" text @click="snack = false">Close</v-btn>
      </template>
    </v-snackbar>

  </div>
</template>

<script>
    import { required } from 'vuelidate/lib/validators'
    import axios from 'axios'
    export default {
        data () {
            return {
                // v-model
                first_name: null,
                last_name: null,

                // For Form Exception
                errorAlert: false,
                serverError: null,

                // Loading Overlay - Form
                overlay: false,
                
                // Response
                snack: false,
                snackText: null,
                snackColor: null,
            }
        }, // End of Data

        validations: {
            first_name: {
                required,
            },
            last_name: {
                required,
            },
        }, // End of Validations

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
        }, // End of Computed

        methods: {  
            edit: function() {
                let currentObj = this
                
                currentObj.overlay = true

                if (currentObj.$v.$invalid) {
                    currentObj.snack = true
                    currentObj.snackColor = 'error'
                    currentObj.snackText = 'Input Data Invalid'
                    currentObj.overlay = false
                } else {
                    axios.post('api/auth/profile/identity/edit', {
                        first_name: currentObj.first_name,
                        last_name: currentObj.last_name,
                    })
                    .then(function (response) {
                        
                        currentObj.snack = true
                        currentObj.snackColor = 'success'
                        currentObj.snackText = 'Your Identity Has Been Succesfully Changed'

                        currentObj.overlay = false

                    })
                    .catch(function (error) {
                        currentObj.overlay = false
                        if(error.response) {
                            currentObj.serverError = error.response.data.errors
                            currentObj.errorAlert = true
                        }
                    })
                }
            }, // End of Edit Method

            getIdentity: function() {
                let currentObj = this
                
                currentObj.overlay = true

                axios.get('api/auth/me')
                .then(function (response) {

                    currentObj.first_name = response.data.user.first_name
                    currentObj.last_name = response.data.user.last_name

                    currentObj.overlay = false

                })
                .catch(function (error) {
                    currentObj.overlay = false
                    if(error.response) {
                        currentObj.serverError = error.response.data.errors
                        currentObj.errorAlert = true
                 
                        currentObj.snack = true
                        currentObj.snackColor = 'error'
                        currentObj.snackText = 'Cannot Get Old Identity'
                    }
                })
            }
        }, // End of Methods

        mounted: function() {
            let currentObj = this
            currentObj.getIdentity()
        }, // End of Mounted
    }
</script>