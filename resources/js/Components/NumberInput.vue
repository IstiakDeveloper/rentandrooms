<template>
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700">{{ label }}</label>
      <input
        type="number"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        min="0"
      >
    </div>
  </template>

  <script setup>
  defineProps({
    label: String,
    modelValue: [Number, String]
  })
  defineEmits(['update:modelValue'])
  </script>
