<script setup>
import { computed, ref } from 'vue';
import Button from 'primevue/button';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import Password from 'primevue/password';

const isForgotPasswordPage = window.location.pathname === '/forgot-password';
const username = ref('');
const password = ref('');
const errors = ref({});
const isSubmitting = ref(false);
const registrationCompleted = new URLSearchParams(window.location.search).has('registered');
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

const pageTitle = computed(() => isForgotPasswordPage ? 'Reset your password' : 'Sign in');
const pageDescription = computed(() => isForgotPasswordPage
    ? 'Enter your username and we will send you password reset instructions.'
    : 'Use your specs-store account to continue.'
);

const submitLogin = async () => {
    if (isForgotPasswordPage) {
        return;
    }

    errors.value = {};
    isSubmitting.value = true;

    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                username: username.value,
                password: password.value,
            }),
        });

        if (response.status === 422) {
            const validationResponse = await response.json();
            errors.value = validationResponse.errors ?? {};

            return;
        }

        if (!response.ok) {
            errors.value = { form: ['Sign in could not be completed. Please try again.'] };

            return;
        }

        const authenticatedResponse = await response.json();

        window.location.assign(authenticatedResponse.redirect ?? '/');
    } catch {
        errors.value = { form: ['Sign in could not be completed. Please try again.'] };
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <div class="login-shell">
        <header class="login-header">
            <a class="brand" href="/" aria-label="specs-store αρχική">
                <span class="brand-mark">SS</span>
                <span class="brand-copy">
                    <strong>specs-store</strong>
                    <small>PC hardware specialists</small>
                </span>
            </a>

            <Button as="a" href="/" label="Back to store" icon="pi pi-arrow-left" severity="secondary" text />
        </header>

        <main class="login-main">
            <Card class="auth-panel">
                <template #title>{{ pageTitle }}</template>
                <template #subtitle>{{ pageDescription }}</template>
                <template #content>
                    <form class="auth-form" @submit.prevent="submitLogin">
                        <Message v-if="registrationCompleted && !isForgotPasswordPage" severity="success">
                            Your account was created. You can now sign in.
                        </Message>
                        <Message v-if="errors.form" severity="error">{{ errors.form[0] }}</Message>

                        <div class="auth-field">
                            <label for="username">Username</label>
                            <InputText
                                id="username"
                                v-model="username"
                                name="username"
                                autocomplete="username"
                                placeholder="Enter your username"
                                :invalid="Boolean(errors.username)"
                                required
                                autofocus
                            />
                            <Message v-if="errors.username" severity="error" size="small" variant="simple">
                                {{ errors.username[0] }}
                            </Message>
                        </div>

                        <div v-if="!isForgotPasswordPage" class="auth-field">
                            <label for="password">Password</label>
                            <Password
                                v-model="password"
                                input-id="password"
                                name="password"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                :feedback="false"
                                :invalid="Boolean(errors.password)"
                                toggle-mask
                                fluid
                                required
                            />
                            <Message v-if="errors.password" severity="error" size="small" variant="simple">
                                {{ errors.password[0] }}
                            </Message>
                        </div>

                        <Button
                            type="submit"
                            :label="isForgotPasswordPage ? 'Send reset link' : 'Sign in'"
                            :icon="isForgotPasswordPage ? 'pi pi-envelope' : 'pi pi-sign-in'"
                            :loading="isSubmitting"
                            fluid
                        />

                        <a v-if="!isForgotPasswordPage" class="forgot-password-link" href="/forgot-password">
                            Forgot password?
                        </a>
                        <a v-if="!isForgotPasswordPage" class="signup-link" href="/register">
                            Sign up if you do not have an account
                        </a>
                        <a v-else class="forgot-password-link" href="/login">
                            Back to sign in
                        </a>
                    </form>
                </template>
            </Card>
        </main>
    </div>
</template>
