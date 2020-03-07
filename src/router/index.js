import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
	routes: [
		{
            path: '/',
            redirect: 'standing',
		},
		{
			path: '/login',
			name: 'login',
			component: () => import(/* webpackChunkName: "login" */ '../components/Login.vue')
        },
        {
			path: '/standing',
			name: 'standing',
			component: () => import(/* webpackChunkName: "standing" */ '../components/Standing.vue')
		},
        {
			path: '/result',
			name: 'result',
			component: () => import(/* webpackChunkName: "result" */ '../components/Result.vue')
        },
        {
			path: '/emblem',
			name: 'emblem',
			component: () => import(/* webpackChunkName: "emblem" */ '../components/Emblem.vue')
		},
        {
			path: '/arena',
			name: 'arena',
			component: () => import(/* webpackChunkName: "arena" */ '../components/Arena.vue')
		},
		// {
		// 	path: '/details/:id',
		// 	name: 'details',
		// 	props: true,
		// 	component: () => import(/* webpackChunkName: "details" */ '../components/Details.vue')
		// },
		
	]
})