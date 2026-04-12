import { reactive } from 'vue';

const state = reactive({
    message: null,
    type: 'success', // 'success' | 'error' | 'info'
    show: false,
});

let timeout = null;

export function useToast() {
    const showToast = (message, type = 'success', duration = 3000) => {
        if (timeout) clearTimeout(timeout);
        
        state.message = message;
        state.type = type;
        state.show = true;

        timeout = setTimeout(() => {
            state.show = false;
        }, duration);
    };

    return {
        state,
        showToast,
    };
}
