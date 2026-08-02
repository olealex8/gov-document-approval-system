import { defineStore } from "pinia";
import api from "../api/axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("user")) || null,
        token: localStorage.getItem("token") || null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
        roles: (state) => state.user?.roles?.map((r) => r.name) || [],
        hasRole: (state) => (role) =>
            state.user?.roles?.some((r) => r.name === role) || false,
    },
    actions: {
        async login(email, password) {
            const { data } = await api.post("/login", { email, password });
            this.setSession(data);
        },
        async register(payload) {
            const { data } = await api.post("/register", payload);
            this.setSession(data);
        },
        async fetchMe() {
            const { data } = await api.get("/me");
            this.user = data;
            localStorage.setItem("user", JSON.stringify(data));
        },
        async logout() {
            await api.post("/logout");
            this.clearSession();
        },
        setSession(data) {
            this.user = data.user;
            this.token = data.token;
            localStorage.setItem("token", data.token);
            localStorage.setItem("user", JSON.stringify(data.user));
        },
        clearSession() {
            this.user = null;
            this.token = null;
            localStorage.removeItem("token");
            localStorage.removeItem("user");
        },
    },
});
