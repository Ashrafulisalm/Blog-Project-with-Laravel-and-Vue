<template>
  <v-app id="inspire">
  	<v-data-table 
  	:headers="headers"
      :items="users.data"
      :items-per-page="5"
      :footer-props="{
        itemsPerPageOptions:[5,10,15],
        itemsPerPageText:'users per Page',
        'show-current-page':true,
        'show-first-last-page':true
      }"
      
      show-select
      @input="selectData"
      :server-items-length="users.total"
      @pagination="paginate"
      item-key="name" 
      class="elevation-1" 
      :loading="loading" 
      loading-text="Loading... Please wait">
        

  		<template v-slot:top>
  		  <v-toolbar flat color="green">
  		    <v-toolbar-title>users Detail</v-toolbar-title>
  		    <v-divider
  		      class="mx-4"
  		      inset
  		      vertical
  		    ></v-divider>
  		    <v-spacer></v-spacer>
  		    <v-dialog v-model="dialog" max-width="500px">
  		      <template v-slot:activator="{ on, attrs }">
              <v-btn
                color="error"
                dark
                class="mb-2"
                v-bind="attrs"
                @click="deleteMultiple"
              >Delete Selected</v-btn>
  		        <v-btn
  		          color="primary"
  		          dark
  		          class="mb-2"
  		          v-bind="attrs"
  		          v-on="on"
  		        >Add User</v-btn>
  		      </template>
  		      <v-card>
  		        <v-card-title>
  		          <span class="headline">{{ formTitle }}</span>
  		        </v-card-title>
    		        <v-card-text>
    		          <v-container>
    		            <v-row>
    		              <v-col cols="12">
    		                <v-text-field v-model="editedItem.name" label="User Name" :rules="[rules.required,rules.min]"></v-text-field>
    		              </v-col>
                      <v-col cols="12">
                        <v-select :items="roles" v-model="editedItem.role" label="User Role" :rules="[rules.required]" ></v-select>
                      </v-col>
                      <v-col cols="12">
                        <v-text-field v-model="editedItem.email" label="User Email" :rules="[rules.required, rules.validEmail]"></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field append-icon="mdi-eye" v-model="editedItem.password" type="password" label="User Password" :rules="[rules.required]"></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field  append-icon="mdi-eye" v-model="rpassword" type="password" label="Confirm Password" :rules="[rules.required, passwordMatch]"></v-text-field>
                      </v-col>
    		              
    		            </v-row>
    		          </v-container>
    		        </v-card-text>

    		        <v-card-actions>
    		          <v-spacer></v-spacer>
    		          <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
    		          <v-btn color="blue darken-1" text @click.prevent="save">Save</v-btn>
    		        </v-card-actions>
  		      </v-card>
  		    </v-dialog>
  		  </v-toolbar>
        <v-text-field
          append-icon="mdi-magnify"
          label="Search"
          @input="searchIt"
          class="mx-12"                
        ></v-text-field>
  		</template>
  		<template v-slot:item.image="{item}">
        <v-img :src="item.image" aspect-ratio="1.7"></v-img>
      </template>  
  		<template v-slot:item.actions="{ item }">
  		  <v-icon
  		    small
  		    class="mr-2"
  		    @click="editItem(item)"
  		  >
  		    mdi-pencil
  		  </v-icon>
  		  <v-icon
  		    small
  		    @click="deleteItem(item)"
  		  >
  		    mdi-delete
  		  </v-icon>
  		</template>
  		<template v-slot:no-data>
  		  <v-btn color="primary" @click="initialize">Reset</v-btn>
  		</template>
  	</v-data-table>
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
   </v-app>
</template>

<script>
  export default {
    data: () => ({
      dialog: false,
      loading : false,
      snackbar: false,
      texts:'',
      selected:[],
      rpassword:'',
      headers: [
        {
          text: 'Id',
          align: 'start',
          sortable: false,
          value: 'id',
        },
        { text: 'Name', value: 'name' },
        { text: 'Email', value: 'email' },
        { text: 'Role', value: 'role' },
        { text: 'Image', value: 'image' },
        { text: 'Created At', value: 'created_at' },
        { text: 'Updated At', value: 'updated_at' },
        
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      users: [],
      roles:[],
      rules:{
        required: v => !!v || 'This field is required',
        min: v => v.length>=3 || 'Name at least 3 Character',
        validEmail: v => /.+@.+\..+/.test(v) || 'Must enter a valid Email'
      },
      editedIndex: -1,
      editedItem: {
        id:'',
        name: '',
        email:'',
        role:'',
        image:'',
        password:'',
        created_at:'',
        updated_at:'',
       }, 
      defaultItem: {
        id:'',
        name: '',
        email:'',
        role:'',
        image:'',
        password:'',
        created_at:'',
        updated_at:'',
        
      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New User' : 'Edit User'
      },
      passwordMatch(){
        return this.editedItem.password != this.rpassword?'Password Does not Match':''
      }
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
    },

    created () {
      this.initialize()
    },

    methods: {
      
      initialize () {
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
      	    	    		    	    		
      },

      paginate(event){
        axios.get('/api/users?page='+(event.page), {params:{'per_page':event.itemsPerPage}})
            .then(res => {
              this.users=res.data.users
              this.roles=res.data.roles
              })
            .catch(err => {
              if(err.response.status==401){
                localStorage.removeItem('token');
                this.$router.push('/login');
              }
            })
      },

      searchIt(e){
        if(e.length>3){
          axios.get(`/api/users/${e}`)
            .then(res=> this.users.data = res.data.user)
            .catch(err=>console.dir(err.response))
        } 
        if(e.length<=0){
          axios.get('/api/users')
            .then(res=>this.users.data=res.data.user)
            .catch(err=>console.dir(err.response))

        }
      },

      selectData(e){
        this.selected=[];
        if(e.length>0){
          this.selected=e.map(val=>val.id)
        }
      },

      deleteMultiple(){
          let decide=confirm('Are you sure you want to delete this item?');
          if(decide){ 
            axios.post('/api/users/delete',{'user_id':this.selected})
            .then(res=> {              
              this.selected.map(val=>{
              const index = this.users.data.indexOf(val)
              this.users.data.splice(index, 1)
              this.texts="User Deleted Successfully"
              this.snackbar=true
              })
          }).catch(err=>{
            this.texts="User can't be deleted"
              this.snackbar=true
          }
          )
        }
      },

      editItem (item) {
        this.editedIndex = this.users.data.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.users.data.indexOf(item)
        let decide=confirm('Are you sure you want to delete this item?');
        if(decide){ 
          axios.delete('/api/users/'+item.id)
          .then(res=> {
            this.users.data.splice(index, 1)
            this.texts="User Deleted Successfully"
            this.snackbar=true
          }).catch(err=>console.log(err.response))
        }
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        if (this.editedIndex > -1) {
          //Object.assign(this.desserts[this.editedIndex], this.editedItem)

          const indexs=this.editedIndex;
          axios.put('/api/users/'+ this.editedItem.id , {'name':this.editedItem.name})
          .then(res => {
            Object.assign(this.users.data[indexs], res.data.user)
            this.texts="User Updated Successfully"
            this.snackbar=true})
          .catch(err=> console.log(err.response))
          console.log(this.editedItem);
          
        } else {
          axios.post('/api/users',this.editedItem)
          .then(res=> {
            this.users.data.push(res.data.user)
            this.texts="User Created Successfully"
            this.snackbar=true
          })
          .catch(err=>console.dir(err.response))

        }
        this.close()
      },
    },
  }
</script>

<style scoped></stUser