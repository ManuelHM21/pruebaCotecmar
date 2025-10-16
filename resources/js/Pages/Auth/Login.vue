<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed, reactive } from "vue";
import Textinput from "@/Components/TextInput.vue";
import Loader from "@/Components/Loader.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const isRegisterMode = ref(false);
const showLoginPassword = ref(false);
const showRegisterPassword = ref(false);
const showRegisterPasswordConfirmation = ref(false);

// Estado del loader
const loaderState = reactive({
    show: false,
    type: 'spinner',
    message: '',
    submessage: ''
});

const loginForm = useForm({
    email: "",
    password: "",
    remember: false,
});

const registerForm = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
});

const submitLogin = () => {
    loaderState.show = true;
    loaderState.type = 'circle';
    loaderState.message = 'Iniciando sesión...';
    loaderState.submessage = 'Por favor espere';

    loginForm
        .transform((data) => ({
            ...data,
            remember: loginForm.remember ? "on" : "",
        }))
        .post(route("login"), {
            onFinish: () => {
                loginForm.reset("password");
                loaderState.show = false;
            },
        });
};

const submitRegister = () => {
    loaderState.show = true;
    loaderState.type = 'dots';
    loaderState.message = 'Creando cuenta...';
    loaderState.submessage = 'Configurando tu perfil';

    registerForm.post(route("register"), {
        onFinish: () => {
            registerForm.reset("password", "password_confirmation");
            loaderState.show = false;
        },
    });
};

const toggleMode = () => {
    isRegisterMode.value = !isRegisterMode.value;
};
</script>

