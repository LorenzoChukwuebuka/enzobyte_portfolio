<script setup>
import api from "@/services/api"
import { ref, computed, onMounted } from "vue"

const activeFilter = ref("all")
const loading = ref(false)
const projects = ref([])

const getProjects = async () => {
    loading.value = true
    try {
        let res = await api.get("/projects")
        projects.value = res.data.data
    } catch (error) {
        console.log(error)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    getProjects()
})

const filters = [
    { label: "All Projects", value: "all" },
    { label: "Web Development", value: "web_development" },
    { label: "Mobile Apps", value: "mobile_app" },
    { label: "UI/UX Design", value: "ui_ux_design" },
    { label: "Branding", value: "branding" },
    { label: "E-Commerce", value: "e_commerce" }
]

const filteredProjects = computed(() => {
    if (activeFilter.value === "all") return projects.value
    return projects.value.filter(
        p => p.category === activeFilter.value
    )
})

// Helper function to format duration
const formatDuration = (days) => {
    if (days < 30) return `${days} days`
    const months = Math.floor(days / 30)
    return `${months} month${months > 1 ? 's' : ''}`
}

// Helper function to format date
const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
}

const navigate = ()=>{
    return location.replace("/contact")
}

</script>

<template>
    <div class="bg-[#0a0a0f] text-white">
        <navigation-component></navigation-component>

        <!-- HEADER -->
        <section class="pt-[140px] pb-20 text-center bg-gradient-to-b from-[#0a0a0f] to-[#0f0f1a]">
            <h1
                class="text-5xl font-extrabold mb-4 bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
                Our Portfolio
            </h1>
            <p class="text-white/50 max-w-xl mx-auto text-lg">
                Showcasing successful projects that drive real business results
            </p>
        </section>

        <!-- FILTER -->
        <section class="py-12 border-b border-white/5">
            <div class="max-w-[1400px] mx-auto px-8 flex flex-wrap justify-center gap-4">
                <el-button v-for="tab in filters" :key="tab.value" @click="activeFilter = tab.value" round
                    class="!bg-transparent !border !border-white/10 !text-white/60 hover:!text-white" :class="{
                        '!border-cyan-400 !text-cyan-400 !bg-cyan-400/10':
                            activeFilter === tab.value
                    }">
                    {{ tab.label }}
                </el-button>
            </div>
        </section>

        <!-- PROJECT GRID -->
        <section class="py-20">
            <div class="max-w-[1400px] mx-auto px-8">

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-20">
                    <el-icon class="is-loading text-4xl text-cyan-400">
                        <Loading />
                    </el-icon>
                    <p class="text-white/60 mt-4">Loading projects...</p>
                </div>

                <!-- Empty State -->
                <div v-else-if="filteredProjects.length === 0" class="text-center py-20">
                    <p class="text-white/60 text-lg">No projects found in this category.</p>
                </div>

                <!-- Projects Grid -->
                <div v-else class="grid gap-10 [grid-template-columns:repeat(auto-fill,minmax(450px,1fr))]">
                    <div v-for="project in filteredProjects" :key="project.id" class="group bg-white/[0.02] border border-white/5 rounded-2xl
                            overflow-hidden transition duration-300
                            hover:-translate-y-2
                            hover:border-cyan-400/20
                            hover:shadow-[0_20px_60px_rgba(0,245,255,0.1)]">

                        <!-- PROJECT IMAGE -->
                        <div class="relative h-[320px] bg-gradient-to-br from-[#16213e] to-[#0f3460] overflow-hidden">
                            <img v-if="project.thumbnail_url" :src="project.thumbnail_url" :alt="project.title"
                                class="w-full h-full object-cover" />

                            <div class="absolute inset-0
                                bg-gradient-to-b
                                from-transparent
                                to-[#0a0a0f]/90
                                opacity-0
                                group-hover:opacity-100
                                transition
                                flex items-end p-8">
                                <a v-if="project.project_url" :href="project.project_url" target="_blank"
                                    class="text-cyan-400 font-semibold hover:text-cyan-300">
                                    View Project →
                                </a>
                                <span v-else class="text-cyan-400 font-semibold">
                                    View Case Study →
                                </span>
                            </div>
                        </div>

                        <!-- CONTENT -->
                        <div class="p-8">
                            <!-- Tags/Technologies -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span v-for="tech in project.technologies" :key="tech" class="px-3 py-1 text-xs rounded-full
                                        bg-cyan-400/10
                                        border border-cyan-400/20
                                        text-cyan-400">
                                    {{ tech }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-2xl font-semibold mb-2">
                                {{ project.title }}
                            </h3>

                            <!-- Client Name -->
                            <p v-if="project.client_name" class="text-white/40 text-sm mb-3">
                                {{ project.client_name }}
                            </p>

                            <!-- Description -->
                            <p class="text-white/60 leading-7 mb-6">
                                {{ project.description }}
                            </p>

                            <!-- Stats -->
                            <div class="flex gap-8 pt-6 border-t border-white/5">
                                <div class="flex flex-col">
                                    <span class="text-xs text-white/40 uppercase tracking-wider">
                                        Duration
                                    </span>
                                    <span class="text-cyan-400 font-semibold">
                                        {{ formatDuration(project.duration_days) }}
                                    </span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-xs text-white/40 uppercase tracking-wider">
                                        Completed
                                    </span>
                                    <span class="text-cyan-400 font-semibold">
                                        {{ formatDate(project.completion_date) }}
                                    </span>
                                </div>

                                <div v-if="project.featured" class="flex flex-col">
                                    <span class="text-xs text-white/40 uppercase tracking-wider">
                                        Status
                                    </span>
                                    <span class="text-cyan-400 font-semibold">
                                        Featured ⭐
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-24 text-center bg-gradient-to-br from-[#0f0f1a] to-[#1a1a2e]">
            <h2 class="text-4xl font-bold mb-6">
                Ready to Start Your Project?
            </h2>
            <p class="text-white/60 mb-10 max-w-2xl mx-auto">
                Let's collaborate to bring your vision to life with cutting-edge technology and expert craftsmanship.
            </p>

            <el-button type="primary" @click="navigate" size="large" class="!px-10 !py-6 !rounded-lg
                    !bg-gradient-to-r
                    from-cyan-400 to-blue-500
                    !border-0">
                Get in Touch
            </el-button>
        </section>
         <footer-component></footer-component>
    </div>
</template>