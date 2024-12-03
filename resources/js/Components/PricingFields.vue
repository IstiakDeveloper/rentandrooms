<!-- PricingFields.vue -->
<template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Price Type</label>
        <select
          :value="price.type"
          @input="updatePrice('type', $event.target.value)"
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
          <option value="">Select Option</option>
          <option value="Day">Day</option>
          <option value="Week">Week</option>
          <option value="Month">Month</option>
        </select>
      </div>

      <template v-if="price.type">
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">{{ price.type }} Fixed Price</label>
          <input
            type="number"
            :value="price.fixed_price"
            @input="updatePrice('fixed_price', $event.target.value)"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
        </div>

        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">{{ price.type }} Discount Price</label>
          <input
            type="number"
            :value="price.discount_price"
            @input="updatePrice('discount_price', $event.target.value)"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
        </div>

        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">{{ price.type }} Booking Price</label>
          <input
            type="number"
            :value="price.booking_price"
            @input="updatePrice('booking_price', $event.target.value)"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
        </div>
      </template>
    </div>
  </template>

  <script setup>
  const props = defineProps({
    price: Object,
    canRemove: Boolean
  })

  const emit = defineEmits(['update:price', 'remove'])

  const updatePrice = (field, value) => {
    emit('update:price', {
      ...props.price,
      [field]: field === 'type' ? value : Number(value)
    })
  }
  </script>
