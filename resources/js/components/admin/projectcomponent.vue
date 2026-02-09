<template>
    <div class="projects-page">
        <div class="page-header">
            <h2>Projects</h2>
            <el-button type="primary" icon="el-icon-plus" @click="showDialog()">
                Add Project
            </el-button>
        </div>

        <el-card>
            <el-table :data="projects" v-loading="loading" style="width: 100%">
                <el-table-column prop="thumbnail_url" label="Thumbnail" width="100">
                    <template #default="{ row }">
                        <el-image v-if="row.thumbnail_url" :src="row.thumbnail_url" fit="cover"
                            style="width: 60px; height: 60px; border-radius: 4px" />
                    </template>
                </el-table-column>

                <el-table-column prop="title" label="Title" />

                <el-table-column prop="category" label="Category" width="150">
                    <template #default="{ row }">
                        <el-tag>{{ formatCategory(row.category) }}</el-tag>
                    </template>
                </el-table-column>

                <el-table-column prop="client_name" label="Client" width="150" />

                <el-table-column label="Status" width="120">
                    <template #default="{ row }">
                        <el-tag :type="row.is_published ? 'success' : 'info'">
                            {{ row.is_published ? 'Published' : 'Draft' }}
                        </el-tag>
                    </template>
                </el-table-column>

                <el-table-column label="Featured" width="100" align="center">
                    <template #default="{ row }">
                        <i v-if="row.featured" class="el-icon-star-on" style="color: #f7ba2a; font-size: 18px"></i>
                    </template>
                </el-table-column>

                <el-table-column label="Actions" width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="showDialog(row)">Edit</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div class="pagination">
                <el-pagination v-model:current-page="currentPage" :page-size="20" :total="total"
                    layout="total, prev, pager, next" @current-change="fetchProjects" />
            </div>
        </el-card>

        <!-- Dialog -->
        <el-dialog v-model="dialogVisible" :title="editingProject ? 'Edit Project' : 'Add Project'" width="800px"
            @close="resetForm">
            <el-form ref="formRef" :model="form" :rules="rules" label-width="150px">
                <el-form-item label="Title" prop="title">
                    <el-input v-model="form.title" />
                </el-form-item>

                <el-form-item label="Description" prop="description">
                    <el-input v-model="form.description" type="textarea" :rows="3" />
                </el-form-item>

                <el-form-item label="Full Description">
                    <el-input v-model="form.full_description" type="textarea" :rows="5" />
                </el-form-item>

                <el-form-item label="Client Name">
                    <el-input v-model="form.client_name" />
                </el-form-item>

                <el-form-item label="Project URL">
                    <el-input v-model="form.project_url" />
                </el-form-item>

                <el-form-item label="Category" prop="category">
                    <el-select v-model="form.category" style="width: 100%">
                        <el-option label="Web Development" value="web_development" />
                        <el-option label="Mobile App" value="mobile_app" />
                        <el-option label="UI/UX Design" value="ui_ux_design" />
                        <el-option label="Branding" value="branding" />
                        <el-option label="Consulting" value="consulting" />
                        <el-option label="Other" value="other" />
                    </el-select>
                </el-form-item>

                <el-form-item label="Thumbnail">
                    <el-upload class="upload-demo" :auto-upload="false" :on-change="handleThumbnailChange"
                        :on-remove="handleThumbnailRemove" :file-list="thumbnailFileList" :limit="1" accept="image/*"
                        list-type="picture-card">
                        <el-icon>
                            <Plus />
                        </el-icon>
                    </el-upload>
                    <el-image v-if="!thumbnailFileList.length && form.thumbnail_url" :src="form.thumbnail_url"
                        fit="cover" style="width: 100px; height: 100px; margin-top: 10px" />
                </el-form-item>

                <el-form-item label="Gallery">
                    <el-upload class="upload-demo" :auto-upload="false" :on-change="handleGalleryChange"
                        :on-remove="handleGalleryRemove" :file-list="galleryFileList" multiple accept="image/*"
                        list-type="picture-card">
                        <el-icon>
                            <Plus />
                        </el-icon>
                    </el-upload>
                    <!-- Show existing gallery when editing -->
                    <div v-if="!galleryFileList.length && form.gallery_urls?.length"
                        style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
                        <el-image v-for="(url, index) in form.gallery_urls" :key="index" :src="url" fit="cover"
                            style="width: 100px; height: 100px; border-radius: 4px;" />
                    </div>
                </el-form-item>

                <el-form-item label="Technologies">
                    <el-select v-model="form.technologies" multiple filterable allow-create style="width: 100%"
                        placeholder="Add technologies">
                        <el-option v-for="(tech, index) in technologies" :key="index" :label="tech" :value="tech" />
                    </el-select>
                </el-form-item>

                <el-form-item label="Completion Date">
                    <el-date-picker v-model="form.completion_date" type="date" style="width: 100%" />
                </el-form-item>

                <el-form-item label="Duration (days)">
                    <el-input-number v-model="form.duration_days" :min="1" />
                </el-form-item>

                <el-form-item label="Featured">
                    <el-switch v-model="form.featured" />
                </el-form-item>

                <el-form-item label="Published">
                    <el-switch v-model="form.is_published" />
                </el-form-item>
            </el-form>

            <template #footer>
                <el-button @click="dialogVisible = false">Cancel</el-button>
                <el-button type="primary" :loading="saving" @click="handleSave">Save</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus } from '@element-plus/icons-vue';
import api from '@/services/api';
import { Project } from '../../@types/index';

