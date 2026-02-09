<template>
    <div class="login-container">
        <el-card class="login-card">
            <div class="login-header">
                <h1>Enzobyte Admin</h1>
                <p>Sign in to manage your portfolio</p>
            </div>

            <el-form ref="loginFormRef" :model="loginForm" :rules="rules" @submit.prevent="handleLogin">
                <el-form-item prop="email">
                    <el-input v-model="loginForm.email" placeholder="Email" size="large" prefix-icon="el-icon-user" />
                </el-form-item>

                <el-form-item prop="password">
                    <el-input v-model="loginForm.password" type="password" placeholder="Password" size="large"
                        prefix-icon="el-icon-lock" show-password @keyup.enter="handleLogin" />
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" size="large" :loading="loading" style="width: 100%" @click="handleLogin">
                        Sign In
                    </el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { defineComponent, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import api from '@/services/api';



const router = useRouter();
const loginFormRef = ref();
const loading = ref(false);

const loginForm = reactive({
    email: '',
    password: '',
});

const rules = {
    email: [
        { required: true, message: 'Please enter email', trigger: 'blur' },
        { type: 'email', message: 'Please enter valid email', trigger: 'blur' },
    ],
    password: [
        { required: true, message: 'Please enter password', trigger: 'blur' },
        { min: 6, message: 'Password must be at least 6 characters', trigger: 'blur' },
    ],
};

const handleLogin = async () => {
    try {
        await loginFormRef.value.validate();
        loading.value = true;

        await api.login(loginForm.email, loginForm.password);

        ElMessage.success('Login successful!');
        router.push('/admin/dashboard');
    } catch (error: any) {
        ElMessage.error(error.response?.data?.message || 'Login failed. Please try again.');
    } finally {
        loading.value = false;
    }
};


</script>

<style scoped>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-card {
    width: 100%;
    max-width: 400px;
    padding: 20px;
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h1 {
    font-size: 28px;
    font-weight: 600;
    color: #303133;
    margin-bottom: 10px;
}

.login-header p {
    color: #909399;
    font-size: 14px;
}
</style>