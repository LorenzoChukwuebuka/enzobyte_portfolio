<script setup>
import { reactive } from "vue"
import { ElInput, ElButton, ElForm } from "element-plus"

const company = {
    name: "Enzobyte Technology",
    description:
        "We build modern, scalable digital products — from enterprise platforms to beautiful user experiences."
}

const services = [
    { name: "Web Development", link: "#" },
    { name: "Mobile Apps", link: "#" },
    { name: "UI/UX Design", link: "#" },
    { name: "Branding", link: "#" },
    { name: "Consulting", link: "#" }
]

const projects = [
    { name: "Enterprise Dashboard", link: "#" },
    { name: "POS System", link: "#" },
    { name: "School Management", link: "#" },
    { name: "Email Platform", link: "#" }
]

const quickLinks = [
    { name: "About", link: "/about" },
    { name: "Portfolio", link: "/portfolio" },
    { name: "Contact", link: "/contact" }
]

const socials = [
    { name: "Twitter", icon: "𝕏", link: "#" },
    { name: "LinkedIn", icon: "in", link: "#" },
    { name: "GitHub", icon: "GH", link: "#" }
]

const newsletter = reactive({
    email: ""
})

function subscribe() {
    console.log("Subscribe:", newsletter.email)
    newsletter.email = ""
}
</script>

<template>
    <footer class="bg-[#06060a] border-t border-white/5 mt-24">

        <div class="max-w-[1400px] mx-auto px-8 py-16">

            <!-- GRID -->
            <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-5">

                <!-- BRAND -->
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-bold text-white mb-4">
                        {{ company.name }}
                    </h2>

                    <p class="text-white/50 leading-7 mb-6">
                        {{ company.description }}
                    </p>

                    <div class="flex gap-3">
                        <a v-for="s in socials" :key="s.name" :href="s.link" class="w-10 h-10 flex items-center justify-center
rounded-lg bg-white/5 hover:bg-cyan-400/20
transition text-sm">
                            {{ s.icon }}
                        </a>
                    </div>
                </div>

                <!-- SERVICES -->
                <div>
                    <h3 class="footer-title">Services</h3>
                    <ul class="footer-list">
                        <li v-for="s in services" :key="s.name">
                            <a :href="s.link">{{ s.name }}</a>
                        </li>
                    </ul>
                </div>

                <!-- PROJECTS -->
                <div>
                    <h3 class="footer-title">Projects</h3>
                    <ul class="footer-list">
                        <li v-for="p in projects" :key="p.name">
                            <a :href="p.link">{{ p.name }}</a>
                        </li>
                    </ul>
                </div>

                <!-- NEWSLETTER -->
                <div>
                    <h3 class="footer-title">Newsletter</h3>

                    <el-form @submit.prevent="subscribe" class="space-y-3">
                        <el-input v-model="newsletter.email" placeholder="Your email" size="large" />

                        <el-button type="primary" class="w-full bg-cyan-500 border-none">
                            Subscribe
                        </el-button>
                    </el-form>

                </div>

            </div>

        </div>

        <!-- BOTTOM BAR -->
        <div class="border-t border-white/5 py-6 text-center text-white/40 text-sm">
            © {{ new Date().getFullYear() }} Enzobyte Technology. All rights reserved.
        </div>

    </footer>
</template>

<style scoped>
@reference "tailwindcss";
.footer-title {
    @apply text-white font-semibold mb-4;
}

.footer-list {
    @apply space-y-2 text-white/50 text-sm;
}

.footer-list a {
    @apply hover:text-cyan-400 transition;
}
</style>
