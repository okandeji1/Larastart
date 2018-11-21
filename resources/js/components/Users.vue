<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users Table</h3>

                <div class="card-tools">
                    <button class="btn btn-outline-success" @click="newModal">Add New<i class="icon fa fa-user-plus fa-fw"></i></button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Registered At</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="user in users.data" :key="user.id">
                    <td>{{user.id}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.type | upText}}</td>
                    <td>{{user.created_at | myDate}}</td>
                    <td>
                        <a href="#" @click="editModal(user)" ><i class="fa fa-edit blue"></i></a>/
                        <a href="#" @click="deleteUser(user.id)"><i class="fa fa-trash red"></i></a>
                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="users" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div v-if="!$gate.isAdminOrAuthor()">
            <not-found></not-found>
        </div>
        <!-- Modal -->
<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addNewLabel" v-show="!editmode">Add New</h5>
        <h5 class="modal-title" id="addNewLabel" v-show="editmode">Update User's Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="editmode ? updateUser() : createUser()">
      <div class="modal-body">
         <div class="form-group">
           <input v-model="form.name" type="text" name="name" placeholder="Name"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
           <has-error :form="form" field="name"></has-error>
         </div>

         <div class="form-group">
           <input v-model="form.email" type="email" name="email" placeholder="Email Adress"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
           <has-error :form="form" field="email"></has-error>
         </div>

         <div class="form-group">
           <input v-model="form.password" type="password" name="password" placeholder="Password"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
           <has-error :form="form" field="password"></has-error>
         </div>

         <div class="form-group">
           <select id="type" v-model="form.type" name="type"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                  <option value="">Select User Role</option>
                  <option value="admin">Admin</option>
                  <option value="user">Standard User</option>
                  <option value="author">Author</option>
            </select>
           <has-error :form="form" field="type"></has-error>
         </div>

         <div class="form-group">
           <textarea id="bio" v-model="form.bio" name="bio" placeholder="Short bio for user (optional)"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
           <has-error :form="form" field="bio"></has-error>
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
        <button v-show="editmode" type="submit" class="btn btn-outline-success">Update</button>
        <button v-show="!editmode" type="submit" class="btn btn-outline-primary">Create</button>
      </div>
    </form>
    </div>
  </div>
</div>
    </div>
</template>

<script>
    export default {

        data(){
            return {
                editmode: false,
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                })
            }
        },

        methods: {
            getResults(page = 1){
                axios.get('api/user?page=' + page)
				.then(response => {
					this.users = response.data;
				});
            },
            updateUser(id){
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('api/user/' + this.form.id)
                .then(()=> {
                    //success
                    $('#addNew').modal('hide');

                 swal(
                         'Updated!',
                         'Information has been updated',
                         'Success'
                            )
                    this.$Progress.finish();
                     Fire.$emit('AfterCreate');
                })
                .catch(()=>{
                    this.$Progress.fail();
                })
            },
            editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
            },

             newModal(user){
                 this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            deleteUser(id){
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'Warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3580d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) =>{
                    // Send request to the server
                 if(result.value){
                    this.form.delete('api/user/' + id).then(()=>{
                            swal(
                                'Deleted!',
                                'Your file has been deleted',
                                'Success'
                            )

                     Fire.$emit('AfterCreate');

                    }).catch(()=>{
                        swal(  'Oops!', 'There was something wrong', 'Warning');
                    })
                 }
                })
            },
            loadUsers(){
                if(this.$gate.isAdminOrAuthor()){
                    axios.get("api/user").then(({data})=>(this.users =data));
                }
            },

    createUser(){
        // progress bar
        this.$Progress.start();
      // Submit the form via a POST request
      this.form.post('api/user')

    .then(() => {

    //   Vue custom event
      Fire.$emit('AfterCreate')
    //   Hide Modal after user is created
    $('#addNew').modal('hide');
    //   Sweet alert
    toast({
        type: 'success',
        title: 'User created successfully'
    });
      this.$progress.finish();
        // .then(({ data }) => { console.log(data) })
})

.catch(() =>{

})
    }
  },
        // mounted() {
        //     console.log('Component mounted.')
        // }

        created(){
            Fire.$on('searching', () => {
                let query = this.$parent.search;
                axios.get('api/findUser?q=' + query)
                .then((data) => {
                    this.users = data.data;
                })
                .catch(() => {});
            })
            this.loadUsers();
            Fire.$on('AfterCreate', () => {
                this.loadUsers();
            });
            // setInterval(()=> this.loadUsers(), 3000);
        }
    }
</script>
