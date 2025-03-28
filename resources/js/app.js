import { createApp } from "vue";
import Toast from "vue-toastification";
import router from "./router";
import store from "./store";
import App from "./App.vue";
import "vue-toastification/dist/index.css";

const app = createApp(App);

app.use(store);
app.use(router);
app.use(Toast, {
    position: "top-right",
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: true,
    closeButton: "button",
    icon: true,
    rtl: false,
});
app.mount("#app");
