<template>
    <div>
        <div class="card mt-3" v-if="active.dashboard">
            <div class="card-body">
                <h3 class="d-flex justify-content-between">Manage Users <button class="btn btn-outline-success btn-sm float-right" @click="setActive('createUser')"><i class="fas fa-plus"></i> User</button></h3>

                <div class="alert-success alert" role="alert" v-if="success_message !== null">
                    {{ success_message }}
                </div>

                <div class="alert alert-danger" role="alert" v-if="danger_message !== null">
                    {{ danger_message }}
                </div>

                <Paginator v-if="results !== null" v-bind:results="results" v-on:get-page="getPage" />
                <PaginatorDetails v-if="results !== null" v-bind:results="results"></PaginatorDetails>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>User Since</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody v-if="results !== null">
                        <tr v-for="user in results.data">
                            <th>{{ user.name }}</th>
                            <td>{{ user.email }}</td>
                            <td>{{ user.user_since }}</td>
                            <td>
                                <div class="btn-group">
                                    <div class="button btn btn-secondary btn-sm" title="View User Logs" @click="viewUserLogs(user)"><i class="fas fa-list-alt"></i></div>
                                    <button class="btn btn-sm btn-warning" title="Edit User" @click="editUser(user)"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-secondary" title="Change User Password" @click="changePassword(user)"><i class="fas fa-key"></i></button>
                                    <button class="btn btn-sm btn-danger" @click="deleteUser(user)"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Paginator v-if="results !== null" v-bind:results="results" v-on:get-page="getPage" />
            </div>
        </div>
        <CreateUser v-if="active.createUser" v-on:view-dashboard="setActive('dashboard')" v-on:created-user="flashSuccessAndReload" />

        <UserLogs 
        v-if="user !== null && active.userLogs" 
        v-bind:user="user" 
        v-on:view-dashboard="setActive('dashboard')" />

        <EditUser
        v-if="user !== null && active.editUser"
        v-bind:user="user"
        v-on:view-dashboard="setActive('dashboard')"
        v-on:user-updated="flashSuccessAndReload">
        </EditUser>

        <ChangeUserPassword v-if="user !== null && active.changePassword" v-bind:user="user" v-on:view-dashboard="setActive('dashboard')" v-on:updated-password="flashSuccessAndReload" v-on:sent-reset-link="flashSuccessAndReload" />
    </div>
</template>


<script>
    import Paginator from '../utilities/pagination/Paginator.vue'
    import PaginatorDetails from '../utilities/pagination/PaginatorDetails.vue'
    import CreateUser from './CreateUser.vue'
    import UserLogs from './logs/UserLogs.vue'
    import EditUser from './EditUser.vue'
    import ChangeUserPassword from './ChangeUserPassword.vue'

    export default {
        components:{
            Paginator,
            PaginatorDetails,
            CreateUser,
            UserLogs,
            EditUser,
            ChangeUserPassword,
        },  
        mounted() {
            this.getUsers()
        },
        data() {
            return {
                user: null,
                results: null,
                active: {
                    dashboard: true,
                    createUser: false,
                    userLogs:false,
                    editUser: false,
                    changePassword: false,
                },
                params: {
                    page: 1
                },
                success_message: null,
                danger_message:null,
            }
        }, 
        methods: {
            getUsers: function() {
                axios.get('/data/users', {params: this.params}).then(response => {
                    this.results = response.data.results
                })
            },
            editUser: function(user) {
                this.user = user
                this.setActive('editUser')
            },
            changePassword: function(user) {
                this.user = user
                this.setActive('changePassword')
            },
            deleteUser: function(user) {
                let r = confirm("Are you sure you want to delete " + user.name + " from the system?")
                if (r) {
                    axios.post('/data/users/' + user.id, {_method: 'DELETE'}).then(response => {
                        this.flashSuccessAndReload('Successfully deleted user')
                    }).catch(errors => {
                        if(errors.response.status === 403) {
                            this.flashDanger("Unauthorized to Delete User")
                        }
                    })
                }
            }, 
            viewUserLogs: function(user) {
                this.user = user
                this.setActive('userLogs')
            },
            getPage: function(event) {
                this.params.page = event
                window.scrollTo(0, 0, {behavior: "smooth"})
                this.getUsers()
            },
            setActive: function(component) {
                Object.keys(this.active).forEach(key => this.active[key] = false)
                this.active[component] = true
            },
            flashSuccessAndReload: function(event) {
                this.setActive('dashboard')
                this.flashSuccess(event)
                this.getUsers()
            },
            flashSuccess: function(message) {
                this.success_message = message
                setTimeout(() => {
                    this.success_message = null
                }, 5000)
            },
            flashDanger: function(message) {
                this.danger_message = message
                setTimeout(() => {
                    this.danger_message = null
                }, 5000)
            }
        }
    }
</script>