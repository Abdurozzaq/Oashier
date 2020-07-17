<template>
  <v-app id="inspire">
    <v-navigation-drawer
      dark
      src="https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg"
      v-model="drawer"
      :clipped="$vuetify.breakpoint.lgAndUp"
      app
      elevation="4"
    >

      <template v-slot:prepend>
        <v-list-item two-line>
          <v-list-item-avatar>
            <img src="https://randomuser.me/api/portraits/women/81.jpg">
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title>Sari Wardani</v-list-item-title>
            <v-list-item-subtitle>Logged In</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>

      <v-list>
        <v-list-item
          link
        >
          <v-list-item-icon>
            <v-icon>mdi-view-dashboard-outline</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Home Page</v-list-item-title>
        </v-list-item>

        <v-list-group
          prepend-icon="mdi-note-text-outline"
          value="true"
        >
          <template v-slot:activator>
            <v-list-item-title>Order</v-list-item-title>
          </template>

          <v-list-item
            link
          >
            <v-list-item-title>Create Order</v-list-item-title>
            <v-list-item-icon>
              <v-icon>mdi-note-plus-outline</v-icon>
            </v-list-item-icon>
          </v-list-item>

          <v-list-item
            link
          >
            <v-list-item-title>Order List</v-list-item-title>
            <v-list-item-icon>
              <v-icon>mdi-note-text-outline</v-icon>
            </v-list-item-icon>
          </v-list-item>
        </v-list-group>

        <v-list-group
          prepend-icon="mdi-food"
          value="true"
        >
          <template v-slot:activator>
            <v-list-item-title>Menu</v-list-item-title>
          </template>

          <v-list-item
            link
            href="/home/menu/create"
          >
            <v-list-item-title>Create Menu</v-list-item-title>
            <v-list-item-icon>
              <v-icon>mdi-plus-circle-outline</v-icon>
            </v-list-item-icon>
          </v-list-item>

          <v-list-item
            link
          >
            <v-list-item-title>Menu List</v-list-item-title>
            <v-list-item-icon>
              <v-icon>mdi-format-list-bulleted-type</v-icon>
            </v-list-item-icon>
          </v-list-item>
        </v-list-group>

        <v-list-item
          link
        >
          <v-list-item-icon>
            <v-icon>mdi-food</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Menu Stock</v-list-item-title>
        </v-list-item>
        
      </v-list>
    </v-navigation-drawer>

    <v-app-bar
      :clipped-left="$vuetify.breakpoint.lgAndUp"
      app
      color="blue darken-3"
      dark
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-toolbar-title
        style="width: 300px"
        class="ml-0 pl-4"
      >
        <span class="hidden-sm-and-down">OASHIER</span>
      </v-toolbar-title>
      <v-text-field
        flat
        solo-inverted
        hide-details
        prepend-inner-icon="mdi-magnify"
        label="Search"
        class="hidden-sm-and-down"
      ></v-text-field>
      <v-spacer></v-spacer>
      <v-btn icon>
        <v-icon>mdi-apps</v-icon>
      </v-btn>
      <v-btn icon>
        <v-icon>mdi-bell</v-icon>
      </v-btn>
      

      <v-menu
        v-model="menu"
        :close-on-content-click="false"
        :nudge-width="200"
        offset-x
      >
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            large
            v-on="on"
          >
            <v-avatar
              size="32px"
              item
            >
              <v-img
                src="https://cdn.vuetifyjs.com/images/logos/logo.svg"
                alt="Vuetify"
              ></v-img></v-avatar>
          </v-btn>
        </template>

        <v-card>
          <v-list>
            <v-list-item>
              <v-list-item-avatar>
                <img src="https://cdn.vuetifyjs.com/images/john.jpg" alt="John">
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title>Sari Wardani</v-list-item-title>
                <v-list-item-subtitle>Founder of Warung Sari Rasa</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <v-divider></v-divider>

          <v-list shaped dense>
            <v-subheader>Account</v-subheader>
            <v-list-item-group v-model="itemPopup" color="primary">
              <v-list-item
                v-for="(ip, i) in itemsPopup"
                :key="i"
                :href="ip.url"
              >
                <v-list-item-icon>
                  <v-icon v-text="ip.icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title v-text="ip.text"></v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
                 

          <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn text @click="menu = false">Close</v-btn>
            <v-btn text @click="logout" color="danger">Logout</v-btn>
          </v-card-actions>
        </v-card>
      </v-menu>
    </v-app-bar>
    <v-main>
      <v-container
        fluid
      >
        <router-view></router-view>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
  import axios from 'axios'
  export default {
    props: {
      source: String,
    },
    data: () => ({
      fav: true,
      menu: false,
      message: false,
      hints: true,
      dialog: false,
      drawer: true,

      itemPopup: 1,
      itemsPopup: [
        { text: 'Home', icon: 'mdi-view-dashboard', url: '/home' },
        { text: 'Settings', icon: 'mdi-account', url: '/settings' },
      ],

      items: [
        ['mdi-view-dashboard', 'Home', '/home'],
        ['mdi-account', 'Settings', '/settings'],
      ],

      sidebar: [
        { icon: 'mdi-view-dashboard', text: 'Home', link: '/' },
      ],
    }), // end of data

    methods: {
      logout: function() {
        let currentObj = this
          axios.post('/api/auth/logout')
          .then(function (response) {
            localStorage.removeItem('userToken')
            currentObj.$router.push('/login')
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }
  }
</script>