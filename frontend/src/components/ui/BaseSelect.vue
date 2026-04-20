<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
    </label>
    <select
      :id="id"
      :value="modelValue"
      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm bg-gray-50/50
             focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white outline-none
             transition-all duration-200 disabled:bg-gray-100"
      :disabled="disabled"
      @change="$emit('update:modelValue', $event.target.value)"
    >
      <option value="" disabled>{{ placeholder }}</option>
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: 'Selecione...' },
  options: { type: Array, default: () => [] },
  id: { type: String, default: () => `select-${Math.random().toString(36).slice(2, 9)}` },
  error: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
})

defineEmits(['update:modelValue'])
</script>
