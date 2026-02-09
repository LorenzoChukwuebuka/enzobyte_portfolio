<template>
    <div class="services-page">
        <div class="page-header">
            <h2>Services</h2>
            <el-button type="primary" icon="el-icon-plus" @click="showDialog()">Add Service</el-button>
        </div>

        <el-card>
            <el-table :data="services" v-loading="loading">
                <el-table-column label="Icon" width="80">
                    <template #default="{ row }">
                        <el-avatar v-if="row.icon_url" :src="row.icon_url" shape="square" />
                        <el-icon v-else>
                            <Document />
                        </el-icon>
                    </template>
                </el-table-column>
                <el-table-column prop="title" label="Title" />
                <el-table-column prop="pricing_model" label="Pricing" width="120" />
                <el-table-column label="Status" width="100">
                    <template #default="{ row }">
                        <el-tag :type="row.is_active ? 'success' : 'info'">
                            {{ row.is_active ? 'Active' : 'Inactive' }}
                        </el-tag>
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

        <el-dialog v-model="dialogVisible" title="Service" width="700px" @close="resetForm">
            <el-form ref="formRef" :model="form" label-width="150px">
                <el-form-item label="Title" prop="title">
                    <el-input v-model="form.title" />
                </el-form-item>
                <el-form-item label="Description">
                    <el-input v-model="form.short_description" type="textarea" :rows="3" />
                </el-form-item>
                <el-form-item label="Icon">
                    <el-upload :auto-upload="false" :on-change="handleIconChange" :show-file-list="false"
                        accept="image/*">
                        <el-button size="small">Choose Icon</el-button>
                    </el-upload>
                    <div v-if="iconPreview" style="margin-top: 10px;">
                        <img :src="iconPreview" style="max-width: 100px; max-height: 100px;" />
                    </div>
                </el-form-item>
                <el-form-item label="Pricing Model">
                    <el-select v-model="form.pricing_model">
                        <el-option label="Fixed" value="fixed" />
                        <el-option label="Hourly" value="hourly" />
                        <el-option label="Project Based" value="project_based" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Active">
                    <el-switch v-model="form.is_active" />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="dialogVisible = false">Cancel</el-button>
                <el-button type="primary" @click="handleSave">Save</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Document } from '@element-plus/icons-vue';
import api from '@/services/api';

const loading = ref(false);
const dialogVisible = ref(false);
const formRef = ref();
const services = ref([]);
const editingItem = ref(null);
const iconFile = ref<File | null>(null);
const iconPreview = ref('');

const form = reactive({
    title: '',
    short_description: '',
    pricing_model: 'project_based',
    is_active: true,
});

const fetchServices = async () => {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/services');
        services.value = data.data;
    } finally {
        loading.value = false;
    }
};

const handleIconChange = (file: any) => {
    iconFile.value = file.raw;
    const reader = new FileReader();
    reader.onload = (e) => {
        iconPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file.raw);
};

const showDialog = (item?: any) => {
    if (item) {
        editingItem.value = item;
        Object.assign(form, item);
        iconPreview.value = item.icon_url || '';
    }
    dialogVisible.value = true;
};

const resetForm = () => {
    editingItem.value = null;
    iconFile.value = null;
    iconPreview.value = '';
    Object.assign(form, {
        title: '',
        short_description: '',
        pricing_model: 'project_based',
        is_active: true,
    });
};

const handleSave = async () => {
    try {
        const formData = new FormData();

        Object.keys(form).forEach(key => {
            //@ts-ignore
            if (form[key] !== null && form[key] !== undefined) {
                //@ts-ignore
                formData.append(key, form[key]);
            }
        });

        if (iconFile.value) {
            formData.append('icon', iconFile.value);
        }

        if (editingItem.value) {
            //@ts-ignore
            await api.post(`/admin/services/${editingItem.value.id}?_method=PUT`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            ElMessage.success('Updated successfully');
        } else {
            await api.post('/admin/services', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            ElMessage.success('Created successfully');
        }
        dialogVisible.value = false;
        fetchServices();
    } catch (error) {
        ElMessage.error('Failed to save');
    }
};

const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('Delete this service?', 'Confirm');
        await api.delete(`/admin/services/${id}`);
        ElMessage.success('Deleted successfully');
        fetchServices();
    } catch (error) { }
};

onMounted(fetchServices);
</script>

<style scoped>
.services-page {
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}
</style>