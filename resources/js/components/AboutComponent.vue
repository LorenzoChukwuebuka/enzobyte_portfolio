<script setup>
const stats = [
    { number: "150+", label: "Projects Completed" },
    { number: "50+", label: "Happy Clients" },
    { number: "98%", label: "Satisfaction Rate" }
]

const values = [
    {
        title: "Excellence",
        desc: "We strive for excellence in every project, ensuring the highest quality standards and attention to detail."
    },
    {
        title: "Collaboration",
        desc: "We believe in the power of teamwork and work closely with our clients as partners in success."
    },
    {
        title: "Innovation",
        desc: "We embrace cutting-edge technologies and innovative approaches to solve complex challenges."
    }
]

// const team = [
//     {
//         name: "Sarah Johnson",
//         role: "Chief Technology Officer",
//         bio: "Full-stack architect with 12+ years in enterprise solutions."
//     },
//     {
//         name: "Michael Chen",
//         role: "Lead UI/UX Designer",
//         bio: "Award-winning designer specializing in user-centered design."
//     },
//     {
//         name: "Emily Rodriguez",
//         role: "Mobile Development Lead",
//         bio: "Expert in React Native and Flutter with 50+ apps shipped."
//     },
//     {
//         name: "David Kim",
//         role: "DevOps Engineer",
//         bio: "Cloud infrastructure specialist with AWS & Azure certifications."
//     }
// ]

const timeline = [
    {
        year: "2024",
        title: "Company Founded",
        desc: "Enzobyte Technology was officially registered, marking the beginning of our journey to deliver quality digital solutions."
    },
    {
        year: "2024",
        title: "First Client Projects",
        desc: "Successfully completed initial web development projects, establishing our foundation in the market."
    },
    {
        year: "2025",
        title: "Service Expansion",
        desc: "Expanded our service offerings to include mobile app development and UI/UX design solutions."
    },
    {
        year: "2026",
        title: "Continuous Growth",
        desc: "Building strong client relationships and focusing on delivering innovative solutions that drive business success."
    }
]
</script>

<template>
    <div class="bg-[#0a0a0f] text-white overflow-x-hidden">
         <navigation-component></navigation-component>

        <!-- HERO -->
        <section class="pt-[140px] pb-24 bg-gradient-to-b from-[#0a0a0f] to-[#0f0f1a]">
            <div class="max-w-[1400px] mx-auto px-8 grid lg:grid-cols-2 gap-16 items-center">

                <div>
                    <h1 class="text-5xl font-extrabold leading-tight mb-6">
                        We Build
                        <span class="bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                            Digital Solutions
                        </span>
                        That Matter
                    </h1>

                    <p class="text-white/60 text-lg leading-8 mb-6">
                        Enzobyte Technology is a forward-thinking digital agency focused on transforming businesses
                        through innovative technology solutions.
                    </p>

                    <p class="text-white/60 text-lg leading-8">
                        Our team blends technical excellence with creative thinking to deliver impactful digital
                        experiences.
                    </p>

                    <div class="grid grid-cols-3 gap-8 mt-12">
                        <div v-for="s in stats" :key="s.label">
                            <div
                                class="text-4xl font-extrabold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                                {{ s.number }}
                            </div>
                            <div class="text-white/50 text-sm mt-1">
                                {{ s.label }}
                            </div>
                        </div>
                    </div>

                </div>

                <!-- VISUAL -->
                <div class="hidden lg:block">
                    <div
                        class="h-[500px] flex items-center justify-center border border-white/5 rounded-2xl bg-gradient-to-br from-[#1a1a2e] to-[#2a2a4e] p-12 overflow-hidden">
                       <img src="https://api.iconify.design/simple-icons/react.svg?color=%2361dafb" 
                             alt="Technology Visualization" 
                             class="w-full h-full object-contain opacity-20 animate-pulse"
                             style="filter: drop-shadow(0 0 40px rgba(34, 211, 238, 0.3));">
                    </div>
                </div>

            </div>
        </section>

        <!-- VALUES -->
        <section class="py-24">
            <div class="max-w-[1400px] mx-auto px-8">

                <div class="text-center mb-16">
                    <div class="text-cyan-400 uppercase tracking-widest text-sm mb-3">Our Values</div>
                    <h2 class="text-4xl font-extrabold">What Drives Us</h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="v in values" :key="v.title"
                        class="bg-white/[0.02] border border-white/5 rounded-2xl p-10 text-center transition hover:-translate-y-2 hover:border-cyan-400/20">

                        <div
                            class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20" />

                        <h3 class="text-xl font-semibold mb-3">
                            {{ v.title }}
                        </h3>

                        <p class="text-white/50 leading-7">
                            {{ v.desc }}
                        </p>

                    </div>
                </div>

            </div>
        </section>

        <!-- TEAM SECTION - COMMENTED OUT -->
        <!-- <section class="py-24 bg-gradient-to-b from-[#0a0a0f] to-[#0f0f1a]">
            <div class="max-w-[1400px] mx-auto px-8">

                <div class="text-center mb-16">
                    <div class="text-cyan-400 uppercase tracking-widest text-sm mb-3">Our Team</div>
                    <h2 class="text-4xl font-extrabold">Meet the Experts</h2>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="member in team" :key="member.name"
                        class="bg-white/[0.02] border border-white/5 rounded-2xl overflow-hidden transition hover:-translate-y-2 hover:border-cyan-400/20">

                        <div class="h-[280px] bg-gradient-to-br from-[#1a1a2e] to-[#2a2a4e]" />

                        <div class="p-6">
                            <h3 class="font-semibold text-lg mb-1">
                                {{ member.name }}
                            </h3>

                            <p class="text-cyan-400 text-sm mb-3">
                                {{ member.role }}
                            </p>

                            <p class="text-white/50 text-sm leading-6">
                                {{ member.bio }}
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </section> -->

        <!-- TIMELINE -->
        <section class="py-24 bg-gradient-to-b from-[#0a0a0f] to-[#0f0f1a]">
            <div class="max-w-[900px] mx-auto px-8 relative">

                <div class="text-center mb-16">
                    <div class="text-cyan-400 uppercase tracking-widest text-sm mb-3">Our Journey</div>
                    <h2 class="text-4xl font-extrabold">Company Timeline</h2>
                </div>

                <div
                    class="absolute left-1/2 top-0 bottom-0 w-[2px] bg-gradient-to-b from-cyan-400 to-blue-500 -translate-x-1/2" />

                <div v-for="(item, i) in timeline" :key="item.year + item.title" class="grid md:grid-cols-2 gap-10 mb-16 relative">

                    <div :class="i % 2 === 1 ? 'md:order-2 text-right' : ''"
                        class="text-4xl font-extrabold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent self-center">
                        {{ item.year }}
                    </div>

                    <div class="bg-white/[0.02] border border-white/5 rounded-xl p-8">
                        <h3 class="text-lg font-semibold mb-2">
                            {{ item.title }}
                        </h3>
                        <p class="text-white/50 leading-7">
                            {{ item.desc }}
                        </p>
                    </div>

                    <div
                        class="absolute left-1/2 top-1/2 w-4 h-4 bg-cyan-400 rounded-full border-4 border-[#0a0a0f] -translate-x-1/2 -translate-y-1/2" />

                </div>

            </div>
        </section>
<footer-component></footer-component>
    </div>
</template>