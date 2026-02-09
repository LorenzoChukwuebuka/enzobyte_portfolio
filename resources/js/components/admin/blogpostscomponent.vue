<template>
    <div class="blog-page">
        <div class="page-header">
            <h2>Blog Posts</h2>
            <el-button type="primary" icon="el-icon-plus" @click="showDialog()">
                Add Post
            </el-button>
        </div>

        <el-card>
            <el-table :data="posts" v-loading="loading" style="width: 100%">
                <el-table-column prop="featured_image_url" label="Image" width="100">
                    <template #default="{ row }">
                        <el-image v-if="row.featured_image_url" :src="row.featured_image_url" fit="cover"
                            style="width: 60px; height: 60px; border-radius: 4px" />
                    </template>
                </el-table-column>

                <el-table-column prop="title" label="Title" />

                <el-table-column prop="category" label="Category" width="120">
                    <template #default="{ row }">
                        <el-tag>{{ row.category }}</el-tag>
                    </template>
                </el-table-column>

                <el-table-column prop="author.name" label="Author" width="150" />

                <el-table-column prop="views" label="Views" width="100" />

                <el-table-column label="Status" width="120">
                    <template #default="{ row }">
                        <el-tag :type="row.is_published ? 'success' : 'info'">
                            {{ row.is_published ? 'Published' : 'Draft' }}
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

            <div class="pagination">
                <el-pagination v-model:current-page="currentPage" :page-size="20" :total="total"
                    layout="total, prev, pager, next" @current-change="fetchPosts" />
            </div>
        </el-card>

        <!-- Dialog -->
        <el-dialog v-model="dialogVisible" :title="editingPost ? 'Edit Post' : 'Add Post'" width="900px"
            @close="resetForm">
            <el-form ref="formRef" :model="form" :rules="rules" label-width="150px">
                <el-form-item label="Title" prop="title">
                    <el-input v-model="form.title" />
                </el-form-item>

                <el-form-item label="Excerpt" prop="excerpt">
                    <el-input v-model="form.excerpt" type="textarea" :rows="2" />
                </el-form-item>

                <el-form-item label="Content" prop="content">
                    <el-input v-model="form.content" type="textarea" :rows="10" />
                </el-form-item>

                <el-form-item label="Featured Image">
                    <el-upload :auto-upload="false" :on-change="handleImageChange" :limit="1" accept="image/*">
                        <el-button size="small" type="primary">Select Image</el-button>
                    </el-upload>
                    <el-image v-if="form.featured_image_url" :src="form.featured_image_url" fit="cover"
                        style="width: 100px; height: 100px; margin-top: 10px" />
                </el-form-item>

                <el-form-item label="Category" prop="category">
                    <el-select v-model="form.category" style="width: 100%">
                        <el-option label="Technology" value="technology" />
                        <el-option label="Design" value="design" />
                        <el-option label="Business" value="business" />
                        <el-option label="Tutorials" value="tutorials" />
                        <el-option label="Case Studies" value="case_studies" />
                        <el-option label="News" value="news" />
                    </el-select>
                </el-form-item>

                <el-form-item label="Tags">
                    <el-select v-model="form.tags" multiple filterable allow-create style="width: 100%"
                        placeholder="Add tags" />
                </el-form-item>

                <el-form-item label="Read Time (min)">
                    <el-input-number v-model="form.read_time_minutes" :min="1" />
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
import { defineComponent, ref, reactive, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import api from '@/services/api';
import type { BlogPost } from '@/@types';

const loading = ref(false);
const saving = ref(false);
const dialogVisible = ref(false);
const formRef = ref();
const posts = ref<BlogPost[]>([]);
const editingPost = ref<BlogPost | null>(null);
const currentPage = ref(1);
const total = ref(0);
const imageFile = ref<File | null>(null);

const form = reactive<Partial<BlogPost>>({
    title: '',
    excerpt: '',
    content: '',
    category: 'technology',
    tags: [],
    read_time_minutes: 5,
    is_published: false,
});

const rules = {
    title: [{ required: true, message: 'Please enter title', trigger: 'blur' }],
    excerpt: [{ required: true, message: 'Please enter excerpt', trigger: 'blur' }],
    content: [{ required: true, message: 'Please enter content', trigger: 'blur' }],
    category: [{ required: true, message: 'Please select category', trigger: 'change' }],
};

const fetchPosts = async () => {
    loading.value = true;
    try {
        const { data } = await api.get(`/admin/blog-posts?page=${currentPage.value}`);
        posts.value = data.data;
        total.value = data.total;
    } catch (error) {
        ElMessage.error('Failed to fetch posts');
    } finally {
        loading.value = false;
    }
};

const showDialog = (post?: BlogPost) => {
    if (post) {
        editingPost.value = post;
        Object.assign(form, post);
    }
    dialogVisible.value = true;
};

const resetForm = () => {
    editingPost.value = null;
    imageFile.value = null;
    Object.assign(form, {
        title: '',
        excerpt: '',
        content: '',
        category: 'technology',
        tags: [],
        read_time_minutes: 5,
        is_published: false,
    });
    formRef.value?.resetFields();
};

const handleImageChange = (file: any) => {
    imageFile.value = file.raw;
};

const handleSave = async () => {
    try {
        await formRef.value.validate();
        saving.value = true;

        const formData = new FormData();
        Object.keys(form).forEach((key) => {
            const value = form[key as keyof typeof form];
            if (value !== null && value !== undefined) {
                if (Array.isArray(value)) {
                    formData.append(key, JSON.stringify(value));
                } else {
                    formData.append(key, String(value));
                }
            }
        });

        if (imageFile.value) {
            formData.append('featured_image', imageFile.value);
        }

        if (editingPost.value) {
            formData.append('_method', 'PUT'); // or 'PATCH'
            await api.post(`/admin/blog-posts/${editingPost.value.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            ElMessage.success('Post updated successfully');
        } else {
            await api.post('/admin/blog-posts', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            ElMessage.success('Post created successfully');
        }

        dialogVisible.value = false;
        fetchPosts();
    } catch (error: any) {
        ElMessage.error(error.response?.data?.message || 'Failed to save post');
    } finally {
        saving.value = false;
    }
};

const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('Are you sure you want to delete this post?', 'Confirm', {
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            type: 'warning',
        });

        await api.delete(`/admin/blog-posts/${id}`);
        ElMessage.success('Post deleted successfully');
        fetchPosts();
    } catch (error) {
        // User cancelled
    }
};

onMounted(() => {
    fetchPosts();
});

</script>

<style scoped>
.blog-page {
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