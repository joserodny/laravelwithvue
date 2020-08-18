
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-5">
                <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header text-white" style="background: url('./images/cover.png') center center;">
                <h3 class="widget-user-username text-right">{{this.form.name}}</h3>
                <h5 class="widget-user-desc text-right">{{this.form.type}}</h5>
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
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
            </div>
                <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Activity</a></li>
                  <li class="nav-item"><a class="nav-link " href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane active" id="activity">
                    <!-- Post -->
                    <div class="post">
                        asfaf
                    </div>
                    <!-- /.post -->

                  </div>

                  <div class="tab-pane " id="settings">
                    <form class="form-horizontal">
                         <input type="hidden" name="_token" :value="csrf">
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input  v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" id="inputName" placeholder="Name">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input v-model="form.email" type="email" class="form-control" id="inputEmail" placeholder="Email" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="inputExperience" class="col-sm-2 col-form-label">Bio</label>
                        <div class="col-sm-10">
                          <textarea v-model="form.bio" class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }" id="inputExperience" placeholder="Bio"></textarea>
                            <has-error :form="form" field="bio"></has-error>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputSkills" class="col-sm-2 col-form-label">Profile Photo</label>
                        <div class="col-sm-10">
                          <input type="file" @change="updateProfile" id="avatar" name="avatar" accept="image/png, image/jpeg">
                        </div>
                      </div>
                         <div class="form-group row">
                        <label for="inputName2" class="col-sm-2 col-form-label">Password (leave empty if not changing)</label>
                        <div class="col-sm-10">
                          <input v-model="form.password" type="password" class="form-control" id="inputName2" :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"></has-error>
                        </div>
                      </div>

                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button @click.prevent="updateInfo" type="submit" class="btn btn-success">Update</button>
                        </div>
                      </div>
                    </form>
                  </div>

                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
               form: new Form({

                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                }),
                 csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            getProfilePhoto(){

             let prefix = (this.form.photo.match(/\//) ? '' : '/images/profile/');
             return prefix + this.form.photo;

            },
            updateInfo() {
                if(this.form.password == ""){
                    this.form.password = undefined;
                }
                  this.$Progress.start();
                    this.form.put('api/profile')
                    .then(() => {
                        reload.$emit('reloadPages');
                        this.form.password = undefined;
                        this.$Progress.finish();

                    })
                    .catch(() => {
                            this.$Progress.fail();
                    });
            },
            updateProfile(e) {
               let file = e.target.files[0];
               let reader = new FileReader();
               if(file['size'] < 2000000) {
                    reader.onloadend = (file) => {
                  this.form.photo = reader.result;
               }

               reader.readAsDataURL(file);
               }else {
                     swal.fire(
                       'Error!',
                       'Oops... The image is over 2mb',
                       'error'
                      )

               }

            }
        },
        created() {
              axios.get('api/profile')
              .then(({ data }) => (this.form.fill(data)));
        }
    }
</script>
