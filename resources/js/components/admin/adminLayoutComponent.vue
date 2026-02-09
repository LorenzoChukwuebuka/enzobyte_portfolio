<template>
    <el-container class="admin-layout">
        <el-aside width="250px" class="sidebar">
            <div class="logo">
                <h2>Enzobyte Admin</h2>
            </div>

            <el-menu :default-active="activeMenu" router class="sidebar-menu">
                <el-menu-item index="/admin/dashboard">
                    <i class="el-icon-s-home"></i>
                    <span>Dashboard</span>
                </el-menu-item>

                <el-menu-item index="/admin/projects">
                    <i class="el-icon-folder-opened"></i>
                    <span>Projects</span>
                </el-menu-item>

                <el-menu-item index="/admin/services">
                    <i class="el-icon-service"></i>
                    <span>Services</span>
                </el-menu-item>

                <el-menu-item index="/admin/team">
                    <i class="el-icon-user"></i>
                    <span>Team Members</span>
                </el-menu-item>

                <el-menu-item index="/admin/blog">
                    <i class="el-icon-document"></i>
                    <span>Blog Posts</span>
                </el-menu-item>

                <el-menu-item index="/admin/testimonials">
                    <i class="el-icon-chat-line-square"></i>
                    <span>Testimonials</span>
                </el-menu-item>

                <el-menu-item index="/admin/inquiries">
                    <i class="el-icon-message"></i>
                    <span>Contact Inquiries</span>
                </el-menu-item>
            </el-menu>
        </el-aside>

        <el-container>
            <el-header class="header">
                <div class="header-content">
                    <span class="page-title">{{ pageTitle }}</span>
                    <el-dropdown @command="handleCommand">
                        <span class="user-menu">
                            <i class="el-icon-user-solid"></i>
                            <i class="el-icon-arrow-down el-icon--right"></i>
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item command="logout">Logout</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </el-header>

            <el-main class="main-content">
                <router-view />
            </el-main>
        </el-container>
    </el-container>
</template>

<script setup lang="ts">
import { defineComponent, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { ElMessage, ElMessageBox } from 'element-plus';
import api from '@/services/api';


const router = useRouter();
const route = useRoute();

const activeMenu = computed(() => route.path);

const pageTitle = computed(() => {
    const titles: Record<string, string> = {
        '/admin/dashboard': 'Dashboard',
        '/admin/projects': 'Projects',
        '/admin/services': 'Services',
        '/admin/team': 'Team Members',
        '/admin/blog': 'Blog Posts',
        '/admin/testimonials': 'Testimonials',
        '/admin/inquiries': 'Contact Inquiries',
    };
    return titles[route.path] || 'Admin';
});

const handleCommand = async (command: string) => {
    if (command === 'logout') {
        try {
            await ElMessageBox.confirm('Are you sure you want to logout?', 'Confirm', {
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                type: 'warning',
            });

            await api.logout();
            ElMessage.success('Logged out successfully');
            router.push('/admin/login');
        } catch (error) {
            // User cancelled
        }
    }
};
</script>

<style scoped>
.admin-layout {
    min-height: 100vh;
}

.sidebar {
    background-color: #304156;
    color: #fff;
}

.logo {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #263445;
}

.logo h2 {
    color: #fff;
    font-size: 18px;
    margin: 0;
}

.sidebar-menu {
    border-right: none;
    background-color: #304156;
}

.sidebar-menu .el-menu-item {
    color: #bfcbd9;
}

.sidebar-menu .el-menu-item:hover,
.sidebar-menu .el-menu-item.is-active {
    background-color: #263445 !important;
    color: #409eff;
}

.header {
    background-color: #fff;
    box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);
    padding: 0 20px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 100%;
}

.page-title {
    font-size: 18px;
    font-weight: 500;
    color: #303133;
}

.user-menu {
    cursor: pointer;
    display: flex;
    align-items: center;
    font-size: 18px;
    color: #606266;
}

.main-content {
    background-color: #f0f2f5;
    padding: 20px;
}
</style>