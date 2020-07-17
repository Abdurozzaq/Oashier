<template>
  <div class="ma-10">

    <v-row justify="start" class="mx-auto">
      <v-col
        cols="12"
        
      >
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
            <span color="white" class="title white--text">Create Menu</span>
          </v-card-title>

          <v-card-text class="headline font-weight-bold">
            <v-row>
              
              <v-col cols="12" sm="12">
                <v-text-field
                  label="Menu Name"
                  single-line
                  solo
                ></v-text-field>

                <v-textarea
                  solo
                  label="Menu Description"
                ></v-textarea>

                <v-text-field
                  label="Menu Price"
                  single-line
                  solo
                ></v-text-field>

                <v-select
                  :items="menu_activate"
                  label="Set Menu To Be Active?"
                  solo
                ></v-select>

                <v-file-input prepend-icon="mdi-camera" solo accept="image/*" label="Menu Picture"></v-file-input>
              </v-col>

            </v-row>
          </v-card-text>

          <v-card-actions>
            <v-list-item class="grow">
              <v-row
                align="center"
                justify="end"
              >
                <v-icon color="white" class="mr-1">mdi-dashboard</v-icon>
                <v-btn color="white" text>Go To Dash</v-btn>
              </v-row>
            </v-list-item>
          </v-card-actions>
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
        menu_activate: [
          'Yes',
          'No'
        ],
      }
    }
  }
</script>