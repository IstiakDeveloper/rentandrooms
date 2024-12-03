    <template>
        <UserLayout>
            <div class="container mx-auto px-4 py-8">
                <!-- Title -->
                <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-100">
                    Available Packages
                </h1>

                <!-- Filters -->
                <div class="mb-6 grid md:grid-cols-4 gap-4">
                    <select v-model="form.country_id"
                        class="form-select bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 text-gray-800 dark:text-gray-200 rounded-lg py-2 px-4 focus:outline-none"
                        @change="submitFilter">
                        <option value="">All Countries</option>
                        <option v-for="country in filterOptions.countries" :key="country.id" :value="country.id">
                            {{ country.name }}
                        </option>
                    </select>

                    <select v-model="form.city_id"
                        class="form-select bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 text-gray-800 dark:text-gray-200 rounded-lg py-2 px-4 focus:outline-none"
                        @change="submitFilter">
                        <option value="">All Cities</option>
                        <option v-for="city in filterOptions.cities" :key="city.id" :value="city.id">
                            {{ city.name }}
                        </option>
                    </select>

                    <select v-model="form.rooms"
                        class="form-select bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 text-gray-800 dark:text-gray-200 rounded-lg py-2 px-4 focus:outline-none"
                        @change="submitFilter">
                        <option value="">All Rooms</option>
                        <option v-for="roomCount in filterOptions.room_counts" :key="roomCount" :value="roomCount">
                            {{ roomCount }} Rooms
                        </option>
                    </select>

                    <input v-model="form.search" placeholder="Search packages..."
                        class="form-input bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 text-gray-800 dark:text-gray-200 rounded-lg py-2 px-4 focus:outline-none"
                        @input="debounceFilter" />
                </div>

                <!-- Packages Grid -->
                <div class="grid md:grid-cols-3 gap-6">
                    <div v-for="packageItem in packages.data" :key="packageItem.id"
                        class="bg-white dark:bg-gray-900 shadow-md rounded-lg overflow-hidden">
                        <!-- Package Image -->
                        <img :src="getImageUrl(packageItem.main_photo)" alt="Package Image"
                            class="w-full h-48 object-cover" />

                        <!-- Package Details -->
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-100">
                                {{ packageItem.name }}
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">
                                {{ packageItem.address }}
                            </p>
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ packageItem.number_of_rooms }} Rooms
                                    </span>
                                    <div class="flex items-center mt-1">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ packageItem.city }}, {{ packageItem.country }}
                                        </span>
                                    </div>
                                </div>
                                <Link
                    :href="route('user.packages.show', packageItem.id)"
                    class="text-blue-500 dark:text-blue-400 hover:underline"
                    >
                    View Details
                    </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-center">
                    <pagination :links="packages.links" />
                </div>
            </div>
        </UserLayout>
    </template>

<script setup>
import { reactive } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import UserLayout from '@/Layouts/UserLayout.vue';


const baseUrl = import.meta.env.VITE_APP_URL || 'http://127.0.0.1:8000'; // Replace with your actual URL if not using an env variable.

const getImageUrl = (path) => {
  return path ? `${baseUrl}/storage/${path}` : `${baseUrl}/default-package.jpg`;
};
// Component props
const props = defineProps({
    packages: Object,
    filterOptions: Object,
    filters: Object
});

// Reactive form for filters
const form = reactive({
    country_id: props.filters.country_id || '',
    city_id: props.filters.city_id || '',
    rooms: props.filters.rooms || '',
    search: props.filters.search || '',
    page: props.filters.page || 1
});

// Submit filter function
const submitFilter = () => {
    router.get(route('packages.index'), form, {
        preserveState: true,
        preserveScroll: true
    });
};

// Debounced filter for search input
const debounceFilter = debounce(submitFilter, 500);
</script>

<style scoped>
.container {
    max-width: 1200px;
}
</style>
