<template>
    <div class="inquiries-page">
        <div class="page-header">
            <h2>Contact Inquiries</h2>
            <div class="stats">
                <el-statistic title="Total" :value="stats.total" />
                <el-statistic title="New" :value="stats.new" />
                <el-statistic title="This Month" :value="stats.this_month" />
            </div>
        </div>

        <el-card>
            <div class="filter-bar">
                <el-select v-model="filterStatus" placeholder="Filter by status" clearable @change="fetchInquiries">
                    <el-option label="New" value="new" />
                    <el-option label="In Progress" value="in_progress" />
                    <el-option label="Contacted" value="contacted" />
                    <el-option label="Qualified" value="qualified" />
                    <el-option label="Converted" value="converted" />
                    <el-option label="Closed" value="closed" />
                </el-select>
            </div>

            <el-table :data="inquiries" v-loading="loading" style="width: 100%; margin-top: 20px">
                <el-table-column prop="name" label="Name" width="150" />
                <el-table-column prop="email" label="Email" width="200" />
                <el-table-column prop="company" label="Company" width="150" />

                <el-table-column prop="service_needed" label="Service" width="150">
                    <template #default="{ row }">
                        <span v-if="row.service_needed">{{ formatService(row.service_needed) }}</span>
                    </template>
                </el-table-column>

                <el-table-column prop="budget_range" label="Budget" width="120">
                    <template #default="{ row }">
                        {{ formatBudget(row.budget_range) }}
                    </template>
                </el-table-column>

                <el-table-column prop="status" label="Status" width="130">
                    <template #default="{ row }">
                        <el-tag :type="getStatusType(row.status)">
                            {{ formatStatus(row.status) }}
                        </el-tag>
                    </template>
                </el-table-column>

                <el-table-column prop="created_at" label="Date" width="110">
                    <template #default="{ row }">
                        {{ formatDate(row.created_at) }}
                    </template>
                </el-table-column>

                <el-table-column label="Actions" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="showDialog(row)">View</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div class="pagination">
                <el-pagination v-model:current-page="currentPage" :page-size="20" :total="total"
                    layout="total, prev, pager, next" @current-change="fetchInquiries" />
            </div>
        </el-card>

        <!-- Dialog -->
        <el-dialog v-model="dialogVisible" title="Inquiry Details" width="700px">
            <div v-if="selectedInquiry" class="inquiry-details">
                <el-descriptions :column="2" border>
                    <el-descriptions-item label="Name">{{ selectedInquiry.name }}</el-descriptions-item>
                    <el-descriptions-item label="Email">{{ selectedInquiry.email }}</el-descriptions-item>
                    <el-descriptions-item label="Phone">{{ selectedInquiry.phone || 'N/A' }}</el-descriptions-item>
                    <el-descriptions-item label="Company">{{ selectedInquiry.company || 'N/A' }}</el-descriptions-item>
                    <el-descriptions-item label="Service">{{ formatService(selectedInquiry.service_needed)
                    }}</el-descriptions-item>
                    <el-descriptions-item label="Budget">{{ formatBudget(selectedInquiry.budget_range)
                    }}</el-descriptions-item>
                    <el-descriptions-item label="Date" :span="2">{{ formatDate(selectedInquiry.created_at)
                    }}</el-descriptions-item>
                    <el-descriptions-item label="Message" :span="2">
                        <p style="white-space: pre-wrap">{{ selectedInquiry.message }}</p>
                    </el-descriptions-item>
                </el-descriptions>

                <div style="margin-top: 20px">
                    <h4>Update Status</h4>
                    <el-select v-model="updateForm.status" style="width: 100%; margin-bottom: 10px">
                        <el-option label="New" value="new" />
                        <el-option label="In Progress" value="in_progress" />
                        <el-option label="Contacted" value="contacted" />
                        <el-option label="Qualified" value="qualified" />
                        <el-option label="Converted" value="converted" />
                        <el-option label="Closed" value="closed" />
                    </el-select>

                    <h4 style="margin-top: 20px">Internal Notes</h4>
                    <el-input v-model="updateForm.internal_notes" type="textarea" :rows="4"
                        placeholder="Add internal notes..." />
                </div>
            </div>

            <template #footer>
                <el-button @click="dialogVisible = false">Cancel</el-button>
                <el-button type="primary" :loading="saving" @click="handleUpdate">Update</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { defineComponent, ref, reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import api from '@/services/api';
import type { ContactInquiry } from '@/@types';

const loading = ref(false);
const saving = ref(false);
const dialogVisible = ref(false);
const inquiries = ref<ContactInquiry[]>([]);
const selectedInquiry = ref<ContactInquiry | null>(null);
const currentPage = ref(1);
const total = ref(0);
const filterStatus = ref('');

const stats = reactive({
    total: 0,
    new: 0,
    this_month: 0,
});

const updateForm = reactive({
    status: '',
    internal_notes: '',
});

const fetchStats = async () => {
    try {
        const { data } = await api.get('/admin/inquiries/stats');
        Object.assign(stats, data);
    } catch (error) {
        console.error('Failed to fetch stats');
    }
};

const fetchInquiries = async () => {
    loading.value = true;
    try {
        const params: any = { page: currentPage.value };
        if (filterStatus.value) {
            params.status = filterStatus.value;
        }

        const { data } = await api.get('/admin/inquiries', { params });
        inquiries.value = data.data;
        total.value = data.total;
    } catch (error) {
        ElMessage.error('Failed to fetch inquiries');
    } finally {
        loading.value = false;
    }
};

const showDialog = (inquiry: ContactInquiry) => {
    selectedInquiry.value = inquiry;
    updateForm.status = inquiry.status;
    updateForm.internal_notes = inquiry.internal_notes || '';
    dialogVisible.value = true;
};

const handleUpdate = async () => {
    if (!selectedInquiry.value) return;

    try {
        saving.value = true;
        await api.put(`/admin/inquiries/${selectedInquiry.value.id}`, updateForm);
        ElMessage.success('Inquiry updated successfully');
        dialogVisible.value = false;
        fetchInquiries();
        fetchStats();
    } catch (error: any) {
        ElMessage.error(error.response?.data?.message || 'Failed to update inquiry');
    } finally {
        saving.value = false;
    }
};

const formatService = (service?: string) => {
    if (!service) return 'N/A';
    return service.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};

const formatBudget = (budget: string) => {
    const budgets: Record<string, string> = {
        under_5k: '< $5k',
        '5k_10k': '$5k - $10k',
        '10k_25k': '$10k - $25k',
        '25k_50k': '$25k - $50k',
        '50k_plus': '> $50k',
        not_specified: 'Not Specified',
    };
    return budgets[budget] || budget;
};

const formatStatus = (status: string) => {
    return status.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};

const getStatusType = (status: string) => {
    const types: Record<string, string> = {
        new: 'danger',
        in_progress: 'warning',
        contacted: 'info',
        qualified: 'primary',
        converted: 'success',
        closed: '',
    };
    return types[status] || '';
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString();
};

onMounted(() => {
    fetchStats();
    fetchInquiries();
});


</script>

<style scoped>
.inquiries-page {
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

.stats {
    display: flex;
    gap: 30px;
}

.filter-bar {
    display: flex;
    gap: 10px;
}

.pagination {
    margin-top: 20px;
    text-align: right;
}

.inquiry-details h4 {
    margin: 10px 0;
    color: #303133;
}
</style>