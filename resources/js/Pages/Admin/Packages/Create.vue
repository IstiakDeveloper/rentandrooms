<template>
    <AdminLayout>
      <template #header>
        <h1 class="text-2xl font-semibold text-gray-800">Create New Package</h1>
      </template>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Step 1: General Information -->
        <div class="bg-white p-6 shadow rounded-lg">
          <h2 class="text-lg font-medium text-gray-800 mb-4">General Information</h2>

          <!-- Country -->
          <div class="mb-4">
            <label for="country_id" class="block text-sm font-medium text-gray-700">Country</label>
            <select
              id="country_id"
              v-model="form.country_id"
              @change="fetchCities"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="" disabled>Select a country</option>
              <option v-for="country in countries" :key="country.id" :value="country.id">
                {{ country.name }}
              </option>
            </select>
            <span v-if="form.errors.country_id" class="text-sm text-red-500">{{ form.errors.country_id }}</span>
          </div>

          <!-- City -->
          <div class="mb-4">
            <label for="city_id" class="block text-sm font-medium text-gray-700">City</label>
            <select
              id="city_id"
              v-model="form.city_id"
              @change="fetchAreas"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="" disabled>Select a city</option>
              <option v-for="city in cities" :key="city.id" :value="city.id">
                {{ city.name }}
              </option>
            </select>
            <span v-if="form.errors.city_id" class="text-sm text-red-500">{{ form.errors.city_id }}</span>
          </div>

          <!-- Area -->
          <div class="mb-4">
            <label for="area_id" class="block text-sm font-medium text-gray-700">Area</label>
            <select
              id="area_id"
              v-model="form.area_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="" disabled>Select an area</option>
              <option v-for="area in areas" :key="area.id" :value="area.id">
                {{ area.name }}
              </option>
            </select>
            <span v-if="form.errors.area_id" class="text-sm text-red-500">{{ form.errors.area_id }}</span>
          </div>

          <!-- Property -->
          <div class="mb-4">
            <label for="property_id" class="block text-sm font-medium text-gray-700">Property</label>
            <select
              id="property_id"
              v-model="form.property_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="" disabled>Select a property</option>
              <option v-for="property in properties" :key="property.id" :value="property.id">
                {{ property.name }}
              </option>
            </select>
            <span v-if="form.errors.property_id" class="text-sm text-red-500">{{ form.errors.property_id }}</span>
          </div>

          <!-- Name -->
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Package Name</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              placeholder="Enter package name"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
            <span v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</span>
          </div>

          <!-- Address -->
          <div class="mb-4">
            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
            <input
              id="address"
              v-model="form.address"
              type="text"
              placeholder="Enter package address"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
            <span v-if="form.errors.address" class="text-sm text-red-500">{{ form.errors.address }}</span>
          </div>
        </div>

        <!-- Step 2: Amenities and Maintenances -->
        <div class="bg-white p-6 shadow rounded-lg">
          <h2 class="text-lg font-medium text-gray-800 mb-4">Amenities & Maintenance</h2>

          <!-- Free Amenities -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Free Amenities</label>
            <div class="flex items-center space-x-4 mt-2">
              <div v-for="amenity in amenities" :key="amenity.id" class="flex items-center">
                <input
                  type="checkbox"
                  :value="amenity.id"
                  v-model="form.freeAmenities"
                  class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                />
                <label class="ml-2 text-sm text-gray-800">{{ amenity.name }}</label>
              </div>
            </div>
          </div>

          <!-- Paid Amenities -->
          <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700">Paid Amenities</label>
            <div class="space-y-4 mt-2">
              <div
                v-for="(paidAmenity, index) in form.paidAmenities"
                :key="index"
                class="flex items-center space-x-4"
              >
                <select
                  v-model="paidAmenity.amenity_id"
                  class="block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="" disabled>Select an amenity</option>
                  <option v-for="amenity in amenities" :key="amenity.id" :value="amenity.id">
                    {{ amenity.name }}
                  </option>
                </select>
                <input
                  v-model="paidAmenity.price"
                  type="number"
                  class="block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="Enter price"
                />
                <button
                  type="button"
                  @click="removePaidAmenity(index)"
                  class="text-red-500 hover:underline text-sm"
                >
                  Remove
                </button>
              </div>
            </div>

            <button
              type="button"
              @click="addPaidAmenity"
              class="mt-3 text-indigo-600 hover:text-indigo-800 text-sm font-medium"
            >
              + Add Paid Amenity
            </button>
          </div>
        </div>

        <!-- Step 3: Photos -->
        <div class="bg-white p-6 shadow rounded-lg">
          <h2 class="text-lg font-medium text-gray-800 mb-4">Upload Photos</h2>
          <input
            type="file"
            multiple
            @change="handlePhotoUpload"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
          />
          <div class="mt-4 flex flex-wrap gap-4">
            <div
              v-for="(photo, index) in uploadedPhotos"
              :key="index"
              class="relative w-32 h-32 border rounded overflow-hidden"
            >
              <img :src="photo.url" alt="Photo" class="object-cover w-full h-full" />
              <button
                type="button"
                @click="removePhoto(index)"
                class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1"
              >
                X
              </button>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button
            type="submit"
            class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:ring focus:ring-indigo-500"
          >
            Create Package
          </button>
        </div>
      </form>
    </AdminLayout>
  </template>

  <script setup>
  import { ref } from 'vue'
  import { useForm } from '@inertiajs/vue3'
  import AdminLayout from '@/Layouts/AdminLayout.vue'
  import axios from 'axios'

  defineProps({
    countries: Array,
    properties: Array,
    maintains: Array,
    amenities: Array,
  })

  const form = useForm({
    country_id: '',
    city_id: '',
    area_id: '',
    property_id: '',
    name: '',
    address: '',
    freeAmenities: [],
    paidAmenities: [],
    photos: [],
  })

  const cities = ref([])
  const areas = ref([])
  const uploadedPhotos = ref([])

  const fetchCities = async () => {
    const response = await axios.get(route('admin.packages.get-cities', form.country_id))
    cities.value = response.data
    areas.value = [] // Reset areas
    form.city_id = ''
    form.area_id = ''
  }

  const fetchAreas = async () => {
    const response = await axios.get(route('admin.packages.get-areas', form.city_id))
    areas.value = response.data
    form.area_id = ''
  }

  const addPaidAmenity = () => {
    form.paidAmenities.push({ amenity_id: '', price: '' })
  }

  const removePaidAmenity = (index) => {
    form.paidAmenities.splice(index, 1)
  }

  const handlePhotoUpload = (event) => {
    Array.from(event.target.files).forEach((file) => {
      uploadedPhotos.value.push({ url: URL.createObjectURL(file), file })
      form.photos.push(file)
    })
  }

  const removePhoto = (index) => {
    uploadedPhotos.value.splice(index, 1)
    form.photos.splice(index, 1)
  }

  const submit = () => {
    form.post(route('admin.packages.store'))
  }
  </script>
