<template>
  <v-app id="inspire">
    <v-main>
      <v-container
        class="fill-height"
        fluid
      >
        <v-row
          align="center"
          justify="center"
        >
          <v-col
            cols="12"
            sm="8"
            md="4"
          >
            <v-card class="elevation-12">
              <v-toolbar
                color="primary"
                dark
                flat
              >
                <v-toolbar-title>Login form</v-toolbar-title>
                <v-spacer></v-spacer>
                
              </v-toolbar>
              
              <v-card-text>
              <v-progress-linear
                      :active="loading"
                      :indeterminate="loading"
                      absolute
                      bottom
                      color="red accent-4"
                    ></v-progress-linear>
                    

                <v-form>
                  <v-text-field
                    label="Login"
                    name="login"
                    v-model="email"
                    prepend-icon="mdi-account"
                    type="email"
                  ></v-text-field>

                  <v-text-field
                    id="password"
                    label="Password"
                    v-model="password"
                    name="password"
                    prepend-icon="mdi-lock"
                    type="password"
                  ></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="login">Login</v-btn>
              </v-card-actions>
            </v-card>
            <v-snackbar
                  v-model="snackbar"
                >
                  {{ texts }}

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
	    data(){
	    	return{
	    		email:'',
	    		password:'',
	    		loading:false,
	    		snackbar:false,
	    		texts:'',
	    	}
	    },
	    methods:{
	    	login:function(){
	    		// Add a request interceptor
	    		axios.interceptors.request.use((config) => {
	    		    this.loading=true;
	    		    return config;
	    		  }, (error) => {
	    		    this.loading=false;
	    		    return Promise.reject(error);
	    		  });

	    		// Add a response interceptor
	    		axios.interceptors.response.use((response) => {
	    		    this.loading=false;
	    		    return response;
	    		  }, (error) => {
	    		    this.loading=false;
	    		    // Do something with response error
	    		    return Promise.reject(error);
	    		  });
	    		   
	    		axios.post('/api/login', {
	    		    email:this.email,
	    		    password:this.password
	    		  })
	    		  .then((response) => {
	    		  
	    		    localStorage.setItem('token',response.data.token);
	    		    this.$router.push('/admin').then(res=> console.dir('Logged In Successfully')).catch(err=>console.dir(err))
	    		  })
	    		  .catch((error) => {
	    		  //console.dir(error);
	    		    this.texts = error.response.data.status;
	    		    this.snackbar=true;
	    		  });
	    	}
	    }
	  }
</script>
<style scoped></style>