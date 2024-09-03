<template>
  <Head title="User Details" />
  <AdminLayout>
    <div class="container mx-auto py-8 px-4">
      <div class="bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-6">User Details</h1>

        <!-- User Details -->
        <div class="mb-6">
          <h2 class="text-2xl font-semibold text-gray-800 mb-2">User Information</h2>
          <div class="bg-gray-100 p-4 rounded-lg">
            <p class="text-lg"><strong>Name:</strong> {{ user.name }}</p>
            <p class="text-lg"><strong>Email:</strong> {{ user.email }}</p>
            <p class="text-lg"><strong>Roles:</strong> {{ user.roles.map(role => role.name).join(', ') }}</p>
          </div>
        </div>

        <!-- Bank Details -->
        <div class="mb-6">
          <h2 class="text-2xl font-semibold text-gray-800 mb-2">Bank Details</h2>
          <div v-if="bankDetails" class="bg-gray-100 p-4 rounded-lg">
            <p class="text-lg"><strong>Name:</strong> {{ bankDetails.name }}</p>
            <p class="text-lg"><strong>Account Number:</strong> {{ bankDetails.account_number }}</p>
            <p class="text-lg"><strong>Sort Code:</strong> {{ bankDetails.sort_code }}</p>
          </div>
        </div>

        <!-- Documents -->
        <div>
          <h2 class="text-2xl font-semibold text-gray-800 mb-2">Documents</h2>
          <div class="bg-gray-100 p-4 rounded-lg">
            <div v-for="(doc, index) in documentList" :key="index" class="mb-4">
              <p class="text-lg"><strong>Type:</strong> {{ doc.type }}</p>
              <p>
                <a :href="getDownloadUrl(doc.path)" download class="text-blue-600 hover:text-blue-800 underline">
                  Download {{ doc.type }}
                </a>
              </p>
            </div>
            <div v-if="!documentList.length" class="mt-4">
              <p class="text-gray-600">No documents available.</p>
            </div>
          </div>
        </div>

        <!-- Download PDF Button -->
        <div class="mt-8">
          <button @click="downloadPDF" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Download as PDF
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

// Define props
const props = defineProps({
  user: Object,
  documents: Array,
  bankDetails: Object,
});

// Reactive states
const user = ref(props.user);
const bankDetails = ref(props.bankDetails);

// Extract the document object from the array
const documents = ref(props.documents.length > 0 ? props.documents[0] : {});

// Watch for changes in props
watch(() => props.documents, (newDocuments) => {
  documents.value = newDocuments.length > 0 ? newDocuments[0] : {};
}, { immediate: true });

// Function to construct the correct download URL
const getDownloadUrl = (path) => {
  return `/storage/${path}`;
};

// Computed property to transform documents into a list
const documentList = computed(() => {
  return [
    { type: documents.value.type_1, path: documents.value.path_1 },
    { type: documents.value.type_2, path: documents.value.path_2 },
    { type: documents.value.type_3, path: documents.value.path_3 },
    { type: documents.value.type_4, path: documents.value.path_4 }
  ].filter(doc => doc.type && doc.path);
});

// Function to download the page as a PDF
const downloadPDF = async () => {
  const doc = new jsPDF();

  // Capture the page content
  const element = document.querySelector('div.bg-white');
  const canvas = await html2canvas(element);
  const imgData = canvas.toDataURL('image/png');

  doc.addImage(imgData, 'PNG', 10, 10);
  doc.save('user-details.pdf');
};
</script>

<style scoped>
/* Custom styles can go here */
</style>
