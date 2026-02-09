import { defineAsyncComponent, App } from 'vue'

const components = {
    "home-component": defineAsyncComponent(() => import('./components/HomeComponent.vue')),
    "portfolio-component": defineAsyncComponent(() => import('./components/PortfolioComponent.vue')),
    "about-component": defineAsyncComponent(() => import('./components/AboutComponent.vue')),
    "contact-component": defineAsyncComponent(() => import('./components/ContactComponent.vue')),
    "navigation-component": defineAsyncComponent(() => import('./components/NavigationComponent.vue')),
    "footer-component":defineAsyncComponent(()=>import('./components/AppFooterComponent.vue')),
    "app":defineAsyncComponent(()=>import('./app.vue')),
    //admin components
    "login-component": defineAsyncComponent(() => import('@/components/admin/logincomponent.vue')),
    "admin-layout-component": defineAsyncComponent(() => import('@/components/admin/adminLayoutComponent.vue')),
    "dashboard-component": defineAsyncComponent(() => import('@/components/admin/dashboardcomponent.vue')),
    "projects-component": defineAsyncComponent(() => import('@/components/admin/projectcomponent.vue')),
    "services-component": defineAsyncComponent(() => import('@/components/admin/servicescomponent.vue')),
    "team-component": defineAsyncComponent(() => import('@/components/admin/teammembercomponent.vue')),
    "blog-component": defineAsyncComponent(() => import('@/components/admin/blogpostscomponent.vue')),
    "testimonials-component": defineAsyncComponent(() => import('@/components/admin/testimonials.vue')),
    "inquiries-component": defineAsyncComponent(() => import('@/components/admin/contactenquiriescomponent.vue')),
}







export const registerComponents = (app: App<Element>) => {
    Object.entries(components).forEach(([name, component]) => {
        app.component(name, component)
    })
}
