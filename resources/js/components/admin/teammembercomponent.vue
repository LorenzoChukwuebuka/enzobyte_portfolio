<template>
    <div class="team-page">
        <div class="page-header">
            <h2>Team Members</h2>
            <el-button type="primary" icon="el-icon-plus" @click="showDialog()">
                Add Member
            </el-button>
        </div>

        <el-card>
            <el-table :data="members" v-loading="loading" style="width: 100%">
                <el-table-column prop="photo_url" label="Photo" width="100">
                    <template #default="{ row }">
                        <el-avatar 
                            v-if="row.photo_url" 
                            :src="row.photo_url" 
                            :size="60"
                            style="border: 2px solid #f0f0f0;" />
                        <el-avatar v-else :size="60">
                            {{ row.name?.charAt(0) }}
                        </el-avatar>
                    </template>
                </el-table-column>

                <el-table-column prop="name" label="Name" />
                
                <el-table-column prop="position" label="Position" width="200" />
                
                <el-table-column prop="email" label="Email" width="200" />

                <el-table-column label="Social" width="120" align="center">
                    <template #default="{ row }">
                        <div style="display: flex; gap: 5px; justify-content: center;">
                            <el-icon v-if="row.linkedin_url" color="#0077b5"><i class="fab fa-linkedin"></i></el-icon>
                            <el-icon v-if="row.twitter_url" color="#1da1f2"><i class="fab fa-twitter"></i></el-icon>
                            <el-icon v-if="row.github_url" color="#333"><i class="fab fa-github"></i></el-icon>
                        </div>
                    </template>
                </el-table-column>

                <el-table-column label="Status" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="row.is_active ? 'success' : 'info'">
                            {{ row.is_active ? 'Active' : 'Inactive' }}
                        </el-tag>
                    </template>
                </el-table-column>

                <el-table-column label="Actions" width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="showDialog(row)">Edit</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- Dialog -->
        <el-dialog 
            v-model="dialogVisible" 
            :title="editingItem ? 'Edit Team Member' : 'Add Team Member'" 
            width="700px" 
            @close="resetForm">
            <el-form ref="formRef" :model="form" :rules="rules" label-width="150px">
                <el-form-item label="Name" prop="name">
                    <el-input v-model="form.name" placeholder="Enter full name" />
                </el-form-item>

                <el-form-item label="Position" prop="position">
                    <el-input v-model="form.position" placeholder="e.g., Senior Developer" />
                </el-form-item>

                <el-form-item label="Email">
                    <el-input v-model="form.email" type="email" placeholder="email@example.com" />
                </el-form-item>

                <el-form-item label="Phone">
                    <el-input v-model="form.phone" placeholder="+1234567890" />
                </el-form-item>

                <el-form-item label="Bio">
                    <el-input 
                        v-model="form.bio" 
                        type="textarea" 
                        :rows="4" 
                        placeholder="Short bio about the team member..." />
                </el-form-item>

                <el-form-item label="Photo">
                    <el-upload 
                        class="photo-uploader" 
                        :auto-upload="false" 
                        :on-change="handlePhotoChange"
                        :on-remove="handlePhotoRemove"
                        :file-list="photoFileList"
                        :limit="1"
                        accept="image/*"
                        list-type="picture-card">
                        <el-icon><Plus /></el-icon>
                    </el-upload>
                    <el-avatar 
                        v-if="!photoFileList.length && form.photo_url" 
                        :src="form.photo_url" 
                        :size="100"
                        style="margin-top: 10px;" />
                </el-form-item>

                <el-form-item label="LinkedIn URL">
                    <el-input v-model="form.linkedin_url" placeholder="https://linkedin.com/in/username" />
                </el-form-item>

                <el-form-item label="Twitter URL">
                    <el-input v-model="form.twitter_url" placeholder="https://twitter.com/username" />
                </el-form-item>

                <el-form-item label="GitHub URL">
                    <el-input v-model="form.github_url" placeholder="https://github.com/username" />
                </el-form-item>

                <el-form-item label="Order">
                    <el-input-number v-model="form.order" :min="0" />
                </el-form-item>

                <el-form-item label="Active">
                    <el-switch v-model="form.is_active" />
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

