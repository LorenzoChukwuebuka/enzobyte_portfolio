<template>
    <!-- NAVIGATION -->
    <nav class="fixed top-0 w-full bg-[#0a0a0f]/95 backdrop-blur border-b border-white/5 z-50 py-6">
        <div class="max-w-[1400px] mx-auto px-8 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <!-- <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-md"></div> -->

                <div class="flex items-center justify-center">
                    <a href="/">
                        <img src="/storage/images/enzobyte_logo.png" alt="Enzobyte Logo"
                            class="object-cover rounded-md h-8 w-8 lg:h-8 lg:w-8" loading="lazy" />
                    </a>
                </div>

                <span
                    class="text-2xl font-bold bg-gradient-to-br from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                    ENZOBYTE
                </span>
            </div>

            <ul class="flex gap-10 text-white/70 text-sm font-medium">
                <li v-for="link in navLinks" :key="link.href">
                    <a :href="link.disabled ? null : link.href" :class="[
                        'transition',
                        link.disabled
                            ? 'pointer-events-none opacity-50 cursor-not-allowed'
                            : 'hover:text-cyan-400'
                    ]">
                        {{ link.name }}
                    </a>
                </li>
            </ul>

        </div>
    </nav>

</template>

<script setup>
import { ref, computed } from 'vue'

// Get current path
const currentPath = window.location.pathname

// Define nav links
const navLinks = computed(() => [
    { name: 'Home', href: '/' },
    {
        name: 'Services',
        href: '#services',
        // disable if not on home
        disabled: currentPath !== '/'
    },
    { name: 'Projects', href: '/projects' },
    { name: 'About', href: '/about' },
    { name: 'Contact', href: '/contact' },
])
</script>
