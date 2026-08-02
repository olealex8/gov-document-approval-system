<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const form = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    company_name: "",
    registration_number: "",
    phone: "",
    address: "",
});

const errors = ref({});
const loading = ref(false);

const auth = useAuthStore();
const router = useRouter();

async function handleRegister() {
    errors.value = {};
    loading.value = true;
    try {
        await auth.register(form.value);
        await auth.fetchMe();
        router.push("/");
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
        } else {
            errors.value = { general: [e.response?.data?.message || "Registration failed"] };
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-950 px-4 py-10">
        <div class="w-full max-w-md bg-slate-800 border border-slate-700 rounded-xl p-8">
            <h1 class="text-2xl font-bold text-white mb-6 text-center">Register</h1>

            <form @submit.prevent="handleRegister" class="flex flex-col gap-4">
                <div>
                    <input
                        v-model="form.name"
                        placeholder="Full Name"
                        required
                        class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <span v-if="errors.name" class="text-red-400 text-xs mt-1 block">{{ errors.name[0] }}</span>
                </div>

                <div>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="Email"
                        required
                        class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <span v-if="errors.email" class="text-red-400 text-xs mt-1 block">{{ errors.email[0] }}</span>
                </div>

                <div>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Password"
                        required
                        class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <span v-if="errors.password" class="text-red-400 text-xs mt-1 block">{{ errors.password[0] }}</span>
                </div>

                <input
                    v-model="form.password_confirmation"
                    type="password"
                    placeholder="Confirm Password"
                    required
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />

                <hr class="border-slate-700 my-2" />

                <div>
                    <input
                        v-model="form.company_name"
                        placeholder="Company Name"
                        required
                        class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <span v-if="errors.company_name" class="text-red-400 text-xs mt-1 block">{{ errors.company_name[0] }}</span>
                </div>

                <input
                    v-model="form.registration_number"
                    placeholder="Registration Number (optional)"
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <input
                    v-model="form.phone"
                    placeholder="Phone (optional)"
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <input
                    v-model="form.address"
                    placeholder="Address (optional)"
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />

                <button
                    type="submit"
                    :disabled="loading"
                    class="bg-blue-500 hover:bg-blue-600 disabled:bg-slate-600 disabled:cursor-not-allowed text-white text-sm font-medium py-2.5 rounded-md mt-2"
                >
                    {{ loading ? "Registering..." : "Register" }}
                </button>
            </form>

            <p v-if="errors.general" class="text-red-400 text-sm mt-4 text-center">{{ errors.general[0] }}</p>

            <router-link
                to="/login"
                class="block text-center text-sm text-blue-400 hover:text-blue-300 mt-5"
            >
                Already have an account? Login
            </router-link>
        </div>
    </div>
</template>