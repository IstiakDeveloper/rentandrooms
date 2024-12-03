<template>
    <div class="space-y-4">
      <h3 class="font-medium text-gray-900">{{ label }}</h3>
      <div class="space-y-2">
        <div v-for="option in options" :key="option.id" class="flex items-center">
          <input
            type="checkbox"
            :id="option.id"
            :value="option.id"
            v-model="selectedItems"
            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
          >
          <label :for="option.id" class="ml-2 text-sm text-gray-700">
            {{ option.name }}
          </label>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { computed } from 'vue'

  const props = defineProps({
    modelValue: Array,
    options: Array,
    label: String
  })

  const emit = defineEmits(['update:modelValue'])

  const selectedItems = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
  })
  </script>
