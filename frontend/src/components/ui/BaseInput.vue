<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
    </label>
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :step="step"
      :min="min"
      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm bg-gray-50/50
             focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white outline-none
             transition-all duration-200 disabled:bg-gray-100"
      :disabled="disabled"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  id: { type: String, default: () => `input-${Math.random().toString(36).slice(2, 9)}` },
  error: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  step: { type: String, default: undefined },
  min: { type: String, default: undefined },
})

defineEmits(['update:modelValue'])
</script>
