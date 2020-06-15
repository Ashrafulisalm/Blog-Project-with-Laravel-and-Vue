<template>
  <v-app id="inspire">
    <v-navigation-drawer
      v-model="drawer"
      app
      clipped
    >
      <v-list dense>


        <v-list-item
          v-for="item in items"
          :key="item.text"
          link
          :to="item.action"
        >
          <v-list-item-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>
              {{ item.text }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-subheader class="mt-4 grey--text text--darken-1">SUBSCRIPTIONS</v-subheader>
        <v-list>
          <v-list-item
            v-for="item in items2"
            :key="item.text"
            link
          >
            <v-list-item-avatar>
              <img
                :src="`https://randomuser.me/api/portraits/men/${item.picture}.jpg`"
                alt=""
              >
            </v-list-item-avatar>
            <v-list-item-title v-text="item.text"></v-list-item-title>
          </v-list-item>
        </v-list>
        <v-list-item
          class="mt-4"
          link
        >
          <v-list-item-action>
            <v-icon color="grey darken-1">mdi-plus-circle-outline</v-icon>
          </v-list-item-action>
          <v-list-item-title class="grey--text text--darken-1">Browse Channels</v-list-item-title>
        </v-list-item>
        <v-list-item link @click="logout">
          <v-list-item-action>
            <v-icon color="grey darken-1">mdi-cog</v-icon>
          </v-list-item-action>
          <v-list-item-title class="grey--text text--darken-1" >Log Out</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar
      app
      clipped-left
      color="red"
      dense
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-icon
        class="mx-4"
        large
      >
        mdi-office-building
      </v-icon>
      <v-toolbar-title class="mr-12 align-center">
        <span class="title">Admin Dashboard</span>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-row
        align="center"
        style="max-width: 650px"
      >
        <v-text-field
          :append-icon-cb="() => {}"
          placeholder="Search..."
          single-line
          append-icon="mdi-magnify"
          color="white"
          hide-details
        ></v-text-field>
      </v-row>
    </v-app-bar>

    <v-main>
      <v-container class="">
        <v-row
          justify="center"
          align="center"
        >
          <v-col>
            <router-view></router-view>

                    <v-snackbar
              v-model="snackbar"
            >
              {{ text }}

              <template v-slot:action="{ attrs }">
                <v-btn
                  color="pink"
                  text
                  v-bind="attrs"
                  @click="snackbar = false"
                >
                  Close
                </v-btn>
              </template>
            </v-snackbar>
            
          </v-col>
          
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>
<script>
	export default {
	    props: {
	      source: String,
        
	    },
	    data: () => ({
	      drawer: null,
        snackbar:false,
        text:'',
	      items: [
	        { icon: 'mdi-account-circle', text: 'User' , action: "/admin" },
	        { icon: 'mdi-post-outline', text: 'Post', action: "posts" },
	        { icon: 'mdi-circle-edit-outline', text: 'Pages', action: "pages" },
	        { icon: 'mdi-briefcase-edit-outline', text: 'catagories', action: "catagories" },
	        { icon: 'mdi-account-tie', text: 'Roles' , action: "/admin/roles"},
	      ],
	      items2: [
	        { picture: 28, text: 'Joseph' },
	        { picture: 38, text: 'Apple' },
	        { picture: 48, text: 'Xbox Ahoy' },
	        { picture: 58, text: 'Nokia' },
	        { picture: 78, text: 'MKBHD' },
	      ],
	    }),
	    created () {
	      this.$vuetify.theme.dark = false
        this.text='Logged In';
        this.sanckbar = true
	    },

      methods:{
        logout:function(){
        console.log('Logout');
         localStorage.removeItem('token');
         this.$router.push('/login').then(res=> console.dir('Loged Out In Successfully')).catch(err=>console.dir(err))
        }
      }
  }
</script>
<style scoped></style>