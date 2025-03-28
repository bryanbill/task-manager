import { useToast } from "vue-toastification";
import { useRouter } from "vue-router";
import { useStore } from "vuex";

/**
 * Global error handler for API requests
 * @param {Error} error - The error object
 * @param {Object} context - Optional context object
 */
export const handleError = (error, context = {}) => {
    const toast = useToast();
    const router = useRouter();
    const store = useStore();

    // Default error message
    let errorMessage = "An unexpected error occurred";
    let shouldRedirect = false;
    let redirectRoute = { name: "login" };

    // Handle Axios errors (API responses)
    if (error.response) {
        const { status, data } = error.response;

        switch (status) {
            case 400:
                errorMessage = data.message || "Invalid request";
                break;
            case 401:
                errorMessage = "Session expired. Please login again.";
                store.dispatch("refreshToken");
                shouldRedirect = true;
                break;
            case 403:
                errorMessage = "You are not authorized to perform this action";
                shouldRedirect = true;
                redirectRoute = { name: "tasks" };
                break;
            case 404:
                errorMessage = "Resource not found";
                shouldRedirect = true;
                redirectRoute = { name: "not-found" };
                break;
            case 422:
                // Validation errors - return them to the form
                return data.errors || {};
            case 500:
                errorMessage = "Server error. Please try again later.";
                break;
            default:
                errorMessage = data.message || `HTTP Error ${status}`;
        }
    } else if (error.request) {
        // The request was made but no response was received
        errorMessage = "Network error. Please check your connection.";
    } else {
        // Something happened in setting up the request
        errorMessage = error.message || "Request setup error";
    }

    // Show error toast
    toast.error(errorMessage);

    // Redirect if needed
    if (shouldRedirect) {
        router.push(redirectRoute);
    }

    // Return error details for components to handle
    return {
        message: errorMessage,
        status: error.response?.status,
        data: error.response?.data,
    };
};

/**
 * Handles API errors and returns validation errors if any
 * @param {Error} error - The error object
 * @returns {Object} Validation errors or null
 */
export const handleValidationErrors = (error) => {
    if (error.response?.status === 422) {
        return error.response.data.errors || {};
    }
    handleError(error);
    return null;
};
