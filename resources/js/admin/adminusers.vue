<template>
    <div>
        <div class="content">
			<div class="container">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Users <button @click="addModal = true"><Icon type="md-add" />Add Users</button></p>

					<div class="_overflow _table_div">
					<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Email</th>
								<th>User type</th>
								<th>Created at</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(user, i) in users" :key="i">
								<td>{{user.id}}</td>
								<td class="_table_name">{{user.fullName}}</td>
								<td class="">{{user.email}}</td>
								<td class="">{{user.userType}}</td>
								<td>{{user.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(user, i)">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(user, i)"  :loading="user.isDeleting">Delete</Button>
									
								</td>
							</tr>
								<!-- ITEMS -->
					</table>
					</div>
				</div>

	<!-- add modal inserted -->
				<Modal
					v-model="addModal"
					title="Add admin"
					:mask-closable="false"
					:closable="false"

					>
                    <div class="space">
                        <Input type="text" v-model="data.fullName" placeholder="Full name"  />
                    </div>
                    <div class="space">
                        <Input type="email" v-model="data.email" placeholder="Email"  />
                    </div>
                    <div class="space">
                        <Input type="password" v-model="data.password" placeholder="Password"  />
                    </div>
                   <div class="space">
                        <Select v-model="data.userType" placeholder="Select admin type">
                            <!-- <Option :value="r.id" v-for="(r, i) in Roles" :key="i">{{r.roleName}}</Option> -->
                            <Option value="Admin">Admin</Option>
                            <Option value="Editor" >Editor</Option>
                        </Select>
                    </div>
					<div slot="footer">
						<Button type="default" @click="addModal=false">Close</Button>
						<Button type="primary" @click="addAdmin" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding..' : 'Add admin'}}</Button>
					</div>

				</Modal>
	<!-- edit modal inserted -->
	<modal v-model="editModal" 
	title="Edit Modal"
	:mask-closable="false">
	 <div class="space">
                        <Input type="text" v-model="editdata.fullName" placeholder="Full name"  />
                    </div>
                    <div class="space">
                        <Input type="email" v-model="editdata.email" placeholder="Email"  />
                    </div>
                    <div class="space">
                        <Input type="password" v-model="editdata.password" placeholder="Password"  />
                    </div>
                    <div class="space">
                        <Select v-model="editdata.role_id"  placeholder="Select admin type">
                            <!-- <Option :value="r.id" v-for="(r, i) in roles" :key="i">{{r.roleName}}</Option> -->
                           <Option value="Admin">Admin</Option>
                            <Option value="Editor" >Editor</Option>
                        </Select>
                    </div>
	<div slot="footer">
			<button @click="editModal = false" type="default" class="_btn _action_btn edit_btn1" size="small">close</button>
			<button 
			@click="editUser" 
			type="primary" 
			class="_btn _action_btn make_btn1" size="small">edit Admin</button>
	</div>
	</modal>
	<!-- <modal v-model="showDeleteModal" 
	width="360"
	>
	<p slot="header" style="color:#164; text-align: center;"><Icon type="ios-information-circle"></Icon>
	<span>Delete Confirmation</span>
	</p>
	<div style="text-align: center;">
		<p>Are you sure you want to delete this tag</p>
	</div>
	<div slot="footer">
		<button @click="deleteTag" type="error" class="_btn _action_btn edit_btn1" 
		size="large"
		long :loading="idDeleting" :disabled="idDeleting"
		>Delete</button>
	</div>
	</modal> -->
	<deleteModal></deleteModal>
			</div>
		</div>
    </div>
</template>
<script>
import deleteModal from '../Components/deleteModal'
import { mapGetters } from "vuex";
export default {
	components:{
		deleteModal
	},
    data(){
        return{
			data:{
				fullName: '',
				email: '',
				password: '',
				userType: '',
			},
			editdata:{
				// fullName: '',
				// email: '',
				// password: '',
				// userType: null
			},
			addModal: false,
			isAdding: false,
			editModal: false,
			users: [],
			index: -1,
			showDeleteModal: false,
			deleteItem: {},
			deleting: -1,
			idDeleting: false,
			Roles: []
        }
    },
   methods:{
	 async addAdmin(){
            if(this.data.fullName.trim()=='') return this.e('Full name is required');
            if(this.data.email.trim()=='') return this.e('Email is required');
			if(this.data.password.trim()=='') return this.e('Password is required');
			// if(this.data.role_id.trim()=='') return this.e('user Type is required');
            if(this.data.userType.trim()=='') return this.e('User type  is required')
			const res = await this.callApi('post', 'app/create_user', this.data)
			if(res.status===201){
				this.users.unshift(res.data)
				this.s('Admin user has been added successfully!')
				this.addModal = false
				this.data.tagName = ''
			}else{
				if(res.status==422){
                    for(let i in res.data.errors){
                        this.e(res.data.errors[i][0])
                    }
				}else{
					this.swr()
				}
				
			}

		},
	   async editUser(){
			 if(this.editdata.fullName.trim()=='') return this.e('Full name is required');
            if(this.editdata.email.trim()=='') return this.e('Email is required');
			if(!this.editdata.role_id) return this.e('user Type is required');
			const res = await this.callApi('post', 'app/edit_users', this.editdata);
			if(res.status===200){
				this.users[this.index] = this.editdata;
				this.s('Admin users been Edited successfully!');    
				this.editModal = false;
			}else{
				if(res.status == 422){
					for(let i in res.data.errors){
                        this.e(res.data.errors[i][0])
                    }
				}else{
			   this.swr()
				}
		   }
	   },
	   showEditModal(user, index){
		   let obj = {
			 id: user.id,
			 fullName: user.fullName,
			 email: user.email,
			 userType: user.userType
		   }
		   this.editdata = obj;
		   this.editModal = true;
		   this.index = index
	   },
	   async deleteTag(){
		   this.isDeleting = true;
		   const res = await this.callApi('post', 'app/delete_tags', this.deleteItem);
		   if(res.status === 200){
			   this.tags.splice(this.deleting,1);
			   this.s('You have succesfully deleted this tag');
		   }else{
			   this.swr();
		   }
		   this.isDeleting = false;
		    this.showDeleteModal = false;
	   },
	   showDeletedModal(tag, i){
		    const deleteModalObj = {
            showDeleteModal: true,
            deleteUrl : 'app/delete_tags',
            data : tag,
            deleting: i,
            isDeleted : false,

		}
		this.$store.commit('setDeletingModalObj', deleteModalObj)
		console.log('delete method has been called')
		//    this.deleteItem = tag;
		//    this.deleting = i;
		//    this.showDeleteModal = true;
	   }
   },
   async created(){
	   const res = await this.callApi('get', 'app/get_users');
	   if(res.status === 200){
			this.users = res.data;
			}else{
			   this.swr()
		   }
	//    const [res, resRole] = await Promise.all([
	// 	this.callApi('get', 'app/get_users'),
	// 	this.callApi('get', 'app/get_roles'),
	//    ])
	// 		if(res.status === 200){
	// 		this.users = res.data;
	// 		}else{
	// 		   this.swr()
	// 	   }
	// 		if(resRole.status === 200){
	// 		this.Roles = resRole.data;
	// 		}else{
	// 		   this.swr()
	// 	   }
   },
       computed : {
        ...mapGetters(['getDeleteModalObj'])
	},
	watch : {
		getDeleteModalObj(obj){
			if(obj.isDeleted){
				  this.tags.splice(obj.deleting,1);
			}
		}
	}
}
</script>