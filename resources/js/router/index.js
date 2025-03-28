import { createRouter, createWebHashHistory } from "vue-router";
import { useStore } from "vuex";
const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        {
            path: "/auth",
            name: "auth",
            children: [
                {
                    path: "login",
                    name: "login",
                    component: () => import("@/views/auth/Login.vue"),
                    meta: { requiresGuest: true },
                },
                {
                    path: "register",
                    name: "register",
                    component: () => import("@/views/auth/Register.vue"),
                    meta: { requiresGuest: true },
                },
            ],
        },
        {
            path: "/tasks",
            name: "tasks.index",
            component: () => import("@/views/tasks/Index.vue"),
            meta: { requiresAuth: true },
        },
        {
            path: "/tasks/create",
            name: "tasks.create",
            component: () => import("@/views/tasks/Create.vue"),
            meta: { requiresAuth: true },
        },
        {
            path: "/tasks/:id",
            name: "tasks.show",
            component: () => import("@/views/tasks/Show.vue"),
            meta: { requiresAuth: true },
        },
        {
            path: "/tasks/:id/edit",
            name: "tasks.edit",
            component: () => import("@/views/tasks/Edit.vue"),
            meta: { requiresAuth: true },
        },
        {
            path: "/",
            redirect: "/tasks",
        },
    ],
});

router.beforeEach((to, from, next) => {
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
    const requiresGuest = to.matched.some(
        (record) => record.meta.requiresGuest
    );
    const store = useStore();
    const token = store.state.token;

    if (requiresAuth && !token) {
        next({ name: "login" });
    } else if (requiresGuest && token) {
        next({ name: "tasks.index" });
    } else {
        next();
    }
});

export default router;
