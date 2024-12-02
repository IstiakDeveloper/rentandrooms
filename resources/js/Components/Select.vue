<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    default: () => [] // Provide default empty array
  },
  optionLabel: {
    type: String,
    default: 'label'
  },
  optionValue: {
    type: String,
    default: 'value'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  placeholder: {
    type: String,
    default: 'Select an option'
  },
  error: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const computedOptions = computed(() => {
  if (!Array.isArray(props.options)) return []

  return props.options.map(option => {
    if (!option) return { label: '', value: '' }

    if (typeof option === 'object') {
      return {
        label: option[props.optionLabel] || '',
        value: option[props.optionValue] || ''
      }
    }
    return {
      label: option,
      value: option
    }
  })
})

const updateValue = (event) => {
  emit('update:modelValue', event.target.value)
}

const selectClasses = computed(() => {
  return [
    'block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm',
    { 'border-red-300 focus:border-red-500 focus:ring-red-500': props.error },
    { 'bg-gray-50': props.disabled }
  ]
})
</script>

<template>
  <select
    :value="modelValue"
    @change="updateValue"
    :disabled="disabled"
    :required="required"
    :class="selectClasses"
  >
    <option value="" disabled selected>{{ placeholder }}</option>
    <option
      v-for="option in computedOptions"
      :key="option.value"
      :value="option.value"
      class="py-2"
    >
      {{ option.label }}
    </option>
  </select>
</template>
