import { ref } from 'vue'

export function useNotification() {
  const notification = ref(null)
  let timer = null

  function notify(message, type = 'success') {
    if (timer) clearTimeout(timer)

    notification.value = { message, type }

    timer = setTimeout(() => {
      notification.value = null
    }, 4000)
  }

  function notifySuccess(message) {
    notify(message, 'success')
  }

  function notifyError(message) {
    notify(message, 'error')
  }

  function dismiss() {
    if (timer) clearTimeout(timer)
    notification.value = null
  }

  return { notification, notify, notifySuccess, notifyError, dismiss }
}
