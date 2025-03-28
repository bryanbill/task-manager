import axios from "@/utils/api";

export const authActions = {
    async login({ commit }, credentials) {
        const response = await axios.post("/login", credentials);
        commit("setToken", response.data.access_token);
        commit("setUser", response.data.user);
        return response.data;
    },

    async register({ commit }, userData) {
        const response = await axios.post("/register", userData);
        commit("setToken", response.data.access_token);
        commit("setUser", response.data.user);
        return response.data;
    },

    async logout({ commit }) {
        await axios.post("/logout");
        commit("clearAuth");
    },

    async refreshToken({ commit }) {
        const response = await axios.post("/refresh-token");
        commit("setToken", response.data.access_token);
        return response.data;
    },
};

export const taskActions = {
    async fetchTasks(
        { commit },
        {
            page = 1,
            search = "",
            due_date_from = null,
            due_date_to = null,
            sort = "asc",
        }
    ) {
        const params = { page };
        if (search) params.search = search;
        if (due_date_from) params.due_date_from = due_date_from;
        if (due_date_to) params.due_date_to = due_date_to;
        params.sort = sort;

        const response = await axios.get("/tasks", { params });
        commit("setTasks", response.data.data);
        commit("setPagination", {
            currentPage: response.data.current_page,
            links: response.data.links,
        });
        return response.data.data;
    },

    async fetchTask({ commit }, taskId) {
        const response = await axios.get(`/tasks/${taskId}`);
        commit("setTask", response.data);
        return response.data;
    },

    async createTask({ commit }, taskData) {
        console.log(taskData);
        const response = await axios.post("/tasks", taskData);
        return response.data;
    },

    async updateTask({ commit }, { id, ...taskData }) {
        const response = await axios.put(`/tasks/${id}`, taskData);
        return response.data;
    },

    async deleteTask({ commit }, taskId) {
        await axios.delete(`/tasks/${taskId}`);
    },
};
