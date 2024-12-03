<!-- components/Packages/PriceInput.vue -->
<script setup>
defineProps({
  modelValue: Object,
  showRemove: Boolean
});

const emit = defineEmits(['update:modelValue', 'remove']);

const types = [
  { id: 'Day', name: 'Daily' },
  { id: 'Week', name: 'Weekly' },
  { id: 'Month', name: 'Monthly' }
];
</script>

<template>
  <div class="grid grid-cols-4 gap-4 items-end bg-gray-50 p-4 rounded-lg">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
      <select
        v-model="modelValue.type"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
      >
        <option v-for="type in types" :key="type.id" :value="type.id">{{ type.name }}</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Fixed Price</label>
      <input
        type="number"
        v-model.number="modelValue.fixed_price"
        min="0"
        step="0.01"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
      />
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Discount Price</label>
      <input
        type="number"
        v-model.number="modelValue.discount_price"
        min="0"
        step="0.01"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
      />
    </div>
    <div class="flex items-end gap-2">
      <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 mb-1">Booking Price</label>
        <input
          type="number"
          v-model.number="modelValue.booking_price"
          min="0"
          step="0.01"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
        />
      </div>
      <button
        v-if="showRemove"
        @click="$emit('remove')"
        type="button"
        class="p-2 text-red-600 hover:text-red-800"
      >
        <i class="fas fa-trash-alt"></i>
      </button>
    </div>
  </div>
</template>
