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
      Your Cashier Users List
        <v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Search"
          hint="Search By User Name, Email And Restaurant Name"
          persistent-hint
          single-line
          append-outer-icon="mdi-send"
          @click:append-outer="searchOrder"
          class="mb-3"
        ></v-text-field>
      </v-card-title>

      <v-data-table
        v-if="usersList && usersListFiltered == null"
        :headers="headers"
        :items="usersList"
        :items-per-page="5"
        class="elevation-1"
      >

        <template v-slot:[`item.created_at`]="{ item }">
          <span>{{ new Date(item.created_at).toLocaleString() }}</span>
        </template>

        <template v-slot:[`item.action`]="props">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="ma-2" fab dark small color="green" @click.prevent="openEditDetails(props.item)">
                <v-icon dark>mdi-account-edit</v-icon>
              </v-btn>
            </template>
            <span>Edit User</span>
          </v-tooltip>

          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="ma-2" fab dark small color="red" @click.prevent="deleteUser(props.item)">
                <v-icon dark>mdi-account-remove</v-icon>
              </v-btn>
            </template>
            <span>Delete User</span>
          </v-tooltip>

        </template>

      </v-data-table>

      <v-data-table
        v-if="usersList && usersListFiltered != null"
        :headers="headers"
        :items="usersListFiltered"
        :items-per-page="5"
        class="elevation-1"
      >

        <template v-slot:[`item.created_at`]="{ item }">
          <span>{{ new Date(item.created_at).toLocaleString() }}</span>
        </template>

        <template v-slot:[`item.action`]="props">

          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="ma-2" fab dark small color="green" @click.prevent="openEditDetails(props.item)">
                <v-icon dark>mdi-account-edit</v-icon>
              </v-btn>
            </template>
            <span>Edit User</span>
          </v-tooltip>

          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="ma-2" fab dark small color="red" @click.prevent="deleteUser(props.item)">
                <v-icon dark>mdi-account-remove</v-icon>
              </v-btn>
            </template>
            <span>Delete User</span>
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
      v-model="cashierDetailsDialog"
      width="500"
    >
      <v-form @submit.prevent="editCashier()">
        <v-alert
          v-model="editErrorAlert"
          border="top"
          color="red lighten-2"
          dark
          dismissible
        >
          <ul v-for="(error, index) in editServerError" v-bind:key="index">
            <li>
              {{ error[0] }}
            </li>
          </ul>
        </v-alert>

        <v-card>
          <v-card-title class="headline grey lighten-2">
            Edit Cashier Details
          </v-card-title>

          <v-card-text>
            <v-text-field
              class="mt-3"
              label="First Name"
              hint="First Name"
              persistent-hint
              filled
              v-model="edit_first_name"
              :error-messages="editFirstNameErrors"
              @input="$v.edit_first_name.$touch()" 
              @blur="$v.edit_first_name.$touch()"
            ></v-text-field>
            <v-text-field
              label="Last Name"
              hint="Last Name"
              persistent-hint
              filled
              v-model="edit_last_name"
              :error-messages="editLastNameErrors"
              @input="$v.edit_last_name.$touch()" 
              @blur="$v.edit_last_name.$touch()"
            ></v-text-field>
            <v-text-field
              label="Restaurant Name"
              hint="Restaurant Name"
              persistent-hint
              filled
              v-model="edit_restaurant_name"
            ></v-text-field>
            <v-text-field
              label="Address"
              hint="Address"
              persistent-hint
              filled
              v-model="edit_address"
            ></v-text-field>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="red"
              text
              @click.prevent="closeAndResetVar()"
            >
              CANCEL
            </v-btn>
            <v-btn
              color="success"
              text
              type="submit"
              @click.prevent="editCashier()"
            >
              EDIT
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
      <v-overlay
        :absolute="true"
        :value="overlayEditCashier"
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
  import { required, email } from 'vuelidate/lib/validators'
  import axios from 'axios'
  import moment from 'moment';
  export default {
    data() {
      return {

        // User List
        cashierDetailsDialog: false,
        overlayEditCashier: false,
        total: null,

        // Form
        edit_id: null,
        edit_first_name: null,
        edit_last_name: null,
        edit_restaurant_name: null,
        edit_address: null,

        // Form Response
        editErrorAlert: false,
        editServerError: null,

        serverError: null,
        errorAlert: false,

        snack: false,
        snackColor: null,
        snackText: null,

        // Data Table
        search: null,
        
        usersList: null,
        usersListFiltered: null,
        overlay: false,
        headers: [
          {
            text: 'First Name',
            align: 'start',
            sortable: true,
            value: 'first_name',
          },
          {
            text: 'Last Name',
            align: 'start',
            sortable: true,
            value: 'last_name',
          },
          {
            text: 'Restaurant Name',
            align: 'start',
            sortable: true,
            value: 'restaurant_name',
          },
          {
            text: 'Address',
            align: 'start',
            sortable: true,
            value: 'address',
          },
          {
            text: 'Email',
            align: 'start',
            sortable: true,
            value: 'email',
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
      edit_first_name: {
        required,
      },
      edit_last_name: {
        required,
      },
    }, // end of validations

    computed: {
      editFirstNameErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.edit_first_name.$dirty) return errors
        !currentObj.$v.edit_first_name.required && errors.push('First Name is required.')
        return errors
      },

      editLastNameErrors () {
        let currentObj = this
        const errors = []
        if (!currentObj.$v.edit_last_name.$dirty) return errors
        !currentObj.$v.edit_last_name.required && errors.push('Last Name is required.')
        return errors
      },
    }, // End of Computed

    methods: {
      getData: function() {
        let currentObj = this

        currentObj.overlay = true
        axios.get('api/siAdmino/users/cashier/list')
          .then(function (response) {

            currentObj.usersList = response.data.data
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
          currentObj.usersListFiltered = currentObj.usersList.filter(
            cashier => 
            cashier.first_name.toLowerCase().includes(currentObj.search.toLowerCase()) 
            ||
            cashier.last_name.toLowerCase().includes(currentObj.search.toLowerCase())
            ||
            cashier.restaurant_name.toLowerCase().includes(currentObj.search.toLowerCase())
            ||
            cashier.email.toLowerCase().includes(currentObj.search.toLowerCase())
          )

        } else {
          currentObj.usersListFiltered = null
        }

      },

      openEditDetails: function(item) {
        let currentObj = this
        currentObj.cashierDetailsDialog = true

        currentObj.edit_id = item.id
        currentObj.edit_first_name = item.first_name
        currentObj.edit_last_name = item.last_name
        currentObj.edit_restaurant_name = item.restaurant_name
        currentObj.edit_address = item.address
      },

      closeAndResetVar: function () {
        let currentObj = this

        currentObj.cashierDetailsDialog = false
      },

      editCashier: function () {
        let currentObj = this

        currentObj.overlayEditCashier = true
        if (currentObj.$v.$invalid) {
          currentObj.snack = true
          currentObj.snackColor = 'error'
          currentObj.snackText = 'Please Enter The Valid Data'
        } else {
          axios.post('api/siAdmino/users/any-role/edit/' + currentObj.edit_id, {
            first_name: currentObj.edit_first_name,
            last_name: currentObj.edit_last_name,
            restaurant_name: currentObj.edit_restaurant_name,
            address: currentObj.edit_address,
          })
            .then(function (response) {
              currentObj.overlayEditCashier = false
              currentObj.snack = true
              currentObj.snackColor = 'success'
              currentObj.snackText = 'Cashier details has been successfully Edited'
              currentObj.closeAndResetVar()
              currentObj.getData()
              currentObj.usersListFiltered = null
              currentObj.search = null
            })
            .catch(function (error) {
              if(error.response) {
                currentObj.editServerError = error.response.data.errors
                currentObj.editErrorAlert = true
              }
              currentObj.overlayEditCashier = false
            })
        }
      },

      deleteUser: function (item) {
        let currentObj = this
        currentObj.overlay = true

        axios.post('api/siAdmino/users/any-role/delete/' + item.id)
          .then(function (response) {
            currentObj.overlay = false
            currentObj.snack = true
            currentObj.snackColor = 'success'
            currentObj.snackText = 'Cashier has been successfully deleted'
            currentObj.getData()
            currentObj.usersListFiltered = null
            currentObj.search = null
          })
          .catch(function (error) {
            if(error.response) {
              currentObj.serverError = error.response.data.errors
              currentObj.errorAlert = true
            }
            currentObj.overlay = false
          })
      }
    }, // End of Methods

    

    mounted: function() {
      let currentObj = this

      currentObj.getData()
    }
  }
</script>
