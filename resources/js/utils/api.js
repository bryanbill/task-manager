import axios from "axios";
// Create axios instance
const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || "/api",
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
    },
});

// Request interceptor
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("token");

        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor
api.interceptors.response.use(
    (response) => {
        return response;
    },
    async (error) => {
        const originalRequest = error.config;

        // Handle token refresh on 401 errors
        if (
            error.response?.status === 401 &&
            !originalRequest._retry &&
            originalRequest.url !== "/refresh-token"
        ) {
            originalRequest._retry = true;

            try {
                // Attempt to refresh token
                const response = await api.post("/refresh-token");
                const { access_token } = response.data;

                // set to localStorage
                localStorage.setItem("token", access_token);

                // Retry the original request
                originalRequest.headers.Authorization = `Bearer ${access_token}`;
                return api(originalRequest);
            } catch (refreshError) {
                localStorage.removeItem("token");

                return Promise.reject(refreshError);
            }
        }

        return Promise.reject(error);
    }
);

export default api;
