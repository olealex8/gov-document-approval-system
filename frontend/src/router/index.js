import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

import LoginView from "../views/LoginView.vue";
import RegisterView from "../views/RegisterView.vue";
import DashboardView from "../views/DashboardView.vue";
import DocumentRequestListView from "../views/DocumentRequestListView.vue";
import DocumentRequestDetailView from "../views/DocumentRequestDetailView.vue";
import DocumentRequestCreateView from "../views/DocumentRequestCreateView.vue";
import UsersListView from "../views/UsersListView.vue";

const routes = [
    { path: "/login", component: LoginView },
    { path: "/register", component: RegisterView },
    { path: "/", component: DashboardView, meta: { requiresAuth: true } },
    { path: "/document-requests", component: DocumentRequestListView, meta: { requiresAuth: true } },
    { path: "/document-requests/create", component: DocumentRequestCreateView, meta: { requiresAuth: true } },
    { path: "/document-requests/:id", component: DocumentRequestDetailView, meta: { requiresAuth: true }, props: true },
    { path: "/users", component: UsersListView, meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const auth = useAuthStore();
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return "/login";
    }
});

export default router;
