import { reactive } from 'vue';

const state = reactive({
    message: null,
    type: 'success', // 'success' | 'error' | 'info' | 'point'
    show: false,
    pointAmount: null,
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

    const showPointToast = (amount) => {
        if (timeout) clearTimeout(timeout);

        state.message = `+${amount}P 적립!`;
        state.type = 'point';
        state.pointAmount = amount;
        state.show = true;

        timeout = setTimeout(() => {
            state.show = false;
            state.pointAmount = null;
        }, 4000);
    };

    return {
        state,
        showToast,
        showPointToast,
    };
}
