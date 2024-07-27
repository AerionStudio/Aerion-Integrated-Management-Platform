import { createRouter, createWebHashHistory } from 'vue-router'
import Home from '../components/home.vue'
import Events from "../components/events.vue";
import EventList from "../components/eventlist.vue";
import Login from "../components/login.vue";
import Loading from "../components/loading.vue";
import Error from "../components/error.vue";
const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/',
        name: 'home1',
        component: Home
    },
    {
        path: '/home',
        name: 'home',
        component: Home
    },
    {
        path: '/error',
        name: 'error',
        component: Error
    },
    {
        path: '/event/now',
        name: 'events',
        component: Events
    },
    {
        path: '/event/list',
        name: 'list',
        component: EventList
    },
    {
        path: '/nouser',
        name: 'nouser',
        component: () => import('../components/nouser.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/event/detail/:id',
        name: 'eventdetail',
        component: () => import('../components/detail.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/user/:id',
        name: 'users',
        component: () => import('../components/users.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/event/list/noevent',
        name: 'noevent',
        component: () => import('../components/noevents.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/weather',
        name: 'weather',
        component: () => import('../components/weather.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/atc',
        name: 'atc',
        component: () => import('../components/atclist.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/gradelist',
        name: 'gradelist',
        component: () => import('../components/gradelist.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/info',
        name: 'info',
        component: () => import('../components/info.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/route',
        name: 'route',
        component: () => import('../components/route.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/loading',
        name: 'Loading',
        component: Loading
    },
    {
        path: '/map',
        name: 'map',
        component: () => import('../components/map.vue') // 替换为你的编辑页面组件
    },
    {
        path: '/user',
        name: 'person',
        component: ()=>import('../components/person.vue')
    },
    {
        path: '/chart',
        name: 'chart',
        component: ()=>import('../components/chart.vue')
    },
    {
        path: '/join',
        name: 'join',
        component: ()=>import('../components/join.vue')
    },
    {
        path: '/download',
        name: 'download',
        component: ()=>import('../components/download.vue')
    },
    // 将通配符路径重定向到 /error
    {
        path: '/:pathMatch(.*)*',
        redirect: '/error'
    }
]

const router = createRouter({
    history: createWebHashHistory(), // 使用Hash模式
    routes
})

router.beforeEach((to, from, next) => {
    const isLoggedIn = sessionStorage.getItem('isLoggedIn'); // 假设使用localStorage来存储登录状态

    // 如果用户尝试访问需要登录的页面，但未登录，则跳转到登录页面
    if (to.path !== '/login' && !isLoggedIn) {
        next('/login');
    } else {
        next();
    }
});

export default router
