<script setup>
import { useAuthStore } from "./stores/auth";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

async function handleLogout() {
    await auth.logout();
    router.push("/login");
}
</script>

<template>
    <div id="app" class="min-h-screen bg-slate-950">
        <nav
            v-if="auth.isAuthenticated"
            class="flex items-center gap-6 px-6 py-3 bg-slate-800 border-b border-slate-700"
        >
            <router-link
                to="/"
                class="text-md font-semibold text-slate-200 hover:text-white no-underline"
            >
                Dashboard
            </router-link>
            <router-link
                to="/document-requests"
                class="text-md font-semibold text-slate-200 hover:text-white no-underline"
            >
                Document Requests
            </router-link>
            <router-link
                v-if="auth.hasRole('penguji')"
                to="/users"
                class="text-md font-semibold text-slate-200 hover:text-white no-underline"
            >
                Users
            </router-link>

            <span class="ml-auto text-md text-slate-400">
                {{ auth.user?.name }} ({{ auth.roles.join(", ") }})
            </span>

            <button
                @click="handleLogout"
                class="bg-red-500 hover:bg-red-600 text-white text-md font-medium px-4 py-1.5 rounded-md"
            >
                Logout
            </button>
        </nav>
        <router-view />
    </div>
</template>
