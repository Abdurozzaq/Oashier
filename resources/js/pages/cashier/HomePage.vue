<template>
  <div class="ma-8">

    <v-row justify="start">
      <v-col cols="auto">
        <div class="text-h5 font-weight-black blue-grey--text text--lighten-1">Restoran Kamu :</div>
      </v-col>
    </v-row>


    <v-row justify="start" class="mx-auto">
      <v-col
        cols="auto"
        
      >
        <v-card
          class=" mx-auto"
          color="#26c6da"
          dark
          elevation="8"
        >
          <v-card-title>
            <v-icon
              large
              left
            >
              mdi-food-fork-drink
            </v-icon>
            <span class="title font-weight-light">Warung Sari Rasa</span>
          </v-card-title>

          <v-card-text class="headline font-weight-bold">
            lorem ipsum dolor ini deskripsi bro. lorem ipsum dolor ini deskripsi bro.
            lorem ipsum dolor ini deskripsi bro.lorem ipsum dolor ini deskripsi bro.
          </v-card-text>

          <v-card-actions>
            <v-list-item class="grow">
              <v-list-item-avatar>
                <v-icon
                  large
                >
                  mdi-account-circle-outline
                </v-icon>
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title>Sari Wardani</v-list-item-title>
              </v-list-item-content>

              <v-row
                align="center"
                justify="end"
              >
                <v-icon class="mr-1">mdi-dashboard</v-icon>
                <v-btn text>Go To Dash</v-btn>
              </v-row>
            </v-list-item>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- BUTTON UNTUK TRIGGER DIALOG ADD RESTORAN -->
    <v-btn @click="dialog = true" fab large color="primary" fixed right bottom>
        <v-icon>mdi-plus</v-icon>
    </v-btn>


    <!-- DIALOG ADD RESTORAN -->
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">Tambah Restoran</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <div class="text-caption">Required</div>
              <v-col cols="12">
                <v-text-field solo label="Nama Restoran*" type="text" required></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field solo label="Deskripsi Restoran*" type="text" required></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field solo label="Alamat Restoran*" type="text" required></v-text-field>
              </v-col>
              <div class="text-caption">Optional</div>
              <v-col cols="12">
                <v-text-field solo label="Midtrans Merchant Id" type="text"></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field solo label="Midtrans Client Key" type="text"></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field solo label="Midtrans Server Key" type="text"></v-text-field>
              </v-col>
            </v-row>
          </v-container>
          <small>*Harus di isi</small>
          <br>
          <small>Form Midtrans digunakan untuk pembayaran melalui Midtrans</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="dialog = false">Close</v-btn>
          <v-btn color="blue darken-1" text @click="dialog = false">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
  export default {
    name: 'HomePage',
    data() {
      return {
        dialog: false,
      }
    }
  }
</script>