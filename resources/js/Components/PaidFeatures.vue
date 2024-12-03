<template>
    <div class="space-y-4">
      <h3 class="font-medium text-gray-900">{{ label }}</h3>

      <div v-for="(feature, index) in features" :key="index"
           class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Select {{ label }}</label>
          <select
            v-model="feature.id"
            @change="updateFeature(index, 'id', $event.target.value)"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
            <option value="">Select Option</option>
            <option v-for="option in availableOptions" :key="option.id" :value="option.id">
              {{ option.name }}
            </option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Price</label>
          <input
            type="number"
            v-model="feature.price"
            @input="updateFeature(index, 'price', $event.target.value)"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
        </div>

        <button
          v-if="features.length > 1"
          @click="$emit('remove', index)"
          class="col-span-2 btn-danger"
        >
          <i class="fas fa-trash mr-2"></i> Remove
        </button>
      </div>

      <button @click="$emit('add')" class="btn-secondary">
        <i class="fas fa-plus mr-2"></i> Add {{ label }}
      </button>
    </div>
  </template>

  <script setup>
  import { computed } from 'vue'

  const props = defineProps({
    features: Array,
    options: Array,
    label: String
  })

  const emit = defineEmits(['update:features', 'add', 'remove'])

  const availableOptions = computed(() => {
    const usedIds = props.features.map(f => f.id)
    return props.options.filter(o => !usedIds.includes(o.id))
  })

  const updateFeature = (index, field, value) => {
    const updatedFeatures = [...props.features]
    updatedFeatures[index] = {
      ...updatedFeatures[index],
      [field]: field === 'price' ? Number(value) : value
    }
    emit('update:features', updatedFeatures)
  }
  </script>

  <style scoped>
  .btn-danger {
    @apply px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors;
  }

  .btn-secondary {
    @apply px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors;
  }
  </style>
