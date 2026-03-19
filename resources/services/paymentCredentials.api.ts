import { http } from "./http.config";
import { useAppContextStore } from "../stores/appContext";

export const getTenantPaymentCredentials = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.get<T>(`/api/tenant/${tenantCode}/payment-credentials`, { params });
  return response.data;
};