<template>
    <Head :title="isRegisterMode ? 'Register' : 'Log in'" />

    <div
        class="flex justify-center items-center min-h-screen w-full bg-[url('/img/login/bgLogin1.jpg')] bg-cover bg-center backdrop-blur-md animate-fade animate-duration-[800ms]"
    >
        <div
            class="w-full h-screen flex justify-center items-center backdrop-blur-md bg-black/50"
        >
            <div
                class="flex flex-col gap-6 max-w-5xl w-full shadow-xl bg-white rounded-xl overflow-hidden animate-fade-up animate-duration-[800ms] animate-ease-in-out"
            >
                <div class="overflow-hidden shadow-sm">
                    <div class="grid p-0 md:grid-cols-2 relative h-[550px]">
                        <!-- Login/Register Forms Container -->
                        <div
                            class="transition-all duration-300 ease-in-out items-center h-full"
                            :class="
                                isRegisterMode ? 'md:order-2' : 'md:order-1'
                            "
                        >
                            <!-- Login Form -->
                            <div
                                class="transition-all duration-300 ease-in-out transform h-full"
                                :class="
                                    isRegisterMode
                                        ? 'opacity-0 translate-x-full pointer-events-none absolute inset-0'
                                        : 'opacity-100 translate-x-0'
                                "
                            >
                                <form
                                    @submit.prevent="submitLogin"
                                    class="p-6 md:p-12 h-full w-full flex justify-center items-center"
                                >
                                    <div
                                        class="flex flex-col gap-6 justify-center w-full"
                                    >
                                        <div
                                            class="flex items-center justify-start gap-4 mb-4"
                                        >
                                            <img
                                                src="img/CotecmarLogo.png"
                                                alt="logo"
                                                class="w-16"
                                            />
                                            <div
                                                class="flex flex-col items-start text-center"
                                            >
                                                <h1 class="text-2xl font-bold">
                                                    Bienvenido
                                                </h1>
                                                <p
                                                    class="text-balance text-muted-foreground"
                                                >
                                                    Inicie sesión con su cuenta de COTECMAR
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            v-if="status"
                                            class="mb-4 font-medium text-sm text-green-600"
                                        >
                                            {{ status }}
                                        </div>

                                        <div class="grid gap-2">
                                            <label
                                                for="login-email"
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            >
                                                correo
                                            </label>

                                            <Textinput
                                                id="login-email"
                                                v-model="loginForm.email"
                                                type="email"
                                                placeholder="m@example.com"
                                                required
                                                autofocus
                                                autocomplete="username"
                                            />
                                            <div
                                                v-if="loginForm.errors.email"
                                                class="text-sm text-red-600"
                                            >
                                                {{ loginForm.errors.email }}
                                            </div>
                                        </div>

                                        <div class="grid gap-2">
                                            <div class="flex items-center">
                                                <label
                                                    for="login-password"
                                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                >
                                                    Contraseña
                                                </label>
                                                <Link
                                                    v-if="canResetPassword"
                                                    :href="
                                                        route(
                                                            'password.request'
                                                        )
                                                    "
                                                    class="ml-auto text-sm underline-offset-2 hover:underline"
                                                >
                                                    ¿Olvidaste tu contraseña?
                                                </Link>
                                            </div>
                                            <div class="relative">
                                                <Textinput
                                                    id="login-password"
                                                    v-model="loginForm.password"
                                                    :type="showLoginPassword ? 'text' : 'password'"
                                                    required
                                                    autocomplete="current-password"
                                                    class="pr-10"
                                                />
                                                <button
                                                    type="button"
                                                    @click="showLoginPassword = !showLoginPassword"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors"
                                                >
                                                    <svg v-if="!showLoginPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div
                                                v-if="loginForm.errors.password"
                                                class="text-sm text-red-600"
                                            >
                                                {{ loginForm.errors.password }}
                                            </div>
                                        </div>

                                        <div class="block">
                                            <label class="flex items-center">
                                                <input
                                                    v-model="loginForm.remember"
                                                    name="remember"
                                                    type="checkbox"
                                                    class="h-4 w-4 rounded border-gray-300 text-[#003882] focus:ring-[#003882]"
                                                />
                                                <span
                                                    class="ms-2 text-sm text-gray-600"
                                                    >Recuerdame</span
                                                >
                                            </label>
                                        </div>

                                        <button
                                            type="submit"
                                            class="w-full bg-[#003882] text-white py-2 rounded-lg border border-[#003882] hover:bg-[#002a66] focus:outline-none focus:ring-2 focus:ring-[#003882] focus:ring-offset-2 inline-flex items-center justify-center whitespace-nowrap font-medium ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 transition-all ease-in-out duration-300 hover:-translate-y-1"
                                            :class="{
                                                'opacity-25':
                                                    loginForm.processing,
                                            }"
                                            :disabled="loginForm.processing"
                                        >
                                            Ingresar
                                        </button>

                                        <div class="text-start text-sm">
                                            ¿No tienes una cuenta?
                                            <button
                                                type="button"
                                                @click="toggleMode"
                                                class="underline underline-offset-4 hover:text-[#003882] transition-colors"
                                            >
                                                Inscribirse
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Register Form -->
                            <div
                                class="transition-all duration-300 ease-in-out transform"
                                :class="
                                    !isRegisterMode
                                        ? 'opacity-0 -translate-x-full pointer-events-none absolute inset-0'
                                        : 'opacity-100 translate-x-0'
                                "
                            >
                                <form
                                    @submit.prevent="submitRegister"
                                    class="p-6 md:p-8 h-full"
                                >
                                    <div class="flex flex-col gap-4">
                                        <div
                                            class="flex items-center justify-start gap-4 w-full"
                                        >
                                            <img
                                                src="img/CotecmarLogo.png"
                                                alt="logo"
                                                class="w-16"
                                            />
                                            <div
                                                class="flex flex-col items-start text-start w-full"
                                            >
                                                <h1 class="text-2xl font-bold">
                                                    Crear cuenta
                                                </h1>
                                                <p
                                                    class="w-full"
                                                >
                                                    Regístrate para acceder a la plataforma COTECMAR
                                                </p>
                                            </div>
                                        </div>

                                        <div class="grid gap-2">
                                            <label
                                                for="register-name"
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            >
                                                Nombre
                                            </label>
                                            <Textinput
                                                id="register-name"
                                                v-model="registerForm.name"
                                                type="text"
                                                placeholder="John Doe"
                                                required
                                                autocomplete="name"
                                            />
                                            <div
                                                v-if="registerForm.errors.name"
                                                class="text-sm text-red-600"
                                            >
                                                {{ registerForm.errors.name }}
                                            </div>
                                        </div>

                                        <div class="grid gap-2">
                                            <label
                                                for="register-email"
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            >
                                                Correo
                                            </label>
                                            <Textinput
                                                id="register-email"
                                                v-model="registerForm.email"
                                                type="email"
                                                placeholder="m@example.com"
                                                required
                                                autocomplete="username"
                                            />
                                            <div
                                                v-if="registerForm.errors.email"
                                                class="text-sm text-red-600"
                                            >
                                                {{ registerForm.errors.email }}
                                            </div>
                                        </div>

                                        <div class="grid gap-2">
                                            <label
                                                for="register-password"
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            >
                                                Contraseña
                                            </label>
                                            <div class="relative">
                                                <Textinput
                                                    id="register-password"
                                                    v-model="registerForm.password"
                                                    :type="showRegisterPassword ? 'text' : 'password'"
                                                    required
                                                    autocomplete="new-password"
                                                    class="pr-10"
                                                />
                                                <button
                                                    type="button"
                                                    @click="showRegisterPassword = !showRegisterPassword"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors"
                                                >
                                                    <svg v-if="!showRegisterPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div
                                                v-if="
                                                    registerForm.errors.password
                                                "
                                                class="text-sm text-red-600"
                                            >
                                                {{
                                                    registerForm.errors.password
                                                }}
                                            </div>
                                        </div>

                                        <div class="grid gap-2">
                                            <label
                                                for="register-password-confirmation"
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            >
                                                Confirmar contraseña
                                            </label>
                                            <div class="relative">
                                                <Textinput
                                                    id="register-password-confirmation"
                                                    v-model="
                                                        registerForm.password_confirmation
                                                    "
                                                    :type="showRegisterPasswordConfirmation ? 'text' : 'password'"
                                                    required
                                                    autocomplete="new-password"
                                                    class="pr-10"
                                                />
                                                <button
                                                    type="button"
                                                    @click="showRegisterPasswordConfirmation = !showRegisterPasswordConfirmation"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors"
                                                >
                                                    <svg v-if="!showRegisterPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div
                                                v-if="
                                                    registerForm.errors
                                                        .password_confirmation
                                                "
                                                class="text-sm text-red-600"
                                            >
                                                {{
                                                    registerForm.errors
                                                        .password_confirmation
                                                }}
                                            </div>
                                        </div>

                                        <div
                                            v-if="
                                                $page.props.jetstream
                                                    ?.hasTermsAndPrivacyPolicyFeature
                                            "
                                            class="block"
                                        >
                                            <label class="flex items-start">
                                                <input
                                                    id="register-terms"
                                                    v-model="registerForm.terms"
                                                    name="terms"
                                                    type="checkbox"
                                                    class="h-4 w-4 rounded border-gray-300 text-[#003882] focus:ring-[#003882] mt-1"
                                                    required
                                                />
                                                <span
                                                    class="ms-2 text-sm text-gray-600"
                                                >

                                                    <a
                                                        target="_blank"
                                                        :href="
                                                            route('terms.show')
                                                        "
                                                        class="underline text-indigo-600 hover:text-indigo-900"
                                                        >Terms of Service</a
                                                    >
                                                    and
                                                    <a
                                                        target="_blank"
                                                        :href="
                                                            route('policy.show')
                                                        "
                                                        class="underline text-indigo-600 hover:text-indigo-900"
                                                        >Privacy Policy</a
                                                    >
                                                </span>
                                            </label>
                                            <div
                                                v-if="registerForm.errors.terms"
                                                class="text-sm text-red-600 mt-1"
                                            >
                                                {{ registerForm.errors.terms }}
                                            </div>
                                        </div>

                                        <button
                                            type="submit"
                                            class="w-full bg-[#003882] text-white py-2 rounded-lg border border-[#003882] hover:bg-[#002a66] focus:outline-none focus:ring-2 focus:ring-[#003882] focus:ring-offset-2 inline-flex items-center justify-center whitespace-nowrap font-medium ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 transition-all ease-in-out duration-300 hover:-translate-y-1"
                                            :class="{
                                                'opacity-25':
                                                    registerForm.processing,
                                            }"
                                            :disabled="registerForm.processing"
                                        >
                                            Registrarme
                                        </button>

                                        <div class="text-center text-sm">
                                            ¿Ya tienes una cuenta?
                                            <button
                                                type="button"
                                                @click="toggleMode"
                                                class="underline underline-offset-4 hover:text-[#003882] transition-colors"
                                            >
                                                Iniciar sesión
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Dynamic Image Panel -->
                        <div
                            class="relative hidden bg-muted md:block transition-all duration-700 ease-in-out overflow-hidden z-50"
                            :class="
                                isRegisterMode ? 'md:order-1' : 'md:order-2'
                            "
                        >
                            <!-- Imagen de Login -->
                            <div
                                class="absolute inset-0 transition-all duration-700 ease-in-out"
                                :class="
                                    isRegisterMode
                                        ? 'opacity-0 scale-110'
                                        : 'opacity-100 scale-100'
                                "
                            >
                                <img
                                    src="/img/login/login1.jpg"
                                    alt="Login Background"
                                    class="absolute inset-0 h-full w-full object-cover"
                                />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-8"
                                >
                                    <div class="text-white">
                                        <h2 class="text-2xl font-bold mb-2">
                                            Bienvenido a COTECMAR
                                        </h2>
                                        <p class="text-gray-200">
                                            Accede a la cuenta y continúa con tus proyectos navales.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Imagen de Register -->
                            <div
                                class="absolute inset-0 transition-all duration-700 ease-in-out"
                                :class="
                                    !isRegisterMode
                                        ? 'opacity-0 scale-110'
                                        : 'opacity-100 scale-100'
                                "
                            >
                                <img
                                    src="/img/login/register.jpg"
                                    alt="Register Background"
                                    class="absolute inset-0 h-full w-full object-cover"
                                />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-8"
                                >
                                    <div class="text-white">
                                        <h2 class="text-2xl font-bold mb-2">
                                            Unete a COTECMAR
                                        </h2>
                                        <p class="text-gray-200">
                                            Crea tu cuenta y forma parte de nuestra comunidad naval.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Loader
        :show="loaderState.show"
        :type="loaderState.type"
        :message="loaderState.message"
        :submessage="loaderState.submessage"
    />
</template>

<style scoped>
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
