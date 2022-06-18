import Vue from "vue";
import Router from 'vue-router';

Vue.use(Router);
import home from './admin/home.vue'
import tags from './admin/tags.vue'
import category from './admin/category.vue'
import adminusers from './admin/adminusers.vue'
import login from './admin/login.vue'
import createBlog from './admin/createBlog.vue'
import blogs from './admin/blogs.vue'
import editblog from './admin/editblog.vue'
import notfound from './admin/notfound.vue'
const routes = [
    {
        path: '/',
        component: home,
        name: 'home'
    },
    {
        path: '/tags',
        component: tags,
        name: 'tags'
    },
    {
        path: '/category',
        component: category,
        name: 'category'

    },
    {
        path: '/adminusers',
        component: adminusers,
        name: 'adminusers'

    },
    {
        path: '/login',
        component: login,
        name: 'login'
    },
    {
        path: '/createBlog',
        component: createBlog,
        name: 'createBlog'
    },
    {
        path: '/blogs',
        component: blogs,
        name: 'blogs'
    },
    {
        path: '/editblog/:id',
        component: editblog,
        name: 'editblog'
    },
    {
        path: '*',
        component: notfound,
        name: 'notfound'
    }
]

export default new Router({
    mode: 'history',
    routes
})