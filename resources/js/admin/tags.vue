<template>
    <div>
        <div class="content">
			<div class="container">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Tags <button @click="addModal = true"><Icon type="md-add"/>Add Tag</button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Tag Name</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(tag, i) in tags" :key="i">
								<td>{{tag.id}}</td>
								<td class="_table_name">{{tag.tagName}}</td>
								<td>{{tag.created_at}}</td>
								<td>
									<button class="_btn _action_btn edit_btn1" type="button"  @click="showEdittag(tag, i)" >Edit</button>
									<button class="_btn _action_btn make_btn1"
									 type="button"
									 :loading="tag.isDeleting"
									  @click="showDeletedModal(tag, i)">Delete</button>
								</td>
							</tr>
						</table>
					</div>
				</div>

	<!-- add modal inserted -->
	<modal v-model="addModal" 
	title="Add Modal"
	:mask-closable="false">
		<input v-model="data.tagName" placeholder="Add fucking tag" style="width:100%;" />
	<div slot="footer">
		<button @click="addModal = false" type="default" class="_btn _action_btn edit_btn1" size="small">close</button>
		<button 
		@click="addTag" 
		type="primary" 
		class="_btn _action_btn make_btn1" size="small">Add tag</button>
	</div>
	</modal>
	<!-- edit modal inserted -->
	<modal v-model="editModal" 
	title="Edit Modal"
	:mask-closable="false">
		<input v-model="editdata.tagName" placeholder="Add fucking tag" style="width:100%;" />
	<div slot="footer">
		<button @click="editModal = false" type="default" class="_btn _action_btn edit_btn1" size="small">close</button>
		<button 
		@click="editTag" 
		type="primary" 
		class="_btn _action_btn make_btn1" size="small">edit tag</button>
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
				tagName: ''
			},
			editdata:{
				tagName: ''
			},
			addModal: false,
			isAdding: false,
			editModal: false,
			tags: [],
			index: -1,
			showDeleteModal: false,
			deleteItem: {},
			deleting: -1,
			idDeleting: false,
        }
    },
   methods:{
	   async addTag(){
			// if(this.data.tagName.trim()=='') return this.e('Tag name is required');
			const res = await this.callApi('post', 'app/create_tags', this.data);
			if(res.status===201){
				this.tags.unshift(res.data);
				this.s('Tag has been added successfully!');    
				this.addModal = false;
				this.data.tagName = ''
			}else{
				if(res.status == 422){
					if(res.data.errors.tagName){
						this.e(res.data.errors.tagName[0])
					}
				}else{
			   this.swr()
				}
		   }
	   },
	   async editTag(){
			if(this.editdata.tagName.trim()=='') return this.e('Tag name is required');
			const res = await this.callApi('post', 'app/edit_tags', this.editdata);
			if(res.status===200){
				this.tags[this.index].tagName = this.editdata.tagName;
				this.s('Tag has been added successfully!');    
				this.editModal = false;
			}else{
				if(res.status == 422){
					if(res.data.errors.tagName){
						this.e(res.data.errors.tagName[0])
					}
				}else{
			   this.swr()
				}
		   }
	   },
	   showEdittag(tag, index){
		   let obj = {
			   id: tag.id,
			   tagName: tag.tagName
		   }
		   this.editdata = tag;
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
       computed : {
        ...mapGetters(['getDeleteModalObj'])
	},
	watch : {
		getDeleteModalObj(obj){
			if(obj.isDeleted){
				  this.tags.splice(obj.deleting,1);
			}
		}
	},
	 async created(){
	    //    console.log(this.isWritePermitted);
			const res = await this.callApi('get', 'app/get_tags');
			if(res.status === 200){
			this.tags = res.data;
			}
			else{
			   this.swr()
		   } 
   }

}
</script>