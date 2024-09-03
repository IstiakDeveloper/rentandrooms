<template>
  <AdminLayout>
    <div class="container mx-auto py-8">

    <div v-if="userRole === 'Partner'" class="flex justify-end mb-2">
      <button @click="showBankModal = true" class="btn-primary">Add Bank Details</button>
    </div>

    

      <div class="flex justify-around mb-4 p-2 bg-gray-50 shadow-md rounded-md">
      <div class="p-4 bg-white rounded shadow">
        <h2 class="text-xl font-semibold text-gray-700">User Information</h2>
        <div class="flex items-center mt-4">
          <img :src="user.photo" alt="User Photo" class="w-16 h-16 rounded-full object-cover"/>
          <div class="ml-4">
            <p class="text-lg font-medium">{{ user.name }}</p>
            <p class="text-sm text-gray-600">{{ user.email }}</p>
          </div>
        </div>
      </div>
        <!-- Display Documents Section -->
        <div class="p-4 bg-white rounded shadow">
            <h2 class="text-xl font-semibold text-gray-700">Uploaded Documents</h2>
            <div v-if="documents.length === 0" class="text-gray-600">
              No documents uploaded.
            </div>
              <div v-else>
                <div v-for="document in documents" :key="document.id" class="mb-4 p-4 bg-gray-100 rounded">
                    <div v-if="document.type_1">
                      <p class="text-lg font-medium">{{ document.type_1 }}</p>
                      <p v-if="document.path_1" class="text-sm text-gray-600">
                          <a :href="'/storage/' + document.path_1" target="_blank" class="text-blue-600 hover:underline">View Document</a>
                      </p>
                    </div>
      
                    <div v-if="document.type_2">
                      <p class="text-lg font-medium">{{ document.type_2 }}</p>
                      <p v-if="document.path_2" class="text-sm text-gray-600">
                          <a :href=" '/storage/' + document.path_2" target="_blank" class="text-blue-600 hover:underline">View Document</a>
                      </p>
                    </div>

                    <div v-if="document.type_3">
                      <p class="text-lg font-medium">{{ document.type_3 }}</p>
                      <p v-if="'/storage/' + document.path_3" class="text-sm text-gray-600">
                          <a :href="document.path_3" target="_blank" class="text-blue-600 hover:underline">View Document</a>
                      </p>
                    </div>

                    <div v-if="document.type_4">
                      <p class="text-lg font-medium">{{ document.type_4 }}</p>
                      <p v-if="document.path_4" class="text-sm text-gray-600">
                          <a :href="'/storage/' + document.path_4" target="_blank" class="text-blue-600 hover:underline">View Document</a>
                      </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Display Bank Details -->
        <div v-if="bankDetail" class="p-4 bg-white rounded shadow">
            <h2 class="text-xl font-semibold text-gray-700">Bank Details</h2>
            <p><strong>Bank Name:</strong> {{ bankDetail.name }}</p>
            <p><strong>Account Number:</strong> {{ bankDetail.account_number }}</p>
            <p><strong>Sort Code:</strong> {{ bankDetail.sort_code }}</p>
        </div>
      </div>

      <div class="p-2 bg-gray-50 shadow-md rounded-md">
            <h1 class="text-2xl font-semibold text-gray-700 mb-6">Upload Documents</h1>
      <form @submit.prevent="submitForm">
        <!-- Conditional Form Sections Based on User Role -->
        <div v-if="userRole === 'User'">
          <!-- Type 1: First Person Photo ID -->
          <div class="mb-6">
            <label for="type_1" class="block text-sm font-medium text-gray-700">First Person Photo ID</label>
            <select v-model="form.type_1" id="type_1" class="form-select">
              <option value="NID">NID</option>
              <option value="Passport">Passport</option>
            </select>
            <input type="file" @change="handleFileUpload($event, 'path_1')" class="form-input" accept=".pdf, image/*" />
          </div>

          <!-- Type 2: First Person Proof ID -->
          <div class="mb-6">
            <label for="type_2" class="block text-sm font-medium text-gray-700">First Person Proof ID</label>
            <select v-model="form.type_2" id="type_2" class="form-select">
              <option value="Student Id">Student Id</option>
              <option value="Work Id">Work Id</option>
              <option value="Other">Other</option>
            </select>
            <input type="file" @change="handleFileUpload($event, 'path_2')" class="form-input" accept=".pdf, image/*" />
          </div>

          <!-- Type 3: Second Person Photo ID -->
          <div class="mb-6">
            <label for="type_3" class="block text-sm font-medium text-gray-700">Second Person Photo ID</label>
            <select v-model="form.type_3" id="type_3" class="form-select">
              <option value="NID">NID</option>
              <option value="Passport">Passport</option>
            </select>
            <input type="file" @change="handleFileUpload($event, 'path_3')" class="form-input" accept=".pdf, image/*" />
          </div>

          <!-- Type 4: Second Person Proof ID -->
          <div class="mb-6">
            <label for="type_4" class="block text-sm font-medium text-gray-700">Second Person Proof ID</label>
            <select v-model="form.type_4" id="type_4" class="form-select">
              <option value="Student Id">Student Id</option>
              <option value="Work Id">Work Id</option>
              <option value="Other">Other</option>
            </select>
            <input type="file" @change="handleFileUpload($event, 'path_4')" class="form-input" accept=".pdf, image/*" />
          </div>
        </div>

        <div v-else-if="userRole === 'Partner'">
          <!-- Gas Certificate Copy -->
          <div class="mb-6">
            <label for="type_1" class="block text-sm font-medium text-gray-700">Gas Certificate Copy</label>
            <input type="file" @change="handleFileUpload($event, 'path_1')" class="form-input" accept=".pdf, image/*" />
          </div>

          <!-- Electric Certificate Copy -->
          <div class="mb-6">
            <label for="type_2" class="block text-sm font-medium text-gray-700">Electric Certificate Copy</label>
            <input type="file" @change="handleFileUpload($event, 'path_2')" class="form-input" accept=".pdf, image/*" />
          </div>

          <!-- Landlord Certificate (HMO/Other) -->
          <div class="mb-6">
            <label for="type_3" class="block text-sm font-medium text-gray-700">Landlord Certificate (HMO/Other)</label>
            <input type="file" @change="handleFileUpload($event, 'path_3')" class="form-input" accept=".pdf, image/*" />
          </div>

          <!-- Building Insurance Certificate -->
          <div class="mb-6">
            <label for="type_4" class="block text-sm font-medium text-gray-700">Building Insurance Certificate</label>
            <input type="file" @change="handleFileUpload($event, 'path_4')" class="form-input" accept=".pdf, image/*" />
          </div>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="btn-primary" :disabled="form.processing">Submit</button>
        </div>
      </form>
      </div>


     <!-- Bank Details Modal -->
      <div v-if="showBankModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
          <h2 class="text-xl font-semibold mb-4">Add Bank Details</h2>
          <form @submit.prevent="submitBankDetails">
            <div class="mb-4">
              <label for="name" class="block text-sm font-medium text-gray-700">Bank Name</label>
              <input type="text" v-model="bankForm.name" id="name" class="form-input" />
            </div>
            <div class="mb-4">
              <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
              <input type="text" v-model="bankForm.account_number" id="account_number" class="form-input" />
            </div>
            <div class="mb-4">
              <label for="sort_code" class="block text-sm font-medium text-gray-700">Sort Code</label>
              <input type="text" v-model="bankForm.sort_code" id="sort_code" class="form-input" />
            </div>
            <div class="flex justify-end">
              <button type="button" @click="showBankModal = false" class="btn-secondary mr-2">Cancel</button>
              <button type="submit" class="btn-primary" :disabled="form.processing">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  user: Object, // Expect the user object to be passed from the controller
  documents: Array, // Expect the documents array to be passed from the controller
  bankDetail: Object,

});

