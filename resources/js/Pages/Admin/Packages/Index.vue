<template>
    <AdminLayout title="Packages">
        <!-- Toast Notification -->
        <Toast ref="toastRef" />

        <!-- Header Section -->
        <template #header>
            <div class="flex justify-between items-center px-6 py-4 bg-white/80 backdrop-blur-sm dark:bg-gray-900/80">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Packages</h2>
                <Link :href="route('admin.packages.create')"
                      class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition duration-150">
                    <i class="fas fa-plus mr-2"></i> Create Package
                </Link>
            </div>
        </template>

        <!-- Main Content Section -->
        <div class="max-w-7xl mx-auto p-6">
            <!-- No Packages Message -->
            <div v-if="packages.length === 0" class="text-center">
                <p class="text-gray-500 dark:text-gray-400">No packages found. Create a new package to get started!</p>
            </div>

            <!-- Packages Table -->
            <div v-else>
                <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-900">
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-800 dark:text-gray-200">Name</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-800 dark:text-gray-200">Address</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-800 dark:text-gray-200">Expiration</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-800 dark:text-gray-200">Status</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-800 dark:text-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pkg in packages" :key="pkg.id" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ pkg.name }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ pkg.address }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(pkg.expiration_date) }}</td>
                            <td class="py-3 px-4 text-sm">
                                <span :class="getStatusClass(pkg.status)">
                                    {{ pkg.status.charAt(0).toUpperCase() + pkg.status.slice(1) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm flex space-x-2">
                                <Link :href="route('admin.packages.edit', pkg.id)"
                                      class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-500">Edit</Link>
                                <button @click="confirmDelete(pkg.id)"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Confirm Delete</h3>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Are you sure you want to delete this package? This action cannot be undone.</p>
                <div class="flex space-x-4 mt-4">
                    <button @click="deletePackage"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600">Delete</button>
                    <button @click="cancelDelete"
                            class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import Toast from '@/Components/UI/Toast.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';


const { packages } = usePage().props;

// Modal State
const showDeleteModal = ref(false);
const packageIdToDelete = ref(null);

// Format Date Utility
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Return Status Class Based on Package Status
const getStatusClass = (status) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800 px-2 py-1 rounded-full dark:bg-green-700 dark:text-green-200';
        case 'expired':
            return 'bg-red-100 text-red-800 px-2 py-1 rounded-full dark:bg-red-700 dark:text-red-200';
        default:
            return 'bg-gray-100 text-gray-800 px-2 py-1 rounded-full dark:bg-gray-700 dark:text-gray-300';
    }
};

// Confirm Delete Action
const confirmDelete = (id) => {
    packageIdToDelete.value = id;
    showDeleteModal.value = true;
};

// Perform Delete Action
const deletePackage = () => {
    Inertia.delete(route('admin.packages.destroy', packageIdToDelete.value), {
        onSuccess: () => {
            showDeleteModal.value = false;
            packageIdToDelete.value = null;
            toastRef.value?.toast.success('Package deleted successfully');
        },
        onError: (errors) => {
            showDeleteModal.value = false;
            toastRef.value?.toast.error('Failed to delete package');
            console.error(errors);
        },
    });
};

// Cancel Delete Action
const cancelDelete = () => {
    showDeleteModal.value = false;
    packageIdToDelete.value = null;
};
</script>

<style scoped>
/* Modify table styles for better appearance in dark mode */
.table th, .table td {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb; /* Light mode */
}

.table th {
    text-transform: uppercase;
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.05em;
}

.table td {
    font-size: 0.875rem;
}

.dark .table th, .dark .table td {
    border-bottom: 1px solid #374151; /* Dark mode */
}
</style>
