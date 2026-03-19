import { createRouter, createWebHistory } from "vue-router";
import { tenantRoutes } from "./route-tenants";
import { TENANTPANEL_ACCOUNT_KEY, getAccessToken } from "../services/tenantAuth";
import { useAppContextStore } from "../stores/appContext";
import NotFoundPage from "../pages/shared/NotFoundPage.vue";

export const router = createRouter({
  history: createWebHistory("/"),
  routes: [
    {
      path: "/tenant-panel",
      children: tenantRoutes,
      beforeEnter: (to) => {
        const token = getAccessToken();
        const isLoginRoute = to.name === "tenant.login";

        if (!token && !isLoginRoute) {
          return { name: "tenant.login" };
        }

        if (token && isLoginRoute) {
          return { name: "tenant.dashboard" };
        }

        return true;
      },
    },
    { path: "/:pathMatch(.*)*", name: "not-found", component: NotFoundPage },
  ],
});

router.beforeEach((to) => {
  const store = useAppContextStore();
  const context = to.meta.context as "tenant" | undefined;

  if (context) {
    const storedUser = localStorage.getItem(TENANTPANEL_ACCOUNT_KEY);
    let userData: any = { id: "", name: "Guest User", email: "" };

    if (storedUser) {
      try {
        const parsed = JSON.parse(storedUser);
        userData = {
          id: parsed.id || "",
          name: parsed.name || "Guest User",
          email: parsed.email || "",
          role: parsed.role,
          tenant: parsed.tenant,
        };
      } catch (e) {}
    }

    const menu = (to.meta.menu as any[]) || [];
    store.setContext("tenant", userData, menu);
  }
});
