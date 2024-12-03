<template>
    <UserLayout>
        <div class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Package Header -->
            <div class="flex flex-col md:flex-row justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        {{ packageProp.name }}
                    </h1>
                    <div class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <span>{{ packageProp.address }}</span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="flex flex-col items-end">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold" :class="[
                        packageProp.status === 'active'
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                    ]">
                        {{ packageProp.status }}
                    </span>
                </div>
            </div>

            <!-- Package Details Grid -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Property Details Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                        <i class="fas fa-home mr-2 text-gray-600 dark:text-gray-400"></i>
                        Property Details
                    </h2>
                    <div class="space-y-3">
                        <PropertyDetailItem label="Rooms" :value="packageProp.number_of_rooms" />
                        <PropertyDetailItem label="Kitchens" :value="packageProp.number_of_kitchens" />
                        <PropertyDetailItem label="Common Bathrooms" :value="packageProp.common_bathrooms" />
                        <PropertyDetailItem label="Seating Capacity" :value="packageProp.seating" />
                    </div>
                </div>

                <!-- Location Details Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                        <i class="fas fa-globe mr-2 text-gray-600 dark:text-gray-400"></i>
                        Location
                    </h2>
                    <div class="space-y-3">
                        <PropertyDetailItem label="Country" :value="packageProp.country.name" />
                        <PropertyDetailItem label="City" :value="packageProp.city.name" />
                        <PropertyDetailItem label="Area" :value="packageProp.area.name" />

                        <!-- Map Link -->
                        <div v-if="packageProp.map_link" class="mt-4">
                            <a :href="packageProp.map_link" target="_blank"
                                class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                <i class="fas fa-map-marked-alt mr-2"></i>
                                View on Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rooms Section -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                    <i class="fas fa-bed mr-2 text-gray-600 dark:text-gray-400"></i>
                    Rooms
                </h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="room in packageProp.rooms" :key="room.id"
                        class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">
                            <i class="fas fa-door-open mr-2 text-gray-600 dark:text-gray-400"></i>
                            {{ room.name }}
                        </h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Beds:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ room.number_of_beds }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Bathrooms:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ room.number_of_bathrooms }}</span>
                            </div>

                            <!-- Room Prices -->
                            <div v-if="room.room_prices.length" class="mt-4">
                                <h4 class="font-semibold mb-2 text-gray-700 dark:text-gray-300">Pricing</h4>
                                <div v-for="price in room.room_prices" :key="price.id"
                                    class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ price.type }} Rate:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ price.fixed_price }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Amenities and Maintenance -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Amenities Card -->
                <div v-if="packageProp.package_amenities.length"
                    class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                        <i class="fas fa-concierge-bell mr-2 text-gray-600 dark:text-gray-400"></i>
                        Amenities
                    </h2>
                    <ul class="space-y-2">
                        <li v-for="amenity in packageProp.package_amenities" :key="amenity.id"
                            class="flex justify-between text-gray-700 dark:text-gray-300">
                            <span>{{ amenity.amenity.name }}</span>
                            <span>{{ amenity.price ? `$${amenity.price}` : 'Free' }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Maintenance Card -->
                <div v-if="packageProp.package_maintains.length"
                    class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                        <i class="fas fa-tools mr-2 text-gray-600 dark:text-gray-400"></i>
                        Maintenance
                    </h2>
                    <ul class="space-y-2">
                        <li v-for="maintain in packageProp.package_maintains" :key="maintain.id"
                            class="flex justify-between text-gray-700 dark:text-gray-300">
                            <span>{{ maintain.maintain.name }}</span>
                            <div class="flex items-center space-x-2">
                                <span>
                                    {{ maintain.price ? `$${maintain.price}` : 'Included' }}
                                </span>
                                <span class="px-2 py-1 rounded-full text-xs" :class="[
                                    maintain.is_paid
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                                ]">
                                    {{ maintain.is_paid ? 'Paid' : 'Pending' }}
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Photo Gallery -->
            <section v-if="photos.length" class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                    <i class="fas fa-images mr-2 text-gray-600 dark:text-gray-400"></i>
                    Photo Gallery
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div v-for="(photo, index) in photos" :key="photo.id" class="relative group cursor-pointer"
                        @click="openLightbox(index)">
                        <img :src="getFullImageUrl(photo.url)" alt="Package Photo"
                            class="w-full h-48 object-cover rounded-lg shadow transition-transform duration-300 group-hover:scale-105" />
                    </div>
                </div>
            </section>

            <!-- Video Tour -->
            <section v-if="packageProp.video_link" class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                    <i class="fas fa-video mr-2 text-gray-600 dark:text-gray-400"></i>
                    Video Tour
                </h2>
                <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-lg">
                    <iframe :src="getYouTubeEmbedUrl(packageProp.video_link)" frameborder="0"
                        allow="autoplay; encrypted-media" allowfullscreen class="w-full h-full"></iframe>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue'
import { h, ref, onMounted, onUnmounted } from 'vue'
import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'

const props = defineProps({
    package: {
        type: Object,
        required: true
    }
})
const getFullImageUrl = (path) => {
    return `${import.meta.env.VITE_APP_URL}/storage/${path}`
}

// Rename props.package to packageProp to avoid naming conflict
const packageProp = props.package
// Get photos from package and map them for PhotoSwipe
const photos = props.package.photos.map(photo => ({
    ...photo,
    src: getFullImageUrl(photo.url),
    width: 1200,  // You might want to fetch actual image dimensions
    height: 800
}))

// Utility to get full image URL

// Lightbox setup
let lightbox = null
onMounted(() => {
    lightbox = new PhotoSwipeLightbox({
        gallery: '#gallery',
        children: 'div',
        pswpModule: () => import('photoswipe'),
        wheelToZoom: true,
        zoom: true,
        close: true,
        counter: true,
        arrowPrev: true,
        arrowNext: true,
        preload: [1, 2],
        showHideAnimationType: 'zoom',
        // Customize further as needed
        bgOpacity: 0.9,
        paddingBottom: 50,
        spacing: 0.1,
        allowPanToNext: true,
        maxZoomLevel: 2,
    })
    lightbox.init()
})

// Open lightbox method
const openLightbox = (index) => {
    if (lightbox) {
        lightbox.loadAndOpen(index, photos)
    }
}

// Clean up on component unmount
onUnmounted(() => {
    if (lightbox) {
        lightbox.destroy()
        lightbox = null
    }
})

// Utility function to convert YouTube watch URL to embed URL
const getYouTubeEmbedUrl = (url) => {
    // Check if it's already an embed URL
    if (url.includes('embed/')) return url

    // Extract video ID from different YouTube URL formats
    const videoIdMatch = url.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/)

    if (videoIdMatch && videoIdMatch[1]) {
        return `https://www.youtube.com/embed/${videoIdMatch[1]}`
    }

    // If no match, return original URL (fallback)
    return url
}

// Reusable Property Detail Item Component
const PropertyDetailItem = (props) => {
    return h('div', { class: 'flex justify-between' }, [
        h('span', { class: 'text-gray-600 dark:text-gray-400' }, `${props.label}:`),
        h('span', { class: 'text-gray-900 dark:text-gray-100' }, props.value)
    ])
}

PropertyDetailItem.props = {
    label: String,
    value: [String, Number]
}


</script>
