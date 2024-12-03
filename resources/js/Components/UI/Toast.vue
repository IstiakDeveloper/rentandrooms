<template>
  <div v-if="show" class="fixed right-4 top-4 z-50">
    <div :class="[
      'p-4 rounded-lg shadow-lg transition-all transform',
      type === 'success' ? 'bg-green-500' : 'bg-red-500',
      'text-white'
    ]">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const show = ref(false);
const message = ref('');
const type = ref('success');

const toast = {
  success(msg) {
    message.value = msg;
    type.value = 'success';
    show.value = true;
    setTimeout(() => show.value = false, 3000);
  },
  error(msg) {
    message.value = msg;
    type.value = 'error';
    show.value = true;
    setTimeout(() => show.value = false, 3000);
  }
};

defineExpose({ toast });
</script>
