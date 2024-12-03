<template>
    <AdminLayout title="Create Package">
        <Toast ref="toastRef" />
        <template #header>
            <div class="flex justify-between items-center px-6 py-4 bg-white/80 backdrop-blur-sm dark:bg-gray-900/80">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Create Package</h2>
                <Link :href="route('admin.packages.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-700 dark:hover:bg-gray-600 transition duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back
                </Link>
            </div>
        </template>

        <div class="max-w-7xl mx-auto p-6">
            <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-8">
                <!-- Error Display -->
                <div v-if="form.errors"
                    class="bg-red-50 dark:bg-red-900/50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                    <div v-for="(error, key) in form.errors" :key="key" class="text-red-700 dark:text-red-400">
                        {{ error }}
                    </div>
                </div>

                <!-- Location Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                        Location Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                            <select v-model="form.country_id" @change="onCountryChange"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="">Select Country</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">
                                    {{ country.name }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                            <select v-model="form.city_id" @change="onCityChange" :disabled="!form.country_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="">Select City</option>
                                <option v-for="city in availableCities" :key="city.id" :value="city.id">
                                    {{ city.name }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Area</label>
                            <select v-model="form.area_id" :disabled="!form.city_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="">Select Area</option>
                                <option v-for="area in availableAreas" :key="area.id" :value="area.id">
                                    {{ area.name }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Property</label>
                            <select v-model="form.property_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="">Select Property</option>
                                <option v-for="property in properties" :key="property.id" :value="property.id">
                                    {{ property.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Basic Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                        Package Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Package
                                Name</label>
                            <input type="text" v-model="form.name"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                            <input type="text" v-model="form.address"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Map Link</label>
                            <input type="text" v-model="form.map_link"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiration
                                Date</label>
                            <input type="date" v-model="form.expiration_date"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select v-model="form.selection"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="">Select Type</option>
                                <option value="entire">Entire Property</option>
                                <option value="room">Room Wise</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Property Numbers -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                        Property Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <NumberInput v-model="form.number_of_kitchens" label="Kitchens" />
                        <NumberInput v-model="form.number_of_rooms" label="Rooms" />
                        <NumberInput v-model="form.common_bathrooms" label="Common Bathrooms" />
                        <NumberInput v-model="form.seating" label="Seating" />
                    </div>
                </div>

                <!-- Entire Property Pricing -->
                <div v-if="form.selection === 'entire'"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                        Entire Property Pricing
                    </h3>
                    <div class="space-y-4">
                        <div v-for="(price, index) in form.entireProperty.prices" :key="index"
                            class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg space-y-4">
                            <PricingFields :price="price" @update:price="updateEntirePropertyPrice(index, $event)"
                                :canRemove="form.entireProperty.prices.length > 1"
                                @remove="removeEntirePropertyPrice(index)" />
                        </div>
                        <button type="button" @click="addEntirePropertyPrice"
                            v-if="form.entireProperty.prices.length < 3"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition duration-150">
                            <i class="fas fa-plus mr-2"></i> Add Price Option
                        </button>
                    </div>
                </div>

                <!-- Room Wise Pricing -->
                <div v-if="form.selection === 'room'"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                        Room Details
                    </h3>
                    <div class="space-y-6">
                        <div v-for="(room, roomIndex) in form.rooms" :key="roomIndex"
                            class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room
                                        Name</label>
                                    <input type="text" v-model="room.name"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                                </div>
                                <NumberInput v-model="room.number_of_beds" label="Beds" />
                                <NumberInput v-model="room.number_of_bathrooms" label="Attach Bathrooms" />
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">Pricing Options</h4>
                                <div v-for="(price, priceIndex) in room.prices" :key="priceIndex"
                                    class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm space-y-4">
                                    <PricingFields :price="price"
                                        @update:price="updateRoomPrice(roomIndex, priceIndex, $event)"
                                        :canRemove="room.prices.length > 1"
                                        @remove="removeRoomPrice(roomIndex, priceIndex)" />
                                </div>
                            </div>

                            <div class="flex space-x-4">
                                <button type="button" @click="addRoomPrice(roomIndex)" class="btn-secondary">
                                    <i class="fas fa-plus mr-2"></i> Add Price Option
                                </button>
                                <button type="button" @click="removeRoom(roomIndex)" class="btn-danger">
                                    <i class="fas fa-trash mr-2"></i> Remove Room
                                </button>
                            </div>
                        </div>

                        <button type="button" @click="addRoom" class="btn-primary">
                            <i class="fas fa-plus mr-2"></i> Add Room
                        </button>
                    </div>
                </div>

                <!-- Features -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Free Features -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Free Features
                        </h3>
                        <div class="grid grid-cols-1 gap-6">
                            <CheckboxGroup v-model="form.freeMaintains" :options="maintains" label="Free Maintains" />
                            <CheckboxGroup v-model="form.freeAmenities" :options="amenities" label="Free Amenities" />
                        </div>
                    </div>

                    <!-- Paid Features -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Paid Features
                        </h3>
                        <div class="space-y-8">
                            <PaidFeatures v-model:features="form.paidMaintains" :options="maintains"
                                label="Paid Maintains" @add="addPaidMaintain" @remove="removePaidMaintain" />
                            <PaidFeatures v-model:features="form.paidAmenities" :options="amenities"
                                label="Paid Amenities" @add="addPaidAmenity" @remove="removePaidAmenity" />
                        </div>
                    </div>
                </div>

                <!-- Photos Upload -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                        Photos
                    </h3>
                    <div class="space-y-4">
                        <div
                            class="group border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 transition-all hover:border-blue-500 dark:hover:border-blue-400">
                            <label class="flex flex-col items-center justify-center cursor-pointer">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                                    <p class="mt-4 text-sm text-gray-600">
                                        <span class="font-semibold text-blue-600">Click to upload</span> or drag and
                                        drop
                                    </p>
                                    <p class="mt-2 text-xs text-gray-500">PNG, JPG, GIF up to 1MB</p>
                                </div>
                                <input type="file" class="hidden" multiple accept="image/*" @change="handlePhotoUpload">
                            </label>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="(photo, index) in form.photos" :key="index"
                                class="relative group rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img :src="photo.preview" alt="Preview" class="w-full h-48 object-cover">
                                <div
                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" @click="removePhoto(index)"
                                        class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                        Additional Information
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Video Link</label>
                            <input type="url" v-model="form.video_link" placeholder="https://..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Package
                                Details</label>
                            <textarea v-model="form.details" rows="4"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400"
                                placeholder="Enter package details..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-6">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 dark:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition duration-150">
                        <i class="fas fa-save mr-2"></i>
                        <span>Create Package</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import NumberInput from '@/Components/NumberInput.vue';
import PricingFields from '@/Components/PricingFields.vue';
import CheckboxGroup from '@/Components/CheckboxGroup.vue';
import PaidFeatures from '@/Components/PaidFeatures.vue';
import Toast from '@/Components/UI/Toast.vue';



const toastRef = ref(null);

const props = defineProps({
    countries: {
        type: Array,
        default: () => []
    },
    cities: {
        type: Array,
        default: () => []
    },
    areas: {
        type: Array,
        default: () => []
    },
    properties: {
        type: Array,
        default: () => []
    },
    maintains: {
        type: Array,
        default: () => []
    },
    amenities: {
        type: Array,
        default: () => []
    }
});

const availableCities = computed(() => {
    const country = props.countries.find(c => c.id === parseInt(form.country_id));
    return country?.cities || [];
});

const availableAreas = computed(() => {
    const country = props.countries.find(c => c.id === parseInt(form.country_id));
    const city = country?.cities?.find(c => c.id === parseInt(form.city_id));
    return city?.areas || [];
})

const form = useForm({
    country_id: '',
    city_id: '',
    area_id: '',
    property_id: '',
    name: '',
    address: '',
    map_link: '',
    number_of_kitchens: 0,
    number_of_rooms: 0,
    common_bathrooms: 0,
    seating: 0,
    details: '',
    video_link: '',
    selection: '',
    expiration_date: '',
    rooms: [{
        name: '',
        number_of_beds: 0,
        number_of_bathrooms: 0,
        prices: [{
            type: '',
            fixed_price: 0,
            discount_price: null,
            booking_price: 0
        }]
    }],
    entireProperty: {
        prices: [{
            type: '',
            fixed_price: 0,
            discount_price: null,
            booking_price: 0
        }]
    },
    freeMaintains: [],
    freeAmenities: [],
    paidMaintains: [{
        maintain_id: '',
        price: 0
    }],
    paidAmenities: [{
        amenity_id: '',
        price: 0
    }],
    photos: []
});



const onCountryChange = () => {
    form.city_id = '';
    form.area_id = '';
};

const onCityChange = () => {
    form.area_id = '';
};

// Room management
const addRoom = () => {
    form.rooms.push({
        name: '',
        number_of_beds: 0,
        number_of_bathrooms: 0,
        prices: [{
            type: '',
            fixed_price: 0,
            discount_price: null,
            booking_price: 0
        }]
    });
};

const removeRoom = (index) => {
    form.rooms.splice(index, 1);
};

// Price management
const addRoomPrice = (roomIndex) => {
    form.rooms[roomIndex].prices.push({
        type: '',
        fixed_price: 0,
        discount_price: null,
        booking_price: 0
    });
};

const removeRoomPrice = (roomIndex, priceIndex) => {
    form.rooms[roomIndex].prices.splice(priceIndex, 1);
};

const updateRoomPrice = (roomIndex, priceIndex, price) => {
    form.rooms[roomIndex].prices[priceIndex] = price;
};

const addEntirePropertyPrice = () => {
    if (form.entireProperty.prices.length < 3) {
        form.entireProperty.prices.push({
            type: '',
            fixed_price: 0,
            discount_price: null,
            booking_price: 0
        });
    }
};

const removeEntirePropertyPrice = (index) => {
    form.entireProperty.prices.splice(index, 1);
};

const updateEntirePropertyPrice = (index, price) => {
    form.entireProperty.prices[index] = price;
};

// Features management
const addPaidMaintain = () => {
    form.paidMaintains.push({
        maintain_id: '',
        price: 0
    });
};

const removePaidMaintain = (index) => {
    form.paidMaintains.splice(index, 1);
};

const addPaidAmenity = () => {
    form.paidAmenities.push({
        amenity_id: '',
        price: 0
    });
};

const removePaidAmenity = (index) => {
    form.paidAmenities.splice(index, 1);
};


const handlePhotoUpload = (e) => {
    const files = Array.from(e.target.files).filter(file => file.type.startsWith('image/'));
    const maxSize = 1024 * 1024; // 1MB

    files.forEach(file => {
        if (file.size > maxSize) {
            toastRef.value?.show({
                type: 'error',
                message: `${file.name} is too large. Maximum size is 1MB`
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            form.photos.push({
                file,
                preview: e.target.result
            });
        };
        reader.readAsDataURL(file);
    });
};

const submit = () => {
    // Create a new FormData instance
    const formData = new FormData();

    // First append all non-file form data
    Object.keys(form).forEach(key => {
        if (key !== 'photos') {
            if (typeof form[key] === 'object') {
                formData.append(key, JSON.stringify(form[key]));
            } else {
                formData.append(key, form[key]);
            }
        }
    });

    // Then append photos properly
    if (form.photos.length > 0) {
        form.photos.forEach((photo, index) => {
            if (photo.file) {
                formData.append(`photos[]`, photo.file); // Note the change here to photos[]
            }
        });
    }

    // Submit the form with the proper FormData
    form.post(route('admin.packages.store'), {
        data: formData,
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toastRef.value?.show({
                type: 'success',
                message: 'Package created successfully'
            });
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors); // For debugging
            toastRef.value?.show({
                type: 'error',
                message: Object.values(errors)[0]
            });
        }
    });
};

const removePhoto = (index) => {
    form.photos.splice(index, 1);
};


// Watchers
watch(() => form.selection, (newValue) => {
    if (newValue === 'entire') {
        form.rooms = [];
    } else if (newValue === 'room') {
        form.entireProperty = {
            prices: [{
                type: '',
                fixed_price: 0,
                discount_price: null,
                booking_price: 0
            }]
        };
    }
});

watch(() => form.freeMaintains, (newValue) => {
    form.paidMaintains = form.paidMaintains.filter(
        pm => !newValue.includes(pm.maintain_id)
    );
}, { deep: true });

watch(() => form.freeAmenities, (newValue) => {
    form.paidAmenities = form.paidAmenities.filter(
        pa => !newValue.includes(pa.amenity_id)
    );
}, { deep: true });
</script>

<style scoped>
.form-grid {
    @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6;
}

.input-group {
    @apply space-y-2;
}

.input-label {
    @apply block text-sm font-medium text-gray-700 dark:text-gray-300;
}

.form-input {
    @apply w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400 shadow-sm transition duration-150;
}

.btn-primary {
    @apply inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition duration-150;
}

.btn-secondary {
    @apply inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white rounded-lg hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800 transition duration-150;
}

.btn-danger {
    @apply inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 text-white rounded-lg hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition duration-150;
}

.section-title {
    @apply text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2;
}
</style>
