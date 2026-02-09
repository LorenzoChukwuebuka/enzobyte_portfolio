<template>
    <div class="bg-[#0a0a0f] text-white overflow-x-hidden">

        <navigation-component></navigation-component>

        <!-- HERO -->
        <section class="mt-20 min-h-[90vh] flex items-center relative overflow-hidden">
            <div class="absolute w-[600px] h-[600px] bg-cyan-400/15 blur-[80px] rounded-full top-[10%] -right-[10%]">
            </div>
            <div class="absolute w-[600px] h-[600px] bg-blue-500/15 blur-[80px] rounded-full bottom-[20%] -left-[10%]">
            </div>

            <div class="max-w-[1400px] mx-auto px-8 relative z-10">
                <div class="max-w-[700px]">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-cyan-400/20 bg-cyan-400/10 text-cyan-400 text-sm mb-8">
                        ● Available for new projects
                    </div>

                    <h1
                        class="text-6xl font-extrabold leading-tight mb-6 bg-gradient-to-br from-white to-neutral-400 bg-clip-text text-transparent">
                        Building
                        <span class="bg-gradient-to-br from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                            Digital Excellence
                        </span>
                        for Modern Businesses
                    </h1>

                    <p class="text-xl text-white/60 leading-8 mb-10">
                        We craft cutting-edge web and mobile solutions that transform ideas
                        into powerful digital experiences. Your vision, our expertise.
                    </p>

                    <div class="flex gap-4">
                        <el-button type="primary" size="large"
                            class="!bg-gradient-to-br !from-cyan-400 !to-blue-500 !border-0 !text-white">
                            Start Your Project
                        </el-button>

                        <el-button size="large"
                            class="!bg-transparent !border-white/10 !text-white hover:!border-cyan-400">
                            View Our Work
                        </el-button>
                    </div>
                </div>
            </div>
        </section>

        <!-- SERVICES -->
        <section id="services" class="py-32 bg-gradient-to-b from-[#0a0a0f] to-[#0f0f1a]">
            <div class="max-w-[1400px] mx-auto px-8">

                <!-- HEADER -->
                <div class="text-center mb-16">
                    <p class="text-cyan-400 uppercase tracking-[2px] text-sm font-semibold mb-4">
                        WHAT WE DO
                    </p>
                    <h2 class="text-5xl font-extrabold mb-4">Our Services</h2>
                    <p class="text-white/50 text-lg">
                        Comprehensive digital solutions tailored to elevate your business
                    </p>
                </div>

                <!-- GRID -->
                <div class="grid gap-8
                md:grid-cols-2
                lg:grid-cols-3">

                    <div v-for="service in services" :key="service.title" class="bg-white/[0.02] border border-white/5 rounded-2xl p-10
               hover:bg-white/[0.04]
               hover:border-cyan-400/20
               transition duration-300">
                        <div class="w-16 h-16 mb-6 text-cyan-400">
                            <component :is="service.icon" />
                        </div>

                        <h3 class="text-2xl font-semibold mb-4">
                            {{ service.title }}
                        </h3>

                        <p class="text-white/50 leading-7">
                            {{ service.description }}
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- PROJECTS -->
        <section id="projects" class="py-32 bg-[#0a0a0f]">
            <div class="max-w-[1400px] mx-auto px-8">

                <!-- HEADER -->
                <div class="text-center mb-16">
                    <p class="text-cyan-400 uppercase tracking-[2px] text-sm font-semibold mb-4">
                        OUR WORK
                    </p>
                    <h2 class="text-5xl font-extrabold mb-4">Featured Projects</h2>
                    <p class="text-white/50 text-lg">
                        Explore our latest work and see how we've helped businesses succeed
                    </p>
                </div>

                <!-- LOADING STATE -->
                <div v-if="loading" class="flex justify-center items-center py-20">
                    <el-icon class="is-loading text-cyan-400 text-4xl">
                        <Loading />
                    </el-icon>
                </div>

                <!-- EMPTY STATE -->
                <div v-else-if="projects.length === 0" class="text-center py-20">
                    <p class="text-white/50 text-lg">No featured projects available at the moment.</p>
                </div>

                <!-- GRID -->
                <div v-else class="grid gap-8 md:grid-cols-2">

                    <div v-for="project in projects" :key="project.id" class="bg-white/[0.02] border border-white/5 rounded-2xl overflow-hidden
               hover:-translate-y-1 transition duration-300">
                        <div class="h-[300px] bg-gradient-to-br from-[#1a1a2e] to-[#2a2a4e] relative overflow-hidden">
                            <img v-if="project.thumbnail_url" :src="project.thumbnail_url" :alt="project.title"
                                class="w-full h-full object-cover" />
                        </div>

                        <div class="p-8">
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span v-for="tech in project.technologies" :key="tech"
                                    class="px-3 py-1 text-xs rounded-full border border-cyan-400/20 bg-cyan-400/10 text-cyan-400">
                                    {{ tech }}
                                </span>
                            </div>

                            <h3 class="text-2xl font-semibold mb-2">
                                {{ project.title }}
                            </h3>

                            <p class="text-white/50 leading-7">
                                {{ project.description }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <footer-component></footer-component>
    </div>

</template>

<script setup>
import api from '@/services/api';
import { onMounted, ref } from 'vue';
import { ElMessage } from 'element-plus';

const loading = ref(false);
const projects = ref([]);

const fetchFeaturedProjects = async () => {
    loading.value = true;
    try {
        const { data } = await api.get('/projects/featured');
        projects.value = data.data;
    } catch (error) {
        ElMessage.error('Failed to fetch featured projects');
        console.error('Error fetching projects:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchFeaturedProjects();
});

const services = [
    {
        title: "Web Development",
        description:
            "Custom websites and web applications built with modern frameworks and best practices.",
        features: [
            "Responsive Design",
            "Progressive Web Apps",
            "E-commerce Solutions",
            "CMS Integration",
        ],
    },
    {
        title: "Mobile Development",
        description:
            "Native and cross-platform mobile applications for iOS and Android.",
        features: [
            "iOS & Android Apps",
            "React Native / Flutter",
            "App Store Optimization",
            "Push Notifications",
        ],
    },
    {
        title: "UI/UX Design",
        description:
            "Beautiful, intuitive interfaces that enhance user experience and drive engagement.",
        features: [
            "User Research",
            "Wireframing & Prototyping",
            "Visual Design",
            "Usability Testing",
        ],
    },
    {
        title: "Cloud Solutions",
        description:
            "Scalable cloud infrastructure and DevOps services for modern applications.",
        features: [
            "AWS / Azure / GCP",
            "CI/CD Pipelines",
            "Container Orchestration",
            "Cloud Migration",
        ],
    },
    {
        title: "API Development",
        description:
            "Robust RESTful and GraphQL APIs for seamless integrations.",
        features: [
            "RESTful APIs",
            "GraphQL Services",
            "API Documentation",
            "Third-party Integrations",
        ],
    },
    {
        title: "Tech Consulting",
        description:
            "Strategic technology guidance to accelerate your digital transformation.",
        features: [
            "Technology Strategy",
            "Architecture Review",
            "Code Audits",
            "Team Training",
        ],
    },
];
</script>