<template>
  <div class="ma-8">

    <v-row justify="start" class="mx-auto">
      <v-col
        cols="12"
        
      >
        <v-card
          class="d-flex align-center mx-auto"
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