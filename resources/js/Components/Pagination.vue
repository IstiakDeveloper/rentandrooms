<template>
    <div v-if="links.length > 3" class="flex items-center justify-center space-x-2">
      <Link
        v-for="link in links"
        :key="link.label"
        :href="link.url"
        :class="{
          'px-4 py-2 border rounded': true,
          'bg-blue-500 text-white': link.active,
          'text-gray-700 hover:bg-gray-100': !link.active
        }"
        v-html="link.label"
      />
    </div>
  </template>

  <script setup>
  import { Link } from '@inertiajs/vue3';

  defineProps({
    links: {
      type: Array,
      required: true
    }
  });
  </script>
