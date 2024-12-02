<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { PlusCircleIcon, TrashIcon, PhotoIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'
import Select from '@/Components/Select.vue'

const props = defineProps({
    countries: Array,
    properties: Array,
    maintains: Array,
    amenities: Array,
    initialRoom: Object,
    initialEntireProperty: Object
})

const newAmenityForm = useForm({
    amenity_type_id: '',
    name: '',
})

const newMaintainForm = useForm({
    maintain_type_id: '',
    name: '',
    photo: null
})


const cities = ref([])
const areas = ref([])
const showNewAmenityModal = ref(false)
const showNewMaintainModal = ref(false)
const photoPreview = ref([])
const photoInput = ref(null)

const form = useForm({
    country_id: '',
    city_id: '',
    area_id: '',
    property_id: '',
    name: '',
    address: '',
    map_link: '',
    number_of_kitchens: 1,
    number_of_rooms: 1,
    common_bathrooms: 1,
    seating: 1,
    details: '',
    video_link: '',
    expiration_date: '',
    selection: 'rooms', // or 'entire'
    rooms: [{ ...props.initialRoom }],
    entireProperty: { ...props.initialEntireProperty },
    freeMaintains: [],
    freeAmenities: [],
    paidMaintains: [{ maintain_id: '', price: 0 }],
    paidAmenities: [{ amenity_id: '', price: 0 }],
    photos: []
})


const handleNewAmenity = () => {
    newAmenityForm.post(route('admin.amenities.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showNewAmenityModal.value = false
            newAmenityForm.reset()
            // Refresh amenities list
        },
    })
}

const handleNewMaintain = () => {
    newMaintainForm.post(route('admin.maintains.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showNewMaintainModal.value = false
            newMaintainForm.reset()
            photoPreview.value = null
            // Refresh maintains list
        },
    })
}

const selectPhoto = () => {
    photoInput.value.click()
}

const updatePhoto = (e) => {
    const file = e.target.files[0]
    if (!file) return

    newMaintainForm.photo = file

    const reader = new FileReader()
    reader.onload = (e) => {
        photoPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
}

const fetchCities = async (countryId) => {
    try {
        const response = await fetch(`/admin/cities/${countryId}`)
        cities.value = await response.json()
        form.city_id = ''
        form.area_id = ''
    } catch (error) {
        console.error('Error fetching cities:', error)
    }
}

const fetchAreas = async (cityId) => {
    try {
        const response = await fetch(`/admin/areas/${cityId}`)
        areas.value = await response.json()
        form.area_id = ''
    } catch (error) {
        console.error('Error fetching areas:', error)
    }
}

const addRoom = () => {
    form.rooms.push({ ...props.initialRoom })
}

const removeRoom = (index) => {
    form.rooms.splice(index, 1)
}

const addPriceOption = (roomIndex) => {
    form.rooms[roomIndex].prices.push({
        type: '',
        fixed_price: 0,
        discount_price: null,
        booking_price: 0
    })
}

const removePriceOption = (roomIndex, priceIndex) => {
    form.rooms[roomIndex].prices.splice(priceIndex, 1)
}

const handlePhotoUpload = (e) => {
    const files = e.target.files
    form.photos = [...form.photos, ...files]

    // Generate previews
    for (let i = 0; i < files.length; i++) {
        const reader = new FileReader()
        reader.onload = (e) => {
            photoPreview.value.push(e.target.result)
        }
        reader.readAsDataURL(files[i])
    }
}

const removePhoto = (index) => {
    form.photos.splice(index, 1)
    photoPreview.value.splice(index, 1)
}

const submit = () => {
    form.post(route('admin.packages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Reset form or redirect
        }
    })
}
</script>

