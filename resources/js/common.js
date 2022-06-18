import axios from "axios"
import { mapGetters } from "vuex";
export default{
    data(){
        return{

        }
    },
    methods:{
        async callApi(method, url, dataObj ){
            try {
              return await axios({
                    method: method,
                    url: url,
                    data: dataObj
                });
            } catch (e) {
                return e.response
            }
        }, 

        i(desc, title="Hey") {
            this.$Notice.info({
                title: title,
                desc: desc
            });
        },
        s(desc, title="Great!") {
            this.$Notice.success({
                title: title,
                desc: desc
            });
        },
        w(desc, title="Oops!") {
            this.$Notice.warning({
                title: title,
                desc: desc
            });
        },
        e(desc, title="Oops!") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        }, 
        Invalid(desc="try again", title="The information you have provided is invalid try again with the correct credentials") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        }, 
        swr(desc='Something went wrong, Likely from server! Please try again.', title="Oops") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        },
        checkUserPermission(key){
            if(!this.userPermitted) return true
            let isPermitted = false;
            for(let d of this.userPermitted){
                if(this.$route.name==d.name){
                    if(d[key]){
                        isPermitted = true
                        break;
                    }else{
                        break
                    }
                }
                
            }
            return isPermitted
        }
    },
    computed: {
        ...mapGetters({
            'userPermitted' : 'getUserPermission'
        }),
        isReadPermitted(){
            // console.log('user permission', this.userPermitted)
           return this.checkUserPermission('read');
        },
        isWritePermitted(){
           return this.checkUserPermission('write');
        },
        isUpdatePermitted(){
            return this.checkUserPermission('update');
        },
        isDeletePermitted(){
            return this.checkUserPermission('delete');
        },
    }
}