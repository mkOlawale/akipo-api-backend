<template>
    <div>
        <div class="content">
			<div class="container">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Category <button @click="addModal = true" style="padding: 20px;"><Icon type="md-add" />Add Category</button></p>

					<div class="_overflow _table_div">
						<table class="_table">
							<tr>
								<th>ID</th>
								 <th>Icon image</th>
								<th>Category Name</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
							<tr v-for="(Category, i) in Categories" :key="i">
								<td>{{Category.id}}</td>
								<td class="table_image"><img :src="Category.iconImage" /></td>
								<td class="_table_name">{{Category.categoryName}}</td>
								<td>{{Category.created_at}}</td>
								<td>
									<button class="_btn _action_btn edit_btn1" type="button" @click="showEdittag(Category, i)">Edit</button>
									<button class="_btn _action_btn make_btn1"
									 type="button"
									 :loading="Category.isDeleting"
									  @click="showDeletedModal(Category, i)">Delete</button>
								</td>
							</tr>
						</table>
					</div>
				</div>

	<!-- add modal inserted -->
	<modal v-model="addModal" 
		title="Add Modal"
		:mask-closable="false">
		<input v-model="data.categoryName" placeholder="Add fucking tag" style="width:100%;" />
			<div class="space"></div>
			<Upload type="drag"
			action="app/upload"
			ref="uploads"
			:headers="{'x-csrf-token': token, 'X-Requested-With' : 'XMLHttpRequest'}"
			:on-success="handleSuccess"
			:on-error="handleError"
			:format="['jpg', 'jpeg', 'png']"
			:max-size="2048"
			:on-format-error="handleFormatError"
			:on-exceeded-size="handleMaxSize">
		<div style="padding: 20px 0;">
			<Icon type="ios-cloud-upload" size="52" style="color: #3399ff;"></Icon>
			<p>Click or drag file here to upload</p>
		</div>
	</Upload>
	 <div class="demo-upload-list" v-if="data.iconImage">
            <img :src="`${data.iconImage}`" />
            <div class="demo-upload-list-cover">
              <Icon type="ios-trash-outline" @click="deleteImage"></Icon>
            </div>
          </div>
	<div slot="footer">
		<button @click="addModal = false" type="default" class="_btn _action_btn edit_btn1" size="small">close</button>
		<button 
		@click="addCategory" 
		type="primary" 
		class="_btn _action_btn make_btn1" size="small">Add Category</button>
	</div>
	</modal>
	<!-- edit modal inserted -->
	<modal v-model="editModal" 
	title="Edit Category"
	:mask-closable="false">
	<input v-model="editdata.categoryName" placeholder="Add fucking Category" style="width:100%;" />
			<div class="space"></div>
			<Upload 
			v-show="idDeletingItem"
			type="drag"
			action="app/upload"
			ref="uploadsEditData"
			:headers="{'x-csrf-token': token, 'X-Requested-With' : 'XMLHttpRequest'}"
			:on-success="handleSuccess"
			:on-error="handleError"
			:format="['jpg', 'jpeg', 'png']"
			:max-size="2048"
			:on-format-error="handleFormatError"
			:on-exceeded-size="handleMaxSize">
		<div style="padding: 20px 0;">
			<Icon type="ios-cloud-upload" size="52" style="color: #3399ff;"></Icon>
			<p>Click or drag file here to upload</p>
		</div>
	</Upload>
	 <div class="demo-upload-list" v-if="editdata.iconImage">
            <img :src="`${editdata.iconImage}`" />
            <div class="demo-upload-list-cover">
              <Icon type="ios-trash-outline" @click="deleteImage(false)"></Icon>
            </div>
          </div>
	<div slot="footer">
		<button @click="closeEditing" type="default" class="_btn _action_btn edit_btn1" size="small">close</button>
		<button 
		@click="editCategory" 
		type="primary" 
		class="_btn _action_btn make_btn1" size="small">edit Category</button>
	</div>
	</modal>
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
				iconImage: '',
				categoryName: ''
			},
			editdata:{
				iconImage: '',
				categoryName: ''
			},
			token: '',
			addModal: false,
			isAdding: false,
			editModal: false,
			Categories: [],
			index: -1,
			showDeleteModal: false,
			deleteItem: {},
			deleting: -1,
			isDeleting: false,
			idDeletingItem: false,
			isEditing: false,
			islodaded: null	
        }
    },
   methods:{
	   async addCategory(){
			if(this.data.iconImage.trim()=='') return this.e('icon image is required');
			if(this.data.categoryName.trim()=='') return this.e('Category name is required');
			this.data.iconImage = `${this.data.iconImage}`;
			const res = await this.callApi('post', 'app/create_category', this.data);
			if(res.status===201){
				this.Categories.unshift(res.data);
				this.s('Tag has been added successfully!');    
				this.addModal = false;
				this.data.iconImage = ''
				this.data.categoryName = ''
			}else{
				if(res.status == 422){
                    	if(res.data.errors.categoryName){
						this.e(res.data.errors.categoryName[0])
					}
					    if(res.data.errors.iconImage){
						this.e(res.data.errors.iconImage[0])
					}
				}else{
			   this.swr()
				}
		   }
	   },
	   async editCategory() {
      if (this.editdata.categoryName.trim() == "")
        return this.e("Category name is required");
      if (this.editdata.iconImage.trim() == "")
        return this.e("Icon image is required");
      const res = await this.callApi(
        "post",
        "app/edit_category",
        this.editdata
      );
      if (res.status === 200) {
		//   this.Categories.unshift(res.data);
        this.Categories[this.index].categoryName = this.editdata.categoryName;
        this.s("Category has been edited successfully!");
        this.editModal = false;
      } else {
        if (res.status == 422) {
          if (res.editdata.errors.categoryName) {
            this.e(res.editdata.errors.categoryName[0]);
          }
          if (res.editdata.errors.iconImage) {
            this.e(res.editdata.errors.iconImage[0]);
          }
        } else {
          this.swr();
        }
      }
    },
	   showEdittag(Category, index){
		//    let obj = {
		// 	   id: tag.id,
		// 	   tagName: tag.tagName
		//    }
		   this.editdata = Category;
		   this.editModal = true;
		   this.index = index;
		   this.isEditing = true
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
	   showDeletedModal (category, i) {
		const deleteModalObj = {
			showDeleteModal: true,
			deleteUrl: "app/delete_category",
			data: category,
			deletingIndex: i,
			isDeleted: false
		};
		this.$store.commit("setDeletingModalObj", deleteModalObj);
		// this.deleteItem = tag
		// this.deletingIndex = i
		// this.showDeleteModal = true
    },
	   handleSuccess(res, file){
		   res = `/uploads/${res}`;
		   if(this.isEditing){
			   return this.editdata.iconImage = res
		   }
		 this.data.iconImage = res  
	   },
	   handleError(res, file){
		   console.log('res', res);
		   console.log('file', file);
		   this.$Notice.warning({
			   title: 'The file format is incorrect',
			   desc: `${file.errors.file.length} ? file.errors.file[0] : 'Something went wrong'`
		   })
	   },
	   handleFormatError(file){
		   this.$Notice.warning({
			   title: 'The file format is incorrect',
			   desc: 'File format of' + file.name+'is incorrect, please select jpg or png'
		   });
	   },
	   handleMaxSize(file){
		    this.$Notice.warning({
			   title: 'Exceeding file limit',
			   desc: 'File of' + file.name+'is too large, no more than 2m'
		   });
	   },
	   async deleteImage(isAdd = true){
		   let image;
		   if(!isAdd){
			this.idDeletingItem = true;
			 image = this.data.iconImage;
		     this.editdata.iconImage = '';
		  	 this.$refs.uploadsEditData.clearFiles();
		   }else{
			    image = this.data.iconImage;
				this.data.iconImage = '';
				this.$refs.uploads.clearFiles();
		   }
		   const res = await this.callApi('post', 'app/delete_image', {imageName: image});
		   if(res.status != 200){
			   this.data.iconImage = image;
			   this.swr()
		   }
	   },
	   closeEditing(){
		   this.isEditing = false;
		   this.editModal = false;
	   }
   },
   async created(){
	   this.token = window.Laravel.csrfToken;
			const res = await this.callApi('get', 'app/get_category');
			if(res.status === 200){
			this.Categories = res.data;
			}else{
			   this.swr()
		   }
   },
          computed : {
        ...mapGetters(['getDeleteModalObj'])
	},
	watch : {
		getDeleteModalObj(obj){
			if(obj.isDeleted){
				  this.Categories.splice(obj.deleting,1);
			}
		}
	}
}
</script>