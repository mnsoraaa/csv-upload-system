import './bootstrap';
import { createApp } from 'vue';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import FileUpload from './components/FileUpload.vue';
import FileHistory from './components/FileHistory.vue';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws'],
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-vue-app]').forEach(element => {
        const app = createApp({});

        app.component('FileUpload', FileUpload);
        app.component('FileHistory', FileHistory);

        app.mount(element);
    });
});