<template>
    <AdminLayout>

        <Head title="Create Package" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-8">
                        <!-- Basic Information Section -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h2 class="text-2xl font-semibold mb-6">Basic Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="name" value="Package Name" />
                                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full"
                                        required />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="country" value="Country" />
                                    <Select id="country" v-model="form.country_id"
                                        @change="fetchCities(form.country_id)" :options="countries" option-label="name"
                                        option-value="id" class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.country_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="city" value="City" />
                                    <Select id="city" v-model="form.city_id" @change="fetchAreas(form.city_id)"
                                        :options="cities" option-label="name" option-value="id"
                                        class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.city_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="area" value="Area" />
                                    <Select id="area" v-model="form.area_id" :options="areas" option-label="name"
                                        option-value="id" class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.area_id" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Property Details Section -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-8">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-semibold">Property Details</h2>
                                <div class="space-x-2">
                                    <SecondaryButton @click="showNewAmenityModal = true">
                                        Add New Amenity
                                    </SecondaryButton>
                                    <SecondaryButton @click="showNewMaintainModal = true">
                                        Add New Maintain
                                    </SecondaryButton>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Property Selection -->
                                <div>
                                    <InputLabel for="property" value="Property Type" />
                                    <Select id="property" v-model="form.property_id" :options="properties"
                                        option-label="name" option-value="id" class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.property_id" class="mt-2" />
                                </div>

                                <!-- Numeric Inputs -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="kitchens" value="Number of Kitchens" />
                                        <TextInput id="kitchens" v-model="form.number_of_kitchens" type="number"
                                            class="mt-1 block w-full" min="0" required />
                                    </div>

                                    <div>
                                        <InputLabel for="bathrooms" value="Common Bathrooms" />
                                        <TextInput id="bathrooms" v-model="form.common_bathrooms" type="number"
                                            class="mt-1 block w-full" min="0" required />
                                    </div>
                                </div>
                            </div>

                            <!-- Amenities and Maintains Section -->
                            <div class="mt-6">
                                <h3 class="text-lg font-medium mb-4">Amenities & Maintains</h3>

                                <!-- Free Amenities -->
                                <div class="space-y-4">
                                    <h4 class="text-sm font-medium">Free Amenities</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <template v-for="amenity in amenities" :key="amenity.id">
                                            <label
                                                class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50">
                                                <input type="checkbox" :value="amenity.id" v-model="form.freeAmenities"
                                                    class="mr-2" />
                                                {{ amenity.name }}
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <!-- Paid Amenities -->
                                <div class="mt-4 space-y-4">
                                    <h4 class="text-sm font-medium">Paid Amenities</h4>
                                    <div class="space-y-2">
                                        <div v-for="(item, index) in form.paidAmenities" :key="index"
                                            class="flex items-center gap-4">
                                            <Select v-model="item.amenity_id"
                                                :options="amenities.filter(a => !form.freeAmenities.includes(a.id))"
                                                option-label="name" option-value="id" class="w-full" />
                                            <TextInput v-model="item.price" type="number" min="0" step="0.01"
                                                placeholder="Price" class="w-32" />
                                            <button type="button" @click="form.paidAmenities.splice(index, 1)"
                                                class="text-red-500 hover:text-red-700">
                                                <TrashIcon class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                    <SecondaryButton @click="form.paidAmenities.push({ amenity_id: '', price: 0 })"
                                        class="mt-2">
                                        Add Paid Amenity
                                    </SecondaryButton>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Section -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h2 class="text-2xl font-semibold mb-6">Pricing</h2>

                            <!-- Selection Type -->
                            <div class="mb-6">
                                <InputLabel value="Pricing Type" />
                                <div class="mt-2 space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.selection" value="rooms" class="mr-2" />
                                        Room-wise
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.selection" value="entire" class="mr-2" />
                                        Entire Property
                                    </label>
                                </div>
                            </div>

                            <!-- Room-wise Pricing -->
                            <template v-if="form.selection === 'rooms'">
                                <div v-for="(room, roomIndex) in form.rooms" :key="roomIndex"
                                    class="border rounded-lg p-4 mb-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-medium">Room {{ roomIndex + 1 }}</h3>
                                        <button type="button" @click="removeRoom(roomIndex)"
                                            class="text-red-500 hover:text-red-700">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>

                                    <!-- Room Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <InputLabel value="Room Name" />
                                            <TextInput v-model="room.name" class="mt-1 block w-full" required />
                                        </div>
                                        <div>
                                            <InputLabel value="Number of Beds" />
                                            <TextInput v-model="room.number_of_beds" type="number" min="1"
                                                class="mt-1 block w-full" required />
                                        </div>
                                        <div>
                                            <InputLabel value="Number of Bathrooms" />
                                            <TextInput v-model="room.number_of_bathrooms" type="number" min="0"
                                                class="mt-1 block w-full" required />
                                        </div>
                                    </div>

                                    <!-- Room Prices -->
                                    <div class="space-y-4">
                                        <div v-for="(price, priceIndex) in room.prices" :key="priceIndex"
                                            class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                            <div>
                                                <InputLabel value="Type" />
                                                <Select v-model="price.type" :options="[
                                                    { label: 'Day', value: 'Day' },
                                                    { label: 'Week', value: 'Week' },
                                                    { label: 'Month', value: 'Month' }
                                                ]" class="mt-1 block w-full" required />
                                            </div>
                                            <div>
                                                <InputLabel value="Fixed Price" />
                                                <TextInput v-model="price.fixed_price" type="number" min="0" step="0.01"
                                                    class="mt-1 block w-full" required />
                                            </div>
                                            <div>
                                                <InputLabel value="Discount Price" />
                                                <TextInput v-model="price.discount_price" type="number" min="0"
                                                    step="0.01" class="mt-1 block w-full" />
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <InputLabel value="Booking Price" />
                                                <TextInput v-model="price.booking_price" type="number" min="0"
                                                    step="0.01" class="mt-1 block w-full" required />
                                                <button type="button" @click="removePriceOption(roomIndex, priceIndex)"
                                                    class="text-red-500 hover:text-red-700"
                                                    v-if="room.prices.length > 1">
                                                    <TrashIcon class="w-5 h-5" />
                                                </button>
                                            </div>
                                        </div>
                                        <SecondaryButton type="button" @click="addPriceOption(roomIndex)" class="mt-2"
                                            v-if="room.prices.length < 3">
                                            Add Price Option
                                        </SecondaryButton>
                                    </div>
                                </div>

                                <SecondaryButton type="button" @click="addRoom" class="mt-4">
                                    <PlusCircleIcon class="w-5 h-5 mr-2" />
                                    Add Another Room
                                </SecondaryButton>
                            </template>

                            <!-- Entire Property Pricing -->
                            <template v-else>
                                <div class="border rounded-lg p-4">
                                    <h3 class="text-lg font-medium mb-4">Entire Property Pricing</h3>
                                    <div class="space-y-4">
                                        <div v-for="(price, index) in form.entireProperty.prices" :key="index"
                                            class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                            <div>
                                                <InputLabel value="Type" />
                                                <Select v-model="price.type" :options="[
                                                    { label: 'Day', value: 'Day' },
                                                    { label: 'Week', value: 'Week' },
                                                    { label: 'Month', value: 'Month' }
                                                ]" class="mt-1 block w-full" required />
                                            </div>
                                            <div>
                                                <InputLabel value="Fixed Price" />
                                                <TextInput v-model="price.fixed_price" type="number" min="0" step="0.01"
                                                    class="mt-1 block w-full" required />
                                            </div>
                                            <div>
                                                <InputLabel value="Discount Price" />
                                                <TextInput v-model="price.discount_price" type="number" min="0"
                                                    step="0.01" class="mt-1 block w-full" />
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="flex-grow">
                                                    <InputLabel value="Booking Price" />
                                                    <TextInput v-model="price.booking_price" type="number" min="0"
                                                        step="0.01" class="mt-1 block w-full" required />
                                                </div>
                                                <button type="button"
                                                    @click="form.entireProperty.prices.splice(index, 1)"
                                                    class="text-red-500 hover:text-red-700"
                                                    v-if="form.entireProperty.prices.length > 1">
                                                    <TrashIcon class="w-5 h-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <SecondaryButton type="button" @click="form.entireProperty.prices.push({
                                        type: '', fixed_price: 0, discount_price: null, booking_price: 0
                                    })" class="mt-4" v-if="form.entireProperty.prices.length < 3">
                                        Add Price Option
                                    </SecondaryButton>
                                </div>
                            </template>
                        </div>

                        <!-- Photos Section -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h2 class="text-2xl font-semibold mb-6">Photos</h2>

                            <!-- Photo Upload Area -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-64
                              border-2 border-gray-300 border-dashed rounded-lg
                              cursor-pointer bg-gray-50 dark:hover:bg-gray-800
                              dark:bg-gray-700 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <PhotoIcon class="w-8 h-8 mb-4 text-gray-500" />
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                <span class="font-semibold">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                PNG, JPG or JPEG (MAX. 1MB)
                                            </p>
                                        </div>
                                        <input type="file" class="hidden" multiple accept="image/*"
                                            @change="handlePhotoUpload" />
                                    </label>
                                </div>

                                <!-- Photo Previews -->
                                <div v-if="photoPreview.length" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div v-for="(preview, index) in photoPreview" :key="index" class="relative group">
                                        <img :src="preview" alt="Preview" class="w-full h-32 object-cover rounded-lg" />
                                        <button type="button" @click="removePhoto(index)" class="absolute top-2 right-2 p-1 bg-red-500 rounded-full
                                   text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details Section -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h2 class="text-2xl font-semibold mb-6">Additional Details</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="address" value="Address" />
                                    <TextInput v-model="form.address" type="text" class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.address" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="map_link" value="Map Link" />
                                    <TextInput v-model="form.map_link" type="url" class="mt-1 block w-full" />
                                    <InputError :message="form.errors.map_link" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="video_link" value="Video Link" />
                                    <TextInput v-model="form.video_link" type="url" class="mt-1 block w-full" />
                                    <InputError :message="form.errors.video_link" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="expiration_date" value="Expiration Date" />
                                    <TextInput v-model="form.expiration_date" type="date" class="mt-1 block w-full"
                                        :min="new Date().toISOString().split('T')[0]" required />
                                    <InputError :message="form.errors.expiration_date" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <InputLabel for="details" value="Details" />
                                    <textarea v-model="form.details" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                                  dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500
                                  focus:ring-indigo-500 shadow-sm"></textarea>
                                    <InputError :message="form.errors.details" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-3">
                            <SecondaryButton>
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing" type="submit">
                                {{ form.processing ? 'Creating...' : 'Create Package' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- New Amenity Modal -->
        <Modal :show="showNewAmenityModal" @close="showNewAmenityModal = false" title="Add New Amenity">
            <form @submit.prevent="handleNewAmenity" class="space-y-4">
                <div>
                    <InputLabel for="amenity_type" value="Amenity Type" />
                    <Select id="amenity_type" v-model="newAmenityForm.amenity_type_id" :options="amenityTypes"
                        option-label="type" option-value="id" placeholder="Select amenity type"
                        :error="!!newAmenityForm.errors.amenity_type_id" required />
                    <InputError :message="newAmenityForm.errors.amenity_type_id" />
                </div>

                <div>
                    <InputLabel for="amenity_name" value="Name" />
                    <TextInput id="amenity_name" v-model="newAmenityForm.name" type="text"
                        :error="!!newAmenityForm.errors.name" required />
                    <InputError :message="newAmenityForm.errors.name" />
                </div>
            </form>

            <template #footer>
                <SecondaryButton @click="showNewAmenityModal = false">
                    Cancel
                </SecondaryButton>
                <PrimaryButton class="ml-3" :disabled="newAmenityForm.processing" @click="handleNewAmenity">
                    {{ newAmenityForm.processing ? 'Creating...' : 'Create Amenity' }}
                </PrimaryButton>
            </template>
        </Modal>

        <!-- New Maintain Modal -->
        <Modal :show="showNewMaintainModal" @close="showNewMaintainModal = false" title="Add New Maintain">
            <form @submit.prevent="handleNewMaintain" class="space-y-4">
                <div>
                    <InputLabel for="maintain_type" value="Maintain Type" />
                    <Select id="maintain_type" v-model="newMaintainForm.maintain_type_id" :options="maintainTypes"
                        option-label="type" option-value="id" placeholder="Select maintain type"
                        :error="!!newMaintainForm.errors.maintain_type_id" required />
                    <InputError :message="newMaintainForm.errors.maintain_type_id" />
                </div>

                <div>
                    <InputLabel for="maintain_name" value="Name" />
                    <TextInput id="maintain_name" v-model="newMaintainForm.name" type="text"
                        :error="!!newMaintainForm.errors.name" required />
                    <InputError :message="newMaintainForm.errors.name" />
                </div>

                <div>
                    <InputLabel value="Photo" />
                    <input type="file" ref="photoInput" @change="updatePhoto" class="hidden" accept="image/*">

                    <div class="mt-2">
                        <div v-if="!photoPreview" @click="selectPhoto"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-2 text-sm text-gray-600">
                                    Click to upload a photo
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    PNG, JPG up to 1MB
                                </div>
                            </div>
                        </div>

                        <div v-else class="relative">
                            <img :src="photoPreview" alt="Preview" class="rounded-lg max-h-64 mx-auto">
                            <button type="button"
                                class="absolute top-2 right-2 p-1 rounded-full bg-gray-100 hover:bg-gray-200"
                                @click="photoPreview = null; newMaintainForm.photo = null">
                                <XMarkIcon class="w-5 h-5 text-gray-600" />
                            </button>
                        </div>
                    </div>
                    <InputError :message="newMaintainForm.errors.photo" />
                </div>
            </form>

            <template #footer>
                <SecondaryButton @click="showNewMaintainModal = false">
                    Cancel
                </SecondaryButton>
                <PrimaryButton class="ml-3" :disabled="newMaintainForm.processing" @click="handleNewMaintain">
                    {{ newMaintainForm.processing ? 'Creating...' : 'Create Maintain' }}
                </PrimaryButton>
            </template>
        </Modal>
    </AdminLayout>
</template>