const loading = ref(false);
const saving = ref(false);
const dialogVisible = ref(false);
const formRef = ref();
const projects = ref<Project[]>([]);
const editingProject = ref<Project | null>(null);
const currentPage = ref(1);
const total = ref(0);
const thumbnailFile = ref<File | null>(null);
const thumbnailFileList = ref<any[]>([]);
const galleryFiles = ref<File[]>([]);
const galleryFileList = ref<any[]>([]);

const technologies = [
    "Laravel",
    "VueJS",
    "ReactJS", "ExpressJS", "NodeJS", "NestJS", "PHP", "MySQL", "PostgreSQL", "Docker", "Linux", "MongoDB", "Gorm", "REST", "gRPC"
];

const form = reactive<Partial<Project>>({
    title: '',
    description: '',
    full_description: '',
    client_name: '',
    project_url: '',
    category: 'web_development',
    technologies: [],
    completion_date: '',
    duration_days: undefined,
    featured: false,
    is_published: true,
});

const rules = {
    title: [{ required: true, message: 'Please enter title', trigger: 'blur' }],
    description: [{ required: true, message: 'Please enter description', trigger: 'blur' }],
    category: [{ required: true, message: 'Please select category', trigger: 'change' }],
};

const fetchProjects = async () => {
    loading.value = true;
    try {
        const { data } = await api.get(`/admin/projects?page=${currentPage.value}`);
        projects.value = data.data;
        total.value = data.total;
    } catch (error) {
        ElMessage.error('Failed to fetch projects');
    } finally {
        loading.value = false;
    }
};

const showDialog = (project?: Project) => {
    if (project) {
        editingProject.value = project;
        Object.assign(form, project);
    }
    dialogVisible.value = true;
};

const resetForm = () => {
    editingProject.value = null;
    thumbnailFile.value = null;
    thumbnailFileList.value = [];
    galleryFiles.value = [];
    galleryFileList.value = [];
    Object.assign(form, {
        title: '',
        description: '',
        full_description: '',
        client_name: '',
        project_url: '',
        category: 'web_development',
        technologies: [],
        completion_date: '',
        duration_days: undefined,
        featured: false,
        is_published: true,
    });
    formRef.value?.resetFields();
};

const handleThumbnailChange = (uploadFile: any, uploadFiles: any[]) => {
    console.log('Thumbnail changed:', uploadFile);

    if (uploadFile.raw) {
        thumbnailFile.value = uploadFile.raw;
        console.log('Thumbnail file set:', thumbnailFile.value);
    }
};

const handleThumbnailRemove = () => {
    thumbnailFile.value = null;
    thumbnailFileList.value = [];
};

const handleGalleryChange = (uploadFile: any, uploadFiles: any[]) => {
    console.log('Gallery changed:', uploadFile);
    console.log('All gallery files:', uploadFiles);

    // Extract raw files from uploadFiles
    galleryFiles.value = uploadFiles
        .filter(file => file.raw)
        .map(file => file.raw);

    console.log('Gallery files set:', galleryFiles.value);
};

const handleGalleryRemove = (uploadFile: any, uploadFiles: any[]) => {
    console.log('Gallery file removed:', uploadFile);

    // Update the array after removal
    galleryFiles.value = uploadFiles
        .filter(file => file.raw)
        .map(file => file.raw);

    console.log('Gallery files after removal:', galleryFiles.value);
};

const normalizeValue = (key: string, value: any) => {
    if (value instanceof Date) {
        return value.toISOString().split('T')[0]; // YYYY-MM-DD
    }
    return value;
};

const handleSave = async () => {
    try {
        await formRef.value.validate();
        saving.value = true;

        const formData = new FormData();

        // Append all form fields
        Object.keys(form).forEach((key) => {
            let value = form[key as keyof typeof form];

            if (value === null || value === undefined) return;

            value = normalizeValue(key, value);

            if (Array.isArray(value)) {
                formData.append(key, JSON.stringify(value));
            } else {
                formData.append(key, String(value));
            }
        });

        // Append thumbnail file
        if (thumbnailFile.value) {
            console.log('Appending thumbnail to FormData:', thumbnailFile.value);
            formData.append('thumbnail', thumbnailFile.value);
        } else {
            console.warn('No thumbnail file to append');
        }

        // Append gallery files
        if (galleryFiles.value.length > 0) {
            console.log('Appending gallery files to FormData:', galleryFiles.value);
            galleryFiles.value.forEach((file, index) => {
                formData.append('gallery[]', file);
                console.log(`Gallery file ${index}:`, file);
            });
        } else {
            console.warn('No gallery files to append');
        }

        // Debug: Log FormData contents
        console.log('FormData contents:');
        for (let pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }

        if (editingProject.value) {

            formData.append('_method', 'PUT'); // or 'PATCH'
            await api.post(`/admin/projects/${editingProject.value.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            ElMessage.success('Project updated successfully');
        } else {
            await api.post('/admin/projects', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            ElMessage.success('Project created successfully');
        }

        dialogVisible.value = false;
        fetchProjects();
    } catch (error: any) {
        console.error('Save error:', error);
        ElMessage.error(error.response?.data?.message || 'Failed to save project');
    } finally {
        saving.value = false;
    }
};

const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('Are you sure you want to delete this project?', 'Confirm', {
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            type: 'warning',
        });

        await api.delete(`/admin/projects/${id}`);
        ElMessage.success('Project deleted successfully');
        fetchProjects();
    } catch (error) {
        // User cancelled
    }
};

const formatCategory = (category: string) => {
    return category.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};

onMounted(() => {
    fetchProjects();
});
</script>

<style scoped>
.projects-page {
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.page-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 500;
}

.pagination {
    margin-top: 20px;
    text-align: right;
}
</style>