import { createApp } from 'vue';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import 'primeicons/primeicons.css';
import HomePage from './HomePage.vue';
import LoginPage from './LoginPage.vue';
import RegisterPage from './RegisterPage.vue';

const authenticationPaths = ['/login', '/forgot-password'];
let RootComponent = HomePage;

if (authenticationPaths.includes(window.location.pathname)) {
    RootComponent = LoginPage;
} else if (window.location.pathname === '/register') {
    RootComponent = RegisterPage;
}

createApp(RootComponent)
    .use(PrimeVue, {
        theme: {
            preset: Aura,
        },
    })
    .mount('#app');
