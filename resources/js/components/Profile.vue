<style>

.widget-user-header {
    height: 300px;
    background-position: center center;
    background-size: cover;
}

.widget-user-header, widget-user-desc {
    padding: 0;
}

</style>


<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-3">
                <div class="card card-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header text-white" style="background-image: url('./img/image.jpeg')">
              <h3 class="widget-user-username">{{this.form.name}}</h3>
              <h5 class="widget-user-desc">{{this.form.type}}</h5>
            </div>
            <div class="widget-user-image">
              <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
            </div>
            <div class="card-footer">
              <div class="row">
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header">3,200</h5>
                    <span class="description-text">SALES</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header">13,000</h5>
                    <span class="description-text">FOLLOWERS</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                  <div class="description-block">
                    <h5 class="description-header">35</h5>
                    <span class="description-text">PRODUCTS</span>
                  </div>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
        </div>
        <div class="col-md-12 mt-3 mb-3">
         <div class="nav-tabs shadow-lg p-3">
            <ul class="nav nav-tabs nav-pills" role="tablist" id="myTab">
              <li class="nav-item"><a class="nav-link active show" id="activity" data-toggle="tab" href="#activity" role="tabs" aria-controls="activity" aria-selected="true">Activity</a></li>
              <li class="nav-item"><a class="nav-link" id="settings" data-toggle="tab" href="#settings" role="tabs" aria-controls="settings" aria-selected="true">Settings</a></li>
            </ul>
            <div class="tab-content tabcontent-border mt-3">
                <!-- Activity Tab -->
                <div class="tab-pane" id="activity" role="tabpanel" aria-labelledby="activity">
                    <h1 class="text-center">Display User Activity</h1>
                </div>
                <div class="tab-pane active show" id="settings" role="tabpanel" aria-labelledby="settings"></div>
                <form class="form-horizontal">
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>

                    <div class="col-sm-10">
                      <input type="text" v-model="form.name" class="form-control" id="inputName" placeholder="Name" :class="{ 'is-invalid': form.errors.has('name') }">
                      <has-error :form="form" field="name"></has-error>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                    <div class="col-sm-10">
                      <input type="email" v-model="form.email" class="form-control" id="inputEmail" placeholder="Email" :class="{ 'is-invalid': form.errors.has('email') }">
                      <has-error :form="form" field="email"></has-error>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                    <div class="col-sm-10">
                      <textarea v-model="form.bio" class="form-control" id="inputExperience" placeholder="Experience" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                      <has-error :form="form" field="bio"></has-error>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="photo" class="col-sm-2 control-label">Profile Photo</label>

                    <div class="col-sm-10">
                      <input type="file" @change="updateProfile" class="form-input" name="photo" placeholder="Photo">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <label for="password" class="control-label">Password (Leave empty if not changing)</label>
                          <input type="password" v-model="form.password" class="form-control" id="password" placeholder="Password" :class="{ 'is-invalid': form.errors.has('password') }">
                          <has-error :form="form" field="password"></has-error>
                      </div>
                    </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button @click.prevent="updateInfo" type="submit" class="btn btn-outline-success">Update</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
         </div>
        </div>
        </div>
</template>

<script>
    export default {
        data(){
            return {
                 form: new Form({
                    id:'',
                    name : '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods:{
            getProfilePhoto(){
                let photo = (this.form.photo.length > 200) ? this.form.photo : "img/profile/"+ this.form.photo ;
                return photo;
            },
            updateInfo(){
                this.$Progress.start();
                if(this.form.password == ''){
                    this.form.password = undefined;
                }
                this.form.put('api/profile/')
                .then(()=>{
                     Fire.$emit('AfterCreate');
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            updateProfile(e){
                let file = e.target.files[0];
                let reader = new FileReader();
                let limit = 1024 * 1024 * 2;
                if(file['size'] > limit){
                    swal({
                        type: 'error',
                        title: 'Oops...',
                        text: 'You are uploading a large file',
                    })
                    return false;
                }
                reader.onloadend = (file) => {
                    this.form.photo = reader.result;
                }
                reader.readAsDataURL(file);
            }
        },
        created() {
            axios.get("api/profile")
            .then(({ data }) => (this.form.fill(data)));
        }
    }
</script>
