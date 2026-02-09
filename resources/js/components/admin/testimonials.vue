<template>
    <div class="testimonials-page">
        <div class="page-header">
            <h2>Testimonials</h2>
            <el-button type="primary" icon="el-icon-plus" @click="showDialog()">Add Testimonial</el-button>
        </div>
        <el-card>
            <el-table :data="testimonials" v-loading="loading">
                <el-table-column prop="client_name" label="Client" />
                <el-table-column prop="client_company" label="Company" />
                <el-table-column prop="rating" label="Rating" width="100">
                    <template #default="{ row }">
                        <el-rate v-model="row.rating" disabled />
                    </template>
                </el-table-column>
                <el-table-column label="Actions" width="180">
                    <template #default="{ row }">
                        <el-button size="small" @click="showDialog(row)">Edit</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
        <!-- Add dialog similar to Services -->
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import api from '@/services/api';

export default defineComponent({
    name: 'Testimonials',
    setup() {
        const loading = ref(false);
        const testimonials = ref([]);
        const dialogVisible = ref(false);

        const fetchTestimonials = async () => {
            loading.value = true;
            try {
                const { data } = await api.get('/admin/testimonials');
                testimonials.value = data.data;
            } finally {
                loading.value = false;
            }
        };

        const showDialog = (item?: any) => {
            dialogVisible.value = true;
        };

        const handleDelete = async (id: number) => {
            // Similar to Services
        };

        onMounted(fetchTestimonials);

        return { loading, testimonials, dialogVisible, showDialog, handleDelete };
    },
});
</script>

<style scoped>
.testimonials-page {
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}
</style>