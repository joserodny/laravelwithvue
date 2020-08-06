<template>
    <div class="container">
       <div class="row pt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">User Table</h3>

                <div class="card-tools">
                    <button class="btn btn-success" @click="userCreateModal()"><i class="fas fa-user-plus fa-fw"></i> Add New</button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Registred at</th>
                      <th>Modify</th>
                    </tr>
                  </thead>
                  <tbody>

                    <tr v-for="user in users" :key="user.id">
                      <td>{{user.id}}</td>
                      <td>{{user.name}}</td>
                      <td>{{user.email}}</td>
                      <td>{{user.type | capitalize}}</td>
                      <td>{{user.created_at | userDateCreated}}</td>

                      <td>
                          <a href="#" @click="userEditModal(user)">
                              Edit <i class="fas fa-edit"></i>
                          </a>
                    /
                           <a href="#" @click="deleteUser(user.id)">
                             <i class="fas fa-trash red"></i>
                          </a>
                      </td>
                    </tr>

                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>



<!-- Modal -->
<div class="modal fade" id="addNewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 v-show="!editMode" class="modal-title" id="exampleModalLabel">Add New User</h5>
         <h5 v-show="editMode" class="modal-title" id="exampleModalLabel">Edit User's Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="editMode ? updateUser(): createUser()">
      <div class="modal-body">
           <div class="form-group">
                <input v-model="form.name" type="text" name="name" placeholder="Name" id="name"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                <has-error :form="form" field="name"></has-error>
           </div>

           <div class="form-group">
                <input v-model="form.email" type="email" name="email" placeholder="Email" id="email"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                <has-error :form="form" field="email"></has-error>
           </div>

            <div class="form-group">
                <textarea v-model="form.bio" type="bio" name="bio" placeholder="Short bio for user (Optional)" id="bio"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                <has-error :form="form" field="bio"></has-error>
           </div>

            <div class="form-group">
                <select v-model="form.type" type="type" name="type" id="type"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                    <option value="">Select User Role</option>
                    <option value="admin">Admin</option>
                     <option value="user">Standard User</option>
                      <option value="author">Author</option>
                </select>
                <has-error :form="form" field="bio"></has-error>
           </div>

           <div class="form-group">
                <input v-model="form.password" type="password" name="password" placeholder="Password" autocomplete="on" id="password"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                <has-error :form="form" field="password"></has-error>
          </div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
         <button v-show="editMode" type="submit" class="btn btn-info">Update</button>
        <button v-show="!editMode" type="submit" class="btn btn-primary">Create</button>
      </div>
      </form>
    </div>
  </div>
</div>

    </div>




</template>

<script>
    export default {
        data() {

            return {
                editMode: false,
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        methods: {
            updateUser(){
                this.$Progress.start();
                    this.form.put('api/user/'+this.form.id)
                    .then(() => {
                        //success
                          swal.fire(
                            'Updated!',
                            'Information has been updated.',
                            'success'
                            )
                         //modal
                        $('#addNewModal').modal('hide');
                         //reloading
                      reload.$emit('reloadPages');
                      this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            },
            userCreateModal() {
                this.editMode = false;
                this.form.reset();
                $('#addNewModal').modal('show');

            },
             userEditModal(user) {
                this.form.reset();
                this.editMode = true;
                $('#addNewModal').modal('show');
                this.form.fill(user);
            },
            deleteUser(id) {
                    swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                        //send request
                        if (result.value) {
                        this.form.delete('api/user/'+id)
                        .then(() => {
                            swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                            )
                         reload.$emit('reloadPages');
                        })
                        .catch(() => {
                            swal.fire('Failed!', 'Something wrong.', 'warning');
                        })
                        }

                    })
            },
            loadUser(){

                if(this.$gate.isAdmin()){
                    axios.get('api/user').then(({ data }) => (this.users = data.data));
                }

            },
            createUser(){
                this.$Progress.start();
                 // Submit the form via a POST request
                this.form.post('api/user')
                .then(() => {
                    //reloading
                reload.$emit('reloadPages');
                //modal
                $('#addNewModal').modal('hide');
                //sweeralert
                toast.fire({
                icon: 'success',
                title: 'Successfully created'
                })

                this.$Progress.finish();
                })
                .catch(() => {

                })


            }
        },
        created() {
           this.loadUser();
           reload.$on('reloadPages', () => {
               this.loadUser();

           });

        }
    }
</script>
