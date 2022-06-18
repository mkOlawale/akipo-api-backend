require('./bootstrap');
import Vue from "vue";
import router from "./router";
import Common from "./Common";
import jsonToHtml from "./jsonToHtml";
import store from './store'
import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
Vue.use(ViewUI);
Vue.mixin(Common);
Vue.mixin(jsonToHtml);
import Editor from 'vue-editor-js'    
Vue.use(Editor)
Vue.component('mainapp', require("./components/mainapp.vue").default);
const app = new Vue({ 
    el: '#app',
    router,
    store
})
