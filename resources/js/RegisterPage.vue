<script setup>
import { computed, ref } from 'vue';
import Button from 'primevue/button';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import Password from 'primevue/password';

const username = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const errors = ref({});
const isSubmitting = ref(false);

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
const passwordsMatch = computed(() => passwordConfirmation.value === '' || password.value === passwordConfirmation.value);

const submitRegistration = async () => {
    errors.value = {};
    isSubmitting.value = true;

    try {
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                username: username.value,
                email: email.value,
                password: password.value,
                password_confirmation: passwordConfirmation.value,
            }),
        });

        if (response.status === 422) {
            const validationResponse = await response.json();
            errors.value = validationResponse.errors ?? {};

            return;
        }

        if (!response.ok) {
            errors.value = { form: ['Registration could not be completed. Please try again.'] };

            return;
        }

        window.location.assign('/login?registered=1');
    } catch {
        errors.value = { form: ['Registration could not be completed. Please try again.'] };
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

        <main class="login-main registration-main">
            <Card class="auth-panel registration-panel">
                <template #title>Create account</template>
                <template #subtitle>Register for a specs-store account.</template>
                <template #content>
                    <form class="auth-form" @submit.prevent="submitRegistration">
                        <Message v-if="errors.form" severity="error">{{ errors.form[0] }}</Message>

                        <div class="auth-field">
                            <label for="username">Username</label>
                            <InputText
                                id="username"
                                v-model="username"
                                name="username"
                                autocomplete="username"
                                placeholder="Choose a username"
                                :invalid="Boolean(errors.username)"
                                required
                                autofocus
                            />
                            <Message v-if="errors.username" severity="error" size="small" variant="simple">
                                {{ errors.username[0] }}
                            </Message>
                        </div>

                        <div class="auth-field">
                            <label for="email">Email</label>
                            <InputText
                                id="email"
                                v-model="email"
                                type="email"
                                name="email"
                                autocomplete="email"
                                placeholder="name@example.com"
                                :invalid="Boolean(errors.email)"
                                required
                            />
                            <Message v-if="errors.email" severity="error" size="small" variant="simple">
                                {{ errors.email[0] }}
                            </Message>
                        </div>

                        <div class="auth-field">
                            <label for="register-password">Password</label>
                            <Password
                                v-model="password"
                                input-id="register-password"
                                name="password"
                                autocomplete="new-password"
                                placeholder="Create a password"
                                :feedback="false"
                                :invalid="Boolean(errors.password)"
                                toggle-mask
                                fluid
                                required
                            />
                            <p class="password-requirements">
                                At least 8 characters, 2 uppercase letters, 2 numbers, and 1 special character.
                            </p>
                            <Message v-if="errors.password" severity="error" size="small" variant="simple">
                                {{ errors.password[0] }}
                            </Message>
                        </div>

                        <div class="auth-field">
                            <label for="password-confirmation">Confirm password</label>
                            <Password
                                v-model="passwordConfirmation"
                                input-id="password-confirmation"
                                name="password_confirmation"
                                autocomplete="new-password"
                                placeholder="Repeat your password"
                                :feedback="false"
                                :invalid="Boolean(errors.password) || !passwordsMatch"
                                toggle-mask
                                fluid
                                required
                            />
                            <Message v-if="!passwordsMatch" severity="error" size="small" variant="simple">
                                The passwords do not match.
                            </Message>
                        </div>

                        <Button type="submit" label="Sign up" icon="pi pi-user-plus" :loading="isSubmitting" fluid />

                        <a class="signup-link" href="/login">Already have an account? Sign in</a>
                    </form>
                </template>
            </Card>
        </main>
    </div>
</template>
