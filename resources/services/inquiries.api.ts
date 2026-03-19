import { http } from "./http.config";
import { useAppContextStore } from "../stores/appContext";

export const createTenantInquiry = async <T>(payload: any): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.post<T>(`/api/tenant/${tenantCode}/inquiries`, payload);
  return response.data;
};
