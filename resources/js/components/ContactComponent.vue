<script setup>
import { reactive, ref } from "vue"

const loading = ref(false)
const message = ref(null)
const messageType = ref("success")

const services = [
    { value: "web_development", label: "Web Development" },
    { value: "mobile_app", label: "Mobile App Development" },
    { value: "ui_ux_design", label: "UI/UX Design" },
    { value: "branding", label: "Branding & Identity" },
    { value: "consulting", label: "Tech Consulting" },
    { value: "maintenance", label: "Maintenance & Support" },
    { value: "other", label: "Other" }
]

const budgets = [
    { value: "not_specified", label: "Prefer not to say" },
    { value: "under_500k", label: "Under ₦500,000" },
    { value: "500k_1m", label: "₦500,000 - ₦1,000,000" },
    { value: "1m_2m", label: "₦1,000,000 - ₦2,000,000" },
    { value: "2m_5m", label: "₦2,000,000 - ₦5,000,000" },
    { value: "5m_plus", label: "₦5,000,000+" }
]

const form = reactive({
    name: "",
    email: "",
    phone: "",
    company: "",
    service_needed: "",
    budget_range: "not_specified",
    message: ""
})

const contactInfo = [
    {
        title: "Email",
        lines: ["hello@enzobyte.tech", "support@enzobyte.tech"]
    },
    {
        title: "Phone",
        lines: ["+234 (813) 451-4639", "Mon-Fri 9:00 AM - 6:00 PM EST"]
    },
    // {
    //     title: "Office",
    //     lines: ["123 Tech Street", "Silicon Valley, CA 94025"]
    // }
]

async function submitForm() {
    loading.value = true
    message.value = null

    try {
        const res = await fetch("/api/contact", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(form)
        })

        if (!res.ok) throw new Error()

        message.value =
            "Thank you for contacting us! We'll get back to you within 24 hours."
        messageType.value = "success"

        Object.keys(form).forEach(k => {
            if (k === "budget_range") form[k] = "not_specified"
            else form[k] = ""
        })
    } catch {
        message.value =
            "Oops! Something went wrong. Please try again or email us directly."
        messageType.value = "error"
    } finally {
        loading.value = false
        setTimeout(() => (message.value = null), 5000)
    }
}
</script>

<template>
    <div class="bg-[#0a0a0f] text-white overflow-x-hidden">
        <navigation-component></navigation-component>

        <!-- HEADER -->
        <section class="pt-[140px] pb-12 text-center bg-gradient-to-b from-[#0a0a0f] to-[#0f0f1a]">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-3">Get In Touch</h1>
            <p class="text-white/50 max-w-xl mx-auto text-base">
                Let's discuss how we can help transform your digital presence
            </p>
        </section>

        <!-- CONTACT -->
        <section class="py-12">
            <div class="max-w-[1400px] mx-auto px-8 grid lg:grid-cols-[1fr_1.5fr] gap-12">

                <!-- INFO -->
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-3">Let's Work Together</h2>
                    <p class="text-white/60 mb-8 leading-relaxed text-sm">
                        Have a project in mind? We'd love to hear about it. Send us a message
                        and we'll get back to you within 24 hours.
                    </p>

                    <div class="space-y-6">
                        <div v-for="info in contactInfo" :key="info.title" class="flex gap-4">

                            <div class="w-10 h-10 rounded-lg bg-cyan-400/10 border border-cyan-400/20 flex-shrink-0" />

                            <div>
                                <h3 class="text-cyan-400 font-semibold mb-1 text-sm">
                                    {{ info.title }}
                                </h3>

                                <p v-for="l in info.lines" :key="l" class="text-white/70 text-sm">
                                    {{ l }}
                                </p>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- FORM -->
                <div class="bg-white/[0.02] border border-white/5 rounded-2xl p-8">

                    <div v-if="message" :class="[
                        'p-3 rounded-lg mb-5 text-sm',
                        messageType === 'success'
                            ? 'bg-green-500/10 border border-green-400/20 text-green-400'
                            : 'bg-red-500/10 border border-red-400/20 text-red-400'
                    ]">
                        {{ message }}
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-4">

                        <div class="grid md:grid-cols-2 gap-4">
                            <input v-model="form.name" required placeholder="Your Name" class="input" />
                            <input v-model="form.email" type="email" required placeholder="Email" class="input" />
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <input v-model="form.phone" placeholder="Phone" class="input" />
                            <input v-model="form.company" placeholder="Company" class="input" />
                        </div>

                        <select v-model="form.service_needed" required class="input">
                            <option value="">Select Service</option>
                            <option v-for="s in services" :key="s.value" :value="s.value">
                                {{ s.label }}
                            </option>
                        </select>

                        <select v-model="form.budget_range" class="input">
                            <option v-for="b in budgets" :key="b.value" :value="b.value">
                                {{ b.label }}
                            </option>
                        </select>

                        <textarea v-model="form.message" required rows="4" placeholder="Project details..."
                            class="input resize-none" />

                        <button :disabled="loading"
                            class="w-full py-3 rounded-lg font-semibold text-sm bg-gradient-to-r from-cyan-400 to-blue-500 hover:-translate-y-1 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ loading ? "Sending..." : "Send Message" }}
                        </button>

                    </form>

                </div>

            </div>
        </section>

        <!-- MAP -->
        <!-- <section class="py-12 bg-gradient-to-b from-[#0a0a0f] to-[#0f0f1a]">
            <div class="max-w-[1400px] mx-auto px-8">
                <div
                    class="h-[350px] rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-center text-white/30 text-sm">
                    Map Placeholder
                </div>
            </div>
        </section> -->

        <!-- MAP -->
        <section class="py-12 bg-gradient-to-b from-[#0a0a0f] to-[#0f0f1a]">
            <div class="max-w-[1400px] mx-auto px-8">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.0!2d5.6037!3d6.3350!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMjAnMDYuMCJOIDXCsDM2JzEzLjMiRQ!5e0!3m2!1sen!2sng!4v1234567890"
                    class="w-full h-[350px] rounded-2xl border border-white/5" style="border:0;" allowfullscreen=""
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </section>

        <footer-component></footer-component>

    </div>
</template>

<style scoped>
@reference "tailwindcss";

.input {
    @apply w-full px-4 py-3 text-sm rounded-lg bg-white/[0.03] border border-white/10 focus:border-cyan-400 focus:outline-none transition-colors placeholder:text-white/40;
}

select.input option {
    @apply bg-[#0a0a0f] text-white;
}
</style>