interface TeamMember {
    id?: number;
    name: string;
    position: string;
    email?: string;
    phone?: string;
    bio?: string;
    photo_url?: string;
    linkedin_url?: string;
    twitter_url?: string;
    github_url?: string;
    order?: number;
    is_active: boolean;
}

const loading = ref(false);
const saving = ref(false);
const dialogVisible = ref(false);
const formRef = ref();
const members = ref<TeamMember[]>([]);
const editingItem = ref<TeamMember | null>(null);
const photoFile = ref<File | null>(null);
const photoFileList = ref<any[]>([]);

const form = reactive<Partial<TeamMember>>({
    name: '',
    position: '',
    email: '',
    phone: '',
    bio: '',
    linkedin_url: '',
    twitter_url: '',
    github_url: '',
    order: 0,
    is_active: true,
});

const rules = {
    name: [{ required: true, message: 'Please enter name', trigger: 'blur' }],
    position: [{ required: true, message: 'Please enter position', trigger: 'blur' }],
};

const fetchMembers = async () => {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/team-members');
        members.value = data.data;
    } catch (error) {
        ElMessage.error('Failed to fetch team members');
    } finally {
        loading.value = false;
    }
};

const showDialog = (item?: TeamMember) => {
    if (item) {
        editingItem.value = item;
        Object.assign(form, item);
    }
    dialogVisible.value = true;
};

const resetForm = () => {
    editingItem.value = null;
    photoFile.value = null;
    photoFileList.value = [];
    Object.assign(form, {
        name: '',
        position: '',
        email: '',
        phone: '',
        bio: '',
        linkedin_url: '',
        twitter_url: '',
        github_url: '',
        order: 0,
        is_active: true,
    });
    formRef.value?.resetFields();
};

const handlePhotoChange = (uploadFile: any, uploadFiles: any[]) => {
    console.log('Photo changed:', uploadFile);
    
    if (uploadFile.raw) {
        photoFile.value = uploadFile.raw;
        console.log('Photo file set:', photoFile.value);
    }
};

const handlePhotoRemove = () => {
    photoFile.value = null;
    photoFileList.value = [];
};

const handleSave = async () => {
    try {
        await formRef.value.validate();
        saving.value = true;

        const formData = new FormData();

        // Append all form fields
        Object.keys(form).forEach((key) => {
            let value = form[key as keyof typeof form];

            if (value === null || value === undefined || value === '') return;

            if (typeof value === 'boolean') {
                formData.append(key, value ? '1' : '0');
            } else {
                formData.append(key, String(value));
            }
        });

        // Append photo file
        if (photoFile.value) {
            console.log('Appending photo to FormData:', photoFile.value);
            formData.append('photo', photoFile.value);
        }

        // Debug: Log FormData contents
        console.log('FormData contents:');
        for (let pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }

        if (editingItem.value) {
            // Method spoofing for Laravel PUT request with files
            formData.append('_method', 'PUT');
            
            await api.post(`/admin/team-members/${editingItem.value.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            ElMessage.success('Team member updated successfully');
        } else {
            await api.post('/admin/team-members', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            ElMessage.success('Team member created successfully');
        }

        dialogVisible.value = false;
        fetchMembers();
    } catch (error: any) {
        console.error('Save error:', error);
        ElMessage.error(error.response?.data?.message || 'Failed to save team member');
    } finally {
        saving.value = false;
    }
};

const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('Are you sure you want to delete this team member?', 'Confirm', {
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            type: 'warning',
        });

        await api.delete(`/admin/team-members/${id}`);
        ElMessage.success('Team member deleted successfully');
        fetchMembers();
    } catch (error) {
        // User cancelled
    }
};

onMounted(() => {
    fetchMembers();
});
</script>

<style scoped>
.team-page {
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

.photo-uploader {
    display: inline-block;
}
</style>