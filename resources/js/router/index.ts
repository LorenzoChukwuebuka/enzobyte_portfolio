import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import api from '@/services/api';

const routes: RouteRecordRaw[] = [
    {
        path: '/admin/login',
        name: 'Login',
        component: () => import('@/components/admin/logincomponent.vue'),
        meta: { requiresGuest: true },
    },

    {
        path: '/admin',
        component: () => import('@/components/admin/adminLayoutComponent.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                redirect: '/admin/dashboard',
            },
            {
                path: 'dashboard',
                name: 'Dashboard',
                component: () => import('@/components/admin/dashboardcomponent.vue'),
            },
            {
                path: 'projects',
                name: 'Projects',
                component: () => import('@/components/admin/projectcomponent.vue'),
            },
            {
                path: 'services',
                name: 'Services',
                component: () => import('@/components/admin/servicescomponent.vue'),
            },
            {
                path: 'team',
                name: 'Team',
                component: () => import('@/components/admin/teammembercomponent.vue'),
            },
            {
                path: 'blog',
                name: 'Blog',
                component: () => import('@/components/admin/blogpostscomponent.vue'),
            },
            {
                path: 'testimonials',
                name: 'Testimonials',
                component: () => import('@/components/admin/testimonials.vue'),
            },
            {
                path: 'inquiries',
                name: 'Inquiries',
                component: () => import('@/components/admin/contactenquiriescomponent.vue'),
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const isAuthenticated = api.isAuthenticated();

    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/admin/login');
    } else if (to.meta.requiresGuest && isAuthenticated) {
        next('/admin/dashboard');
    } else {
        next();
    }
});

export default router;