const userRole = ref(props.user.role);
const documents = ref(props.documents);
const showBankModal = ref(false)

const bankForm = useForm({
  name: '',
  account_number: '',
  sort_code: '',
});

const form = useForm({
  type_1: userRole.value === 'Partner' ? 'Gas Certificate Copy' : '',
  path_1: null,
  type_2: userRole.value === 'Partner' ? 'Electric Certificate Copy' : '',
  path_2: null,
  type_3: userRole.value === 'Partner' ? 'Landlord Certificate (HMO/Other)' : '',
  path_3: null,
  type_4: userRole.value === 'Partner' ? 'Building Insurance Certificate' : '',
  path_4: null,
});

// Watch for changes in userRole to handle default values
watch(userRole, (newRole) => {
  if (newRole === 'Partner') {
    form.type_1 = 'Gas Certificate Copy';
    form.type_2 = 'Electric Certificate Copy';
    form.type_3 = 'Landlord Certificate (HMO/Other)';
    form.type_4 = 'Building Insurance Certificate';
  }
});

function handleFileUpload(event, pathKey) {
  form[pathKey] = event.target.files[0];
}

function submitForm() {
  form.post(route('documents.store'), {
    onSuccess: () => {
      form.reset();
    },
    onError: (errors) => {
      console.error('Error submitting form:', errors);
    },
  });
}


const submitBankDetails = () => {
  bankForm.post(route('bank-details.store'), {
    onSuccess: () => {
      showBankModal.value = false; // Close modal on success
      bankForm.reset(); // Reset form fields
    },
    onError: (errors) => {
      console.error('Error submitting bank details:', errors);
    },
  });
}
</script>

<style scoped>
.btn-primary {
  @apply bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-500;
  @apply disabled:bg-gray-400 disabled:cursor-not-allowed;
}

.form-select {
  @apply block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm;
}

.form-input {
  @apply mt-3 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100;
}
.btn-secondary {
  @apply bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-500;
}
</style>