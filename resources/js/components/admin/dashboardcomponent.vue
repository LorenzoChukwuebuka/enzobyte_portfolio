<template>
    <div class="dashboard">
        <h2>Welcome to Enzobyte Admin</h2>

        <el-row :gutter="20" style="margin-top: 30px">
            <el-col :span="6">
                <el-card shadow="hover">
                    <el-statistic title="Total Projects" :value="stats.projects" />
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card shadow="hover">
                    <el-statistic title="Blog Posts" :value="stats.posts" />
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card shadow="hover">
                    <el-statistic title="Team Members" :value="stats.team" />
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card shadow="hover">
                    <el-statistic title="New Inquiries" :value="stats.inquiries" />
                </el-card>
            </el-col>
        </el-row>

        <el-card style="margin-top: 20px">
            <h3>Quick Actions</h3>
            <div class="quick-actions">
                <el-button type="primary" icon="el-icon-plus" @click="$router.push('/admin/projects')">
                    Add Project
                </el-button>
                <el-button type="success" icon="el-icon-document" @click="$router.push('/admin/blog')">
                    New Blog Post
                </el-button>
                <el-button type="info" icon="el-icon-message" @click="$router.push('/admin/inquiries')">
                    View Inquiries
                </el-button>
            </div>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { defineComponent, reactive, onMounted } from 'vue';
import api from '@/services/api';


const stats = reactive({
    projects: 0,
    posts: 0,
    team: 0,
    inquiries: 0,
});

const fetchStats = async () => {
    try {
        // Fetch basic counts
        const [projects, posts, team, inquiries] = await Promise.all([
            api.get('/admin/projects?page=1'),
            api.get('/admin/blog-posts?page=1'),
            api.get('/admin/team-members'),
            api.get('/admin/inquiries/stats'),
        ]);

        stats.projects = projects.data.total || 0;
        stats.posts = posts.data.total || 0;
        stats.team = team.data.data?.length || 0;
        stats.inquiries = inquiries.data.new || 0;
    } catch (error) {
        console.error('Failed to fetch stats');
    }
};

onMounted(() => {
    fetchStats();
});

</script>

<style scoped>
.dashboard {
    padding: 20px;
}

.dashboard h2 {
    font-size: 24px;
    font-weight: 500;
    color: #303133;
}

.quick-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}
</style>