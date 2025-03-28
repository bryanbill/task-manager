import { createStore } from "vuex";
import { authActions, taskActions } from "./actions";

const store = createStore({
    state() {
        return {
            user: null,
            token: localStorage.getItem("token") || null,
            isLoading: false,
            tasks: [],
            pagination: {
                currentPage: 1,
                links: [],
            },
            task: null,
        };
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        },
        setToken(state, token) {
            state.token = token;
            localStorage.setItem("token", token);
        },
        clearAuth(state) {
            state.user = null;
            state.token = null;
            localStorage.removeItem("token");
        },
        setLoading(state, isLoading) {
            state.isLoading = isLoading;
        },
        setTasks(state, tasks) {
            state.tasks = tasks;
        },
        setPagination(state, pagination) {
            state.pagination = pagination;
        },
        setTask(state, task) {
            state.task = task;
        },
    },
    actions: {
        ...authActions,
        ...taskActions,
    },
});

export default store;
