<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const email = ref("");
const password = ref("");
const error = ref("");
const loading = ref(false);

const auth = useAuthStore();
const router = useRouter();

async function handleLogin() {
    error.value = "";
    loading.value = true;
    try {
        await auth.login(email.value, password.value);
        await auth.fetchMe();
        router.push("/");
    } catch (e) {
        error.value = e.response?.data?.message || "Login failed";
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-950 px-4">
        <div class="w-full max-w-sm bg-slate-800 border border-slate-700 rounded-xl p-8">
            <h1 class="text-2xl font-bold text-white mb-6 text-center">Login</h1>

            <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
                <input
                    v-model="email"
                    type="email"
                    placeholder="Email"
                    required
                    class="bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <input
                    v-model="password"
                    type="password"
                    placeholder="Password"
                    required
                    class="bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button
                    type="submit"
                    :disabled="loading"
                    class="bg-blue-500 hover:bg-blue-600 disabled:bg-slate-600 disabled:cursor-not-allowed text-white text-sm font-medium py-2.5 rounded-md mt-2"
                >
                    {{ loading ? "Logging in..." : "Login" }}
                </button>
            </form>

            <p v-if="error" class="text-red-400 text-sm mt-4 text-center">{{ error }}</p>

            <router-link
                to="/register"
                class="block text-center text-sm text-blue-400 hover:text-blue-300 mt-5"
            >
                Don't have an account? Register
            </router-link>
        </div>
    </div>
</template>
